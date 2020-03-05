<?php

namespace App\Common\DB\Impl\MySQL;

use App\Common\DB\DBQuerySelect;
use App\Common\DB\DBQueryWhere;
use App\Common\DB\DBRawValue;

/**
 * DBクエリSELECT。MySQL実装。
 */
class MySQLQuerySelect implements DBQuerySelect {

    /**
     * @var string テーブル名
     */
    private $from;

    /**
     * @var array カラムリスト
     */
    private $columns;

    /**
     * @var array 並び順
     */
    private $orderBy;

    /**
     * @var bool ロック有無
     */
    private $lock;

    /**
     * @var bool ロック時に待機なし
     */
    private $nowait;

    /**
     * @var int オフセット
     */
    private $offset;

    /**
     * @var int リミット
     */
    private $limit;

    /**
     * @var DBQueryWhere DBQueryWhere
     */
    private $where;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->from = null;
        $this->columns = [];
        $this->orderBy = [];
        $this->lock = false;
        $this->nowait = false;
        $this->offset = null;
        $this->limit = null;
        $this->where = null;
    }

    /**
     * オーバーライド
     */
    public function column(...$column): DBQuerySelect {
        foreach ($column as $c) {
            $this->columns[] = $c;
        }
        return $this;
    }

    /**
     * オーバーライド
     */
    public function from(string $from): DBQuerySelect {
        $this->from = $from;

        return $this;
    }

    /**
     * オーバーライド
     */
    public function offset(int $offset): DBQuerySelect {
        $this->offset = $offset;
        return $this;
    }

    /**
     * オーバーライド
     */
    public function limit(int $limit): DBQuerySelect {
        $this->limit = $limit;
        return $this;
    }

    /**
     * オーバーライド
     */
    public function where(): DBQuerySelect {

        if ($this->where !== null) {
            return $this;
        }

        $this->where = new MySQLQueryWhere();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function condition(string $column, string $op, $value): DBQuerySelect {
        $this->where->condition($column, $op, $value);
        return $this;
    }

    /**
     * オーバーライド
     */
    public function _and(): DBQuerySelect {
        $this->where->_and();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function _or(): DBQuerySelect {
        $this->where->_or();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function open(): DBQuerySelect {
        $this->where->open();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function close(): DBQuerySelect {
        $this->where->close();
        return $this;
    }

    /**
     * オーバーライド
     */
    public function orderByAsc(string $column): DBQuerySelect {
        $this->orderBy[] = ['ASC', $column];
        return $this;
    }

    /**
     * オーバーライド
     */
    public function orderByDesc(string $column): DBQuerySelect {
        $this->orderBy[] = ['DESC', $column];
        return $this;
    }

    /**
     * オーバーライド
     */
    public function lock(): DBQuerySelect {
        $this->lock = true;
        return $this;
    }

    /**
     * オーバーライド
     */
    public function nowait(): DBQuerySelect {
        $this->nowait = true;
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

        foreach ($this->columns as $column) {

            $columnStr = '';
            if ($column instanceof DBRawValue) {
                $columnStr = $column->value[0] . ' AS ' . $column->value[1];
            } else {
                $columnStr = $column;
            }

            $sqlColumn .= $sqlColumnSplit . $columnStr;
            $sqlColumnSplit = ', ';
        }

        $sqlWhere = '';
        if ($this->where !== null) {
            $this->where->compile($sqlWhere, $sqlParams);
            if (mb_strlen($sqlWhere) > 0) {
                $sqlWhere = ' ' . $sqlWhere;
            }
        }

        $sqlOrderby = '';
        $sqlOrderbySplit = '';
        foreach ($this->orderBy as $o) {
            $sqlOrderby .= $sqlOrderbySplit . $o[1] . ' ' . $o[0];
            $sqlOrderbySplit = ', ';
        }
        if (mb_strlen($sqlOrderby) > 0) {
            $sqlOrderby = ' ORDER BY ' . $sqlOrderby;
        }

        $sqlLock = '';
        if ($this->lock) {
            $sqlLock = ' FOR UPDATE';
        }

        if ($this->nowait) {
            $sqlLock .= ' NOWAIT';
        }

        $sqlLimit = '';
        if ($this->limit !== null) {
            $sqlLimit = ' LIMIT ' . $this->limit;
        }

        $sqlOffset = '';
        if ($this->offset !== null) {
            $sqlOffset = ' OFFSET ' . $this->offset;
        }


        $sql = "SELECT {$sqlColumn} FROM {$this->from}{$sqlWhere}{$sqlOrderby}{$sqlLimit}{$sqlOffset}{$sqlLock}";
    }

}
