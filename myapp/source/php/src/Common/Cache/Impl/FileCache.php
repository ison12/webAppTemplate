<?php

namespace App\Common\Cache\Impl;

use App\Common\Cache\ImplException\InvalidArgumentExceptionImpl;
use App\Common\Util\DateUtil;
use App\Common\Util\FileUtil;
use DateInterval;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * ファイルキャッシュ。
 */
class FileCache implements CacheInterface {

    /**
     * @var string キャッシュディレクトリ
     */
    private $cacheDir = null;

    /**
     * コンストラクタ。
     * @param string $cacheDir キャッシュディレクトリ
     */
    public function __construct(string $cacheDir) {
        $this->cacheDir = rtrim($cacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws InvalidArgumentException2
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null) {

        $this->throwIfInvalidArgsForKey($key);

        $path = $this->cacheDir . $key;

        if ($this->has($key)) {
            // キャッシュが存在する

            $cacheValue = include $path;

            $value = $cacheValue['value'] ?? $default;
            return $value;
        } else {
            // キャッシュが存在しない場合
            return $default;
        }
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws InvalidArgumentException2
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null) {

        $this->throwIfInvalidArgsForKey($key);

        $date = DateUtil::getSystemDate();
        $dateStr = $date->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON);

        $ttlInt = null;
        if ($ttl === null) {
            $ttlInt = null;
        } else if (is_int($ttl)) {
            $ttlInt = $ttl;
        } else if ($ttl instanceof DateInterval) {
            $ttlInt = (int) $ttl->format('%s');
        }

        $path = $this->cacheDir . $key;

        $cacheValue = [
            'date' => $dateStr,
            'ttl' => $ttlInt,
            'value' => $value,
        ];

        $code = '<?php return ' . var_export($cacheValue, true) . ';';
        return FileUtil::writeFileSafely($path, $code);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws InvalidArgumentException2
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key) {

        $this->throwIfInvalidArgsForKey($key);

        $path = $this->cacheDir . $key;

        $ret = FileUtil::delete($path);
        return $ret;
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear() {

        $path = $this->cacheDir;

        $ret = FileUtil::delete($path);
        return $ret;
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws InvalidArgumentException2
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null) {

        $this->throwIfInvalidArgsForKeys($keys);

        $ret = [];

        foreach ($keys as $key) {
            $ret[$key] = $this->get($key, $default);
        }

        return $ret;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable               $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws InvalidArgumentException2
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null) {

        $this->throwIfInvalidArgsForKeys($values);

        $ret = true;

        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws InvalidArgumentException2
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys) {

        $this->throwIfInvalidArgsForKeys($keys);

        $ret = true;

        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws InvalidArgumentException2
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function has($key) {

        $this->throwIfInvalidArgsForKey($key);

        $path = $this->cacheDir . $key;

        if (FileUtil::existsFile($path)) {

            $cacheValue = include $path;

            $dateStr = $cacheValue['date'] ?? null;
            $date = DateUtil::createDateTime($dateStr, DateUtil::DATETIME_HYPHEN_FORMAT_COMMON);
            $ttl = $cacheValue['ttl'] ?? null;

            if ($ttl === null && !is_int($ttl)) {
                // キャッシュ期間なし
                return true;
            } else {
                // キャッシュ期間あり
                $now = DateUtil::getSystemDate();
                if ($now <= $date->add(new DateInterval("PT{$ttl}S"))) {
                    // キャッシュが生きている
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * $keysに不正な引数を渡された場合の例外。
     * @param iterable $keys キーリスト
     * @throws InvalidArgumentException
     */
    private function throwIfInvalidArgsForKeys($keys) {

        if (!is_iterable($keys)) {
            throw new InvalidArgumentExceptionImpl("keys={$keys}, FileCacheにて、不正な引数を検出");
        }
    }

    /**
     * $keyに不正な引数を渡された場合の例外。
     * @param string $key キー
     * @throws InvalidArgumentException
     */
    private function throwIfInvalidArgsForKey($key) {

        if ($key === null || $key === '') {
            throw new InvalidArgumentExceptionImpl("key={$key}, FileCacheにて、不正な引数を検出");
        }
    }

}
