<?php

namespace App\Func\Diary\Service;

use App\Common\Data\User;
use App\Common\Util\DateUtil;
use App\Dao\Diary\DiaryDao;
use App\Func\Base\Service\DBBaseService;

/**
 * 日記リスト検索サービス。
 */
class DiaryEditableListSearchService extends DBBaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 検索処理（件数の取得）。
     * @param User $user ユーザー情報
     * @param array $condition 条件
     * @return int 件数
     */
    public function searchCount(User $user, array $condition): int {

        $diaryDao = new DiaryDao($this->dbConnection);

        $countRec = $diaryDao->selectSearchList($user->user_id, $condition, true);
        $count = 0;
        if (count($countRec) > 0) {
            $count = $countRec[0]['count'];
        }

        return $count;
    }

    /**
     * 検索処理（一覧の取得）。
     * @param User $user ユーザー情報
     * @param array $condition 条件
     * @return array レコードリスト
     */
    public function searchList(User $user, array $condition): array {

        $diaryDao = new DiaryDao($this->dbConnection);
        $list = $diaryDao->selectSearchList($user->user_id, $condition);

        return $list;
    }

    /**
     * 検索処理（年月情報の取得）。
     * @param User $user ユーザー情報
     * @return array 年月情報
     */
    public function searchDiaryYearMonthInfo(User $user): array {

        $diaryYearMap = [];
        $diaryYearList = [];

        $diaryMonthListMappedByYear = [];

        // 日付の年月別にデータを取得
        $diaryDao = new DiaryDao($this->dbConnection);
        $recList = $diaryDao->selectGroupByDiaryYearMonth($user->user_id);

        foreach ($recList as $rec) {

            // データを取得
            $diaryYmDate = DateUtil::createDateTime($rec['diary_year_month'], ['Y/m']);

            // 年と月を個別に取得
            $diaryY = $diaryYmDate->format('Y');
            $diaryM = $diaryYmDate->format('m');

            // 年ごとのマップに格納することで年の重複をなくす
            if (!isset($diaryYearMap[$diaryY])) {
                $diaryYearMap[$diaryY] = true;
            }

            // 年ごとのマップを作成して、年に紐づく形で年月を格納する
            if (!isset($diaryMonthListMappedByYear[$diaryY])) {
                $diaryMonthListMappedByYear[$diaryY] = [];
            }
            // 年月の格納
            $diaryMonthListMappedByYear[$diaryY][] = $diaryM;
        }

        // 重複のない年リストを取得
        $diaryYearList = array_keys($diaryYearMap);

        return [
            'diaryYearList' => $diaryYearList,
            'diaryMonthListMappedByYear' => $diaryMonthListMappedByYear
        ];
    }

}
