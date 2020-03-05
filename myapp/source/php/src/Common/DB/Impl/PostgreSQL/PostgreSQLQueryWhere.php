<?php

namespace App\Common\DB\Impl\PostgreSQL;

use App\Common\DB\DBHelper;
use App\Common\DB\DBQueryWhere;

/**
 * DBクエリWHERE。PostgreSQL実装。
 */
class PostgreSQLQueryWhere implements DBQueryWhere {

    /**
     * @var string テーブル名
     */
    private $from;

    /**
     * @var array 条件リスト
     */
    private $condition;

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
        $this->condition = [];
        $this->helper = new PostgreSQLDBHelper();
    }

    /**
     * オーバーライド
     */
    public function condition(string $column, string $op, $value): DBQueryWhere {

        $this->condition[] = ['condition', $column, $op, $value];
        return $this;
    }

    /**
     * オーバーライド
     */
    public function _and(): DBQueryWhere {

        $this->condition[] = ['and'];
        return $this;
    }

    /**
     * オーバーライド
     */
    public function _or(): DBQueryWhere {

        $this->condition[] = ['or'];
        return $this;
    }

    /**
     * オーバーライド
     */
    public function open(): DBQueryWhere {

        $this->condition[] = ['('];
        return $this;
    }

    /**
     * オーバーライド
     */
    public function close(): DBQueryWhere {

        $this->condition[] = [')'];
        return $this;
    }

    /**
     * オーバーライド
     */
    public function compile(string &$sql, array &$sqlParams) {

        $sql = '';
        // $sqlParamsはあえて初期化しない
        /* $sqlParams = []; */

        $sqlWhere = '';

        foreach ($this->condition as $con) {

            switch ($con[0]) {
                case 'condition':
                    $expression = $this->helper->getQueryExpression($con[2], $con[3], $sqlParams);
                    $sqlWhere .= "{$con[1]} {$expression}";
                    break;
                case 'and':
                    $sqlWhere .= ' AND ';
                    break;
                case 'or':
                    $sqlWhere .= ' OR ';
                    break;
                case '(':
                    $sqlWhere .= '(';
                    break;
                case ')':
                    $sqlWhere .= ')';
                    break;
            }
        }

        if (mb_strlen($sqlWhere) > 0) {
            $sql = 'WHERE ' . $sqlWhere;
        }
    }

}
