<?php

namespace App\Common\Util;

/**
 * リトライユーティリティ。
 */
class RetryUtil {

    // リトライインターバール（マイクロ秒）
    const RETRY_INTERVAL = 1000000;
    // リトライ回数
    const RETRY_COUNT = 10;

    /**
     * 繰り返し実行処理。
     * @param function $callback コールバック
     * @param int $retryInterval リトライ
     * @param int $retryCount リトライ回数
     * @return boolean true 正常、false 異常
     */
    public static function retryProcess($callback
    , $retryInterval = self::RETRY_INTERVAL
    , $retryCount = self::RETRY_COUNT) {

        // 失敗回数
        $failedCount = -1;

        do {
            $ret = true;

            // 失敗回数を加算
            $failedCount++;
            // リトライ回数を超過した為、例外を発行する
            if ($failedCount >= $retryCount) {
                // 失敗し続けたのでエラーとする
                return false;
            }

            // 失敗しているので間隔をあける
            if ($failedCount > 0) {
                usleep($retryInterval);
            }

            // 任意の処理
            $ret = $callback();
        } while ($ret === false);

        // ループを脱出できたので正常とする
        return true;
    }

}
