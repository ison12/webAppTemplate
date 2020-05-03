<?php

namespace Tests\App\Common\Process;

use App\Common\Process\AsyncExecutor;
use Tests\Common\BaseTest;

/**
 * 非同期実行サービス。
 *
 * @author hideki.isobe
 */
class AsyncExecutorTest extends BaseTest {

    /**
     * 共通処理。
     */
    public function setUp() {

    }

    /**
     * テスト内容：execメソッド
     */
    public function testExec() {

        $target = new AsyncExecutor();
        $exitCode = $target->exec('echo Hello');

        $this->assertEquals(0, $exitCode);
    }

}
