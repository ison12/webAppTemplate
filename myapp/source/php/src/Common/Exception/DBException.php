<?php

namespace App\Common\Exception;

use App\Common\DB\DBConnection;
use Exception;

/**
 * DBコネクション例外。
 */
class DBException extends Exception {

    /**
     * @var array エラー情報
     */
    public $errorInfo = null;

    /**
     * @var string SQLステータス
     */
    private $sqlState = null;

    /**
     * @var string ドライバステータス
     */
    private $driverState = null;

    /**
     * @var string DB接続文字列
     */
    private $connectionStr = null;

    /**
     * @var mixed 例外データ
     */
    private $previous = null;

    /**
     * @var string SQLステートメント
     */
    private $sqlStatement = null;

    /**
     * @var array SQLパラメータ
     */
    private $sqlParams = null;

    /**
     * コンストラクタ。
     * @param DBConnection $dbConnection DB接続オブジェクト
     * @param string $sqlStatement SQLステートメント
     * @param array $sqlParams SQLパラメータ
     * @param Exception $previous 例外
     */
    public function __construct(DBConnection $dbConnection
    , string $sqlStatement
    , array $sqlParams
    , Exception $previous
    ) {

        $this->connectionStr = $dbConnection->getConnectionStr();
        $this->previous = $previous;
        $this->sqlStatement = $sqlStatement;
        $this->sqlParams = $sqlParams;
        $sqlParamsStr = implode(', ', $sqlParams);

        $this->errorInfo = $previous->errorInfo;
        $this->sqlState = $this->errorInfo[0] ?? null;
        $this->driverState = $this->errorInfo[1] ?? null;

        $message = "SQLSTATE={$this->sqlState}, DRIVER STATE={$this->driverState}, MESSAGE=" . $previous->getMessage()
                . ", SQL={$this->sqlStatement}, SQLParams=[{$sqlParamsStr}]"
        ;

        parent::__construct($message, 0, $previous);
    }

    /**
     * SQLステータスを取得する。
     * @return string SQLステータス
     */
    public function getSqlState() {
        return $this->sqlState;
    }

    /**
     * ドライバステータスを取得する。
     * @return string ドライバステータス
     */
    public function getDriverState() {
        return $this->driverState;
    }

}
