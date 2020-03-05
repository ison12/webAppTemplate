<?php

return [
    'settings' => [
        // Environment settings.
        // development or test or staging or production
        'environment' => 'test',
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../src',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../log/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        // DB Cache
        'dbCache' => __DIR__ . '/../cache/db/'
    ],
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
