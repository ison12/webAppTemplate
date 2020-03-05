<?php

namespace App\Common\Util;

/**
 * 暗号化ユーティリティ。
 */
class EncryptUtil {

    /**
     * 値をハッシュ値に変換する。
     * @param   string $val 値
     * @return  string ハッシュ
     */
    public static function hash(string $val): string {
        return password_hash($val, PASSWORD_BCRYPT);
    }

    /**
     * 生の値（ハッシュ化前）とハッシュを比較して同一性を確認する。
     * @param   array $rawValue 生の値
     * @param   string $hash ハッシュ
     * @return  bool true 一致、false 不一致
     */
    public static function equalsHash(string $rawValue, string $hash): bool {

        return password_verify($rawValue, $hash);
    }

}
