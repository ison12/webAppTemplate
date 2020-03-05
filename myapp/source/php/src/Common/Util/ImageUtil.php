<?php

namespace App\Common\Util;

/**
 * 配列ユーティリティ。
 */
class ImageUtil {

    /**
     * 画像リソースを取得する。
     * @param string $filePath ファイルパス
     * @return resource 画像リソース
     */
    public static function getImageResource($filePath) {

        // 画像の種類別に読み込みをする
        $imageType = exif_imagetype($filePath);
        $imageRes = null;
        if ($imageType === IMAGETYPE_GIF) {
            $imageRes = imagecreatefromgif($filePath);
        } else if ($imageType === IMAGETYPE_JPEG) {
            $imageRes = imagecreatefromjpeg($filePath);
        } else if ($imageType === IMAGETYPE_PNG) {
            $imageRes = imagecreatefrompng($filePath);
        } else if ($imageType === IMAGETYPE_BMP) {
            $imageRes = imagecreatefromwbmp($filePath);
        }

        return $imageRes;
    }

}
