<?php

namespace Tests\App\Common\DB\Impl\PostgreSQL;

use App\Common\Cache\CacheManager;
use App\Common\Cache\ImplException\InvalidArgumentExceptionImpl;
use App\Common\Cache\InvalidArgumentException;
use App\Common\Util\DateUtil;
use App\Common\Util\FileUtil;
use Tests\Common\BaseTest;

/**
 * FileCache。
 * テストクラス。
 *
 * 
 */
class FileCacheTest extends BaseTest {

    /**
     * 共通処理。
     */
    protected function setUp() {
        parent::setUp();

        // キャッシュディレクトリを削除
        FileUtil::delete(__DIR__ . DIRECTORY_SEPARATOR . 'cache');
    }

    /**
     * テスト内容：Setテスト。
     */
    public function testSet() {

        $cache = CacheManager::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'cache');

        // 不正な引数
        try {
            $cache->set('', 'value');
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 不正な引数
        try {
            $cache->set(null, 'value');
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 正常な登録 ttl is null
        try {
            $cache->set('test', 'value');

            $cacheValue = include __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test';
            $this->assertSame(19, strlen($cacheValue['date']));
            $this->assertSame(null, $cacheValue['ttl']);
            $this->assertSame('value', $cacheValue['value']);
        } catch (\Exception $exc) {
            $this->assertTrue(false);
        }

        // 正常な登録
        $testValues = [
            'string',
            30,
            5.5,
            null,
            ['a' => 'A', 'b' => 'B']
        ];

        foreach ($testValues as $value) {
            try {
                $cache->set('test', $value, 60);

                $cacheValue = include __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test';
                $this->assertSame(19, strlen($cacheValue['date']));
                $this->assertSame(60, $cacheValue['ttl']);
                $this->assertSame($value, $cacheValue['value']);
            } catch (InvalidArgumentException $exc) {
                $this->assertTrue(false);
            }
        }
    }

    /**
     * テスト内容：SetMultipleテスト。
     */
    public function testSetMultiple() {

        $cache = CacheManager::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'cache');

        // 不正な引数
        try {
            $cache->setMultiple('');
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 不正な引数
        try {
            $cache->setMultiple(null);
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 正常な登録
        $testValues = [
            'test1' => 'string',
            'test2' => 30,
            'test3' => 5.5,
            'test4' => null,
            'test5' => ['a' => 'A', 'b' => 'B']
        ];

        $cache->setMultiple($testValues, 60);

        $index = 1;
        foreach ($testValues as $value) {
            try {
                $cacheValue = include __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test' . $index;
                $this->assertSame(19, strlen($cacheValue['date']));
                $this->assertSame(60, $cacheValue['ttl']);
                $this->assertSame($value, $cacheValue['value']);
            } catch (InvalidArgumentException $exc) {
                $this->assertTrue(false);
            }
            $index++;
        }
    }

    /**
     * テスト内容：Getテスト。
     */
    public function testGet() {

        $cache = CacheManager::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'cache');

        // 不正な引数
        try {
            $cache->get('');
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 不正な引数
        try {
            $cache->get(null);
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 正常な取得 null値
        try {
            $getCache = $cache->get('test');
            $this->assertSame(null, $getCache);
        } catch (\Exception $exc) {
            $this->assertTrue(false);
        }

        // 正常な取得 デフォルト値
        try {
            $getCache = $cache->get('test', 30);
            $this->assertSame(30, $getCache);
        } catch (\Exception $exc) {
            $this->assertTrue(false);
        }

        // 正常な取得
        $testValues = [
            'string',
            30,
            5.5,
            null,
            ['a' => 'A', 'b' => 'B']
        ];

        // 正常な登録
        foreach ($testValues as $value) {
            try {
                // キャッシュデータ作成
                $cacheValue = [
                    'date' => DateUtil::getSystemDate()->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
                    'ttl' => 300,
                    'value' => $value,
                ];

                // キャッシュファイル書き込み
                FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test', '<?php return ' . var_export($cacheValue, true) . ';');

                // キャッシュデータ取得
                $getCache = $cache->get('test', null);
                $this->assertSame($value, $getCache);
            } catch (InvalidArgumentException $exc) {
                $this->assertTrue(false);
            }
        }

        // 正常な取得 キャッシュ期間
        try {
            // キャッシュデータ作成
            $cacheValue = [
                'date' => DateUtil::getSystemDate()->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
                'ttl' => 5,
                'value' => 'ttl is valid',
            ];

            // キャッシュファイル書き込み
            FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test', '<?php return ' . var_export($cacheValue, true) . ';');

            $getCache = $cache->get('test');
            $this->assertSame($cacheValue['value'], $getCache);

            // 10秒経過したのでキャッシュが無効になる
            sleep(10);
            $getCache = $cache->get('test');
            $this->assertSame(null, $getCache);
        } catch (\Exception $exc) {
            $this->assertTrue(false);
        }
    }

    /**
     * テスト内容：GetMultipleテスト。
     */
    public function testGetMultiple() {

        $cache = CacheManager::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'cache');

        // 不正な引数
        try {
            $cache->getMultiple('');
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 不正な引数
        try {
            $cache->getMultiple(null);
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 正常な取得 null値
        try {
            $getCache = $cache->getMultiple(['test']);
            $this->assertSame(1, count($getCache));
            $this->assertSame(null, $getCache['test']);
        } catch (\Exception $exc) {
            $this->assertTrue(false);
        }

        // 正常な取得
        $testValues = [
            'test1' => 'string',
            'test2' => 30,
            'test3' => 5.5,
            'test4' => null,
            'test5' => ['a' => 'A', 'b' => 'B']
        ];

        $index = 1;
        foreach ($testValues as $value) {
            try {
                // キャッシュデータ作成
                $cacheValue = [
                    'date' => DateUtil::getSystemDate()->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
                    'ttl' => 300,
                    'value' => $value,
                ];

                // キャッシュファイル書き込み
                FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test' . $index, '<?php return ' . var_export($cacheValue, true) . ';');
            } catch (InvalidArgumentException $exc) {
                $this->assertTrue(false);
            }
            $index ++;
        }

        $index = 1;
        $getCache = $cache->getMultiple(array_keys($testValues));
        foreach ($getCache as $key => $get) {
            $this->assertSame($testValues[$key], $get);
        }
    }

    /**
     * テスト内容：Hasテスト。
     */
    public function testHas() {

        $cache = CacheManager::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'cache');

        // 不正な引数
        try {
            $cache->get('');
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 不正な引数
        try {
            $cache->get(null);
            $this->assertTrue(false);
        } catch (InvalidArgumentExceptionImpl $exc) {
            $this->assertTrue(true);
        }

        // 正常なチェック null値
        try {
            $hasCache = $cache->has('test');
            $this->assertSame(false, $hasCache);
        } catch (\Exception $exc) {
            $this->assertTrue(false);
        }

        // 正常なチェック キャッシュ期間
        try {
            // キャッシュデータ作成
            $cacheValue = [
                'date' => DateUtil::getSystemDate()->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
                'ttl' => 5,
                'value' => 'ttl is valid',
            ];

            // キャッシュファイル書き込み
            FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test', '<?php return ' . var_export($cacheValue, true) . ';');

            $hasCache = $cache->has('test');
            $this->assertSame(true, $hasCache);

            // 10秒経過したのでキャッシュが無効になる
            sleep(10);
            $hasCache = $cache->has('test');
            $this->assertSame(false, $hasCache);
        } catch (\Exception $exc) {
            $this->assertTrue(false);
        }
    }

    /**
     * テスト内容：Deleteテスト。
     */
    public function testDelete() {

        $cache = CacheManager::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'cache');

        // キャッシュデータ作成
        $cacheValue = [
            'date' => DateUtil::getSystemDate()->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
            'ttl' => 5,
            'value' => 'ttl is valid',
        ];

        // キャッシュファイル書き込み
        FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test', '<?php return ' . var_export($cacheValue, true) . ';');

        $cache->delete('test');

        $this->assertSame(false, FileUtil::existsFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test'));
    }

    /**
     * テスト内容：DeleteMultipleテスト。
     */
    public function testDeleteMultiple() {

        $cache = CacheManager::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'cache');

        // キャッシュデータ作成
        $cacheValue = [
            'date' => DateUtil::getSystemDate()->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
            'ttl' => 5,
            'value' => 'ttl is valid',
        ];

        // キャッシュファイル書き込み
        FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test1', '<?php return ' . var_export($cacheValue, true) . ';');
        FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test2', '<?php return ' . var_export($cacheValue, true) . ';');

        $this->assertSame(2, count(FileUtil::getFiles(__DIR__ . DIRECTORY_SEPARATOR . 'cache')));
        $cache->deleteMultiple(['test1', 'test2', 'test3']);
        $this->assertSame(0, count(FileUtil::getFiles(__DIR__ . DIRECTORY_SEPARATOR . 'cache')));
    }

    /**
     * テスト内容：Clearテスト。
     */
    public function testClear() {

        $cache = CacheManager::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'cache');

        // キャッシュデータ作成
        $cacheValue = [
            'date' => DateUtil::getSystemDate()->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
            'ttl' => 5,
            'value' => 'ttl is valid',
        ];

        // キャッシュファイル書き込み
        FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test', '<?php return ' . var_export($cacheValue, true) . ';');
        FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test2', '<?php return ' . var_export($cacheValue, true) . ';');
        FileUtil::writeFile(__DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'test3', '<?php return ' . var_export($cacheValue, true) . ';');

        $cache->clear();

        $files = FileUtil::getFiles(__DIR__ . DIRECTORY_SEPARATOR . 'cache');
        $this->assertSame(0, count($files));
    }

}
