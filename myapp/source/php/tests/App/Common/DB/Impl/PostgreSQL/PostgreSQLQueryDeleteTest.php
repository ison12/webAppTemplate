<?php

namespace Tests\App\Common\DB\Impl\PostgreSQL;

use App\Common\DB\DBFactory;
use Exception;
use Tests\Common\DBBaseTest;

/**
 * PostgreSQLQueryDelete。
 * テストクラス。
 *
 * 
 */
class PostgreSQLQueryDeleteTest extends DBBaseTest {

    /**
     * 共通処理。
     */
    protected function setUp() {
        parent::setUp();

        // テーブルをクリアする
        $dbConnection = DBFactory::getConnection(self::$postgresDBName);
        $dbConnection->queryAction('DELETE FROM test');
    }

    /**
     * テスト内容：Delete実行テスト。
     */
    public function testDelete() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // DELETEクエリ
        $queryDelete = DBFactory::createDelete(self::$postgresDBName)
                ->from('test')
        ;

        // DELETEクエリの生成
        $queryDelete->compile($sql, $sqlParams);

        try {
            // トランザクション開始
            $dbConnection->beginTransaction();
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2000-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2000-01-01 00:11:22', 12345, true)");
            // DELETE実行
            $deleteCount = $dbConnection->queryAction($sql, $sqlParams);
            // コミット
            $dbConnection->commit();
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 結果を検証
        $ret = $dbConnection->queryFetch('SELECT * FROM test');

        // 1件削除されていることを検証
        $this->assertSame(2, $deleteCount);
        $this->assertSame(0, count($ret));
    }

    /**
     * テスト内容：Delete条件指定テスト。
     */
    public function testDeleteWhere() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // DELETEクエリ
        $queryDelete = DBFactory::createDelete(self::$postgresDBName)
                ->from('test')
                ->where()
                ->open()
                ->condition('column1', '=', 'value2')
                ->_or()
                ->condition('column1', '=', 'value2')
                ->_and()
                ->condition('column1', '=', 'value2')
                ->close()
        ;

        // DELETEクエリの生成
        $queryDelete->compile($sql, $sqlParams);

        try {
            // トランザクション開始
            $dbConnection->beginTransaction();
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2000-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2000-01-01 00:11:22', 12345, true)");
            // DELETE実行
            $deleteCount = $dbConnection->queryAction($sql, $sqlParams);
            // コミット
            $dbConnection->commit();
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 結果を検証
        $ret = $dbConnection->queryFetch("SELECT * FROM test WHERE column1 = 'value1'");

        // 1件更新されていることを検証
        $this->assertSame(1, $deleteCount);
        $this->assertSame(1, count($ret));
    }

}
