<?php

namespace App\Common\DB;

/**
 * DBクエリSELECT。
 */
interface DBQuerySelect {

    /**
     * カラムの指定。
     * @param mixed $column カラム
     * @return DBQuerySelect 自オブジェクト
     */
    public function column(...$column): DBQuerySelect;

    /**
     * テーブルの指定。
     * @param string $from テーブル
     * @return DBQuerySelect 自オブジェクト
     */
    public function from(string $from): DBQuerySelect;

    /**
     * 条件の指定。
     * @return DBQuerySelect Whereオブジェクト
     */
    public function where(): DBQuerySelect;

    /**
     * 条件の指定。
     * @param string $column カラム
     * @param string $op 比較記号
     * @param mixed $value 値
     * @return DBQuerySelect 自オブジェクト
     */
    public function condition(string $column, string $op, $value): DBQuerySelect;

    /**
     * AND条件の指定。
     * @return DBQuerySelect 自オブジェクト
     */
    public function _and(): DBQuerySelect;

    /**
     * OR条件の指定。
     * @return DBQuerySelect 自オブジェクト
     */
    public function _or(): DBQuerySelect;

    /**
     * 括弧の開始指定。
     * @return DBQuerySelect 自オブジェクト
     */
    public function open(): DBQuerySelect;

    /**
     * 括弧の終了指定。
     * @return DBQuerySelect 自オブジェクト
     */
    public function close(): DBQuerySelect;

    /**
     * ASCの並び替え指定。
     * @return DBQuerySelect 自オブジェクト
     */
    public function orderByAsc(string $column): DBQuerySelect;

    /**
     * DESCの並び替え指定。
     * @param string $column カラム
     * @return DBQuerySelect 自オブジェクト
     */
    public function orderByDesc(string $column): DBQuerySelect;

    /**
     * ロック指定。
     * @return DBQuerySelect 自オブジェクト
     */
    public function lock(): DBQuerySelect;

    /**
     * ロック時に待機なし指定。
     * @return DBQuerySelect 自オブジェクト
     */
    public function nowait(): DBQuerySelect;

    /**
     * オフセット指定。
     * @param int $offset オフセット
     */
    public function offset(int $offset): DBQuerySelect;

    /**
     * リミット指定。
     * @param int $limit リミット
     * @return DBQuerySelect 自オブジェクト
     */
    public function limit(int $limit): DBQuerySelect;

    /**
     * SQLの生成。
     * @param string $sql SQL
     * @param array $sqlParams SQLパラメータ
     */
    public function compile(string &$sql, array &$sqlParams);
}
