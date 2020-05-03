<?php

namespace Tests\App\Common\Command;

use App\Common\App\AppContext;
use App\Common\Command\CommandExecutor;
use App\Common\Util\FileUtil;
use Tests\Common\BaseTest;

/**
 * コマンド実行。
 *
 * @author hideki.isobe
 */
class CommandExecutorTest extends BaseTest {

    /**
     * 共通処理。
     */
    public function setUp() {

    }

    /**
     * テスト内容：execCommandメソッドのphp.iniファイルが見つからないテスト。
     */
    public function testExecCommandIniFileNotFound() {

        $app = AppContext::get();
        $appContainer = $app->getContainer();

        $commandSettings = $appContainer->get('config')['command'];

        $outFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'CommandExecutorTest.txt';
        $outFileContent = 'あいうえおテストですよ';

        $parms = [
            'outFilePath' => $outFilePath,
            'fileContent' => $outFileContent
        ];

        try {

            CommandExecutor::execCommand(
                    $commandSettings['phpExecPath']
                    , 'c:/aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/aaaaa.ini'
                    , 'Tests\App\Common\Command\TestCommand'
                    , $parms
                    , false /* 同期 */);
            $this->assertTrue(false);
        } catch (\App\Common\Exception\FileException $exc) {
            $this->assertTrue(true);
        }
    }

    /**
     * テスト内容：execCommandメソッド（同期）のテスト。
     */
    public function testExecCommandSync() {

        $app = AppContext::get();
        $appContainer = $app->getContainer();

        $commandSettings = $appContainer->get('config')['command'];

        $outFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'CommandExecutorTest.txt';
        $outFileContent = 'あいうえおテストですよ';

        $parms = [
            'outFilePath' => $outFilePath,
            'fileContent' => $outFileContent
        ];

        $exitCode = CommandExecutor::execCommand(
                        $commandSettings['phpExecPath']
                        , $commandSettings['phpExecConfigPath']
                        , 'Tests\App\Common\Command\TestCommand'
                        , $parms
                        , false /* 同期 */);

        $content = FileUtil::readFile($outFilePath);

        $this->assertEquals(0, $exitCode);
        $this->assertEquals($outFileContent, $content);

        FileUtil::delete($outFilePath);
    }

    /**
     * テスト内容：execCommandメソッド（非同期）のテスト。
     */
    public function testExecCommandAsync() {

        $app = AppContext::get();
        $appContainer = $app->getContainer();

        $commandSettings = $appContainer->get('config')['command'];

        $outFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'CommandExecutorTest.txt';
        $outFileContent = 'あいうえおテストですよ';

        $parms = [
            'outFilePath' => $outFilePath,
            'fileContent' => $outFileContent
        ];

        $exitCode = CommandExecutor::execCommand(
                        $commandSettings['phpExecPath']
                        , $commandSettings['phpExecConfigPath']
                        , 'Tests\App\Common\Command\TestCommand'
                        , $parms
                        , true /* 非同期 */);

        sleep(1);

        $content = FileUtil::readFile($outFilePath);

        $this->assertEquals(0, $exitCode);
        $this->assertEquals($outFileContent, $content);

        FileUtil::delete($outFilePath);
    }

}
