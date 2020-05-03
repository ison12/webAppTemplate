<?php

namespace App\Common\Command;

use App\Common\Exception\FileException;
use App\Common\Process\AsyncPHPExecutor;
use App\Common\Util\FileUtil;
use App\Common\Util\JsonUtil;
use const SRC_PATH;

/**
 * コマンド実行。
 *
 * @author hideki.isobe
 */
class CommandExecutor {

    /**
     * プロセス実行する。
     * @param string $phpExecPath PHP実行ファイルパス
     * @param string $phpExecConfigPath PHP設定パス
     * @param string $commandClass コマンドクラス
     * @param array $params パラメータリスト
     * @param boolean $isAsync 非同期有無
     * @return int コマンドの実行結果
     */
    public static function execCommand($phpExecPath, $phpExecConfigPath, $commandClass, $params = array(), $isAsync = true) {

        if (!FileUtil::existsFile($phpExecConfigPath)) {
            // php.iniファイルが見つからない場合（phpコマンド実行時にエラーにならずに危険なので明示的にチェックする）
            throw new FileException("php.ini file not found. filePath={$phpExecConfigPath}");
        }

        $baseParams = [
            'LOCAL_ADDR' => $_SERVER['LOCAL_ADDR'] ?? null
            , 'HTTPS' => $_SERVER['HTTPS'] ?? null
            , 'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? null
            , 'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? null
            , 'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? null
            , 'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null
        ];

        // コマンドパス
        $commandPath = SRC_PATH . 'Common' . DIRECTORY_SEPARATOR . 'Command' . DIRECTORY_SEPARATOR . 'Command.php';
        // コマンドパラメータ
        $commandParams = JsonUtil::encode(array_merge($baseParams, $params));

        if ($isAsync) {
            // コマンドパラメータをファイルに書き込む
            $commandArgsFilePath = FileUtil::realpath(FileUtil::createTempFile(SRC_PATH . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'command'));
            FileUtil::writeFile($commandArgsFilePath, $commandParams);
            // 非同期で処理を実行する
            $asyncExec = new AsyncPHPExecutor($phpExecPath, $phpExecConfigPath);
            return $asyncExec->exec($commandPath, $commandClass, $commandArgsFilePath);
        } else {
            // 同期実行
            $command = new $commandClass();
            return $command->exec(JsonUtil::decode($commandParams, true));
        }
    }

}
