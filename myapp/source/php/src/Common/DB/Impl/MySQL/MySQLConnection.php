<?php

namespace App\Common\DB\Impl\MySQL;

use App\Common\DB\DBConnection;
use App\Common\DB\DBObserver;
use App\Common\Exception\DBException;
use PDO;
use PDOException;
use PDOStatement;

/**
 * DBコネクション。MySQL実装。
 */
class MySQLConnection implements DBConnection {

    /**
     * @var bool 持続的な接続を使用するかどうかのフラグ
     */
    public static $isPersistant = true;

    /**
     * @var PDO PDOオブジェクト
     */
    private $pdo = null;

    /**
     * @var string DB接続文字列
     */
    private $connectionStr = null;

    /**
     * @var string ユーザーID
     */
    private $userId = null;

    /**
     * @var string パスワード
     */
    private $password = null;

    /**
     * @var int 接続タイムアウト
     */
    private $connectTimeout = null;

    /**
     * @var int クエリタイムアウト
     */
    private $queryTimeout = null;

    /**
     * コンストラクタ。
     * @param string $connectionStr DB接続文字列
     * @param string $userId ユーザーID
     * @param string $password パスワード
     * @param int $connectTimeoutMSec 接続タイムアウト（ミリ秒）
     * @param int $queryTimeoutMSec クエリタイムアウト（ミリ秒）
     */
    public function __construct(string $connectionStr, string $userId, string $password, int $connectTimeoutMSec = null, int $queryTimeoutMSec = null) {

        $this->connectionStr = $connectionStr;
        $this->userId = $userId;
        $this->password = $password;
        $this->connectTimeout = $connectTimeoutMSec;
        $this->queryTimeout = $queryTimeoutMSec;

        $this->connect($connectionStr, $userId, $password, $connectTimeoutMSec, $queryTimeoutMSec);
    }

    /**
     * デストラクタ。
     */
    public function __destruct() {
        $this->disconnect();
    }

    /**
     * PDOを取得する。
     * @return PDO PDO
     */
    public function getPDO() {
        return $this->pdo;
    }

    /**
     * DB接続文字列を取得する。
     * @return string DB接続文字列
     */
    public function getConnectionStr() {
        return $this->connectionStr;
    }

    /**
     * DBに接続する。
     * @param string $connectionStr DB接続文字列
     * @param string $userId ユーザーID
     * @param string $password パスワード
     * @param int $connectTimeoutMSec 接続タイムアウト（ミリ秒）
     * @param int $queryTimeoutMSec クエリタイムアウト（ミリ秒）
     */
    public function connect(string $connectionStr, string $userId, string $password, int $connectTimeoutMSec = null, int $queryTimeoutMSec = null) {

        try {
            DBObserver::notifyOnQueryExecute($this, "$connectionStr userId=$userId", [], ['method' => 'connect']);

            $attrs = [
                // Force PDO to use exceptions for all errors
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                // 持続的な接続の設定
                , PDO::ATTR_PERSISTENT => self::$isPersistant
                // 接続タイムアウトの設定
                , PDO::ATTR_TIMEOUT => $connectTimeoutMSec
            ];

            $timeStart = microtime(true); // performance measure
            // PDOを初期化する
            $this->pdo = new PDO($connectionStr, $userId, $password, $attrs);
            // クエリタイムアウトの設定
            $this->setTimeout($this->pdo, $queryTimeoutMSec);
            $timeEnd = microtime(true); // performance measure

            DBObserver::notifyOnQueryExecutePost($this, "$connectionStr userId=$userId", [], ['method' => 'connect'], null, ['startTime' => $timeStart, 'endTime' => $timeEnd]);
        } catch (PDOException $ex) {
            throw new DBException($this, "$connectionStr userId=$userId", [], $ex);
        }
    }

    /**
     * DBから切断する。
     */
    public function disconnect() {

        if ($this->pdo !== null) {
            DBObserver::notifyOnQueryExecute($this, "disconnect", [], ['method' => 'disconnect']);

            $timeStart = microtime(true); // performance measure
            $this->pdo = null;
            $timeEnd = microtime(true); // performance measure

            DBObserver::notifyOnQueryExecutePost($this, "disconnect", [], ['method' => 'disconnect'], null, ['startTime' => $timeStart, 'endTime' => $timeEnd]);
        }
    }

    /**
     * トランザクションを開始する。
     */
    public function beginTransaction() {
        try {
            $inTransaction = $this->pdo->inTransaction();
            if ($inTransaction === null || $inTransaction !== true) {
                DBObserver::notifyOnQueryExecute($this, "begin transaction", [], ['method' => 'beginTransaction']);

                // トランザクションがアクティブではない場合に開始する
                $timeStart = microtime(true); // performance measure
                $this->pdo->beginTransaction();
                $timeEnd = microtime(true); // performance measure

                DBObserver::notifyOnQueryExecutePost($this, "begin transaction", [], ['method' => 'beginTransaction'], null, ['startTime' => $timeStart, 'endTime' => $timeEnd]);
            }
        } catch (PDOException $ex) {
            throw new DBException($this, 'begin transaction', [], $ex);
        }
    }

    /**
     * トランザクションをロールバックする。
     */
    public function rollback() {
        try {
            $inTransaction = $this->pdo->inTransaction();
            if ($inTransaction === null || $inTransaction === true) {
                DBObserver::notifyOnQueryExecute($this, "rollback", [], ['method' => 'rollback']);

                $timeStart = microtime(true); // performance measure
                $this->pdo->rollBack();
                $timeEnd = microtime(true); // performance measure

                DBObserver::notifyOnQueryExecutePost($this, "rollback", [], ['method' => 'rollback'], null, ['startTime' => $timeStart, 'endTime' => $timeEnd]);
            }
        } catch (PDOException $ex) {
            throw new DBException($this, 'rollback', [], $ex);
        }
    }

    /**
     * トランザクションをコミットする。
     */
    public function commit() {
        try {
            $inTransaction = $this->pdo->inTransaction();
            if ($inTransaction === null || $inTransaction === true) {
                DBObserver::notifyOnQueryExecute($this, "commit", [], ['method' => 'commit']);

                $timeStart = microtime(true); // performance measure
                $this->pdo->commit();
                $timeEnd = microtime(true); // performance measure

                DBObserver::notifyOnQueryExecutePost($this, "commit", [], ['method' => 'commit'], null, ['startTime' => $timeStart, 'endTime' => $timeEnd]);
            }
        } catch (PDOException $ex) {
            throw new DBException($this, 'commit', [], $ex);
        }
    }

    /**
     * 更新系のクエリを実行する。
     * @param string $statement ステートメント
     * @param array $params ステートメントパラメータ配列
     * @param mixed $ext 拡張情報
     * @return int 更新件数
     */
    public function queryAction(string $statement, array $params = [], $ext = null): int {

        $retResult = 0;

        $pdoStatement = null;
        try {
            DBObserver::notifyOnQueryExecute($this, $statement, $params, ['method' => 'queryAction', 'detail' => $ext]);

            $timeStart = microtime(true); // performance measure
            $pdoStatement = $this->createPDOStatement($this->pdo, $statement, $params);
            $pdoStatement->execute();
            $retResult = $pdoStatement->rowCount();

            $pdoStatement->closeCursor();
            $timeEnd = microtime(true); // performance measure

            DBObserver::notifyOnQueryExecutePost($this, $statement, $params, ['method' => 'queryAction', 'detail' => $ext], $retResult, ['startTime' => $timeStart, 'endTime' => $timeEnd]);
        } catch (PDOException $ex) {
            if ($pdoStatement !== null) {
                try {
                    $pdoStatement->closeCursor();
                } catch (PDOException $exCloseCursor) {

                }
            }
            $exWrapper = new DBException($this, $statement, $params, $ex);
            throw $exWrapper;
        }

        return $retResult;
    }

    /**
     * 取得系のクエリを実行する。
     * デフォルトのフェッチモードは、FETCH_ASSOC を指定する。
     *
     * @param string $statement ステートメント
     * @param array $params ステートメントパラメータ配列
     * @param mixed $fetchMode フェッチモード
     * @param mixed $ext 拡張情報
     * @return array 取得データ配列
     */
    public function queryFetch(string $statement, array $params = [], $fetchMode = PDO::FETCH_ASSOC, $ext = null): array {

        $retResult = [];

        $pdoStatement = null;
        try {
            $timeStart_fire = microtime(true);
            DBObserver::notifyOnQueryExecute($this, $statement, $params, ['method' => 'queryFetch', 'detail' => $ext]);

            $timeStart = microtime(true); // performance measure
            $pdoStatement = $this->createPDOStatement($this->pdo, $statement, $params);
            $pdoStatement->execute();
            $timeEnd = microtime(true); // performance measure

            $retResult = $pdoStatement->fetchAll($fetchMode);
            $pdoStatement->closeCursor();

            $timeStart_fire = microtime(true);
            DBObserver::notifyOnQueryExecutePost($this, $statement, $params, ['method' => 'queryFetch', 'detail' => $ext], $retResult, ['startTime' => $timeStart, 'endTime' => $timeEnd]);
        } catch (PDOException $ex) {
            if ($pdoStatement !== null) {
                try {
                    $pdoStatement->closeCursor();
                } catch (PDOException $exCloseCursor) {

                }
            }
            $exWrapper = new DBException($this, $statement, $params, $ex);
            throw $exWrapper;
        }

        return $retResult;
    }

    /**
     * 取得系のクエリをコールバックメソッドによって処理する。
     * 取得データが大量（100万件など）にあり、レスポンスやファイルなど他の出力装置にデータを転送する際の使用を想定。
     * @param string $statement ステートメント
     * @param array $params ステートメントパラメータ配列
     * @param callable $fetchCallback フェッチコールバック関数
     * @param mixed $fetchMode フェッチモード
     * @param mixed $ext 拡張情報
     * @return int 取得件数
     */
    public function queryFetchCallback(string $statement, array $params, callable $fetchCallback, $fetchMode = PDO::FETCH_ASSOC, $ext = null): int {

        $count = 0;

        $pdoStatement = null;
        try {
            $timeStart_fire = microtime(true);
            DBObserver::notifyOnQueryExecute($this, $statement, $params, ['method' => 'queryFetch', 'detail' => $ext]);

            $timeStart = microtime(true); // performance measure
            $pdoStatement = $this->createPDOStatement($this->pdo, $statement, $params);
            $pdoStatement->execute();
            $timeEnd = microtime(true); // performance measure

            while ($result = $pdoStatement->fetch($fetchMode)) {
                call_user_func($fetchCallback, $result);
                $count++;
            }
            $pdoStatement->closeCursor();

            $timeStart_fire = microtime(true);
            DBObserver::notifyOnQueryExecutePost($this, $statement, $params, ['method' => 'queryFetch', 'detail' => $ext], null, ['startTime' => $timeStart, 'endTime' => $timeEnd]);
        } catch (PDOException $ex) {
            if ($pdoStatement !== null) {
                try {
                    $pdoStatement->closeCursor();
                } catch (PDOException $exCloseCursor) {

                }
            }
            $exWrapper = new DBException($this, $statement, $params, $ex);
            throw $exWrapper;
        }

        return $count;
    }

    /**
     * （PDOステートメント）取得系のクエリを実行する。
     * 取得データが大量（100万件など）にあり、レスポンスやファイルなど他の出力装置にデータを転送する際の使用を想定。
     * @param string $statement ステートメント
     * @param array $params ステートメントパラメータ配列
     * @param string $queryKey クエリを識別するキー
     * @param mixed $ext 拡張情報
     * @return PDOStatement PDOステートメント
     */
    public function queryFetchStatement(string $statement, array $params = [], $ext = null): PDOStatement {

        $pdoStatement = null;

        try {
            $timeStart_fire = microtime(true);
            DBObserver::notifyOnQueryExecute($this, $statement, $params, ['method' => 'queryFetch', 'detail' => $ext]);

            $timeStart = microtime(true); // performance measure
            $pdoStatement = $this->createPDOStatement($this->pdo, $statement, $params);
            $pdoStatement->execute();

            $timeEnd = microtime(true); // performance measure

            $timeStart_fire = microtime(true);
            DBObserver::notifyOnQueryExecutePost($this, $statement, $params, ['method' => 'queryFetch', 'detail' => $ext], $pdoStatement, ['startTime' => $timeStart, 'endTime' => $timeEnd]);
        } catch (PDOException $ex) {
            if ($pdoStatement !== null) {
                try {
                    $pdoStatement->closeCursor();
                } catch (PDOException $exCloseCursor) {

                }
            }
            $exWrapper = new DBException($this, $statement, $params, $ex);
            throw $exWrapper;
        }

        return $pdoStatement;
    }

    /**
     * 登録したIDを取得する。
     * @return mixed 登録したID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * タイムアウト時間を設定する。
     * @param PDO $pdo PDO
     * @param int $timeoutMSec タイムアウト時間（ミリ秒）
     */
    private function setTimeout($pdo, $timeoutMSec) {

        $pdo->query('SET @@session.innodb_lock_wait_timeout=' . (int) ($timeoutMSec / 1000));
    }

    /**
     * PDOステートメントを生成する。
     * @param PDO $pdo PDO
     * @param string $statement ステートメント
     * @param array $params ステートメントパラメータ配列
     * @return PDOStatement PDOステートメント
     */
    private function createPDOStatement(PDO $pdo, string $statement, array $params) {

        $pdoStatement = $pdo->prepare($statement, [PDO::ATTR_EMULATE_PREPARES => true]);

        $index = 1;
        foreach ($params as /* $key => */ $val) {
            $pdoStatement->bindValue($index, $val);
            $index++;
        }

        return $pdoStatement;
    }

}
