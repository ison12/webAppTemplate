<?php

namespace App\Common\Util;

/**
 * 配列ユーティリティ。
 */
class ArrayUtil {

    /**
     * クロージャーによって、マップを生成する。
     * @param array $data データ配列
     * @param string $callback コールバック関数
     * @return array マップ配列
     */
    public static function convertToMap($data, $callback) {

        $ret = array();

        foreach ($data as $d) {
            $ret[(string) $callback($d)] = $d;
        }

        return $ret;
    }

    /**
     * クロージャーによって、マップを生成する。
     * データ配列から要素を取得する際に参照取得する。
     * @param array $data データ配列
     * @param string $callback コールバック関数
     * @return array マップ配列
     */
    public static function convertToReferElementMap($data, $callback) {

        $ret = array();

        foreach ($data as &$d) {
            $ret[(string) $callback($d)] = $d;
        }

        return $ret;
    }

    /**
     * 連想配列のキーによって、マップを生成する。
     * @param array $data データ配列
     * @param string $key キー
     * @return array マップ配列
     */
    public static function convertToMapByArrayKey($data, $key) {

        $ret = array();

        foreach ($data as $d) {
            $ret[($d[$key])] = $d;
        }

        return $ret;
    }

    /**
     * オブジェクトのフィールドによって、マップを生成する。
     * @param array $data データ配列
     * @param string $key キー
     * @return array マップ配列
     */
    public static function convertToMapByObjField($data, $key) {

        $ret = array();

        foreach ($data as $d) {
            $ret[($d->$key)] = $d;
        }

        return $ret;
    }

    /**
     * オブジェクトのメソッドによって、マップを生成する。
     * @param array $data データ配列
     * @param string $key キー
     * @return array マップ配列
     */
    public static function convertToMapByObjMethod($data, $key) {

        $ret = array();

        foreach ($data as $d) {

            $arrayKey = $d->$key();
            $ret[$arrayKey] = $d;
        }

        return $ret;
    }

    /**
     * 配列を文字列に変換する。
     * @param array $data データ
     * @param string $separate 区切り文字
     * @param callable $callback コールバック関数
     * @return string 文字列表現
     */
    public static function toString($data, $separate, $callback) {

        if (!is_array($data) && !is_object($data)) {
            return (string) $data;
        }

        $ret = array();

        foreach ($data as $k => $v) {
            $ret[] = $callback($k, $v);
        }

        return join($separate, $ret);
    }

    /**
     * 配列を文字列に変換する。
     * @param array $data データ
     * @return string 文字列表現
     */
    public static function toFlatString($data) {

        if (!is_array($data)) {
            return (string) $data;
        }

        $ret = self::toString($data, ', ', function ($k, $v) {
                    return '' . $k . ' => ' . $v;
                });

        return $ret;
    }

    /**
     * 2次元配列を文字列に変換する。
     * @param array $data データ
     * @return string 文字列表現
     */
    public static function toFlatStringFor2Dim($data) {

        if (!is_array($data)) {
            return (string) $data;
        }

        $ret = array();

        foreach ($data as $dataChild) {
            $ret[] = self::toFlatString($dataChild);
        }

        return join(' : ', $ret);
    }

    /**
     * 配列の次元数を取得する。
     * @param array $ary 配列
     * @param int $cnt 次元数
     * @return int 次元数
     */
    public static function depth(array $ary, $cnt = 0) {

        if (!is_array($ary)) {
            return $cnt;
        } else {
            $cnt++;
        }

        $max = $cnt;
        $i = 0;

        foreach ($ary as $v) {
            if (is_array($v)) {
                $i = self::depth($v, $cnt);
                if ($max < $i) {
                    $max = $i;
                }
            }
        }

        return $max;
    }

    /**
     * レコードリストの内容を文字列フォーマットする・
     * @param array $records レコードリスト
     * @param string $separate 区切り文字
     * @return string 文字列フォーマット
     */
    public static function formatStringFromRecords($records, $separate = ' / ') {

        // カラム別に最大長を取得する
        $columnsLength = array();

        foreach ($records as $record) {
            foreach ($record as $colName => $column) {
                // 文字列長を取得
                $valLen = max(mb_strlen($colName), mb_strlen($column));

                if (isset($columnsLength[$colName])) {
                    // 該当カラムで設定済みの場合は、文字列長を比較し、大きければ設定する
                    if ($columnsLength[$colName] < $valLen) {
                        $columnsLength[$colName] = $valLen;
                    }
                } else {
                    // 初期化
                    $columnsLength[$colName] = $valLen;
                }
            }
        }

        return ArrayUtil::toString($records, PHP_EOL, function($i, $record) use($columnsLength, $separate) {

                    $recStr = ArrayUtil::toString($record, $separate, function($colName, $colVal) use($columnsLength) {
                                $val = 'NULL';
                                if ($colVal !== null) {
                                    $val = $colVal;
                                }

                                $padLen = 0;
                                if (isset($columnsLength[$colName])) {
                                    $padLen = $columnsLength[$colName];
                                }
                                $val = str_pad($val, $padLen, " ");
                                return $val;
                            });
                    if ($i <= 0) {
                        $cols = array();
                        $totalLength = 0;
                        foreach ($record as $k => $v) {
                            $padLen = 0;
                            if (isset($columnsLength[$k])) {
                                $padLen = $columnsLength[$k];
                            }
                            $col = str_pad($k, $padLen, " ");
                            $cols[] = $col;
                            $totalLength += $padLen + 3;
                        }
                        $recStr = join($separate, $cols)
                                . PHP_EOL
                                . str_repeat('-', $totalLength)
                                . PHP_EOL
                                . $recStr;
                    }
                    return $recStr;
                });
    }

    /**
     * 配列の最初の要素を取得する。
     * @param array $arr 配列
     * @return mixed 配列の最初の要素
     */
    public static function getFirstElement($arr) {
        $a = array_values($arr);
        return $a[0];
    }

    /**
     * 配列のソートを再帰的に実施する。
     * ソート順は、連想配列の場合は辞書順で、添え字の場合は数値順とする。
     *
     * @param array $arr 配列
     */
    public static function sortIndexRecursive(&$arr) {

        $isKey = false;
        if (count($arr) > 0) {
            foreach ($arr as $key => $value) {
                if (is_int($key)) {
                    $isKey = false;
                } else {
                    $isKey = true;
                }
            }
        }

        foreach ($arr as &$value) {

            if (is_array($value)) {
                self::sortIndexRecursive($value);
            }
        }

        if ($isKey === true) {
            ksort($arr);
        } else {
            $arr = array_values($arr);
        }
    }

    /**
     * インデックスで配列の内容を取得する。
     * 連想配列の場合に数値添字でアクセスしたい場合に使用する。
     * @param array $a 配列
     * @param int $index インデックス
     * @return mixed 取得した値
     */
    public static function getByIndex($a, $index) {
        $ret = current(array_slice($a, $index, 1));
        if ($ret === false) {
            return null;
        }

        return $ret;
    }

}
