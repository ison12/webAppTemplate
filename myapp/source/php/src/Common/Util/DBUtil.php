<?php

namespace App\Common\Util;

/**
 * DBユーティリティ。
 */
class DBUtil {

    /**
     * クエリステートメントとパラメータを結合する。
     * @param string $statement ステートメント
     * @param array $params パラメータ配列
     * @return string クエリステートメントとパラメータを結合する
     */
    public static function combineQueryStatementAndParams($statement, $params) {

        if ($statement === null || $statement === '') {
            return $statement;
        }

        if ($params === null || count($params) <= 0) {
            return $statement;
        }

        $i = count($params) - 1;
        $pos = mb_strlen($statement);

        do {
            $pos--;
            $pos = mb_strrpos($statement, '?', -(mb_strlen($statement) - $pos));
            if ($pos === false) {
                break;
            }

            $statement = PHPUtil::mb_substr_replace($statement, "'" . str_replace("'", "''", $params[$i]) . "'", $pos, 1);
            $i--;
        } while ($i >= 0);

        return $statement;
    }

}
