<?php

namespace Tests\App\Common\DB\Impl\PostgreSQL;

use App\Common\DB\DBFactory;
use Exception;
use Tests\Common\PostgreSQLBaseTest;

/**
 * PostgreSQLQueryUpdate。
 * テストクラス。
 *
 *
 */
class PostgreSQLQueryUpdateTest extends PostgreSQLBaseTest {

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
     * テスト内容：Update実行テスト。
     */
    public function testUpdate() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // UPDATEクエリ
        $queryUpdate = DBFactory::createUpdate(self::$postgresDBName)
                ->from('test')
                ->set('column1', 'あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'()-=^~\\@`[{;+:*]},<.>/_')
                ->set('column2', '2002-01-01 00:11:22')
                ->set('column3', '22345')
                ->set('column4', 0)
        ;

        // UPDATEクエリの生成
        $queryUpdate->compile($sql, $sqlParams);

        try {
            // トランザクション開始
            $dbConnection->beginTransaction();
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2000-01-01 00:11:22', 12345, true)");
            // UPDATE実行
            $dbConnection->queryAction($sql, $sqlParams);
            // コミット
            $dbConnection->commit();
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 結果を検証
        $ret = $dbConnection->queryFetch('SELECT * FROM test');

        // 1件更新されていることを検証
        $this->assertSame(1, count($ret));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertTrue(is_numeric($ret[$rowIndex]['id']));
        $this->assertSame('あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'()-=^~\\@`[{;+:*]},<.>/_', $ret[$rowIndex]['column1']);
        $this->assertSame('2002-01-01 00:11:22', $ret[$rowIndex]['column2']);
        $this->assertSame(22345, $ret[$rowIndex]['column3']);
        $this->assertSame(false, $ret[$rowIndex]['column4']);
    }

    /**
     * テスト内容：Update条件指定テスト。
     */
    public function testUpdateWhere() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // UPDATEクエリ
        $queryUpdate = DBFactory::createUpdate(self::$postgresDBName)
                ->from('test')
                ->set('column2', '2002-01-01 00:11:22')
                ->set('column3', '22345')
                ->set('column4', 0)
                ->where()
                ->open()
                ->condition('column1', '=', 'value2')
                ->_or()
                ->condition('column1', '=', 'value2')
                ->_and()
                ->condition('column1', '=', 'value2')
                ->close()
        ;

        // UPDATEクエリの生成
        $queryUpdate->compile($sql, $sqlParams);

        try {
            // トランザクション開始
            $dbConnection->beginTransaction();
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2000-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2000-01-01 00:11:22', 12345, true)");
            // UPDATE実行
            $updateCount = $dbConnection->queryAction($sql, $sqlParams);
            // コミット
            $dbConnection->commit();
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 結果を検証
        $ret = $dbConnection->queryFetch("SELECT * FROM test WHERE column1 = 'value2'");

        // 1件更新されていることを検証
        $this->assertSame(1, $updateCount);
        $this->assertSame(1, count($ret));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertTrue(is_numeric($ret[$rowIndex]['id']));
        $this->assertSame('value2', $ret[$rowIndex]['column1']);
        $this->assertSame('2002-01-01 00:11:22', $ret[$rowIndex]['column2']);
        $this->assertSame(22345, $ret[$rowIndex]['column3']);
        $this->assertSame(false, $ret[$rowIndex]['column4']);
    }

}
