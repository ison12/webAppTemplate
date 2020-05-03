<?php

namespace App\Common\Process;

use App\Common\Exception\ProcessException;
use App\Common\Log\AppLogger;
use App\Common\Util\OSUtil;

/**
 * 非同期実行サービス。
 */
class AsyncExecutor {

    /**
     * @var AppLogger ロガー
     */
    private $logger;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->logger = AppLogger::get();
    }

    /**
     * 任意のコマンドを実行する。
     * @param string $command コマンド
     * @return int コマンドの実行結果
     */
    public function exec($command) {

        if (OSUtil::isWindows()) {
            // Windows OSのため、Windows系のコマンドを発行する
            return $this->execForWin($command);
        } else {
            // Windows OSではないため、Unix系のコマンドを発行する
            return $this->execForUnix($command);
        }
    }

    /**
     * Unix系の任意のコマンドを実行する。
     * @param string $command コマンド
     * @return int コマンドの実行結果
     */
    public function execForUnix($command) {

        $execCommand = 'nohup ' . $command . ' >/dev/null 2>&1 &';
        $this->logger->notice('コマンド実行（非同期）：' . $execCommand);

        $output = array();
        $returnVar = null;

        exec($execCommand, $output, $returnVar);
        return $returnVar;
    }

    /**
     * Windows系の任意のコマンドを実行する。
     * @param string $command コマンド
     * @param string $mode モード
     * @return int コマンドの実行結果
     */
    public function execForWin($command, $mode = 'r') {

        $execCommand = 'start /b cmd /c ' . '"' . $command . '"';
        $this->logger->notice('コマンド実行（非同期）：' . $execCommand);

        $fp = popen($execCommand, $mode);

        if ($fp === false) {
            throw new ProcessException('Failed to start process. ' . $execCommand);
        } else {
            pclose($fp);
        }

        return 0;
    }

}
