<?php

namespace App\Common\Cache\ImplException;

use Psr\SimpleCache\InvalidArgumentException;

/**
 * キャッシュ引数の不正な例外
 */
class InvalidArgumentExceptionImpl extends \Exception implements InvalidArgumentException {

    /**
     * コンストラクタ。
     * @param type $message
     */
    public function __construct($message) {
        parent::__construct($message, 0, null);
    }

}
