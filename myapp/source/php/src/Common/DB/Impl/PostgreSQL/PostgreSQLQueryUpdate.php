<?php

namespace App\Common\DB\Impl\PostgreSQL;

use App\Common\DB\DBHelper;
use App\Common\DB\DBQueryUpdate;
use App\Common\DB\DBQueryWhere;

/**
 * DBクエリUPDATE。PostgreSQL実装。
 */
class PostgreSQLQueryUpdate implements DBQueryUpdate {

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
    private $values;

    /**
     * @var DBQueryWhere DBQueryWhere
     */
    private $where;

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
        $this->values = [];
        $this->where = null;
        $this->helper = new PostgreSQLDBHelper();
    }

    /**
     * オーバーライド
     */
    public function from(string $from): DBQueryUpdate {
        $this->from = $from;

        return $this;
    }

    /**
     * オーバーライド
     */
    public function set(string $column, string $value = null): DBQueryUpdate {
        $this->columns[] = $column;
        $this->values[] = $value;

        return $this;
    }

    /**
     * オーバーライド
     */
    public function where(): DBQueryUpdate {

        if ($this->where !== null) {
            return $this;
        }

        $this->where = new PostgreSQLQueryWhere();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function condition(string $column, string $op, $value): DBQueryUpdate {
        $this->where->condition($column, $op, $value);
        return $this;
    }

    /**
     * オーバーライド
     */
    public function _and(): DBQueryUpdate {
        $this->where->_and();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function _or(): DBQueryUpdate {
        $this->where->_or();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function open(): DBQueryUpdate {
        $this->where->open();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function close(): DBQueryUpdate {
        $this->where->close();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function compile(string &$sql, array &$sqlParams) {

        $sql = '';
        $sqlParams = [];

        $sqlValue = '';
        $sqlValueSplit = '';

        for ($index = 0, $count = count($this->columns); $index < $count; $index++) {
            $sqlValue .= $sqlValueSplit . $this->columns[$index] . ' = ' . $this->helper->getQueryValue($this->values[$index], $sqlParams);
            $sqlValueSplit = ', ';
        }

        $sqlWhere = '';
        if ($this->where !== null) {
            $this->where->compile($sqlWhere, $sqlParams);
            if (mb_strlen($sqlWhere) > 0) {
                $sqlWhere = ' ' . $sqlWhere;
            }
        }

        $sql = "UPDATE {$this->from} SET {$sqlValue}$sqlWhere";
    }

}
