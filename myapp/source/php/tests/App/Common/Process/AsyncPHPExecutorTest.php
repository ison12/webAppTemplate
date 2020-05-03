<?php

namespace Tests\App\Common\Process;

use App\Common\App\AppContext;
use App\Common\Process\AsyncPHPExecutor;
use Tests\Common\BaseTest;

/**
 * PHP非同期実行サービス。
 *
 * @author hideki.isobe
 */
class AsyncPHPExecutorTest extends BaseTest {

    /**
     * 共通処理。
     */
    public function setUp() {

    }

    /**
     * テスト内容：set/get メソッドのテスト。
     */
    public function testExec() {

        $app = AppContext::get();
        $appContainer = $app->getContainer();

        $commandSettings = $appContainer->get('config')['command'];

        $target = new AsyncPHPExecutor($commandSettings['phpExecPath'], $commandSettings['phpExecConfigPath']);
        $exitCode = $target->exec("-v");

        $this->assertEquals(0, $exitCode);
    }

}
