<?php

namespace Tests\App\Common\Util;

use App\Common\Util\CalcUtil;
use Tests\Common\BaseTest;

/**
 * CalcUtil。
 * テストクラス。
 */
class CalcUtilTest extends BaseTest {

    /**
     * 共通処理。
     */
    protected function setUp() {
        parent::setUp();
    }

    /**
     * テスト内容：convertMmToPtテスト。
     */
    public function testConvertMmToPt() {

        $pt = CalcUtil::convertMmToPt(297);
        $this->assertSame(841.89, $pt);

        $pt = CalcUtil::convertMmToPt(420);
        $this->assertSame(1190.55, $pt);
    }

    /**
     * テスト内容：convertPtToPixelテスト。
     */
    public function testConvertPtToPixel() {

        $pt = CalcUtil::convertPtToPixel(1);
        $this->assertSame(1, $pt);

        $pt = CalcUtil::convertPtToPixel(1025);
        $this->assertSame(1367, $pt);
    }

    /**
     * テスト内容：convertPixelToPtテスト。
     */
    public function testConvertPixelToPt() {

        $pt = CalcUtil::convertPixelToPt(1);
        $this->assertSame(0.75, $pt);

        $pt = CalcUtil::convertPixelToPt(1027);
        $this->assertSame(770.25, $pt);
    }

}
