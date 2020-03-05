<?php

namespace App\Common\ResponseCache;

/**
 * レスポンスキャッシュ。
 */
class ResponseCache {

    /**
     * キャッシュファイルが存在しているかを判定する。
     * @param string $filePath ファイルパス
     * @param string $ifNoneMatch If-None-Matchヘッダ値
     * @param string $ifModifiedSince If-Modified-Sincehヘッダ値
     * @return array キャッシュファイルの存在有無情報
     */
    public static function existsCacheFile($filePath, $ifNoneMatch, $ifModifiedSince) {

        // ファイルの更新日時
        $lastMod = filemtime($filePath);
        $lastModHex = dechex($lastMod);

        // ファイルのサイズ
        $lastSize = filesize($filePath);
        $lastSizeHex = dechex($lastSize);

        /*
         * ファイルハッシュ
         */
        $etag = "{$lastSizeHex}-{$lastModHex}";

        if ($ifNoneMatch && $ifNoneMatch === $etag) {
            // キャッシュが存在する
            return ['exists' => true];
        }

        /*
         * 更新日時
         */
        if ($ifModifiedSince && $lastMod <= strtotime($ifModifiedSince)) {
            // キャッシュが存在する
            return ['exists' => true];
        }

        // キャッシュが存在しない
        return [
            'exists' => false,
            'eTag' => $etag,
            'lastModified' => $lastMod,
        ];
    }

}
