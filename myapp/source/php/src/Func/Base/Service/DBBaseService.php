<?php

namespace App\Func\Base\Service;

use App\Common\DB\DBConnection;
use App\Common\DB\DBFactory;
use Exception;

/**
 * DBに接続する、基本となるサービスクラス。
 * サービスクラスは、本クラスを継承すること。
 */
class DBBaseService extends BaseService {

    /**
     * @var DBConnection DBコネクション
     */
    protected $dbConnection;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
        $this->dbConnection = DBFactory::getConnection();
    }

    /**
     * トランザクション処理。
     * @param DBConnection $dbConnection DBコネクション
     * @param callable $onProcess 処理
     * @return mixed 戻り値
     */
    public function transaction($dbConnection, $onProcess) {

        $ret = null;

        try {
            // 開始
            $dbConnection->beginTransaction();

            // トランザクション処理
            $ret = call_user_func($onProcess, $dbConnection);

            // コミット
            $dbConnection->commit();
        } catch (Exception $exc) {
            // ロールバック
            $dbConnection->rollback();
            throw $exc;
        }

        return $ret;
    }

}
