<?php

use App\Common\Log\AppLogger;
use App\Common\Util\FileUtil;

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
if (isset($paramsObj['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $paramsObj['REQUEST_URI'];
}

// Boot the app（クラスローダーを合わせて読み込む）
$app = require __DIR__ . '/../../bootstrap.php';

// ↓↓以降、クラスのオートローダーが動き出す
// --------------------------------------------------
// 第2引数からクラス名を取得する
$className = $argv[1];
$classNameArray = explode('\\', $className);
// 第3引数からパラメータを取得する
$argsFilePath = $argv[2] ?? null;
// パラメータ文字列を取得する
$params = FileUtil::readFile($argsFilePath);

// json文字列からデータを復元する
$paramsObj = [];
$paramsObjError = 0;
$paramsObjErrorMsg = '';
if ($params !== null) {
    $paramsObj = json_decode($params, true);
    $paramsObjError = json_last_error();
    $paramsObjErrorMsg = json_last_error_msg();
}

$log = AppLogger::get();

if ($paramsObj === null) {
    $log->error('パラメータ : ' . $params . ', Jsonエラー : ' . $paramsObjError . ', Jsonエラーメッセージ : ' . $paramsObjErrorMsg);
    exit(1);
}

// パラメータファイルを削除する
FileUtil::delete($argsFilePath);

/**
 * オブジェクトの生成
 */
// コマンドオブジェクトを生成する
$obj = new $className();

set_time_limit(0);
ini_set('memory_limit', '2048M');

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
