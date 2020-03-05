<?php

namespace App\Common\Exception;

use Exception;

/**
 * ファイル関連の例外。
 */
class FileException extends Exception {

    /**
     * コンストラクタ。
     * @param string $message メッセージ
     * @param Exception $previous 例外
     */
    public function __construct(string $message, Exception $previous = null) {

        parent::__construct($message, 0, $previous);
    }

}
