<?php

namespace App\Common\Process;

use App\Common\Util\CommandUtil;

/**
 * PHP非同期実行サービス。
 *
 * @author hideki.isobe
 */
class AsyncPHPExecutor {

    /**
     * @var AsyncExecutor 非同期実行クラス
     */
    private $service;

    /**
     * @var string PHP実行ファイルパス
     */
    private $phpExecPath;

    /**
     * @var string PHP設定パス
     */
    private $phpExecConfigPath;

    /**
     * コンストラクタ。
     * @param string $phpExecPath PHP実行ファイルパス
     * @param string $phpExecConfigPath PHP設定パス
     */
    public function __construct($phpExecPath, $phpExecConfigPath) {
        $this->service = new AsyncExecutor();
        $this->phpExecPath = $phpExecPath;
        $this->phpExecConfigPath = $phpExecConfigPath;
    }

    /**
     * PHPプログラムを実行する。
     * @param array $args 引数
     * @return int コマンドの実行結果
     */
    public function exec() {

        $argsStr = '';
        $argsStrSplit = '';

        $args = func_get_args();
        foreach ($args as $v) {

            // エスケープする
            $v = CommandUtil::escapeCommandLineArg($v);
            // 引数文字列を結合する
            $argsStr = $argsStr . $argsStrSplit . $v;
            $argsStrSplit = ' ';
        }

        // PHP実行パス関連を取得する
        $phpExecPath = CommandUtil::escapeCommandLineArg($this->phpExecPath);
        $phpExecConfigPath = CommandUtil::escapeCommandLineArg($this->phpExecConfigPath);

        $command = "{$phpExecPath} -c {$phpExecConfigPath} {$argsStr}";

        // PHPを実行する
        return $this->service->exec($command);
    }

}
