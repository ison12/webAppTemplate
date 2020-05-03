<?php

use App\Common\Shutdown\ShutdownManager;
use App\Middleware\AuthCheckMiddleware;
use App\Middleware\AuthPrepareMiddleware;
use App\Middleware\AuthProcessMiddleware;
use App\Middleware\DBQueryLoggingMiddleware;
use Slim\Exception\HttpNotFoundException;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// -----------------------------------------------------------------------------
// フロントコントローラ index.php
//
// ここでは、HTTPリクエストに関する共通処理を定義したり実行する。
// -----------------------------------------------------------------------------

/*
 * PHP起動時の共通処理実行
 */
$app = require __DIR__ . '/../src/bootstrap.php';

$container = $app->getContainer();
$config = $container->get('config');
$logger = $container->get('logger');

/*
 * Middlewareの設定
 * addメソッドの呼び出し順に、Slim\Appを中心とした同心円状の内側に追加されていくイメージになる。
 * そのため、最後に追加されたMiddlewareが最初に実行される処理となる。
 *
 * http://www.slimframework.com/docs/v4/concepts/middleware.html
 */
$app->addRoutingMiddleware();
$app->addErrorMiddleware(
        $config['environment'] !== 'production' /* $displayErrorDetails */,
        true /* $logErrors */,
        true /* $logErrorDetails */,
        $logger /* $logger */);

$app->add(new ContentLengthMiddleware());
$app->add(new AuthProcessMiddleware($app));
$app->add(new AuthCheckMiddleware($app));
$app->add(new AuthPrepareMiddleware($app));
$app->add(new DBQueryLoggingMiddleware($app));

/*
 * Routingの設定
 * http://www.slimframework.com/docs/v4/objects/routing.html
 */
$exception = null;

$app->any('/[{func1}[/{func2}[/{func3}]]]', function (Request $request, Response $response, array $args) use($app, &$exception) {
    // 本処理にて、上位にスローされた例外は、SlimのaddErrorMiddlewareにより追加されたエラーハンドラで処理される
    // その際に、ロガーオブジェクトによりエラーログが出力される

    $func1 = $args['func1'] ?? null;
    $func2 = $args['func2'] ?? null;
    $func3 = $args['func3'] ?? null;

    $class = '';
    $method = '';
    $pattern = null;

    if ($func1 === $func2) {
        // 例）/login/login/index → Func\Login\Controller\LoginControllerが存在していたとしても、同様の単語が2つ続くパターンは無効とする
        throw new HttpNotFoundException($request);
    }

    if ($func1 && $func2 && $func3) {

        // 例）/password/change/exec → Func\Login\Controller\AuthController#actionExec
        $pattern = 1;
        $class = '\\App\\Func\\' . $func1 . '\\Controller\\' . $func2 . 'Controller';
        $method = 'action' . $func3;
    } else if ($func1 && $func2) {

        // 例）/password/change → Func\Password\Controller\ChangeController#actionIndex
        $pattern = 2;
        $class = '\\App\\Func\\' . $func1 . '\\Controller\\' . $func2 . 'Controller';
        $method = 'actionIndex';

        if (!class_exists($class)) {
            // 例）/login/exec → Func\Login\Controller\LoginController#actionExec
            $class = '\\App\\Func\\' . $func1 . '\\Controller\\' . $func1 . 'Controller';
            $method = 'action' . $func2;
        }
    } else if ($func1) {
        // 例）/login → Func\Login\Controller\LoginController#actionIndex
        $pattern = 3;
        $class = '\\App\\Func\\' . $func1 . '\\Controller\\' . $func1 . 'Controller';
        $method = 'actionIndex';
    }

    if (!class_exists($class)) {
        throw new HttpNotFoundException($request);
    }

    $controller = new $class($app, $request, $response);
    if (!method_exists($controller, $method)) {
        throw new HttpNotFoundException($request);
    }

    try {
        return $controller->$method();
    } catch (Throwable $exc) {
        $exception = $exc;
        throw $exc;
    }
});

/*
 * アプリケーションの実行
 */
$app->run();

/*
 * アプリケーション実行時に発生した未キャッチ例外（があれば）を設定する
 */
$shutdownManager = ShutdownManager::getInstance();
$shutdownManager->setLastUncaughtException($exception);
