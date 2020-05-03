<?php

return [
    // 環境設定
    // development or test or staging or production
    'environment' => 'development',
    // ベースURI
    'baseUri' => '/myapp',
    // 公開ページ（公開されているページでは認証チェックを実施しない）
    // 設定値は全て小文字にすること
    'publicPage' => [
        'login' => true,
        'logout' => true,
        'password' => [
            'change' => true,
            'changerequest' => true
        ],
        'user' => [
            'regist' => true,
            'registrequest' => true
        ],
    ],
    // Monolog settings
    'logger' => [
        'name' => 'app',
        'path' => __DIR__ . '/../log/app.log',
        'level' => \Monolog\Logger::DEBUG,
    ],
    // コマンド実行
    'command' => [
        // Windowsは "php-win"、Linuxは "php" とする
        'phpExecPath' => "php-win",
        // Windowsのファイル区切り文字はバックスラッシュとする "\" 、Linuxはスラッシュとする "/"
        'phpExecConfigPath' => "php.ini",
    ],
    // DBキャッシュ
    'dbCache' => __DIR__ . '/../cache/db/',
    // DB設定
    'db' => [
        'default' => [
            'type' => 'mysql',
            'connectionStr' => 'mysql:host=127.0.0.1;dbname=myapp;',
            'userId' => 'myappapp',
            'password' => 'myappapp',
            'connectTimeoutMsec' => 30 * 1000,
            'queryTimeoutMsec' => 30 * 1000,
        ],
    ],
];
