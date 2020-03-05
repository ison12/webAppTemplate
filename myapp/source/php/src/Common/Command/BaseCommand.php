<?php

namespace App\Common\Command;

use App\Common\DB\DBObserver;
use App\Common\Log\AppLogger;
use App\Common\Util\DateUtil;
use App\Common\Util\DBUtil;
use DateTime;

/**
 * コマンド。
 */
abstract class BaseCommand {

    /**
     * @var AppLogger ログオブジェクト
     */
    protected $logger = null;

    /**
     * @var DateTime 現在日時
     */
    protected $systemDate;

    /**
     * コンストラクタ。
     */
    public function __construct() {

        $this->logger = AppLogger::get();
        $this->systemDate = DateUtil::getSystemDate();

        DBObserver::addQueryObserver('global', function($dbConnection, $statement, $params, $ext) {
            $this->logger->info(DBUtil::combineQueryStatementAndParams($statement, $params));
        });
    }

    /**
     * 実行処理。
     * @param mixed $params パラメータ
     * @return int 結果コード
     */
    abstract public function exec($params);
}
