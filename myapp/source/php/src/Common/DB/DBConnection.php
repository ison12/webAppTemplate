<?php

namespace App\Common\DB;

use PDO;
use PDOStatement;

/**
 * DBコネクション。
 */
interface DBConnection {

    /**
     * PDOを取得する。
     * @return PDO PDO
     */
    public function getPDO();

    /**
     * DB接続文字列を取得する。
     * @return string DB接続文字列
     */
    public function getConnectionStr();

    /**
     * DBに接続する。
     * @param string $connectionStr DB接続文字列
     * @param string $userId ユーザーID
     * @param string $password パスワード
     * @param int $connectTimeoutMSec 接続タイムアウト（ミリ秒）
     * @param int $queryTimeoutMSec クエリタイムアウト（ミリ秒）
     */
    public function connect(string $connectionStr, string $userId, string $password, int $connectTimeoutMSec = null, int $queryTimeoutMSec = null);

    /**
     * DBから切断する。
     */
    public function disconnect();

    /**
     * トランザクションを開始する。
     */
    public function beginTransaction();

    /**
     * トランザクションをロールバックする。
     */
    public function rollback();

    /**
     * トランザクションをコミットする。
     */
    public function commit();

    /**
     * 更新系のクエリを実行する。
     * @param string $statement ステートメント
     * @param array $params ステートメントパラメータ配列
     * @param mixed $ext 拡張情報
     * @return int 更新件数
     */
    public function queryAction(string $statement, array $params = [], $ext = null): int;

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
    public function queryFetch(string $statement, array $params = [], $fetchMode = PDO::FETCH_ASSOC, $ext = null): array;

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
    public function queryFetchCallback(string $statement, array $params, callable $fetchCallback, $fetchMode = PDO::FETCH_ASSOC, $ext = null): int;

    /**
     * （PDOステートメント）取得系のクエリを実行する。
     * 取得データが大量（100万件など）にあり、レスポンスやファイルなど他の出力装置にデータを転送する際の使用を想定。
     * @param string $statement ステートメント
     * @param array $params ステートメントパラメータ配列
     * @param string $queryKey クエリを識別するキー
     * @param mixed $ext 拡張情報
     * @return PDOStatement PDOステートメント
     */
    public function queryFetchStatement(string $statement, array $params = [], $ext = null): PDOStatement;

    /**
     * 登録したIDを取得する。
     * @return mixed 登録したID
     */
    public function lastInsertId();
}
