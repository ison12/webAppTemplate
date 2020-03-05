<?php

namespace Tests\App\Common\App;

use App\Common\App\AppContext;
use Slim\App;
use Tests\Common\BaseTest;

/**
 * アプリケーションコンテキスト。
 * テストクラス。
 *
 * 
 */
class AppContextTest extends BaseTest {

    /**
     * 共通処理。
     */
    public function setUp() {

    }

    /**
     * テスト内容：set/get メソッドのテスト。
     */
    public function testAccess() {

        $beforeApp = AppContext::get();

        try {
            $app = new App();
            AppContext::set($app);

            // 設定したオブジェクトが正常に設定されていること
            $this->assertEquals($app, AppContext::get());
        } finally {
            AppContext::set($beforeApp);
        }
    }

}
