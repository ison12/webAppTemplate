<?php

namespace App\Common\DB;

use App\Common\Exception\DBException;

/**
 * DBヘルパークラス。
 */
interface DBHelper {

    /**
     * DBオブジェクトを囲み文字で囲んだ状態に加工する。
     * @param string $value 値
     * @return string DBオブジェクトを囲み文字で囲んだ文字列
     */
    public function ecnloseDBObject($value): string;

    /**
     * 式を取得する。
     * @param string $op 記号
     * @param mixed $value 値
     * @param $params （戻り値）パラメータ
     * @return string 式
     */
    public function getQueryExpression($op, $value, &$params);

    /**
     * 値を取得する。
     * @param mixed $value 値
     * @param $params （戻り値）パラメータ
     * @return string 値
     */
    public function getQueryValue($value, &$params): string;

    /**
     * LIKEのエスケープ句を指定する。
     * @param string $escape エスケープ値
     * @return string LIKEのエスケープ句
     */
    public function escapeLike(string $escape = '!'): string;

    /**
     * エスケープしたLIKE値を取得する。
     * @param string $value 値
     * @param string $escape エスケープ値
     * @return string エスケープしたLIKE値
     */
    public function escapeLikeValue(string $value, string $escape = '!'): string;

    /**
     * ロックエラーかどうかを判断する。
     * @param DBException $ex 例外
     * @return bool true ロックエラー、false 正常
     */
    public function isErrorForLock(DBException $ex): bool;

    /**
     * タイムアウトエラーかどうかを判断する。
     * @param DBException $ex 例外
     * @return bool true タイムアウトエラー、false 正常
     */
    public function isErrorForTimeout(DBException $ex): bool;

    /**
     * 重複エラーかどうかを判断する。
     * @param DBException $ex 例外
     * @return bool true 重複エラー、false 正常
     */
    public function isErrorForDuplicate(DBException $ex): bool;
}
