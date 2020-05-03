<?php

namespace Tests\App\Common\Process;

use App\Common\Process\SyncExecutor;
use Tests\Common\BaseTest;

/**
 * 同期実行サービス。
 *
 * @author hideki.isobe
 */
class SyncExecutorTest extends BaseTest {

    /**
     * 共通処理。
     */
    public function setUp() {

    }

    /**
     * テスト内容：execメソッド
     */
    public function testExec() {

        $target = new SyncExecutor();
        $exitCode = $target->exec('echo Hello');

        $this->assertEquals(0, $exitCode);
    }

}
