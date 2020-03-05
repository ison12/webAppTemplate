<?php

use App\Common\Log\AppLogger;

// 第3引数からパラメータを取得する
$params = $argv[2] ?? null;

// json文字列からデータを復元する
$paramsObj = [];
$paramsObjError = 0;
if ($params !== null) {
    $paramsObj = json_decode($params, true);
    $paramsObjError = json_last_error();
}

/*
 * サーバーパラメータの設定
 */
if (isset($paramsObj['LOCAL_ADDR'])) {
    $_SERVER['LOCAL_ADDR'] = $paramsObj['LOCAL_ADDR'];
}
if (isset($paramsObj['HTTPS'])) {
    $_SERVER['HTTPS'] = $paramsObj['HTTPS'];
}
if (isset($paramsObj['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = $paramsObj['SERVER_NAME'];
}
if (isset($paramsObj['SERVER_PORT'])) {
    $_SERVER['SERVER_PORT'] = $paramsObj['SERVER_PORT'];
}
if (isset($paramsObj['REMOTE_ADDR'])) {
    $_SERVER['REMOTE_ADDR'] = $paramsObj['REMOTE_ADDR'];
}
if (isset($paramsObj['REQUEST_URL'])) {
    $_SERVER['REQUEST_URL'] = $paramsObj['REQUEST_URL'];
}
if (isset($paramsObj['APP_POOL_ID'])) {
    $_SERVER['APP_POOL_ID'] = $paramsObj['APP_POOL_ID'];
}

// Boot the app
$app = require __DIR__ . '/../../bootstrap.php';

/**
 * オブジェクトの生成
 */
// 第2引数からクラス名を取得する
$className = $argv[1];
$classNameArray = explode('\\', $className);

$log = AppLogger::get();

// コマンドオブジェクトを生成する
$obj = new $className();

set_time_limit(0);

if ($paramsObj === null) {
    $log->error('パラメータ : ' . $params . ', Jsonエラー : ' . $paramsObjError);
    exit(1);
}

$username = getenv('USERNAME') ?: getenv('USER');
$log->info('コマンド : ' . $className);
$log->info('現在のユーザー : ' . get_current_user() . ', ' . $username);
$log->info('パラメータ : ' . $params);
$log->info('パラメータ : ' . print_r($paramsObj, true));

/**
 * コマンドの実行
 */
$ret = null;
try {
    // コマンドを実行する
    $ret = $obj->exec($paramsObj);
} catch (Throwable $ex) {
    $log->error((string) $ex);
    $ret = 1;
}

$log->info('結果コード : ' . $ret);

exit($ret);
