<?php

namespace App\Common\DB;

/**
 * DB監視クラス。
 */
class DBObserver {

    /**
     * @var callback クエリ監視オブジェクト
     */
    private static $observerQueries = [];

    /**
     * @var callback クエリ監視オブジェクト（発行後）
     */
    private static $observerQueriesPost = [];

    /**
     * クエリが発行されることを通知する。
     * @param DBConnection $dbConnection DB接続
     * @param string $statement ステートメント
     * @param array $params パラメータリスト
     * @param mixed $ext 拡張情報
     */
    public static function notifyOnQueryExecute(
    DBConnection $dbConnection
    , string $statement
    , array $params = []
    , $ext = null) {

        foreach (self::$observerQueries as $query) {
            call_user_func_array($query, [$dbConnection, $statement, $params, $ext]);
        }
    }

    /**
     * クエリが発行されたことを通知する。
     * （発行後）
     * @param DBConnection $dbConnection DB接続
     * @param string $statement ステートメント
     * @param array $params パラメータリスト
     * @param mixed $ext 拡張情報
     * @param mixed $result 結果リスト
     * @param array $processTime 処理時間リスト
     */
    public static function notifyOnQueryExecutePost(
    DBConnection $dbConnection
    , string $statement
    , array $params = []
    , $ext = null
    , $result = null
    , array $processTime = null) {

        foreach (self::$observerQueriesPost as $query) {
            call_user_func_array($query, [$dbConnection, $statement, $params, $ext, $result, $processTime]);
        }
    }

    /**
     * クエリ監視オブジェクトを追加する。
     * @param string $name 名前
     * @param callable $callback コールバック関数
     */
    public static function addQueryObserver(string $name, callable $callback) {

        self::$observerQueries[$name] = $callback;
    }

    /**
     * クエリ監視オブジェクトを削除する。
     * @param string $name 名前
     */
    public static function removeQueryObserver(string $name) {

        unset(self::$observerQueries[$name]);
    }

    /**
     * クエリ監視オブジェクトを追加する。（発行後）
     * @param string $name 名前
     * @param callable $callback コールバック関数
     */
    public static function addQueryObserverPost($name, $callback) {

        self::$observerQueriesPost[$name] = $callback;
    }

    /**
     * クエリ監視オブジェクトを削除する。（発行後）
     * @param string $name 名前
     */
    public static function removeQueryObserverPost($name) {

        unset(self::$observerQueriesPost[$name]);
    }

}
