<?php

namespace Tests\App\Common\DB\Impl\PostgreSQL;

use App\Common\DB\DBFactory;
use App\Common\DB\Impl\PostgreSQL\PostgreSQLConnection;
use App\Common\Exception\DBException;
use Exception;
use Tests\Common\PostgreSQLBaseTest;

/**
 * PostgreSQLQuerySelect。
 * テストクラス。
 *
 *
 */
class PostgreSQLQuerySelectTest extends PostgreSQLBaseTest {

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
     * テスト内容：Select条件指定テスト。
     */
    public function testSelect() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column('column1', 'column2')
                ->from('test')
                ->orderByAsc('id')
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2001-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2002-01-01 00:11:22', 12345, true)");
            // SELECT実行
            $selectRet = $dbConnection->queryFetch($sql, $sqlParams);
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 1件更新されていることを検証
        $this->assertSame(2, count($selectRet));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertSame('value1', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2001-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
        $rowIndex = 1;
        $this->assertSame('value2', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2002-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
    }

    /**
     * テスト内容：Selectのカラム句にRawValueを指定するテスト。
     */
    public function testSelectRawValue() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        // カラム句にUPPER関数を指定
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column(new \App\Common\DB\DBRawValue(['upper(column1)', 'column1']), 'column2')
                ->from('test')
                ->orderByAsc('id')
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2001-01-01 00:11:22', 12345, true)");
            // SELECT実行
            $selectRet = $dbConnection->queryFetch($sql, $sqlParams);
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 1件更新されていることを検証
        $this->assertSame(1, count($selectRet));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertSame('VALUE1', $selectRet[$rowIndex]['column1']);
    }

    /**
     * テスト内容：Select条件指定で文字種のテスト。
     */
    public function testSelectWhereAllChar() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column('column1', 'column2')
                ->from('test')
                ->where()
                ->condition('column1', '=', 'あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'()-=^~\\@`[{;+:*]},<.>/_')
                ->orderByAsc('id')
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('" . 'あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'\'()-=^~\\@`[{;+:*]},<.>/_' . "', '2003-01-01 00:11:22', 12345, true)");
            // SELECT実行
            $selectRet = $dbConnection->queryFetch($sql, $sqlParams);
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 1件更新されていることを検証
        $this->assertSame(1, count($selectRet));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertSame('あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'()-=^~\\@`[{;+:*]},<.>/_', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2003-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
    }

    /**
     * テスト内容：Select条件指定テスト。
     */
    public function testSelectWhere() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column('column1', 'column2')
                ->from('test')
                ->where()
                ->open()
                ->condition('column1', '=', 'value2')
                ->_or()
                ->condition('column1', '=', 'あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'()-=^~\\@`[{;+:*]},<.>/_')
                ->_and()
                ->condition('column1', '<>', 'value1')
                ->close()
                ->orderByAsc('id')
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2001-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2002-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('" . 'あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'\'()-=^~\\@`[{;+:*]},<.>/_' . "', '2003-01-01 00:11:22', 12345, true)");
            // SELECT実行
            $selectRet = $dbConnection->queryFetch($sql, $sqlParams);
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 1件更新されていることを検証
        $this->assertSame(2, count($selectRet));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertSame('value2', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2002-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
        $rowIndex = 1;
        $this->assertSame('あいうえおabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!"#$%&\'()-=^~\\@`[{;+:*]},<.>/_', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2003-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
    }

    /**
     * テスト内容：Select条件 NULL指定テスト。
     */
    public function testSelectWhereNull() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column('column1', 'column2')
                ->from('test')
                ->where()
                ->open()
                ->condition('column1', '=', null)
                ->_and()
                ->condition('column2', '<>', null)
                ->close()
                ->orderByAsc('id')
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES (null, '2001-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2002-01-01 00:11:22', 12345, true)");
            // SELECT実行
            $selectRet = $dbConnection->queryFetch($sql, $sqlParams);
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 1件更新されていることを検証
        $this->assertSame(1, count($selectRet));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertSame(null, $selectRet[$rowIndex]['column1']);
        $this->assertSame('2001-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
    }

    /**
     * テスト内容：Select条件 IN指定テスト。
     */
    public function testSelectWhereIn() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column('column1', 'column2')
                ->from('test')
                ->where()
                ->open()
                ->condition('column1', 'in', ['value1', 'value2'])
                ->_and()
                ->condition('column2', 'not in', ['2003-01-01 00:11:22', '2004-01-01 00:11:22'])
                ->close()
                ->orderByAsc('id')
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2001-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2002-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value3', '2003-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value4', '2004-01-01 00:11:22', 12345, true)");
            // SELECT実行
            $selectRet = $dbConnection->queryFetch($sql, $sqlParams);
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 1件更新されていることを検証
        $this->assertSame(2, count($selectRet));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertSame('value1', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2001-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
        $rowIndex = 1;
        $this->assertSame('value2', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2002-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
    }

    /**
     * テスト内容：Select条件 LIKE指定テスト。
     */
    public function testSelectWhereLike() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column('column1', 'column2')
                ->from('test')
                ->where()
                ->open()
                ->condition('column1', 'like', 'value%')
                ->_and()
                ->condition('column1', 'not like', '%alue2')
                ->_or()
                ->condition('column1', 'like', DBFactory::createDBHelper(self::$postgresDBName)->escapeLikeValue('%_!\"#$%&\'()-=^~\\|@`[{;+:*]},<.>/_'))
                ->close()
                ->orderByAsc('column2')
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2001-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2002-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES (" . "'" . str_replace("'", "''", '%_!\"#$%&\'()-=^~\\|@`[{;+:*]},<.>/_') . "'" . ", '2003-01-01 00:11:22', 12345, true)");
            // SELECT実行
            $selectRet = $dbConnection->queryFetch($sql, $sqlParams);
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 1件更新されていることを検証
        $this->assertSame(2, count($selectRet));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertSame('value1', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2001-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
        $rowIndex = 1;
        $this->assertSame('%_!\"#$%&\'()-=^~\\|@`[{;+:*]},<.>/_', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2003-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
    }

    /**
     * テスト内容：Select条件 ORDER BY指定テスト。
     */
    public function testSelectOrderby() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column('column1', 'column2')
                ->from('test')
                ->orderByAsc('column2')
                ->orderByDesc('column1')
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2001-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2002-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value3', '2002-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value4', '2004-01-01 00:11:22', 12345, true)");
            // SELECT実行
            $selectRet = $dbConnection->queryFetch($sql, $sqlParams);
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 1件更新されていることを検証
        $this->assertSame(4, count($selectRet));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertSame('value1', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2001-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
        $rowIndex = 1;
        $this->assertSame('value3', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2002-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
        $rowIndex = 2;
        $this->assertSame('value2', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2002-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
        $rowIndex = 3;
        $this->assertSame('value4', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2004-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
    }

    /**
     * テスト内容：Select条件 OFFSET LIMIT指定テスト。
     */
    public function testSelectOffsetLimit() {

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column('column1', 'column2')
                ->from('test')
                ->orderByAsc('column1')
                ->offset(1)
                ->limit(2)
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // 一旦登録
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2001-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2002-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value3', '2003-01-01 00:11:22', 12345, true)");
            $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value4', '2004-01-01 00:11:22', 12345, true)");
            // SELECT実行
            $selectRet = $dbConnection->queryFetch($sql, $sqlParams);
        } catch (Exception $ex) {
            // ロールバック
            $dbConnection->rollback();
            throw $ex;
        }

        // 1件更新されていることを検証
        $this->assertSame(2, count($selectRet));

        // レコードの内容を検証
        $rowIndex = 0;
        $this->assertSame('value2', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2002-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
        $rowIndex = 1;
        $this->assertSame('value3', $selectRet[$rowIndex]['column1']);
        $this->assertSame('2003-01-01 00:11:22', $selectRet[$rowIndex]['column2']);
    }

    /**
     * テスト内容：Select条件 NO LOCK指定テスト。
     */
    public function testSelectNoLock() {

        PostgreSQLConnection::$isPersistant = false;

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);
        $dbConnectionOther = DBFactory::getConnection(self::$postgresDBName, true);

        $sql = '';
        $sqlParams = [];

        // SELECTクエリ
        $querySelect = DBFactory::createSelect(self::$postgresDBName)
                ->column('column1', 'column2')
                ->from('test')
                ->where()
                ->condition('column1', '=', 'value1')
                ->lock()
                ->nowait()
        ;

        // SELECTクエリの生成
        $querySelect->compile($sql, $sqlParams);

        try {
            // データ準備
            {
                $dbConnection->beginTransaction();
                // 一旦登録
                $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2001-01-01 00:11:22', 12345, true)");
                $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value2', '2002-01-01 00:11:22', 12345, true)");
                $dbConnection->commit();
            }

            // 1つ目のコネクションでロックする
            {
                $dbConnection->beginTransaction();
                // 事前にロックする
                $dbConnection->queryFetch("SELECT * FROM test WHERE column1 = 'value1' FOR UPDATE");
            }

            // 2つ目のコネクションでロックする
            {
                $dbConnectionOther->beginTransaction();
                // SELECT実行
                $dbConnectionOther->queryFetch($sql, $sqlParams);
            }

            $this->assertFalse(true);
        } catch (DBException $ex) {

            $this->assertTrue(DBFactory::createDBHelper(self::$postgresDBName)->isErrorForLock($ex));
        }
    }

}
