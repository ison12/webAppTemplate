<?php

namespace Tests\App\Common\Util;

use App\Common\Util\FileUtil;
use Tests\Common\BaseTest;

/**
 * FileUtil。
 * テストクラス。
 */
class FileUtilTest extends BaseTest {

    /**
     * 共通処理。
     */
    protected function setUp() {
        parent::setUp();
    }

    /**
     * テスト内容：extractArchiveテスト。様々な文字。
     */
    public function testExtractArchiveCharCode() {

        $outFilePath = __DIR__ . "/out";
        $zipFilePath = __DIR__ . "/FileUtilTest/aA1!#$%&'()-=^~@`[{;+]},._あ漢.zip";

        FileUtil::extractArchive($zipFilePath, $outFilePath);

        $this->assertSame(true, file_exists($outFilePath . "/aA1!#$%&'()-=^~@`[{;+]},._あ漢/aA1!#$%&'()-=^~@`[{;+]},._あ漢/aA1!#$%&'()-=^~@`[{;+]},._あ漢.txt"));
        $this->assertSame('テスト', file_get_contents($outFilePath . "/aA1!#$%&'()-=^~@`[{;+]},._あ漢/aA1!#$%&'()-=^~@`[{;+]},._あ漢/aA1!#$%&'()-=^~@`[{;+]},._あ漢.txt"));

        FileUtil::deleteDir($outFilePath);
    }

    /**
     * テスト内容：extractArchiveテスト。
     */
    public function testExtractArchive() {

        $outFilePath = __DIR__ . "/out";
        $zipFilePath = __DIR__ . "/FileUtilTest/ZIPファイル.zip";

        FileUtil::extractArchive($zipFilePath, $outFilePath);

        $this->assertSame(true, file_exists($outFilePath . "/ZIPファイル/ディレクトリ1/ディレクトリ1-1/ファイル1-1.txt"));
        $this->assertSame(true, file_exists($outFilePath . "/ZIPファイル/ディレクトリ1/ディレクトリ1-2/ファイル1-2.txt"));
        $this->assertSame(true, file_exists($outFilePath . "/ZIPファイル/ディレクトリ1/ファイル1.txt"));
        $this->assertSame(true, file_exists($outFilePath . "/ZIPファイル/ディレクトリ2/ファイル2.txt"));
        $this->assertSame(true, file_exists($outFilePath . "/ZIPファイル/ファイル.txt"));

        $this->assertSame('ファイル1-1', file_get_contents($outFilePath . "/ZIPファイル/ディレクトリ1/ディレクトリ1-1/ファイル1-1.txt"));
        $this->assertSame('ファイル1-2', file_get_contents($outFilePath . "/ZIPファイル/ディレクトリ1/ディレクトリ1-2/ファイル1-2.txt"));
        $this->assertSame('ファイル1', file_get_contents($outFilePath . "/ZIPファイル/ディレクトリ1/ファイル1.txt"));
        $this->assertSame('ファイル2', file_get_contents($outFilePath . "/ZIPファイル/ディレクトリ2/ファイル2.txt"));
        $this->assertSame('ファイル', file_get_contents($outFilePath . "/ZIPファイル/ファイル.txt"));

        FileUtil::deleteDir($outFilePath);
    }

    /**
     * テスト内容：convertToOsDirSeparatorテスト。
     */
    public function testConvertToOsDirSeparator() {

        $filePath = 'aaa/bbb/ccc\\ddd\\eee.txt';

        $this->assertSame('', FileUtil::convertToOsDirSeparator(null));
        $this->assertSame('', FileUtil::convertToOsDirSeparator(''));
        $this->assertSame('aaaあ', FileUtil::convertToOsDirSeparator('aaaあ'));
        $this->assertSame(str_replace('/', DIRECTORY_SEPARATOR, 'aaa/bbb/ccc/ddd/eee.txt'), FileUtil::convertToOsDirSeparator($filePath));
    }

}
