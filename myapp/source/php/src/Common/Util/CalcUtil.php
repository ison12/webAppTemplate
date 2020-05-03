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

    /**
     * 任意の座標に対して、任意の角度で回転した後の座標を算出する。
     * 中心位置の座標を(0,0)とした場合の、(x,y)座標を与えること。
     * @param float x X
     * @param float y Y
     * @param float angle 角度
     * @returns array 座標情報
     */
    public static function calcRotatePosition($x, $y, $angle) {

        $angleRadians = deg2rad($angle);
        $calcX = ($x * cos($angleRadians)) - ($y * sin($angleRadians));
        $calcY = ($x * sin($angleRadians)) + ($y * cos($angleRadians));

        return [
            'x' => $calcX,
            'y' => $calcY
        ];
    }

    /**
     * 任意の座標に対して、任意の角度で回転した後の座標を算出する。
     * 計算したい対象オブジェクトの、(x,y) と 中心位置の (cx,cy) 座標を与える。
     *
     * 例）$objが描画オブジェクトの場合
     * $x = obj.left;
     * $y = obj.top;
     * $posCenter = $obj.getCenterPoint();
     * $posWhenAngle0 = CalcUtil::calcRotateAbsolutePosition(
     *     $x,
     *     $y,
     *     $posCenter.x,
     *     $posCenter.y,
     *     $obj.angle // angleには、現在の角度をそのまま渡すことで、0度の情報を取得できる
     *     );
     *
     * @param float x 左上のX
     * @param float y 左上のY
     * @param float cx 中央のX
     * @param float cy 中央のY
     * @param float angle 角度
     * @returns array 座標情報
     */
    public static function calcRotateAbsolutePosition($x, $y, $cx, $cy, $angle) {

        $xx = $cx - $x;
        $yy = $cy - $y;

        $calc = self::calcRotatePosition($xx, $yy, $angle);

        return [
            'x' => $cx - $calc['x'],
            'y' => $cy - $calc['y']
        ];
    }

    /**
     * 回転あり矩形の範囲を取得する。
     * $x・$yは角度0の座標位置とする。
     *
     * @param float $x X
     * @param float $y Y
     * @param float $w 幅
     * @param float $h 高さ
     * @param float angle 角度
     * @returns array 座標情報
     */
    public static function calcBoundingRect($x, $y, $w, $h, $angle) {

        $angle = -$angle;

        $cx = $x + ($w / 2.0);
        $cy = $y + ($h / 2.0);

        // 回転後の4辺の座標を算出する
        $lt = self::calcRotateAbsolutePosition($x, $y, $cx, $cy, $angle);
        $rt = self::calcRotateAbsolutePosition($x + $w, $y, $cx, $cy, $angle);
        $lb = self::calcRotateAbsolutePosition($x, $y + $h, $cx, $cy, $angle);
        $rb = self::calcRotateAbsolutePosition($x + $w, $y + $h, $cx, $cy, $angle);

        // 回転後の4辺の座標から最小値と最大値を算出して矩形領域を求める
        $left = min($lt['x'], $rt['x'], $lb['x'], $rb['x']);
        $top = min($lt['y'], $rt['y'], $lb['y'], $rb['y']);
        $right = max($lt['x'], $rt['x'], $lb['x'], $rb['x']);
        $bottom = max($lt['y'], $rt['y'], $lb['y'], $rb['y']);

        // 矩形を返却する
        return [
            'x' => (float) $left,
            'y' => (float) $top,
            'w' => (float) $right - (float) $left,
            'h' => (float) $bottom - (float) $top,
        ];
    }

}
