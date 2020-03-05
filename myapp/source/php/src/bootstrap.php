<?php

use App\Common\App\AppContext;
use App\Common\Log\AppLogger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Slim\App;
use Slim\Views\PhpRenderer;

const PUBLIC_PATH = __DIR__ . '/../public/';
const SRC_PATH = __DIR__ . '/';

/*
 * Handle ErrorException
 */
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting, so ignore it
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$GLOBALS['shutdownFunctions'] = [];
$GLOBALS['unexpectedException'] = null;

// プログラム終了時一律でコールされる register_shutdown_function にコールバックを登録。
register_shutdown_function(function() {

    $log = AppLogger::get();

    $errorData = null;
    $ce = $GLOBALS['unexpectedException']; // 重大なエラー

    if ($ce === null) {
        $e = error_get_last();
        if ($e !== null && (
                $e['type'] === E_ERROR ||
                $e['type'] === E_PARSE ||
                $e['type'] === E_CORE_ERROR ||
                $e['type'] === E_COMPILE_ERROR ||
                $e['type'] === E_USER_ERROR)) {
            // error_get_last()の返却値を見て、 「重大なエラーなら」 処理する。
            // 重大なエラーでなければ、set_error_handler 例外変換が動くはず。
            // これがないと、ハンドリングされていないエラーでは「白い画面」が出る。
            $ce = $e;
        }
    }

    $errorData = $ce;

    if ($log !== null && $log->isErrorEnabled() && $errorData !== null) {

        $log->error('unexpected error occurred.', ['exception' => $errorData]);
    }

    // 登録されたシャットダウン関数を順次コールする
    $shutdownFunctions = $GLOBALS['shutdownFunctions'];
    foreach (array_reverse($shutdownFunctions) as $shutdownFunction) {
        $shutdownFunction($errorData);
    }
});

/*
 * Load the autoloader
 */
$autoloader = require __DIR__ . '/../vendor/autoload.php';

/*
 * Start the session
 */
session_start();
header_remove('Cache-Control');

/*
 * Instantiate the app
 */
if (!isset($settingsPath)) {
    $settingsSuffix = filter_input(INPUT_ENV, '__ENV') ? '.' . filter_input(INPUT_ENV, '__ENV') : '';
    $settingsPath = __DIR__ . '/../config/appConfig' . $settingsSuffix . '.php';
}

$settings = require $settingsPath;
$app = new App($settings);

/*
 * Set up dependencies
 */
$container = $app->getContainer();

// View Renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new PhpRenderer($settings['template_path']);
};

// Override the default Not Found Handler
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {

        $renderer = $container->renderer;

        $uri = $request->getUri();
        $url = $uri->getBasePath() . '/' . $uri->getPath();
        if ($request->isXhr()) {
            $page = json_encode(['url' => $url, 'message' => "The requested URL $url was not found on this server."]);
        } else {
            $page = $renderer->fetch('/View/Error/404.php', ['url' => $url]);
        }

        return $container['response']
                        ->withStatus(404)
                        ->withHeader('Content-Type', 'text/html')
                        ->write($page);
    };
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new UidProcessor());

    $handler = new RotatingFileHandler($settings['path'], 0, $settings['level'], true, 0664);
    $handler->setFilenameFormat('{date}-{filename}', 'Y/m/d');
    $logger->pushHandler($handler);

    //$logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

AppContext::set($app);
AppLogger::set($container->get('logger'));

return $app;
