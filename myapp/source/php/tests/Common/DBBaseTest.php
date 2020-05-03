<?php

namespace Tests\Common;

use App\Common\App\AppContext;
use App\Common\DB\DBObserver;
use App\Common\Util\DBUtil;
use PHPUnit_Framework_TestCase;

/**
 * DB基本テストクラス。
 *
 */
class DBBaseTest extends PHPUnit_Framework_TestCase {

    protected static $defaultDBNameApp = 'default';

    /**
     * クラスロード時に実行。
     */
    public static function setUpBeforeClass() {

        $app = AppContext::get();
        $container = $app->getContainer();
        $logger = $container->get('logger');

        DBObserver::addQueryObserver('test-global', function($dbConnection, $statement, $params, $ext) use($logger) {
            $logger->info(DBUtil::combineQueryStatementAndParams($statement, $params));
        });
    }

    /**
     * クラスアンロード時に実行。
     */
    public static function tearDownAfterClass() {

    }

    /**
     * コンストラクタ。
     */
    public function __construct() {

    }

    /**
     * デストラクタ。
     */
    public function __destruct() {

    }

    /**
     * テスト開始時。
     */
    protected function setUp() {

    }

    /**
     * テスト終了後。
     */
    protected function tearDown() {

    }

}
