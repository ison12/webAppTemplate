<?php

namespace App\Common\DB;

/**
 * DBクエリDELETE。
 */
interface DBQueryDelete {

    /**
     * 登録するテーブルの指定。
     * @param string $from 登録するテーブル
     * @return DBQueryDelete 自オブジェクト
     */
    public function from(string $from): DBQueryDelete;

    /**
     * 条件の指定。
     * @return DBQueryWhere Whereオブジェクト
     */
    public function where(): DBQueryDelete;

    /**
     * 条件の指定。
     * @param string $column カラム
     * @param string $op 比較記号
     * @param mixed $value 値
     * @return DBQueryWhere 自オブジェクト
     */
    public function condition(string $column, string $op, $value): DBQueryDelete;

    /**
     * AND条件の指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function _and(): DBQueryDelete;

    /**
     * OR条件の指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function _or(): DBQueryDelete;

    /**
     * 括弧の開始指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function open(): DBQueryDelete;

    /**
     * 括弧の終了指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function close(): DBQueryDelete;

    /**
     * SQLの生成。
     * @param string $sql SQL
     * @param array $sqlParams SQLパラメータ
     */
    public function compile(string &$sql, array &$sqlParams);
}
