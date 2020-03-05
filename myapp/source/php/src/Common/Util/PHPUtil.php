<?php

namespace App\Common\Util;

/**
 * PHPユーティリティクラス。
 */
class PHPUtil {

    public static function mb_substr_replace($string, $replacement, $start, $length) {
        return mb_substr($string, 0, $start) . $replacement . mb_substr($string, $start + $length);
    }

}
