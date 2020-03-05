<?php

namespace App\Common\DB;

/**
 * DBクエリINSERT。
 */
interface DBQueryInsert {

    /**
     * 登録するテーブルの指定。
     * @param string $from 登録するテーブル
     * @return DBQueryInsert 自オブジェクト
     */
    public function from(string $from): DBQueryInsert;

    /**
     * カラムの指定。
     * @param string $column カラム
     * @return DBQueryInsert 自オブジェクト
     */
    public function column(string ...$column): DBQueryInsert;

    /**
     * 値の指定。
     * @param array $value カラム
     * @return DBQueryInsert 自オブジェクト
     */
    public function value(array ...$value): DBQueryInsert;

    /**
     * SQLの生成。
     * @param string $sql SQL
     * @param array $sqlParams SQLパラメータ
     */
    public function compile(string &$sql, array &$sqlParams);
}
