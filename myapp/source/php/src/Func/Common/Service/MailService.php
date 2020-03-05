<?php

namespace App\Func\Common\Service;

use App\Common\Exception\ServiceException;
use App\Common\Mail\MailSender;
use App\Common\Util\ValUtil;
use App\Common\Util\ViewUtil;
use App\Dao\SystemSetting\SystemSettingDao;
use App\Func\Base\Service\BaseService;
use App\Func\Base\Service\DBBaseService;
use const SRC_PATH;

/**
 * メール送信サービス。
 */
class MailService extends DBBaseService {

    /**
     * コンストラクタ。
     * @param type $dbConnection DBコネクション
     */
    public function __construct($dbConnection = null) {

        if (ValUtil::isEmpty($dbConnection)) {
            parent::__construct();
        } else {
            $this->dbConnection = $dbConnection;
            BaseService::__construct();
        }
    }

    /**
     * メールを送信する。
     * @param string $templateName テンプレート
     *              SRC_PATH以下のテンプレートファイル名を指定する
     *              例）Mail/Template/TestMail とした場合、srcフォルダからの以下のファイルが使用される。
     *                  件名：Mail/Template/TestMailSubject.php
     *                  本文：Mail/Template/TestMailBody.php
     * @param array $embedList 本文埋込みパラメータリスト([埋込みキー名 => 埋込みパラメータ]の形式)
     * @param array $toAddressList 送信先(To)アドレスリスト([['address' => 'メールアドレス, 'name' => '宛先名']]の形式)
     * @param array $ccAddressList 送信先(Cc)アドレスリスト([['address' => 'メールアドレス, 'name' => '宛先名']]の形式)
     * @throws ServiceException
     */
    public function send(string $templateName, array $embedList, array $toAddressList, array $ccAddressList = array()) {

        // システム設定情報一覧を取得
        $systemDao = new SystemSettingDao($this->dbConnection);
        $systems = $systemDao->getFromCache();
        $setting = array_column($systems, 'system_value', 'system_code');

        // メール送信情報を生成する
        $mailSubject = $this->createSubject($templateName . '_Subject.php', $embedList);
        $mailBody = $this->createBody($templateName . '_Body.php', $embedList);

        // メール送信
        $mailSender = MailSender::getInstance('default', $setting);
        $mailSender->send($toAddressList, $ccAddressList, $mailSubject, $mailBody);
    }

    /**
     * 管理者用メール送信先リストを取得する
     * @return array
     */
    public function getAdminAddress(): array {

        // 管理者用メール送信先情報を取得
        $systemDao = new SystemSettingDao($this->dbConnection);

        // 管理者メール送信先アドレス
        $address = '';
        $addrSetting = $systemDao->getFromCacheBySystemCode(ConstSystemSetting::MAIL_TO_ADDRESS);
        if (!ValUtil::isEmpty($addrSetting)) {
            $address = $addrSetting['system_value'];
        }
        // 管理者メール送信先名
        $name = '';
        $nameSetting = $systemDao->getFromCacheBySystemCode(ConstSystemSetting::MAIL_TO_NAME);
        if (!ValUtil::isEmpty($nameSetting)) {
            $name = $addrSetting['system_value'];
        }
        return array(
            array('address' => $address, 'name' => $name)
        );
    }

    /**
     * メール件名を作成する。
     * @param string $templateName テンプレート名
     * @param array $embedList 埋め込みパラメータリスト
     * @return string 件名
     */
    private function createSubject(string $templateName, array $embedList): string {

        // メール本文テンプレートファイルを読込み
        $body = ViewUtil::render(sprintf('%sMail/Template/%s', SRC_PATH, $templateName), $embedList);
        return $body;
    }

    /**
     * メール本文を作成する。
     * @param string $templateName テンプレート名
     * @param array $embedList 埋め込みパラメータリスト
     * @return string 本文
     */
    private function createBody(string $templateName, array $embedList): string {

        // メール本文テンプレートファイルを読込み
        $body = ViewUtil::render(sprintf('%sMail/Template/%s', SRC_PATH, $templateName), $embedList);
        // 改行コードをCRLFに統一する
        $body = str_replace("\r\n", "\n", $body);
        $body = str_replace("\r", "\n", $body);
        $body = str_replace("\n", "\r\n", $body);
        return $body;
    }

}
