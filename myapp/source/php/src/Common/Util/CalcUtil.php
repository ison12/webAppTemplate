<?php

namespace App\Common\Util;

/**
 * 計算ユーティリティ。
 */
class CalcUtil {

    /**
     * 1ptあたりのミリメートル
     */
    const MM_PER_PT = 0.352778;

    /**
     * ポイントの基準インチ
     */
    const PT_INCH = 72;

    /**
     * ブラウザの論理DPI
     */
    const BROWSER_DPI = 96;

    /**
     * ミリメートルをポイントに変換する。
     * @param float $mm ミリメートル
     * @return float ポイント
     */
    public static function convertMmToPt($mm) {

        // mmをptに変換
        $pt = $mm / self::MM_PER_PT;

        // 四捨五入して小数点4桁まで保持（1.1234）
        $pt = round($pt, 4, PHP_ROUND_HALF_UP);

        return $pt;
    }

    /**
     * ポイントをミリメートに変換する。
     * @param float $pt ポイント
     * @return float ミリメートル
     */
    public static function convertPtToMm($pt) {

        // ptをmmに変換
        $mm = $pt * self::MM_PER_PT;

        // 四捨五入して小数点4桁まで保持（1.1234）
        $mm = round($mm, 4, PHP_ROUND_HALF_UP);

        return $mm;
    }

    /**
     * ポイントをピクセルに変換する。
     * @param float $pt ポイント
     * @param int $dpi 解像度
     * @return float ピクセル
     */
    public static function convertPtToPixel($pt, $dpi = self::BROWSER_DPI) {

        // ptをpxに変換
        $px = $pt / self::PT_INCH * $dpi;

        // 四捨五入して小数点4桁まで保持（1.1234）
        $px = round($px, 4, PHP_ROUND_HALF_UP);

        return $px;
    }

    /**
     * ピクセルをポイントに変換する。
     * @param float $px ピクセル
     * @param int $dpi 解像度
     * @return float ポイント
     */
    public static function convertPixelToPt($px, $dpi = self::BROWSER_DPI) {

        // pxをptに変換
        $pt = $px / $dpi * self::PT_INCH;

        // 四捨五入して小数点4桁まで保持（1.1234）
        $pt = round($pt, 4, PHP_ROUND_HALF_UP);

        return $pt;
    }

    /**
     * ポイントをピクセルに変換する。
     * @param float $mm ポイント
     * @param int $dpi 解像度
     * @return float ピクセル
     */
    public static function convertMmToPixel($mm, $dpi = self::BROWSER_DPI) {

        // mmをptに変換
        $pt = $mm / self::MM_PER_PT;
        // ptをpxに変換
        $px = $pt / self::PT_INCH * $dpi;

        // 四捨五入して小数点4桁まで保持（1.1234）
        $px = round($px, 4, PHP_ROUND_HALF_UP);

        return $px;
    }

    /**
     * ピクセルをポイントに変換する。
     * @param float $px ピクセル
     * @param int $dpi 解像度
     * @return float ポイント
     */
    public static function convertPixelToMm($px, $dpi = self::BROWSER_DPI) {

        // pxをptに変換
        $pt = $px / $dpi * self::PT_INCH;
        // ptをmmに変換
        $mm = $pt * self::MM_PER_PT;

        // 四捨五入して小数点4桁まで保持（1.1234）
        $mm = round($mm, 4, PHP_ROUND_HALF_UP);

        return $mm;
    }

}
