<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../../src/View/',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../../log/test.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
    'db' => [
        'default' => [// 業務アプリ用のテーブルが保持されているカタログ
            'type' => 'pgsql',
            'connectionStr' => 'pgsql:dbname=unit_test_app; host=127.0.0.1; port=5432;',
            'userId' => 'postgres',
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
