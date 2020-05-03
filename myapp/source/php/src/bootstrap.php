<?php

use App\Common\App\AppContext;
use App\Common\Log\AppLogger;
use App\Common\Shutdown\ShutdownManager;
use DI\Container;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

const PUBLIC_PATH = __DIR__ . '/../public/';
const SRC_PATH = __DIR__ . '/';

// -----------------------------------------------------------------------------
// PHP起動時の共通処理の定義 bootstrap.php
//
// ここでは、PHP起動時に関する共通処理の定義したり実行する。
// 本処理では、Webリクエストまたはバッチ起動などで呼び出されることを想定している。
// -----------------------------------------------------------------------------

/*
 * ロケール指定（php.iniやini_set関数で変更できないので、起動共通処理でロケール指定を実施する）
 * ※basename・pathinfo関数などでUTF-8などのマルチバイト文字を取り扱う際に事前に設定が必要
 */
setlocale(LC_ALL, 'ja_JP.UTF-8');
/*
 * クラスオートローダーの読み込み
 */
$autoloader = require __DIR__ . '/../vendor/autoload.php';

/*
 * エラー例外をハンドリングする
 */
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting, so ignore it
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

/*
 * プログラム終了時に必ず呼び出されるコールバック関数を登録
 */
$shutdownManager = ShutdownManager::getInstance();
$shutdownManager->regist(function($errorData) {

    if ($errorData !== null) {
        // エラー時にログ出力する
        $log = AppLogger::get();
        if ($log->isErrorEnabled()) {
            $log->error('Unexpected error occurred in shutdown function.', ['exception' => $errorData]);
        }
    }
});

/*
 * セッションを有効にする
 */
session_start();
header_remove('Cache-Control');

/*
 * Slimアプリケーションオブジェクトの初期化
 */
if (!isset($configPath)) {
    $configSuffix = filter_input(INPUT_ENV, '__ENV') ? '.' . filter_input(INPUT_ENV, '__ENV') : '';
    $configPath = __DIR__ . '/../config/appConfig' . $configSuffix . '.php';
}

$config = require $configPath;

$container = new Container();
$container->set('config', $config);

$app = AppFactory::createFromContainer($container);
$app->setBasePath($config['baseUri'] ?? '');

/*
 * DIコンテナの初期化
 */
$container = $app->getContainer();

// PHP View Renderer
$container->set('renderer', function (/* ContainerInterface $c */) {
    return new PhpRenderer(SRC_PATH);
});

// monolog
$container->set('logger', function ($c) {
    $loggerConfig = $c->get('config')['logger'];
    $logger = new Logger($loggerConfig['name']);
    $logger->pushProcessor(new UidProcessor());

    $handler = new RotatingFileHandler($loggerConfig['path'], 0, $loggerConfig['level'], true, 0664);
    $handler->setFilenameFormat('{date}-{filename}', 'Y/m/d');
    $logger->pushHandler($handler);

    //$logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
});

/*
 * アプリケーションオブジェクトとロガーを設定する
 */
AppContext::set($app);
AppLogger::set($container->get('logger'));

return $app;
