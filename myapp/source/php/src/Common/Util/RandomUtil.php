<?php

namespace App\Common\Util;

/**
 * 乱数ユーティリティ。
 */
class RandomUtil {

    /**
     * ランダムな数値を取得する。
     * @param int $length 桁数
     * @return string ランダムな数値文字列
     */
    public static function generateRandomNumber($length): string {

        $ret = "";
        for ($i = 0; $i < $length; $i++) {
            $ret .= random_int(0, 9);
        }

        return $ret;
    }
}
