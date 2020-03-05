<?php

namespace App\Common\Exception;

use Exception;

/**
 * サービス例外。
 */
class ServiceException extends Exception {

    /**
     * $errors =
     *   [
     *     ['id' => ..., 'message' => ... ],
     *     ['id' => ..., 'message' => ... ],
     *   ];
     * @var array エラーリスト
     */
    private $errors;

    /**
     * 本例外の使用者が自由に扱うためのデータ領域
     * @var mixed 拡張データ
     */
    private $ext;

    /**
     * コンストラクタ。
     * @param array $errors エラーリスト
     * @param mixed $ext 拡張データ
     * @param Exception $previous 例外
     */
    public function __construct(array $errors, $ext = null, Exception $previous = null) {
        $this->errors = $errors;
        $this->ext = $ext;

        $message = '';
        $code = 0;
        if ($previous !== null) {
            $message = $previous->getMessage();
            $code = $previous->getCode();
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * エラーリストを取得する。
     * @return array エラーリスト
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * 拡張データを取得する。
     * @return mixed 拡張データ
     */
    public function getExt() {
        return $this->ext;
    }

}
