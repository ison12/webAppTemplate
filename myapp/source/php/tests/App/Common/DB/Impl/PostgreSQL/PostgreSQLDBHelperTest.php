<?php

namespace Tests\App\Common\DB\Impl\PostgreSQL;

use App\Common\DB\DBFactory;
use Tests\Common\DBBaseTest;

/**
 * PostgreSQLDBHelperTest。
 * テストクラス。
 *
 * 
 */
class PostgreSQLDBHelperTest extends DBBaseTest {

    /**
     * 共通処理。
     */
    protected function setUp() {
        parent::setUp();
    }

    /**
     * テスト内容：EscapeLikeテスト。
     */
    public function testEscapeLike() {

        $dbHelper = DBFactory::createDBHelper(self::$postgresDBName);

        $ret = $dbHelper->escapeLike(';');

        $this->assertSame("ESCAPE ';'", $ret);
    }

    /**
     * テスト内容：EscapeLikeValueテスト。
     */
    public function testEscapeLikeValue() {

        $dbHelper = DBFactory::createDBHelper(self::$postgresDBName);

        $ret = $dbHelper->escapeLikeValue('あいうえお%かきくけこ_', ';');
        $this->assertSame("あいうえお;%かきくけこ;_", $ret);

        $ret = $dbHelper->escapeLikeValue('あいうえお%%かきくけこ__', ';');
        $this->assertSame("あいうえお;%;%かきくけこ;_;_", $ret);
    }

}
