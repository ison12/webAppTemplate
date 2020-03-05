<?php

namespace App\Common\Cache;

use App\Common\Cache\Impl\FileCache;
use Psr\SimpleCache\CacheInterface;

/**
 * キャッシュ管理クラス。
 */
class CacheManager {

    /**
     * @var CacheInterface キャッシュクラス
     */
    private static $instances = array();

    /**
     * キャッシュクラスのインスタンスを取得する。
     * @param string $cacheDir キャッシュディレクトリ
     * @return CacheInterface
     */
    public static function getInstance($cacheDir) {

        // Make default if first instance
        if (!isset(self::$instances[$cacheDir])) {
            self::$instances[$cacheDir] = new FileCache($cacheDir);
            return self::$instances[$cacheDir];
        }

        return self::$instances[$cacheDir];
    }

}
