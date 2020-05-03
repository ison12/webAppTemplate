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
        'phpExecConfigPath' => "D:/xampp7.4/php/php.ini",
    ],
    // DBキャッシュ
    'dbCache' => __DIR__ . '/../cache/db/',
    'db' => [
        'default' => [// 業務アプリ用のテーブルが保持されているカタログ
            'type' => 'mysql',
            'connectionStr' => 'mysql:dbname=unit_test_app; host=127.0.0.1; port=3306;',
            'userId' => 'root',
            'password' => 'password',
            'connectTimeoutMsec' => 30 * 1000,
            'queryTimeoutMsec' => 30 * 1000,
        ],
        'pgsql_master' => [
            'type' => 'pgsql',
            'connectionStr' => 'pgsql:dbname=postgres; host=127.0.0.1; port=5432;',
            'userId' => 'postgres',
            'password' => 'password',
            'connectTimeoutMsec' => 30 * 1000,
            'queryTimeoutMsec' => 30 * 1000,
        ],
        'pgsql_normal' => [
            'type' => 'pgsql',
            'connectionStr' => 'pgsql:dbname=unit_test; host=127.0.0.1; port=5432;',
            'userId' => 'postgres',
            'password' => 'password',
            'connectTimeoutMsec' => 30 * 1000,
            'queryTimeoutMsec' => 30 * 1000,
        ],
        'mysql_master' => [
            'type' => 'mysql',
            'connectionStr' => 'mysql:dbname=mysql; host=127.0.0.1; port=3306;',
            'userId' => 'root',
            'password' => 'password',
            'connectTimeoutMsec' => 30 * 1000,
            'queryTimeoutMsec' => 30 * 1000,
        ],
        'mysql_normal' => [
            'type' => 'mysql',
            'connectionStr' => 'mysql:dbname=unit_test; host=127.0.0.1; port=3306;',
            'userId' => 'root',
            'password' => 'password',
            'connectTimeoutMsec' => 30 * 1000,
            'queryTimeoutMsec' => 30 * 1000,
        ],
    ],
];
