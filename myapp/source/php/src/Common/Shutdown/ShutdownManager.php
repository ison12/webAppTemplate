<?php

namespace App\Common\Shutdown;

use Throwable;

/**
 * シャットダウン管理クラス。
 */
class ShutdownManager {

    /**
     * @var ?ShutdownManager シャットダウン管理インスタンス
     */
    private static ?ShutdownManager $instance = null;

    /**
     * インスタンスを取得する。
     * @return ShutdownManager シャットダウン管理クラス
     */
    public static function getInstance() {

        if (self::$instance === null) {
            self::$instance = new ShutdownManager();
        }

        return self::$instance;
    }

    /**
     * @var ShutdownObserver シャットダウン監視オブジェクト
     */
    private ShutdownObserver $shutdownObserver;

    /**
     * @var Throwable 最後に発生した例外
     */
    private ?Throwable $lastUncaughtException;

    /**
     * コンストラクタ。
     */
    public function __construct() {

        $this->shutdownObserver = new ShutdownObserver();
        $this->lastUncaughtException = null;
    }

    /**
     * 最後に発生したキャッチできない例外を設定。
     * @param Throwable $ex 例外
     */
    public function setLastUncaughtException(?Throwable $ex) {
        $this->lastUncaughtException = $ex;
    }

    /**
     * シャットダウン監視オブジェクトを取得する。
     * @return ShutdownObserver シャットダウン監視オブジェクト
     */
    public function getObserver(): ShutdownObserver {
        return $this->shutdownObserver;
    }

    /**
     * シャットダウン時のコールバック関数を登録する。
     * @return callable $onShutdown シャットダウン時の関数
     */
    public function regist(callable $onShutdown) {

        register_shutdown_function(function() use($onShutdown) {

            $errorData = null;

            $lastError = error_get_last();
            if ($lastError !== null && (
                    $lastError['type'] === E_ERROR ||
                    $lastError['type'] === E_PARSE ||
                    $lastError['type'] === E_CORE_ERROR ||
                    $lastError['type'] === E_COMPILE_ERROR ||
                    $lastError['type'] === E_USER_ERROR)
            ) {
                // error_get_last()の返却値を見て、 「重大なエラーなら」 処理する。
                // 重大なエラーでなければ、set_error_handler 例外変換が動くはず。
                $errorData = $lastError;
            } else {
                // 最後に発生したキャッチできなかった例外
                $errorData = $this->lastUncaughtException;
            }

            $this->execute($onShutdown, $errorData);
        });
    }

    /**
     * シャットダウン時の処理を実行する。
     * @param callable $onShutdown シャットダウン処理
     * @param mixed $errorData エラーデータ
     */
    public function execute(callable $onShutdown, $errorData) {

        $onShutdown($errorData);

        // 登録されたシャットダウン関数を順次コールする
        foreach ($this->shutdownObserver->each() as $shutdownFunction) {
            $shutdownFunction($errorData);
        }
    }

}
