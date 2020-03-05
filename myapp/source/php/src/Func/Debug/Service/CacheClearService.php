<?php

namespace App\Func\Debug\Service;

use App\Cache\DBCacheManager;
use App\Func\Base\Service\BaseService;

/**
 * キャッシュクリアサービス。
 */
class CacheClearService extends BaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * DBキャッシュクリアを実施する。
     */
    public function clearDBCache() {

        $cache = DBCacheManager::getInstance();
        $cache->clear();
    }

}
