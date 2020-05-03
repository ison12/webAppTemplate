<?php

namespace Tests\App\Common\DB\Impl\MySQL;

use App\Common\DB\DBFactory;
use App\Common\DB\DBRawValue;
use Exception;
use Tests\Common\MySQLBaseTest;

/**
 * MySQLQueryInsert。
 * テストクラス。
 *
 *
 */
class MySQLQueryInsertTest extends MySQLBaseTest {

    /**
     * 共通処理。
     */
    protected function setUp() {
        parent::setUp();

        // テーブルをクリアする
        $dbConnection = DBFactory::getConnection(self::$mysqlDBName);
        $dbConnection->queryAction('DELETE FROM test');
    }

    /**
     * テスト内容：Insert実行テスト。
     */
    public function testInsert() {

        $dbConnection = DBFactory::getConnection(self::$mysqlDBName);

        $sql = '';
        $sqlParams = [];

        // INSERTクエリ
        $queryInsert = DBFactory::createInsert(self::$mysqlDBName)
                ->from('test')
                ->column('column1', 'column2', 'column3', 'column4')
                ->value(['value1\'あいうえお', '2000-01-01 00:11:22', 12345, true]
        );

        // INSERTクエリの生成
        $queryInsert->compile($sql, $sqlParams);


        try {
            // トランザクション開始
            $dbConnection->beginTransaction();
            // INSERT実行
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

        // 1件登録されていることを検証
        $this->assertSame(1, count($ret));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertTrue(is_numeric($ret[$rowIndex]['id']));
        $this->assertSame('value1\'あいうえお', $ret[$rowIndex]['column1']);
        $this->assertSame('2000-01-01 00:11:22', $ret[$rowIndex]['column2']);
        $this->assertSame(12345, (int) $ret[$rowIndex]['column3']);
        $this->assertSame(true, (bool) $ret[$rowIndex]['column4']);
    }

    /**
     * テスト内容：Insertの複数回実行テスト。
     */
    public function testInsertAllChar() {

        $dbConnection = DBFactory::getConnection(self::$mysqlDBName);

        $sql = '';
        $sqlParams = [];

        // INSERTクエリ
        $insertColumns = ['column1', 'column2', 'column3', 'column4'];
        $insertValues = [
            ['あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'()-=^~\\@`[{;+:*]},<.>/?_', '2003-01-01 00:11:22', 32345, 0],
        ];

        $queryInsert = DBFactory::createInsert(self::$mysqlDBName)
                ->from('test')
                ->column(...$insertColumns)
                ->value(...$insertValues);

        // INSERTクエリの生成
        $queryInsert->compile($sql, $sqlParams);


        try {
            // トランザクション開始
            $dbConnection->beginTransaction();
            // INSERT実行
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

        // 1件登録されていることを検証
        $this->assertSame(1, count($ret));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertTrue(is_numeric($ret[$rowIndex]['id']));
        $this->assertSame('あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'()-=^~\\@`[{;+:*]},<.>/?_', $ret[$rowIndex]['column1']);
        $this->assertSame('2003-01-01 00:11:22', $ret[$rowIndex]['column2']);
        $this->assertSame(32345, (int) $ret[$rowIndex]['column3']);
        $this->assertSame(false, (bool) $ret[$rowIndex]['column4']);
    }

    /**
     * テスト内容：Insertの複数回実行テスト。
     */
    public function testInsertMultiple() {

        $dbConnection = DBFactory::getConnection(self::$mysqlDBName);

        $sql = '';
        $sqlParams = [];

        // INSERTクエリ
        $insertColumns = ['column1', 'column2', 'column3', 'column4'];
        $insertValues = [
            ['value1', '2000-01-01 00:11:22', 12345, 1],
            ['value2', '2002-01-01 00:11:22', 22345, 0],
        ];

        $queryInsert = DBFactory::createInsert(self::$mysqlDBName)
                ->from('test')
                ->column(...$insertColumns)
                ->value(...$insertValues);

        // INSERTクエリの生成
        $queryInsert->compile($sql, $sqlParams);


        try {
            // トランザクション開始
            $dbConnection->beginTransaction();
            // INSERT実行
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

        // 1件登録されていることを検証
        $this->assertSame(2, count($ret));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertTrue(is_numeric($ret[$rowIndex]['id']));
        $this->assertSame('value1', $ret[$rowIndex]['column1']);
        $this->assertSame('2000-01-01 00:11:22', $ret[$rowIndex]['column2']);
        $this->assertSame(12345, (int) $ret[$rowIndex]['column3']);
        $this->assertSame(true, (bool) $ret[$rowIndex]['column4']);
        $rowIndex = 1;
        $this->assertTrue(is_numeric($ret[$rowIndex]['id']));
        $this->assertSame('value2', $ret[$rowIndex]['column1']);
        $this->assertSame('2002-01-01 00:11:22', $ret[$rowIndex]['column2']);
        $this->assertSame(22345, (int) $ret[$rowIndex]['column3']);
        $this->assertSame(false, (bool) $ret[$rowIndex]['column4']);
    }

    /**
     * テスト内容：Insert実行テスト（関数の使用）。
     */
    public function testInsertUseFunction() {

        $dbConnection = DBFactory::getConnection(self::$mysqlDBName);

        $sql = '';
        $sqlParams = [];

        // INSERTクエリ
        $queryInsert = DBFactory::createInsert(self::$mysqlDBName)
                ->from('test')
                ->column('column1', 'column2', 'column3', 'column4')
                ->value(['value1', new DBRawValue('current_timestamp'), 12345, true]
        );

        // INSERTクエリの生成
        $queryInsert->compile($sql, $sqlParams);


        try {
            // トランザクション開始
            $dbConnection->beginTransaction();
            // INSERT実行
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

        // 1件登録されていることを検証
        $this->assertSame(1, count($ret));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertTrue(is_numeric($ret[$rowIndex]['id']));
        $this->assertSame('value1', $ret[$rowIndex]['column1']);
        $this->assertGreaterThan('2000-01-01 00:11:22', $ret[$rowIndex]['column2']);
        $this->assertSame(12345, (int) $ret[$rowIndex]['column3']);
        $this->assertSame(true, (bool) $ret[$rowIndex]['column4']);
    }

}
