<?php

namespace App\Common\Util;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use Throwable;
use const PUBLIC_PATH;

/**
 * ビューユーティリティ。
 */
class ViewUtil {

    /**
     * ファイルタイムスタンプ付きのURLを取得する。
     * @param string $url URL
     * @param string $publicPath 公開先ディレクトリのルートファイルパス
     * @return string ファイルタイムスタンプ付きのURL
     */
    public static function getUrlWithFileTimestamp($url, $publicPath = null): string {

        if ($publicPath === null) {
            $publicPath = PUBLIC_PATH;
        }

        $filePath = $publicPath . ltrim($url, '/');
        $filemtime = filemtime($filePath);
        return $url . '?' . $filemtime;
    }

    /**
     * Renders a template and returns the result as a string
     *
     * cannot contain template as a key
     *
     * throws RuntimeException if $templatePath . $template does not exist
     *
     * @param $templatePath
     * @param array $data
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function render($templatePath, array $data = []) {

        try {
            ob_start();
            self::protectedIncludeScope($templatePath, $data);
            $output = ob_get_clean();
        } catch (Throwable $e) { // PHP 7+
            ob_end_clean();
            throw $e;
        } catch (Exception $e) { // PHP < 7
            ob_end_clean();
            throw $e;
        }

        return $output;
    }

    /**
     * ファイルをインクルードする。
     * @param string $template テンプレートファイルパス
     * @param array $data テンプレートに渡すデータ情報
     */
    protected static function protectedIncludeScope($template, array $data) {
        extract($data);
        include $template;
    }

}
