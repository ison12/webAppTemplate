<?php

namespace App\Common\DB;

use App\Common\App\AppContext;
use App\Common\DB\Impl\MySQL\MySQLConnection;
use App\Common\DB\Impl\MySQL\MySQLDBHelper;
use App\Common\DB\Impl\MySQL\MySQLQueryDelete;
use App\Common\DB\Impl\MySQL\MySQLQueryInsert;
use App\Common\DB\Impl\MySQL\MySQLQuerySelect;
use App\Common\DB\Impl\MySQL\MySQLQueryUpdate;
use App\Common\DB\Impl\MySQL\MySQLQueryWhere;
use App\Common\DB\Impl\PostgreSQL\PostgreSQLConnection;
use App\Common\DB\Impl\PostgreSQL\PostgreSQLDBHelper;
use App\Common\DB\Impl\PostgreSQL\PostgreSQLQueryDelete;
use App\Common\DB\Impl\PostgreSQL\PostgreSQLQueryInsert;
use App\Common\DB\Impl\PostgreSQL\PostgreSQLQuerySelect;
use App\Common\DB\Impl\PostgreSQL\PostgreSQLQueryUpdate;
use App\Common\DB\Impl\PostgreSQL\PostgreSQLQueryWhere;

/**
 * DBコネクションファクトリ。
 */
class DBFactory {

    /**
     * @var array DB設定情報インスタンスリスト。
     */
    private static $dbSettings = [];

    /**
     * DB設定情報を取得する。
     * @param string|array $dbInfo DB情報
     * @return array DB設定情報
     */
    public static function getDBSetting($dbInfo): array {

        if (is_array($dbInfo)) {
            return $dbInfo;
        }

        if (isset(self::$dbSettings[$dbInfo])) {
            return self::$dbSettings[$dbInfo];
        }

        $app = AppContext::get();
        $appContainer = $app->getContainer();

        $dbSettings = $appContainer->get('db')[$dbInfo];

        self::$dbSettings[$dbInfo] = $dbSettings;

        return $dbSettings;
    }

    /**
     * @var array DBConnection DBコネクションインスタンスリスト。
     */
    private static $connections = [];

    /**
     * DB設定情報を元にインスタンスを取得する。
     * @param string|array $dbInfo DB情報
     * @param bool $forceNew 強制的にインスタンスを生成するかどうかのフラグ
     * @return DBConnection DBコネクション
     */
    public static function getConnection($dbInfo = 'default', bool $forceNew = true): DBConnection {

        $connection = null;

        /*
         * DB設定情報を取得する
         */
        $dbSettings = null;
        if (is_array($dbInfo)) {
            $dbSettings = $dbInfo;
        } else {
            $dbSettings = self::getDBSetting($dbInfo);
        }

        if ($forceNew === true) {
            /*
             * 強制的にインスタンスを生成する
             */
            $connection = self::getConnectionBySetting($dbSettings);
        } else {
            /*
             * 通常のモード
             */
            if (!is_array($dbInfo) && isset(self::$connections[$dbInfo])) {
                // キャッシュされている場合は、キャッシュから取得する
                $connection = self::$connections[$dbInfo];
            } else {
                // DB設定情報を取得しDB接続を取得する
                $connection = self::getConnectionBySetting($dbSettings);

                self::$connections[$dbInfo] = $connection;
            }
        }

        return $connection;
    }

    /**
     * DB設定情報を元にインスタンスを取得する。
     * @param array $dbSettings DB設定情報
     * @return DBConnection DBコネクション
     */
    public static function getConnectionBySetting(array $dbSettings): DBConnection {

        $instance = null;
        if ($dbSettings['type'] === 'pgsql') {

            $instance = new PostgreSQLConnection(
                    $dbSettings['connectionStr']
                    , $dbSettings['userId']
                    , $dbSettings['password']
                    , $dbSettings['connectTimeoutMsec']
                    , $dbSettings['queryTimeoutMsec']);
        } else if ($dbSettings['type'] === 'mysql') {

            $instance = new MySQLConnection(
                    $dbSettings['connectionStr']
                    , $dbSettings['userId']
                    , $dbSettings['password']
                    , $dbSettings['connectTimeoutMsec']
                    , $dbSettings['queryTimeoutMsec']);
        }

        return $instance;
    }

    /**
     * INSERTオブジェクトを生成する。
     * @param string|array $dbInfo DB情報
     * @return DBQueryInsert INSERT
     */
    public static function createInsert(string $dbInfo = 'default'): DBQueryInsert {

        $dbSetting = self::getDBSetting($dbInfo);
        if ($dbSetting['type'] === 'pgsql') {
            return new PostgreSQLQueryInsert();
        } else if ($dbSetting['type'] === 'mysql') {
            return new MySQLQueryInsert();
        }

        return null;
    }

    /**
     * UPDATEオブジェクトを生成する。
     * @param string|array $dbInfo DB情報
     * @return DBQueryUpdate UPDATE
     */
    public static function createUpdate(string $dbInfo = 'default'): DBQueryUpdate {

        $dbSetting = self::getDBSetting($dbInfo);
        if ($dbSetting['type'] === 'pgsql') {
            return new PostgreSQLQueryUpdate();
        } else if ($dbSetting['type'] === 'mysql') {
            return new MySQLQueryUpdate();
        }

        return null;
    }

    /**
     * DELETEオブジェクトを生成する。
     * @param string|array $dbInfo DB情報
     * @return DBQueryDelete DELETE
     */
    public static function createDelete(string $dbInfo = 'default'): DBQueryDelete {

        $dbSetting = self::getDBSetting($dbInfo);
        if ($dbSetting['type'] === 'pgsql') {
            return new PostgreSQLQueryDelete();
        } else if ($dbSetting['type'] === 'mysql') {
            return new MySQLQueryDelete();
        }

        return null;
    }

    /**
     * SELECTオブジェクトを生成する。
     * @param string|array $dbInfo DB情報
     * @return DBQuerySelect SELECT
     */
    public static function createSelect(string $dbInfo = 'default'): DBQuerySelect {

        $dbSetting = self::getDBSetting($dbInfo);
        if ($dbSetting['type'] === 'pgsql') {
            return new PostgreSQLQuerySelect();
        } else if ($dbSetting['type'] === 'mysql') {
            return new MySQLQuerySelect();
        }

        return null;
    }

    /**
     * WHEREオブジェクトを生成する。
     * @param string|array $dbInfo DB情報
     * @return DBQueryWhere WHERE
     */
    public static function createWhere(string $dbInfo = 'default'): DBQueryWhere {

        $dbSetting = self::getDBSetting($dbInfo);
        if ($dbSetting['type'] === 'pgsql') {
            return new PostgreSQLQueryWhere();
        } else if ($dbSetting['type'] === 'mysql') {
            return new MySQLQueryWhere();
        }

        return null;
    }

    /**
     * DBヘルパーオブジェクトを生成する。
     * @param string|array $dbInfo DB情報
     * @return DBHelper DBヘルパー
     */
    public static function createDBHelper(string $dbInfo = 'default'): DBHelper {

        $dbSetting = self::getDBSetting($dbInfo);
        if ($dbSetting['type'] === 'pgsql') {
            return new PostgreSQLDBHelper();
        } else if ($dbSetting['type'] === 'mysql') {
            return new MySQLDBHelper();
        }

        return null;
    }

}
