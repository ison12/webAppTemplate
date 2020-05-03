<?php

namespace App\Cache;

use App\Common\App\AppContext;
use App\Common\Cache\CacheManager;
use Psr\SimpleCache\CacheInterface;

/**
 * キャッシュ管理クラス。
 */
class DBCacheManager {

    /**
     * @var string キャッシュディレクトリ
     */
    private static $cacheDir = null;

    /**
     * キャッシュクラスのインスタンスを取得する。
     * @return CacheInterface
     */
    public static function getInstance() {

        if (self::$cacheDir === null) {
            self::$cacheDir = AppContext::get()->getContainer()->get('config')['dbCache'];
        }

        return CacheManager::getInstance(self::$cacheDir);
    }

}
