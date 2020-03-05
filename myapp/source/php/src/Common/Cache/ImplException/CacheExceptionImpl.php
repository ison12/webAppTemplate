<?php

namespace App\Common\Cache\ImplException;

use Psr\SimpleCache\CacheException;

/**
 * キャッシュ例外
 */
class CacheExceptionImpl extends \Exception implements CacheException {

    /**
     * コンストラクタ。
     * @param type $message
     */
    public function __construct($message) {
        parent::__construct($message, 0, null);
    }

}
