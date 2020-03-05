<?php

namespace Tests\App\Common\Util;

use App\Common\Util\DBUtil;
use Tests\Common\BaseTest;

/**
 * DBUtil。
 * テストクラス。
 */
class DBUtilTest extends BaseTest {

    /**
     * 共通処理。
     */
    protected function setUp() {
        parent::setUp();
    }

    /**
     * テスト内容：combineQueryStatementAndParamsテスト。
     */
    public function testCombineQueryStatementAndParams() {

        /*
         * 不正なパラメータ
         */
        $ret = DBUtil::combineQueryStatementAndParams(null, ['あいうえお', 'かきくけこ']);
        $this->assertSame(null, $ret);

        $ret = DBUtil::combineQueryStatementAndParams('', ['あいうえお', 'かきくけこ']);
        $this->assertSame('', $ret);

        /*
         * 不正なパラメータ
         */
        $ret = DBUtil::combineQueryStatementAndParams('not empty', null);
        $this->assertSame('not empty', $ret);

        $ret = DBUtil::combineQueryStatementAndParams('not empty', []);
        $this->assertSame('not empty', $ret);

        /*
         * 正常ケース
         */
        $ret = DBUtil::combineQueryStatementAndParams('select * from test where id = ?', ['あいうえお']);
        $this->assertSame("select * from test where id = 'あいうえお'", $ret);

        $ret = DBUtil::combineQueryStatementAndParams('select * from test where id = ?', ['あ\'いうえお']);
        $this->assertSame("select * from test where id = 'あ''いうえお'", $ret);

        $ret = DBUtil::combineQueryStatementAndParams('select * from test where id = ? or name = ?', ['あいうえお', 'かきくけこ']);
        $this->assertSame("select * from test where id = 'あいうえお' or name = 'かきくけこ'", $ret);

        $ret = DBUtil::combineQueryStatementAndParams('?select * from test where id = ? or name = ?', ['最初', 'あいうえお', 'かきくけこ']);
        $this->assertSame("'最初'select * from test where id = 'あいうえお' or name = 'かきくけこ'", $ret);

        // ?パラメータと実際のパラメータの個数が異なる
        $ret = DBUtil::combineQueryStatementAndParams('select * from test where id = ?', ['あいうえお', 'かきくけこ']);
        $this->assertSame("select * from test where id = 'かきくけこ'", $ret);

        // ?パラメータと実際のパラメータの個数が異なる
        $ret = DBUtil::combineQueryStatementAndParams('select * from test where id = ? or name = ?', ['あいうえお']);
        $this->assertSame("select * from test where id = ? or name = 'あいうえお'", $ret);
    }

}
