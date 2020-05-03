<?php

namespace App\Common\Util;

/**
 * コマンドユーティリティ。
 */
class CommandUtil {

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

    /**
     * コマンドラインのパラメータをエスケープする。
     * @param string $arg 引数
     * @return string コマンドラインのパラメータ
     */
    public static function escapeCommandLineArgWithVariableMark($arg) {
        if (PHP_OS !== 'WIN32' && PHP_OS !== 'WINNT') {
            // Unix系
            return escapeshellarg($arg);
        } else {
            // Windows OS
            return '"' . str_replace('%', '%%', str_replace('"', '""', $arg)) . '"';
        }
    }

    /**
     * コマンドラインのパラメータをエスケープする。
     * @param string $arg 引数
     * @return string コマンドラインのパラメータ
     */
    public static function escapeCommandLineArg($arg) {
        if (PHP_OS !== 'WIN32' && PHP_OS !== 'WINNT') {
            //Unix系
            return escapeshellarg($arg);
        } else {
            // Windows OS
            return '"' . str_replace('"', '""', $arg) . '"';
        }
    }

    /**
     * コマンドラインのパラメータをエスケープする。
     * ダブルクォートをバックスラッシュにエスケープするバージョン。
     * @param string $arg 引数
     * @return string コマンドラインのパラメータ
     */
    public static function escapeCommandLineArgForBackslash($arg) {
        return '"' . str_replace('"', '\"', $arg) . '"';
    }

}
