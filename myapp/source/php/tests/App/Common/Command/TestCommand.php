<?php

namespace Tests\App\Common\Command;

use App\Common\Command\BaseCommand;
use App\Common\Util\FileUtil;

/**
 * テストコマンド。
 *
 * @author hideki.isobe
 */
class TestCommand extends BaseCommand {

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

        $outFilePath = $params['outFilePath'];
        $fileContent = $params['fileContent'];

        FileUtil::writeFile($outFilePath, $fileContent);

        // 結果コード
        $ret = 0;
        return $ret;
    }

}
