<?php

namespace App\Common\Message;

/**
 * メッセージ管理
 */
class MessageManager {

    /**
     * @var MessageManager メッセージ管理
     */
    private static $instances = array();

    /**
     * インスタンスを取得する。
     * @return MessageManager メッセージ管理
     */
    public static function getInstance($messageFilePath) {

        // Make default if first instance
        if (!isset(self::$instances[$messageFilePath])) {
            self::$instances[$messageFilePath] = new MessageManager($messageFilePath);
            return self::$instances[$messageFilePath];
        }

        return self::$instances[$messageFilePath];
    }

    /**
     * @var string コンテンツ
     */
    private $contents;

    /**
     * コンストラクタ。
     */
    protected function __construct($messageFilePath) {
        $this->load($messageFilePath);
    }

    /**
     * ロードする。
     * @return array 情報
     */
    public function load($messageFilePath) {

        $filePath = $messageFilePath;

        $this->contents = include($filePath);
        return $this->contents;
    }

    /**
     * メッセージを取得する。
     * @param string $key キー
     * @param array $data メッセージパラメータ
     * @return string メッセージ
     */
    public function get($key, $data = array()) {

        $msg = $this->contents[$key];

        if ($data === null) {
            return $msg;
        }

        foreach ($data as $k => $v) {
            if (is_array($v)) {
                continue;
            }
            $msg = str_replace($k, $v, $msg);
        }

        return $msg;
    }

    /**
     * メッセージを取得する。
     * @param string $key キー
     * @param array $data メッセージパラメータ
     * @return array [id, メッセージ]
     */
    public function getMessages($key, $data = array()) {
        return ['id' => $key, 'message' => $this->get($key, $data)];
    }

}
