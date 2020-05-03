<?php

namespace App\Common\Util;

/**
 * OSユーティリティ。
 *
 * @author hideki.isobe
 */
class OSUtil {

    /**
     * WindowsかUnixかどうかを判定する。
     * @return boolean true Windows OSの場合、false Unix OSの場合
     */
    public static function isWindows() {
        if (PHP_OS !== 'WIN32' && PHP_OS !== 'WINNT') {
            return false;
        } else {
            return true;
        }
    }

}
