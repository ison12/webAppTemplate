<?php

namespace App\Common\Session;

use App\Common\Data\User;

/**
 * セッションデータ。
 */
class SessionData {

    /**
     * ユーザーオブジェクトを取得する。
     * @return UserData ユーザーオブジェクト
     */
    public static function getUser(): ?User {
        return $_SESSION[SessionData::class . '_USER'] ?? null;
    }

    /**
     * ユーザーオブジェクトを設定する。
     * @param User $user ユーザーオブジェクト
     */
    public static function setUser(?User $user) {
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
