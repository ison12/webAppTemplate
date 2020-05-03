<?php

namespace App\Common\Shutdown;

/**
 * シャットダウン時の監視クラス。
 */
class ShutdownObserver {

    /**
     * @var array  シャットダウン関数リスト
     */
    private array $shutdownFunctions;

    /**
     * コンストラクタ。
     */
    public function __construct() {

        $this->shutdownFunctions = [];
    }

    /**
     * シャットダウン時のコールバック関数を登録する。
     * @return callable $onShutdown シャットダウン時の関数
     */
    public function add(callable $onShutdown) {

        $this->shutdownFunctions[] = $onShutdown;
    }

    /**
     * シャットダウン時のコールバック関数を全てクリアする。
     */
    public function clear() {

        $this->shutdownFunctions = [];
    }

    /**
     * シャットダウン関数の反復処理が可能な関数リストを返却する。
     */
    public function each() {

        foreach (array_reverse($this->shutdownFunctions) as $shutdownFunction) {
            yield $shutdownFunction;
        }
    }

}
