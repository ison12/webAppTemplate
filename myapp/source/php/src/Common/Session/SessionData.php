<?php

namespace App\Common\Session;

use App\Common\Data\User;

/**
 * セッションデータ。
 */
class SessionData {

    /**
     * ユーザーオブジェクトを取得する。
     * @return User ユーザーオブジェクト
     */
    public static function getUser() {
        return $_SESSION[SessionData::class . '_USER'];
    }

    /**
     * ユーザーオブジェクトを設定する。
     * @param User $user ユーザーオブジェクト
     */
    public static function setUser($user) {
        $_SESSION[SessionData::class . '_USER'] = $user;
    }

    /**
     * データを設定する。
     * @param string $key キー
     * @param mixed $val 値
     */
    public static function setData(string $key, $val) {
        $_SESSION[$key] = $val;
    }

    /**
     * データを取得する。
     * @param string $key キー
     * @param mixed $val 値
     */
    public static function getData(string $key) {
        return $_SESSION[$key] ?? null;
    }

}
