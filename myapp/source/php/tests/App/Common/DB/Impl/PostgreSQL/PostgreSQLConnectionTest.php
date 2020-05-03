<?php

namespace Tests\App\Common\DB\Impl\PostgreSQL;

use App\Common\DB\DBFactory;
use App\Common\DB\Impl\PostgreSQL\PostgreSQLConnection;
use App\Common\Exception\DBException;
use PDO;
use Tests\Common\PostgreSQLBaseTest;

/**
 * PostgreSQLConnection。
 * テストクラス。
 *
 *
 */
class PostgreSQLConnectionTest extends PostgreSQLBaseTest {

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
     * テスト内容：DBへの接続テスト。
     */
    public function testConnectSuccess() {

        try {
            // 正常に接続できること
            $connection = DBFactory::getConnection(self::$postgresDBName);
            $connection->disconnect();

            $this->assertNull($connection->getPDO());
        } catch (DBException $exc) {
            throw $exc;
        }
    }

    /**
     * テスト内容：DBへの接続テスト。
     */
    public function testConnectFailed() {

        try {
            // パスワードが不正のため、接続エラー
            DBFactory::getConnection([
                'type' => 'pgsql',
                'connectionStr' => 'pgsql:dbname=unit_test; host=127.0.0.1; port=5432;',
                'userId' => 'postgres',
                'password' => 'failed-password',
                'connectTimeoutMsec' => 30 * 1000,
                'queryTimeoutMsec' => 30 * 1000,
            ]);
            $this->assertTrue(false);
        } catch (DBException $exc) {
            echo $exc->getMessage();
            $this->assertTrue(true);
        }
    }

    /**
     * テスト内容：DBへの接続タイムアウトテスト。
     */
    public function testConnectTimeout() {

        $timeStart = microtime(true);
        try {
            // ポートが不正のため、接続エラー
            DBFactory::getConnection([
                'type' => 'pgsql',
                'connectionStr' => 'pgsql:dbname=unit_test; host=localhost; port=9999;',
                'userId' => 'postgres',
                'password' => 'password',
                'connectTimeoutMsec' => 3 * 1000,
                'queryTimeoutMsec' => 3 * 1000,
            ]);
            $this->assertTrue(false);
        } catch (DBException $exc) {
            $elapsedTime = microtime(true) - $timeStart;

            $this->assertGreaterThanOrEqual(1, $elapsedTime);

            echo $exc->getMessage();
            $this->assertTrue(true);
        }
    }

    /**
     * テスト内容：DBへの接続テスト。
     */
    public function testTransaction() {

        try {
            $connection = DBFactory::getConnection(self::$postgresDBName);

            // トランザクションを開始していない状態でのロールバック処理のチェック
            $connection->rollback();
            $this->assertSame(false, $connection->getPDO()->inTransaction());

            // トランザクションを開始していない状態でのコミット処理のチェック
            $connection->commit();
            $this->assertSame(false, $connection->getPDO()->inTransaction());

            // トランザクションを開始してからのロールバック処理のチェック
            // トランザクション中に登録するが、ロールバックによって取り消されたことをチェックする
            $connection->beginTransaction();
            $connection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES (?, ?, ?, ?)", ['value1', '2001-01-01 00:11:22', 12345, true]);
            $connection->rollback();
            $ret = $connection->queryFetch('SELECT * FROM test');
            $this->assertSame(0, count($ret));
            $this->assertSame(false, $connection->getPDO()->inTransaction());

            // トランザクションを開始してからのコミット処理のチェック
            // トランザクション中に登録して、コミットによって確定されたことをチェックする
            $connection->beginTransaction();
            $connection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES (?, ?, ?, ?)", ['value1', '2001-01-01 00:11:22', 12345, true]);
            $connection->commit();
            $ret = $connection->queryFetch('SELECT * FROM test');
            $this->assertSame(1, count($ret));
            $this->assertSame(false, $connection->getPDO()->inTransaction());
        } catch (DBException $exc) {
            throw $exc;
        }
    }

    /**
     * テスト内容：DBへの更新系クエリのテスト。
     */
    public function testQueryAction() {

        try {
            $connection = DBFactory::getConnection(self::$postgresDBName);

            $actionRet = $connection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES (?, ?, ?, ?)", ['value1', '2001-01-01 00:11:22', 12345, true]);
            $this->assertSame(1, $actionRet);

            $fetchRet = $connection->queryFetch('SELECT * FROM test');
            $this->assertSame(1, count($fetchRet));
        } catch (DBException $exc) {
            throw $exc;
        }
    }

    /**
     * テスト内容：DBへの参照系クエリのテスト。
     */
    public function testQueryFetch() {

        try {
            $connection = DBFactory::getConnection(self::$postgresDBName);

            $actionRet = $connection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES (?, ?, ?, ?)", ['value1', '2001-01-01 00:11:22', 12345, true]);
            $this->assertSame(1, $actionRet);

            // queryFetch
            {
                $fetchRet = $connection->queryFetch('SELECT * FROM test WHERE column1 = ?', ['value1']);
                $this->assertSame(1, count($fetchRet));
                $rowIndex = 0;
                $this->assertSame('value1', $fetchRet[$rowIndex]['column1']);
                $this->assertSame('2001-01-01 00:11:22', $fetchRet[$rowIndex]['column2']);
                $this->assertSame(12345, $fetchRet[$rowIndex]['column3']);
                $this->assertSame(true, $fetchRet[$rowIndex]['column4']);
            }

            // queryFetchCallback
            {
                $fetchRet = $connection->queryFetchCallback('SELECT * FROM test WHERE column1 = ?', ['value1'], function($rec) {
                    $this->assertSame('value1', $rec['column1']);
                    $this->assertSame('2001-01-01 00:11:22', $rec['column2']);
                    $this->assertSame(12345, $rec['column3']);
                    $this->assertSame(true, $rec['column4']);
                });
                $this->assertSame(1, $fetchRet);
            }

            // queryFetchStatement
            {
                $fetchStatement = $connection->queryFetchStatement('SELECT * FROM test WHERE column1 = ?', ['value1']);

                $fetchRet = $fetchStatement->fetchAll(PDO::FETCH_ASSOC);
                $fetchStatement->closeCursor();

                $this->assertSame(1, count($fetchRet));
                $rowIndex = 0;
                $this->assertSame('value1', $fetchRet[$rowIndex]['column1']);
                $this->assertSame('2001-01-01 00:11:22', $fetchRet[$rowIndex]['column2']);
                $this->assertSame(12345, $fetchRet[$rowIndex]['column3']);
                $this->assertSame(true, $fetchRet[$rowIndex]['column4']);
            }
        } catch (DBException $exc) {
            throw $exc;
        }
    }

    /**
     * テスト内容：ロックタイムアウト。
     */
    public function testLockTimeout() {

        PostgreSQLConnection::$isPersistant = false;

        $dbConnection = DBFactory::getConnection(self::$postgresDBName);
        $dbConnectionOther = DBFactory::getConnection([
                    'type' => 'pgsql',
                    'connectionStr' => 'pgsql:dbname=unit_test; host=localhost; port=5432;',
                    'userId' => 'postgres',
                    'password' => 'password',
                    'connectTimeoutMsec' => 30 * 1000,
                    'queryTimeoutMsec' => 100,
                        ], true);

        try {
            // データ準備
            {
                $dbConnection->beginTransaction();
                $dbConnection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES ('value1', '2001-01-01 00:11:22', 12345, true)");
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
                // ロックする
                $dbConnectionOther->queryFetch("SELECT * FROM test WHERE column1 = 'value1' FOR UPDATE");
            }

            $this->assertFalse(true);
        } catch (DBException $ex) {

            $this->assertTrue(
                    DBFactory::createDBHelper(self::$postgresDBName)->isErrorForTimeout($ex) ||
                    DBFactory::createDBHelper(self::$postgresDBName)->isErrorForLock($ex));
        }
    }

    /**
     * テスト内容：登録時のIDを取得する。
     */
    public function testLastInsertId() {

        try {
            $connection = DBFactory::getConnection(self::$postgresDBName);

            $connection->queryAction("INSERT INTO test (column1, column2, column3, column4) VALUES (?, ?, ?, ?)", ['value1', '2001-01-01 00:11:22', 12345, true]);

            $id = $connection->lastInsertId();
            $this->assertTrue($id !== null);
            $this->assertTrue($id !== '');
            $this->assertTrue($id !== false);
        } catch (DBException $exc) {
            throw $exc;
        }
    }

}
