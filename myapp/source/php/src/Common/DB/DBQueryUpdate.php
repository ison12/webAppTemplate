<?php

namespace App\Common\DB;

/**
 * DBクエリUPDATE。
 */
interface DBQueryUpdate {

    /**
     * 登録するテーブルの指定。
     * @param string $from 登録するテーブル
     * @return DBQueryInsert 自オブジェクト
     */
    public function from(string $from): DBQueryUpdate;

    /**
     * 値の指定。
     * @param string $column カラム
     * @param string $value 値
     * @return DBQueryInsert 自オブジェクト
     */
    public function set(string $column, string $value = null): DBQueryUpdate;

    /**
     * 条件の指定。
     * @return DBQueryWhere Whereオブジェクト
     */
    public function where(): DBQueryUpdate;

    /**
     * 条件の指定。
     * @param string $column カラム
     * @param string $op 比較記号
     * @param mixed $value 値
     * @return DBQueryWhere 自オブジェクト
     */
    public function condition(string $column, string $op, $value): DBQueryUpdate;

    /**
     * AND条件の指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function _and(): DBQueryUpdate;

    /**
     * OR条件の指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function _or(): DBQueryUpdate;

    /**
     * 括弧の開始指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function open(): DBQueryUpdate;

    /**
     * 括弧の終了指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function close(): DBQueryUpdate;

    /**
     * SQLの生成。
     * @param string $sql SQL
     * @param array $sqlParams SQLパラメータ
     */
    public function compile(string &$sql, array &$sqlParams);
}
