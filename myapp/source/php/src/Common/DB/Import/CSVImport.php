<?php

namespace App\Common\DB\Import;

use App\Common\Csv\CsvReader;
use App\Common\DB\DBConnection;
use App\Common\DB\DBFactory;
use Exception;

/**
 * DBへのCSVインポート。
 */
class CSVImport {

    /**
     *
     * @var DBConnection DBコネクション
     */
    private $dbConnection;

    /**
     * コンストラクタ。
     * @param DBConnection $dbConnection DBコネクション
     */
    public function __construct(DBConnection $dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    /**
     * インポートを実施する。
     * @param string $tableName テーブル名
     * @param string $csvFilePath CSVファイルパス
     * @param string $delimiter 区切り文字
     * @param string $enclose 囲み文字
     * @param string $escape エスケープ文字
     * @param string $srcEncode 区切り文字
     * @param string $desEncode 区切り文字
     */
    public function import(string $tableName, string $csvFilePath, string $delimiter = ',', string $enclose = '"', string $escape = '"', string $srcEncode = 'SJIS-win', string $desEncode = 'UTF-8') {

        $csvReader = new CsvReader($csvFilePath, $delimiter, $enclose, $escape, $srcEncode, $desEncode);
        $csvRecords = $csvReader->read();

        if (!(count($csvRecords) >= 2)) {
            // ヘッダ＋データで2行以上ない場合はエラーとする
            throw new Exception("DBCSVImport failed, tableName={$tableName}, csvFilePath={$csvFilePath}");
        }

        // INSERTカラム句
        $columns = $csvRecords[0];
        // INSERT値句
        array_shift($csvRecords);
        $values = $csvRecords;

        // INSERT実施
        $insert = DBFactory::createInsert();
        $insert->from($tableName)->column(...$columns)->value(...$values);

        $sql = '';
        $sqlParams = [];
        $insert->compile($sql, $sqlParams);

        return $this->dbConnection->queryAction($sql, $sqlParams);
    }

}
