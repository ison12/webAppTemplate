<?php

namespace App\Common\Util;

use Psr\Http\Message\UriInterface;

/**
 * URLユーティリティ。
 */
class UrlUtil {

    /**
     * ルートURLを生成する。
     * @param UriInterface $uri URI
     * @param string $basePath ベースパス
     * @return string ルートURL
     */
    public static function createRootUrlWithBase(UriInterface $uri, string $basePath): string {

        $port = '';
        if ($uri->getPort()) {
            $port = ':' . $uri->getPort();
        }

        $loginUrl = "{$uri->getScheme()}://{$uri->getHost()}{$port}{$basePath}";
        return $loginUrl;
    }

}
