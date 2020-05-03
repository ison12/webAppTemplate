<?php

namespace App\Common\Util;

/**
 * 値ユーティリティ。
 */
class ValUtil {

    /**
     * 値が空かどうかを確認する。
     * @param   mixed $val 値
     * @return  bool true 値が空、false 値が設定されている
     */
    public static function isEmpty($val): bool {
        return ($val === false or $val === null or $val === '' or $val === array());
    }

    /**
     * 値が空かどうかを確認する。
     * @param   array $params パラメータ
     * @param   string $key キー
     * @return  bool true 値が空、false 値が設定されている
     */
    public static function isEmptyElementOfArray(array $params, string $key): bool {

        if (!isset($params[$key])) {
            return true;
        }

        return self::isEmpty($params[$key]);
    }

    /**
     * 値が空かどうかを確認する。
     * @param   array $params パラメータ
     * @return  bool true 値が空、false 値が設定されている
     */
    public static function isEmptyArray($params): bool {

        if (null == $params || !isset($params) || count($params) == 0) {
            return true;
        }

        return self::isEmpty($params);
    }

    /**
     * キャメルケースをケバブケースに変換する。
     * 例）camelCase → camel-case
     * @param string $val 値
     * @return string 変換結果
     */
    public static function convertCamelToKebab(string $val): string {

        $ret = $val;

        // 英大文字を見つけた場合に、直前にハイフンを挿入する
        $ret = preg_replace('/([A-Z])/', '-$1', $ret);
        // 全体を小文字に変換
        $ret = strtolower($ret);
        // 先頭の余分なハイフンを取り除く
        $ret = ltrim($ret, '-');

        return $ret;
    }

    /**
     * キャメルケースをスネークケースに変換する。
     * 例）camelCase → camel_case
     * @param string $val 値
     * @return string 変換結果
     */
    public static function convertCamelToSnake(string $val): string {

        $ret = $val;

        // 英大文字を見つけた場合に、直前にアンダースコアを挿入する
        $ret = preg_replace('/([A-Z])/', '_$1', $ret);
        // 全体を小文字に変換
        $ret = strtolower($ret);
        // 先頭の余分なハイフンを取り除く
        $ret = ltrim($ret, '_');

        return $ret;
    }

    /**
     * 文字列の真偽値を、bool型の真偽値に変換する。
     * @param mixed $val 値
     * @return boolean 真偽値
     */
    public static function convertToBool($val): bool {

        if (
                $val === true ||
                $val === 'true') {
            return true;
        }

        return false;
    }

    /**
     * nullの場合、空文字に変換する。
     * @param string $val 値
     * @return string 変換値
     */
    public static function convertNullToEmpty(string $val): string {

        if (self::isEmpty($val)) {
            return '';
        }

        return $val;
    }

    /**
     * nullの場合、0に変換する。
     * @param int $val 値
     * @return int 変換値
     */
    public static function convertNullToZero(?int $val): int {

        if (self::isEmpty($val)) {
            return 0;
        }

        return $val;
    }

    /**
     * 文字列（数値）からカンマを取り除く。
     * @param string $val 値
     * @return string 変換値
     */
    public static function omitCammaForNum(string $val): string {

        if (self::isEmpty($val)) {
            return '0';
        }

        return str_replace(",", "", $val);
    }

    /**
     * 結合値の先頭に任意のキーワードを付与する。
     *
     * $value値が設定済みの場合、以下のように変換する。
     *   $value . $concatKeyword . $concatValue
     * 未設定の場合、以下のように変換する。
     *   $value . $concatValue
     *
     * @param mixed $value 値
     * @param string $concatKeyword キーワード
     * @param string $concatValue 結合値
     */
    public static function prependConcat($value, string $concatKeyword, string $concatValue = '') {

        if (!self::isEmpty($value)) {
            return $value . $concatKeyword . $concatValue;
        }

        return $value . $concatValue;
    }

    /**
     * 値の先頭に任意のキーワードを付与する。
     *
     * $value値が設定済みの場合、以下のように変換する。
     *   $headKeyword . $value
     * 未設定の場合、以下のように変換する。
     *   $value
     *
     * @param mixed $value 値
     * @param string $headKeyword キーワード
     */
    public static function prependHead($value, string $headKeyword) {

        if (!self::isEmpty($value)) {
            return $headKeyword . $value;
        }

        return $value;
    }

    /**
     * 区切り文字で連結した文字列を取得
     * @param array $params 連結対象のパラメータリスト
     * @param bool $isQuote シングルクォーテーション付与フラグ(true:付与|false:付与しない)
     * @param string $sep 区切り文字
     * @return string 区切り文字で連結した文字列
     */
    public static function getConcatBySeparate(array $params, bool $isQuote = false, string $sep = ','): string {

        $result = '';

        $max = count($params);
        for ($i = 0; $i < $max; $i++) {
            if ($isQuote) {
                $result .= sprintf('\'%s\'', $params[$i]);
            } else {
                $result .= $params[$i];
            }
            if ($i < ($max - 1)) {
                $result .= $sep;
            }
        }
        return $result;
    }

}
