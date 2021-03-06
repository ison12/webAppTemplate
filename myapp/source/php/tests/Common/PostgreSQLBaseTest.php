<?php

namespace Tests\Common;

use App\Common\App\AppContext;
use App\Common\DB\DBConnection;
use App\Common\DB\DBFactory;
use App\Common\DB\DBObserver;
use App\Common\Util\DBUtil;
use Exception;

/**
 * DB基本テストクラス。
 *
 */
class PostgreSQLBaseTest extends \PHPUnit_Framework_TestCase {

    protected static $defaultDBNameApp = 'default';
    protected static $postgresDBNameMaster = 'pgsql_master';
    protected static $postgresDBName = 'pgsql_normal';

    /**
     * クラスロード時に実行。
     */
    public static function setUpBeforeClass() {
        // データベースを生成する
        $dbConnection = DBFactory::getConnection(self::$postgresDBNameMaster);
        self::createDatabaseForPostgres($dbConnection);

        // テーブルを生成する
        $dbConnection = DBFactory::getConnection(self::$postgresDBName);
        self::createTableForPostgres($dbConnection);

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

    /**
     * データベースを作成する。
     * @param DBConnection $dbConnection
     */
    private static function createDatabaseForPostgres($dbConnection) {

        try {
            $dbConnection->queryAction("CREATE DATABASE unit_test ENCODING 'utf8'");
        } catch (Exception $ex) {
            // データベースが既に存在して作成ができなかった場合でもOKとする
        }
    }

    /**
     * テーブルを作成する。
     * @param DBConnection $dbConnection
     */
    private static function createTableForPostgres($dbConnection) {

        $dbConnection->queryAction("DROP TABLE IF EXISTS test");
        $dbConnection->queryAction(<<< EOM
CREATE TABLE "test" (
      "id"              bigserial          NOT NULL
    , "column1"         varchar(100)       NULL
    , "column2"         timestamp          NULL DEFAULT '1000-01-01 00:00:00'
    , "column3"         bigint             NULL DEFAULT '0'
    , "column4"         boolean            NULL DEFAULT '0'
);
EOM
        );
    }

}
