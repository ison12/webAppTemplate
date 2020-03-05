<?php

namespace App\Dao;

use App\Common\DB\DBConnection;

/**
 * 基本となるDAOクラス。
 * DAOクラスは、本クラスを継承すること。
 */
class BaseDao {

    /**
     * @var \App\Common\DB\Connection\DBConnection DBコネクション
     */
    protected $dbConnection;

    /**
     * コンストラクタ。
     * @param DBConnection $dbConnection DBコネクション
     */
    public function __construct(DBConnection $dbConnection) {
        $this->dbConnection = $dbConnection;
    }

}
