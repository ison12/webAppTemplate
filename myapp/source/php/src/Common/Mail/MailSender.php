<?php

namespace App\Common\Mail;

use App\Common\Util\ValUtil;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * メール送信
 */
class MailSender {

    /**
     * @var MailSender
     */
    private static $instances = array();

    /**
     * インスタンスを取得する。
     * @param string $name 名前
     * @param string $setting 設定情報
     * @return MailSender メール送信
     */
    public static function getInstance($name, $setting) {

        // Make default if first instance
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new MailSender($setting);
            return self::$instances[$name];
        }

        return self::$instances[$name];
    }

    private $mailSmtpHost;
    private $mailSmtpUserName;
    private $mailSmtpPassword;
    private $mailSmtpSecure;
    private $mailSmtpPort;
    private $mailFromAddress;
    private $mailFromName;
    private $mailReplyToAddress;
    private $mailReplyToName;
    private $mailBccList;

    /**
     * コンストラクタ。
     * @param array $setting 設定情報
     */
    public function __construct(array $setting) {

        $this->mailSmtpHost = $setting['SMTP_SERVER_HOST'];
        $this->mailSmtpPort = $setting['SMTP_SERVER_PORT'];
        $this->mailSmtpUserName = $setting['SMTP_SERVER_USER_ID'];
        $this->mailSmtpPassword = $setting['SMTP_SERVER_PASSWORD'];
        $this->mailSmtpSecure = $setting['SMTP_SERVER_SECURE'];
        $this->mailFromAddress = $setting['MAIL_FROM_ADDRESS'];
        $this->mailFromName = $setting['MAIL_FROM_NAME'];
        $this->mailReplyToAddress = $setting['MAIL_REPLY_TO_ADDRESS'];
        $this->mailReplyToName = $setting['MAIL_REPLY_TO_NAME'];
        $this->mailBccList = $setting['MAIL_BCC_LIST'] ?? [];
    }

    /**
     * メールを送信する。
     * @param array $toAddressList 宛先リスト
     * @param array $ccAddressList CCリスト
     * @param array $bccAddressList BCCリスト
     * @param string $subject 件名
     * @param string $body 本文
     */
    public function send(array $toAddressList
            , array $ccAddressList
            , array $bccAddressList
            , string $subject
            , string $body) {

        // PHPMailerのインスタンス生成
        $mail = new PHPMailer(true);

        // SMTPを使うようにメーラーを設定する
        $mail->isSMTP();

        if (!ValUtil::isEmpty($this->mailSmtpUserName)) {
            // 認証実行時
            $mail->SMTPAuth = true;
        }

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->Host = $this->mailSmtpHost; // メインのSMTPサーバーを指定する

        if (!ValUtil::isEmpty($this->mailSmtpUserName)) {
            // 認証実行時
            $mail->Username = $this->mailSmtpUserName; // SMTPユーザー名
            $mail->Password = $this->mailSmtpPassword; // SMTPパスワード
        }

        $mail->SMTPSecure = $this->mailSmtpSecure; // TLS暗号化を有効にし、「SSL」も受け入れます
        $mail->Port = $this->mailSmtpPort; // 接続するTCPポート
        //
        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";

        $mail->setFrom($this->mailFromAddress, $this->mailFromName);
        $mail->addReplyTo($this->mailReplyToAddress, $this->mailReplyToName);

        foreach ($toAddressList as $address) {
            $mail->addAddress($address['address'], $address['name']);
        }

        foreach ($ccAddressList as $address) {
            $mail->addCC($address['address'], $address['name']);
        }

        foreach ($bccAddressList as $address) {
            $mail->addBcc($address['address'], $address['name']);
        }

        $mail->Subject = $subject; // メールタイトル
        $mail->Body = $body; // メール本文
        // HTMLメール
        //$mail->isHTML(true);

        $mail->send();
    }

}
