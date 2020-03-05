<?php

namespace App\Common\DB\Impl\PostgreSQL;

use App\Common\DB\DBQueryDelete;
use App\Common\DB\DBQueryWhere;

/**
 * DBクエリDELETE。PostgreSQL実装。
 */
class PostgreSQLQueryDelete implements DBQueryDelete {

    /**
     * @var string テーブル名
     */
    private $from;

    /**
     * @var DBQueryWhere DBQueryWhere
     */
    private $where;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->from = null;
        $this->where = null;
    }

    /**
     * オーバーライド
     */
    public function from(string $from): DBQueryDelete {
        $this->from = $from;

        return $this;
    }

    /**
     * オーバーライド
     */
    public function where(): DBQueryDelete {

        if ($this->where !== null) {
            return $this;
        }

        $this->where = new PostgreSQLQueryWhere();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function condition(string $column, string $op, $value): DBQueryDelete {
        $this->where->condition($column, $op, $value);
        return $this;
    }

    /**
     * オーバーライド
     */
    public function _and(): DBQueryDelete {
        $this->where->_and();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function _or(): DBQueryDelete {
        $this->where->_or();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function open(): DBQueryDelete {
        $this->where->open();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function close(): DBQueryDelete {
        $this->where->close();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function compile(string &$sql, array &$sqlParams) {

        $sql = '';
        $sqlParams = [];

        $sqlWhere = '';
        if ($this->where !== null) {
            $this->where->compile($sqlWhere, $sqlParams);
            if (mb_strlen($sqlWhere) > 0) {
                $sqlWhere = ' ' . $sqlWhere;
            }
        }

        $sql = "DELETE FROM {$this->from}$sqlWhere";
    }

}
