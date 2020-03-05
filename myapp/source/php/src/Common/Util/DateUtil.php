<?php

namespace App\Common\Util;

use DateTime;

/**
 * 日付ユーティリティ。
 */
class DateUtil {

    const DATETIME_FORMAT_COMMON = 'Y/m/d H:i:s';
    const DATETIME_MICRO_FORMAT_COMMON = 'Y/m/d H:i:s.u';
    const DATETIME_HYPHEN_FORMAT_COMMON = 'Y-m-d H:i:s';
    const DATETIME_NOSEPARATE_FORMAT_COMMON = 'YmdHis';
    const DATETIME_HYPHEN_MICRO_FORMAT_COMMON = 'Y-m-d H:i:s.u';
    const DATETIME_YMD_HYPHEN_FORMAT_COMMON = 'Y-m-d';
    const DATETIME_YMD_FORMAT_COMMON = 'Y/m/d';
    const DATETIME_MD_MICRO_FORMAT_COMMON = 'm/d H:i:s.u';
    const DATETIME_MD_FORMAT_COMMON = 'm/d H:i:s';
    const DATETIME_MD_HYPHEN_FORMAT_COMMON = 'm-d H:i:s';
    const DATETIME_MD_HYPHEN_MICRO_FORMAT_COMMON = 'm-d H:i:s.u';
    const DATETIME_UNIX_TIMESTAMP = 'U.u';
    const TIME_MILLI_FORMAT_COMMON = 'H:i:s.u';
    const TIME_MICRO_FORMAT_COMMON = 'H:i:s.u';

    /**
     * @var DateTime 固定化システム日付
     */
    private static $fixedDateTime = null;

    /**
     * システム日付を固定化する。
     * @param DateTime $t 固定化する日付
     */
    public static function setFixedDateTime(DateTime $t) {
        self::$fixedDateTime = $t;
    }

    /**
     * 日付を取得する。
     * @param float $t マイクロ秒
     * @return DateTime 日付オブジェクト
     */
    public static function getDate(int $t): DateTime {

        // マイクロ秒を取得する
        $micro = sprintf('%06d', ($t - floor($t)) * 1000000);

        // DateTimeオブジェクトに変換する
        return DateTime::createFromFormat(self::DATETIME_MICRO_FORMAT_COMMON
                        , date(self::DATETIME_FORMAT_COMMON, $t) . '.' . $micro);
    }

    /**
     * 現在日付を取得する。
     * @return DateTime 日付オブジェクト
     */
    public static function getSystemDate(): DateTime {

        if (self::$fixedDateTime !== null) {
            return self::$fixedDateTime;
        }

        // DateTimeオブジェクトの通常の生成ではマイクロ秒を取得できないため
        // microtime経由でDateTimeオブジェクトを生成する
        // マイクロ秒を取得する
        $t = microtime(true);
        // マイクロ秒を取得する
        $micro = sprintf('%06d', ($t - floor($t)) * 1000000);

        // DateTimeオブジェクトに変換する
        return DateTime::createFromFormat(self::DATETIME_MICRO_FORMAT_COMMON
                        , date(self::DATETIME_FORMAT_COMMON, $t) . '.' . $micro);
    }

    /**
     * 時間文字列からDateTimeオブジェクトを生成する。
     * @param string $time 時間
     * @param array $format フォーマット
     * @return DateTime DateTimeオブジェクト
     */
    public static function createDateTime(
            ?string $time
            , $format = [
                self::DATETIME_MICRO_FORMAT_COMMON,
                self::DATETIME_HYPHEN_MICRO_FORMAT_COMMON,
                self::DATETIME_FORMAT_COMMON,
                self::DATETIME_HYPHEN_FORMAT_COMMON,
                self::DATETIME_YMD_FORMAT_COMMON,
                self::DATETIME_YMD_HYPHEN_FORMAT_COMMON,
            ]
    ) {

        if (is_array($format)) {

            foreach ($format as $f) {
                $ret = DateTime::createFromFormat($f, $time);
                if ($ret !== false) {
                    return $ret;
                }
            }

            return null;
        } else {

            $ret = DateTime::createFromFormat($format, $time);

            if ($ret !== false) {
                return $ret;
            }

            return null;
        }
    }

    /**
     * 経過時間を取得する。
     * @param DateTime $d1 日付1
     * @param DateTime $d2 日付2
     * @return string 経過時間文字列
     */
    public static function getElapsedHmTime(DateTime $d1, DateTime $d2, string $format): string {

        if ($d1 === null || $d2 === null) {
            return null;
        }

        $interval = $d2->diff($d1);

        $d = (int) $interval->format('%d') * 24;
        $h = (int) $interval->format('%h');
        $i = (int) $interval->format('%i');

        $ret = str_replace(array('h', 'i'), array($d + $h, $i), $format);

        return $ret;
    }

    /**
     * 指定経過日付を取得する。
     * @param mixed    $baseDate 日付
     * @param DateTime $days 経過日数(+-)
     * @param string フォーマット
     * @return string N日後経過日付
     */
    public static function getSpacifiedElapsedDate($baseDate, int $days, string $format): string {

        if (ValUtil::isEmpty($baseDate) || $days === 0) {
            return null;
        }

        // 文字列に変換
        $strDays = strval($days) . " day";

        return date($format, strtotime($strDays, strtotime($baseDate)));
    }

    /**
     * 秒数から時分秒を算出する。
     * @param float $val 秒数
     * @param int $hours 時
     * @param int $minutes 分
     * @param int $seconds 秒
     */
    public static function convertSecondsToHms(float $val, int &$hours, int &$minutes, int &$seconds) {

        $hours = floor($val / 3600);
        $minutes = floor(($val / 60) % 60);
        $seconds = $val % 60;
    }

    /**
     * 時間部分を最大値に変換した日時を取得する。
     * @param \DateTime $dateTime 日時
     * @return \DateTime 日時
     */
    public static function GetStartOfTimeHms(\DateTime $dateTime = null) {

        if ($dateTime === null) {
            return null;
        }

        $ymdStr = $dateTime->format('Y-m-d');

        return \DateTime::createFromFormat('Y-m-d H:i:s.u', $ymdStr . ' 00:00:00.000000');
    }

    /**
     * 時間部分を最大値に変換した日時を取得する。
     * @param \DateTime $dateTime 日時
     * @return \DateTime 日時
     */
    public static function GetEndOfTimeHms(\DateTime $dateTime = null) {

        if ($dateTime === null) {
            return null;
        }

        $ymdStr = $dateTime->format('Y-m-d');

        return \DateTime::createFromFormat('Y-m-d H:i:s.u', $ymdStr . ' 23:59:59.999999');
    }

}
