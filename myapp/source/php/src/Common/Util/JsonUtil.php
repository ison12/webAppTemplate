<?php

namespace App\Common\Util;

use Exception;

/**
 * Jsonユーティリティ。
 *
 * @author hideki.isobe
 */
class JsonUtil {

    /**
     * エンコード処理。
     * @param mixed $value エンコードする値。 リソース 型以外の任意の型を指定できます。
     * @param int $options JSON_FORCE_OBJECT, JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_NUMERIC_CHECK, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_PRESERVE_ZERO_FRACTION, JSON_PRETTY_PRINT, JSON_UNESCAPED_LINE_TERMINATORS, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE, JSON_THROW_ON_ERROR からなるビットマスク。
     * @param int $depth 最大の深さを設定します。正の数でなければいけません。
     * @return type JSON エンコードされた文字列を返します。
     * @throws Exception エンコード失敗時
     */
    public static function encode($value, int $options = 0, int $depth = 512) {

        $ret = json_encode($value, $options, $depth);
        $lastError = \json_last_error();
        $lastErrorMsg = \json_last_error_msg();

        if ($lastError !== JSON_ERROR_NONE) {

            throw new Exception("JsonUtil#encode failed. errorCode={$lastError}, errorMessage={$lastErrorMsg}", $lastError);
        }

        return $ret;
    }

    /**
     * デコード処理。
     * @param string $json デコード対象となる json 文字列。
     * @param bool $assoc TRUE の場合、返されるオブジェクトは連想配列形式になります。
     * @param int $depth ユーザー指定の再帰の深さ。
     * @param int $options JSON_BIGINT_AS_STRING, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_OBJECT_AS_ARRAY, JSON_THROW_ON_ERROR. からなるビットマスク。
     * @return mixed json でエンコードされたデータを、適切な PHP の型として返します。 true、false および null はそれぞれ TRUE、FALSE そして NULL として返されます。
     * @throws Exception デコード失敗時
     */
    public static function decode(string $json, bool $assoc = FALSE, int $depth = 512, int $options = 0) {

        $ret = json_decode($json, $assoc, $depth, $options);
        $lastError = \json_last_error();
        $lastErrorMsg = \json_last_error_msg();

        if ($lastError !== JSON_ERROR_NONE) {

            throw new Exception("JsonUtil#decode failed. errorCode={$lastError}, errorMessage={$lastErrorMsg}", $lastError);
        }

        return $ret;
    }

}
