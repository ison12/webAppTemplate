<?php

namespace App\Common\DB\Impl\PostgreSQL;

use App\Common\DB\DBHelper;
use App\Common\DB\DBQueryInsert;

/**
 * DBクエリINSERT。PostgreSQL実装。
 */
class PostgreSQLQueryInsert implements DBQueryInsert {

    /**
     * @var string テーブル名
     */
    private $from;

    /**
     * @var array カラムリスト
     */
    private $columns;

    /**
     * @var array 値リスト
     */
    private $valuesSet;

    /**
     *
     * @var DBHelper DBヘルパー
     */
    private $helper;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->from = null;
        $this->columns = [];
        $this->valuesSet = [];
        $this->helper = new PostgreSQLDBHelper();
    }

    /**
     * オーバーライド
     */
    public function from(string $from): DBQueryInsert {
        $this->from = $from;

        return $this;
    }

    /**
     * オーバーライド
     */
    public function column(string ...$column): DBQueryInsert {
        foreach ($column as $c) {
            $this->columns[] = $c;
        }
        return $this;
    }

    /**
     * オーバーライド
     */
    public function value(array ...$value): DBQueryInsert {
        foreach ($value as $v) {
            $this->valuesSet[] = $v;
        }
        return $this;
    }

    /**
     * オーバーライド
     */
    public function compile(string &$sql, array &$sqlParams) {

        $sql = '';
        $sqlParams = [];

        $sqlColumn = '';
        $sqlColumnSplit = '';

        $sqlValueSet = '';
        $sqlValueSetSplit = '';

        foreach ($this->columns as $column) {
            $sqlColumn .= $sqlColumnSplit . $column;
            $sqlColumnSplit = ', ';
        }

        foreach ($this->valuesSet as $values) {

            $sqlValue = '';
            $sqlValueSplit = '';
            foreach ($values as $value) {
                $sqlValue .= $sqlValueSplit . $this->helper->getQueryValue($value, $sqlParams);
                $sqlValueSplit = ', ';
            }

            $sqlValueSet .= $sqlValueSetSplit . '(' . $sqlValue . ')';
            $sqlValueSetSplit = ', ';
        }

        $sql = 'INSERT INTO ' . $this->from . ' (' . $sqlColumn . ') VALUES ' . $sqlValueSet;
    }

}
