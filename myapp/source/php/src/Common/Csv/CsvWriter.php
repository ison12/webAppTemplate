<?php

namespace App\Common\Csv;

use App\Common\Exception\FileException;
use App\Common\Util\FileUtil;
use SplFileObject;
use Throwable;

/**
 * CSV書き込み。
 *
 * 
 */
class CsvWriter {

    /**
     * @var string ファイルパス
     */
    protected $filePath;

    /**
     * @var string 変換元エンコード
     */
    protected $srcEncode;

    /**
     * @var string 変換先エンコード
     */
    protected $desEncode;

    /**
     * @var string 区切り文字
     */
    protected $delimiter;

    /**
     * @var string 囲み文字
     */
    protected $enclose;

    /**
     * @var string エスケープ文字
     */
    protected $escape;

    /**
     * @var string ファイルオブジェクト
     */
    protected $file;

    /**
     * コンストラクタ。
     * @param string $filePath ファイルパス
     * @param string $delimiter 区切り文字
     * @param string $enclose 囲み文字
     * @param string $escape エスケープ文字
     */
    public function __construct(string $filePath, string $srcEncode = 'UTF-8', string $desEncode = 'SJIS-win', string $delimiter = ',', string $enclose = '"', string $escape = '"') {
        $this->filePath = $filePath;
        $this->srcEncode = $srcEncode;
        $this->desEncode = $desEncode;
        $this->delimiter = $delimiter;
        $this->enclose = $enclose;
        $this->escape = $escape;

        try {
            if (FileUtil::existsFile($filePath) !== true) {
                FileUtil::makeDirectory(dirname($filePath));
            }
            $this->file = new SplFileObject($filePath, 'w');
        } catch (Throwable $ex) {

            $fileName = basename($this->filePath);
            $filePath = $this->filePath;

            throw new FileException("fileName = {$fileName}, filePath = {$filePath}, CSV出力の初期化に失敗", $ex);
        }
    }

    /**
     * レコードの書き込み処理。
     * @param array $record レコード
     */
    public function write($record) {

        foreach ($record as &$col) {
            $col = $this->enclose . str_replace($this->enclose, $this->escape . $this->enclose, $col) . $this->enclose;
        }

        $this->file->fwrite(join($this->delimiter, $record) . "\n");
    }

    /**
     * レコードリストの書き込み処理。
     * @param array $recordList レコードリスト
     */
    public function writeList($recordList) {

        foreach ($recordList as &$record) {
            $this->write($record);
        }
    }

    /**
     * クローズ処理。
     */
    public function close() {

        try {
            $this->file->fflush();
            $this->file = null;

            // ファイルを読み込む
            $data = file_get_contents($this->filePath);

            $data = $this->convertFileContents($data, $this->srcEncode, $this->desEncode);

            file_put_contents($this->filePath, $data);
        } catch (Throwable $ex) {

            $fileName = basename($this->filePath);
            $filePath = $this->filePath;

            throw new FileException("fileName = {$fileName}, filePath = {$filePath}, CSV出力のクローズに失敗", $ex);
        }
    }

    /**
     * ファイルコンテンツを変換する。
     * @param string $data データ
     * @param string $srcEncode 変換元になるエンコード
     * @param string $desEncode 変換後になるエンコード
     * @return string 変換後のデータ
     */
    private function convertFileContents(string $data, string $srcEncode, string $desEncode): string {

        // 文字コードを変換
        $dataConv = mb_convert_encoding($data, $desEncode, $srcEncode);
        // 改行コードを変換
        $dataConv = strtr($dataConv, array("\r\n" => "\r\n", "\r" => "\r\n", "\n" => "\r\n"));

        return $dataConv;
    }

}
