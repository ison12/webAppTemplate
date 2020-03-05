<?php

namespace App\Common\App;

use Slim\App;

/**
 * アプリケーションコンテキスト。
 */
class AppContext {

    /**
     * @var App アプリケーションオブジェクト
     */
    private static $app = null;

    /**
     * アプリケーションオブジェクトを設定する。
     * @param App $app アプリケーションオブジェクト
     */
    public static function set(App $app) {
        self::$app = $app;
    }

    /**
     * アプリケーションオブジェクトを取得する。
     * @return App アプリケーションオブジェクト
     */
    public static function get(): App {
        return self::$app;
    }

}
