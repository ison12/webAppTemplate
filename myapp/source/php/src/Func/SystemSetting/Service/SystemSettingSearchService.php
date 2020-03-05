<?php

namespace App\Func\SystemSetting\Service;

use App\Common\Exception\ServiceException;
use App\Common\Validation\Validatation;
use App\Dao\SystemSetting\SystemSettingDao;
use App\Func\Base\Service\DBBaseService;

/**
 * システム設定検索サービス。
 */
class SystemSettingSearchService extends DBBaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * IDからレコードを取得する処理。
     * @param string $id ID
     * @return array レコード
     */
    public function findById(string $id): array {

        $systemDao = new SystemSettingDao($this->dbConnection);

        $records = $systemDao->selectLinkedUserAccoutById($id);
        if (count($records) !== 1) {
            // データが見つからなかった場合
            // エラーデータを生成し例外を送出する
            $error = Validatation::createError(
                            'error_data_not_found'
                            , $this->errorMessage->get('error_data_not_found', ['%itemName%' => 'システム設定', '%id%' => $id]));
            throw new ServiceException([$error]);
        }

        return $records[0];
    }

    /**
     * 検索処理（件数の取得）。
     * @param array $condition 条件
     * @return int 件数
     */
    public function searchCount(array $condition): int {

        $systemDao = new SystemSettingDao($this->dbConnection);

        $countRec = $systemDao->selectSearchList($condition, true);
        $count = 0;
        if (count($countRec) > 0) {
            $count = $countRec[0]['count'];
        }

        return $count;
    }

    /**
     * 検索処理（一覧の取得）。
     * @param array $condition 条件
     * @return array レコードリスト
     */
    public function searchList(array $condition): array {

        $systemDao = new SystemSettingDao($this->dbConnection);
        $list = $systemDao->selectSearchList($condition);

        return $list;
    }

}
