<?php

namespace App\Common\Command;

/**
 * 空コマンド。
 */
class BlankCommand extends BaseCommand {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 実行処理。
     * @param mixed $params パラメータ
     * @return int 結果コード
     */
    public function exec($params) {

        // 結果コード
        $ret = 0;
        return $ret;
    }

}
