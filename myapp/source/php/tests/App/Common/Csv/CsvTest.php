<?php

namespace Tests\App\Common\Csv;

use App\Common\Csv\CsvReader;
use App\Common\Csv\CsvWriter;
use Exception;
use Tests\Common\BaseTest;

/**
 * CsvWriter & CsvReader。
 * テストクラス。
 *
 * 
 */
class CsvTest extends BaseTest {

    /**
     * 共通処理。
     */
    protected function setUp() {
        parent::setUp();
    }

    /**
     * テスト内容：Csv出力＆読込テスト。
     */
    public function testCsv1() {

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'test/csv.csv';
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $csvWriter = new CsvWriter($filePath);
        try {
            $csvWriter->write(['列1', '列2', '列3']);
            $csvWriter->write(['値1-1', '値1-2', "改行\r\nしました"]);
            $csvWriter->write(['値2-1', '値2-2', "\"ダブルクオ－テーション\""]);
        } catch (Exception $exc) {
            throw $exc;
        } finally {
            if ($csvWriter !== null) {
                $csvWriter->close();
            }
        }

        $csvReader = new CsvReader($filePath);
        try {
            $csvRecords = $csvReader->read();

            $this->assertSame(3, count($csvRecords));
            $this->assertSame('列1', $csvRecords[0][0]);
            $this->assertSame('列2', $csvRecords[0][1]);
            $this->assertSame('列3', $csvRecords[0][2]);
            $this->assertSame('値1-1', $csvRecords[1][0]);
            $this->assertSame('値1-2', $csvRecords[1][1]);
            $this->assertSame("改行\r\nしました", $csvRecords[1][2]);
            $this->assertSame('値2-1', $csvRecords[2][0]);
            $this->assertSame('値2-2', $csvRecords[2][1]);
            $this->assertSame("\"ダブルクオ－テーション\"", $csvRecords[2][2]);
        } catch (Exception $exc) {
            throw $exc;
        } finally {

        }
    }

    /**
     * テスト内容：Csv出力＆読込テスト。
     */
    public function testCsv2() {

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'test/csv2.csv';
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $csvWriter = new CsvWriter($filePath);
        try {
            $csvWriter->write(['列1', '列2', '列3']);
            $csvWriter->write(['値1-1', '値1-2', "改行\r\nしました"]);
            $csvWriter->write(['値2-1', '値2-2', "\"ダブルクオ－テーション\""]);
        } catch (Exception $exc) {
            throw $exc;
        } finally {
            if ($csvWriter !== null) {
                $csvWriter->close();
            }
        }

        $csvReader = new CsvReader($filePath);
        try {
            $csvRecords = $csvReader->read();

            $this->assertSame(3, count($csvRecords));
            $this->assertSame('列1', $csvRecords[0][0]);
            $this->assertSame('列2', $csvRecords[0][1]);
            $this->assertSame('列3', $csvRecords[0][2]);
            $this->assertSame('値1-1', $csvRecords[1][0]);
            $this->assertSame('値1-2', $csvRecords[1][1]);
            $this->assertSame("改行\r\nしました", $csvRecords[1][2]);
            $this->assertSame('値2-1', $csvRecords[2][0]);
            $this->assertSame('値2-2', $csvRecords[2][1]);
            $this->assertSame("\"ダブルクオ－テーション\"", $csvRecords[2][2]);
        } catch (Exception $exc) {
            throw $exc;
        } finally {

        }
    }

    /**
     * テスト内容：Csv出力＆読込テスト。
     */
    public function testCsvReadFetch() {

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'test/csv3.csv';
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $csvWriter = new CsvWriter($filePath);
        try {
            $csvWriter->write(['列1', '列2', '列3']);
            $csvWriter->write(['値1-1', '値1-2', "改行\r\nしました"]);
            $csvWriter->write(['値2-1', '値2-2', "\"ダブルクオ－テーション\""]);
        } catch (Exception $exc) {
            throw $exc;
        } finally {
            if ($csvWriter !== null) {
                $csvWriter->close();
            }
        }

        $csvReader = new CsvReader($filePath);
        try {
            $line = 0;
            $csvReader->read(function($data) use(&$line) {

                if ($line === 0) {
                    $this->assertSame('列1', $data[0]);
                    $this->assertSame('列2', $data[1]);
                    $this->assertSame('列3', $data[2]);
                } else if ($line === 1) {
                    $this->assertSame('値1-1', $data[0]);
                    $this->assertSame('値1-2', $data[1]);
                    $this->assertSame("改行\r\nしました", $data[2]);
                } else {
                    $this->assertSame('値2-1', $data[0]);
                    $this->assertSame('値2-2', $data[1]);
                    $this->assertSame("\"ダブルクオ－テーション\"", $data[2]);
                }

                $line++;
            });
        } catch (Exception $exc) {
            throw $exc;
        } finally {

        }
    }

}
