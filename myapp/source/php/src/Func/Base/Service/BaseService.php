<?php

namespace App\Func\Base\Service;

use App\Common\Exception\ServiceException;
use App\Common\Log\AppLogger;
use App\Common\Message\MessageManager;
use App\Common\Util\DateUtil;
use App\Common\Validation\Validatation;
use DateTime;
use const SRC_PATH;

/**
 * 基本となるサービスクラス。
 * サービスクラスは、本クラスを継承すること。
 */
class BaseService {

    /**
     * @var DateTime システム日付
     */
    protected $systemDate;

    /**
     *
     * @var MessageManager メッセージ管理
     */
    protected $errorMessage;

    /**
     * @var AppLogger ロガー
     */
    protected $logger;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->systemDate = DateUtil::getSystemDate();
        $this->errorMessage = MessageManager::getInstance(SRC_PATH . 'Message/MessageConfig.php');
        $this->logger = AppLogger::get();
    }

    /**
     * データ存在なし例外をスローする。
     * @param string $itemName 項目名
     * @param string $id ID
     * @throws ServiceException データ存在なし例外
     */
    protected function throwDataNotFound($itemName, $id) {

        $this->throwAnyError('error_data_not_found', 'error_data_not_found', ['%itemName%' => $itemName, '%id%' => $id]);
    }

    /**
     * 例外をスローする。
     * @param string $itemId 項目ID
     * @param string $messageId メッセージID
     * @param string $messageParams メッセージパラメータリスト
     * @throws ServiceException データ存在なし例外
     */
    protected function throwAnyError(string $itemId, string $messageId, array $messageParams) {

        $error = Validatation::createError(
                        $itemId
                        , $this->errorMessage->get($messageId, $messageParams));
        throw new ServiceException([$error]);
    }

}
