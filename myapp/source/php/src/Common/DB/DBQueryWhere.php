<?php

namespace App\Common\DB;

/**
 * DBクエリWHERE句。
 */
interface DBQueryWhere {

    /**
     * 条件の指定。
     * @param string $column カラム
     * @param string $op 比較記号
     * @param mixed $value 値
     * @return DBQueryWhere 自オブジェクト
     */
    public function condition(string $column, string $op, $value): DBQueryWhere;

    /**
     * AND条件の指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function _and(): DBQueryWhere;

    /**
     * OR条件の指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function _or(): DBQueryWhere;

    /**
     * 括弧の開始指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function open(): DBQueryWhere;

    /**
     * 括弧の終了指定。
     * @return DBQueryWhere 自オブジェクト
     */
    public function close(): DBQueryWhere;

    /**
     * SQLの生成。
     * @param string $sql SQL
     * @param array $sqlParams SQLパラメータ
     */
    public function compile(string &$sql, array &$sqlParams);
}
