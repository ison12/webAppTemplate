<?php

namespace App\Common\Csv;

use App\Common\Exception\FileException;
use SplFileObject;
use SplTempFileObject;
use Throwable;

/**
 * CSV読み込み。
 */
class CsvReader {

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
     * コンストラクタ。
     * @param string $filePath ファイルパス
     * @param string $srcEncode 区切り文字
     * @param string $desEncode 区切り文字
     * @param string $delimiter 区切り文字
     * @param string $enclose 囲み文字
     * @param string $escape エスケープ文字
     */
    public function __construct(string $filePath, string $srcEncode = 'SJIS-win', string $desEncode = 'UTF-8', string $delimiter = ',', string $enclose = '"', string $escape = '"') {
        $this->filePath = $filePath;
        $this->srcEncode = $srcEncode;
        $this->desEncode = $desEncode;
        $this->delimiter = $delimiter;
        $this->enclose = $enclose;
        $this->escape = $escape;
    }

    /**
     * 読み込み処理。
     * @param callable $fetchCallback フェッチコールバック関数
     * @return array CSVレコード
     */
    public function read($fetchCallback = null) {

        $records = array();

        try {
            // ファイルを読み込む
            $data = file_get_contents($this->filePath);

            $data = $this->convertFileContents($data, $this->srcEncode, $this->desEncode);

            // fopen('php://temp', 'w') と同義
            $oTmp = new SplTempFileObject();

            // UTF-8に変換したデータを書き込む
            $oTmp->fwrite($data);

            // CSVモード
            $oTmp->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY
                    // | SplFileObject::DROP_NEW_LINE
            );
            $oTmp->setCsvControl($this->delimiter, $this->enclose, $this->escape);

            foreach ($oTmp as $line) {
                if ($fetchCallback !== null) {
                    $fetchCallback($line);
                } else {
                    $records[] = $line;
                }
            }
        } catch (Throwable $ex) {

            $fileName = basename($this->filePath);
            $filePath = $this->filePath;

            throw new FileException("fileName={$fileName}, filePath={$filePath}, CSV読込に失敗", $ex);
        }

        if ($fetchCallback !== null) {
            return;
        } else {
            return $records;
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
