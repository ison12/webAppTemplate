<?php

namespace App\Func\Diary\Controller;

use App\Common\Exception\ServiceException;
use App\Common\Session\SessionData;
use App\Common\Util\ValUtil;
use App\Func\Base\Controller\BaseController;
use App\Func\Diary\Service\DiaryEditableListEditService;
use App\Func\Diary\Service\DiaryEditableListSearchService;
use Slim\App;

/**
 * 日記編集リストコントローラー。
 */
class EditableListController extends BaseController {

    /**
     * コンストラクタ。
     * @param App $app アプリケーションオブジェクト
     */
    public function __construct(App $app) {
        parent::__construct($app);
    }

    /**
     * 表示処理。
     */
    public function actionIndex() {

        return $this->render('/Func/Diary/Front/View/DiaryEditableList', []);
    }

    /**
     * ロード処理。
     */
    public function actionLoad() {

        // ロードデータの生成
        $data = $this->createLoadData();

        // 検索を実行する
        $searchData = $this->searchData($data['condition']);
        // 検索内容と $data をマージする
        $data = array_merge($data, $searchData);

        return $this->renderJson($data);
    }

    /**
     * 検索処理。
     */
    public function actionSearch() {

        // リクエストパラメータ
        $params = $this->getRequestParams();

        // 条件を取得
        $condition = $params['condition'] ?? [];
        // 検索を実行する
        $searchData = $this->searchData($condition);

        return $this->renderJson($searchData);
    }

    /**
     * ロードデータ生成。
     * @return array ロードデータ
     */
    private function createLoadData() {

        $data = [
            'condition' => [
                  'diaryYear' => null
                , 'diaryMonth' => null
            ],
            'list' => [],
            'diaryYearMonthInfo' => [
                'diaryYearList' => [],
                'diaryMonthListMappedByYear' => []],
            'errors' => [],
        ];
        return $data;
    }

    /**
     * 検索処理。
     * @param array $condition 検索条件
     * @return array 検索結果
     */
    private function searchData($condition) {

        $user = SessionData::getUser();

        // 検索サービス
        $service = new DiaryEditableListSearchService();

        // 年月情報の取得
        $diaryYearMonthInfo = $service->searchDiaryYearMonthInfo($user);
        if (count($diaryYearMonthInfo['diaryYearList']) > 0 &&
                ValUtil::isEmpty($condition['diaryYear']) &&
                ValUtil::isEmpty($condition['diaryMonth'])) {
            // 初回の検索時は、年月のデフォルトを一番新しい年月にする
            $yearList = $diaryYearMonthInfo['diaryYearList'];
            $year = $yearList[count($yearList) - 1];
            $monthList = $diaryYearMonthInfo['diaryMonthListMappedByYear'][$year];
            $month = $monthList[count($monthList) - 1];

            $condition['diaryYear'] = $year;
            $condition['diaryMonth'] = $month;
        }

        // 検索結果を取得
        $list = $service->searchList($user, $condition);

        $data = [
            'condition' => $condition,
            'list' => $list,
            'diaryYearMonthInfo' => $diaryYearMonthInfo,
            'errors' => [],
        ];

        return $data;
    }

    /**
     * 保存処理。
     */
    public function actionSave() {

        $params = $this->getRequestParams();

        try {
            // 保存する
            $service = new DiaryEditableListEditService();
            $record = $service->save($params['data'], SessionData::getUser());

            // データを返却する
            $data = [
                'data' => $record,
                'errors' => [],
            ];
        } catch (ServiceException $ex) {
            // エラーが発生した場合は、エラーメッセージを返却
            $data = [
                'errors' => $ex->getErrors(),
            ];
        }

        return $this->renderJson($data);
    }

    /**
     * 削除処理。
     */
    public function actionDelete() {

        $params = $this->getRequestParams();

        try {
            // 保存する
            $service = new DiaryEditableListEditService();
            $service->delete($params['data'], SessionData::getUser());

            // データを返却する
            $data = [
                'errors' => [],
            ];
        } catch (ServiceException $ex) {
            // エラーが発生した場合は、エラーメッセージを返却
            $data = [
                'errors' => $ex->getErrors(),
            ];
        }

        return $this->renderJson($data);
    }

}
