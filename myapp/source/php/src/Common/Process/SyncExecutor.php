<?php

namespace App\Common\Process;

use App\Common\Log\AppLogger;
use App\Common\Util\OSUtil;

/**
 * 同期実行サービス。
 */
class SyncExecutor {

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
     * @param string &$output 出力
     * @return int コマンドの実行結果
     */
    public function exec($command, &$output = array()) {

        if (OSUtil::isWindows()) {
            // Windows OSのため、Windows系のコマンドを発行する
            return $this->execForWin($command, $output);
        } else {
            // Windows OSではないため、Unix系のコマンドを発行する
            return $this->execForUnix($command, $output);
        }
    }

    /**
     * Unix系の任意のコマンドを実行する。
     * @param string $command コマンド
     * @param string &$output 出力
     * @return int コマンドの実行結果
     */
    public function execForUnix($command, &$output) {

        $execCommand = '' . $command . '';
        $this->logger->notice('コマンド実行（同期）：' . $execCommand);

        $output = array();
        $returnVar = null;

        exec($execCommand, $output, $returnVar);
        return $returnVar;
    }

    /**
     * Windows系の任意のコマンドを実行する。
     * @param string $command コマンド
     * @param string &$output 出力
     * @return int コマンドの実行結果
     */
    public function execForWin($command, &$output) {

        $execCommand = '' . $command . '';
        $this->logger->notice('コマンド実行（同期）：' . $execCommand);

        $output = array();
        $returnVar = null;

        exec($execCommand, $output, $returnVar);
        return $returnVar;
    }

}
