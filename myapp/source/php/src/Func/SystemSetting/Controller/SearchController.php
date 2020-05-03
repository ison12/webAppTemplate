<?php

namespace App\Func\SystemSetting\Controller;

use App\Common\Page\PageCalcurator;
use App\Common\Session\SessionData;
use App\Common\Util\ValUtil;
use App\Func\Base\Controller\BaseController;
use App\Func\SystemSetting\Service\SystemSettingSearchService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * システム設定検索コントローラー。
 */
class SearchController extends BaseController {

    /**
     * コンストラクタ。
     * @param App $app アプリケーションオブジェクト
     * @param Request $request HTTPリクエスト
     * @param Response $response HTTPレスポンス
     */
    public function __construct(App $app, Request $request, Response $response) {
        parent::__construct($app, $request, $response);
    }

    /**
     * 表示処理。
     */
    public function actionIndex() {

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        return $this->render('/Func/SystemSetting/Front/View/SystemSettingSearch', []);
    }

    /**
     * ロード処理。
     *
     * 本アクションは、以下の処理パターンを備える。
     * ・ロードデータのみ返却するパターン
     * ・デフォルトの検索条件で検索するパターン
     * ・最後に検索した内容で検索し検索結果も一緒に返却するパターン
     */
    public function actionLoad() {

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        $params = $this->getRequestParams();

        // 前回の検索を実行するかどうかのフラグ
        $isPreviousSearch = $params['previousSearch'] ?? false;
        // 選択ID
        $selectedId = $params['selectedId'] ?? null;
        // 初期表示と共にデータ検索するかどうかのフラグ
        $isSearch = $params['search'] ?? false;

        // ロードデータの生成
        $data = $this->createLoadData();
        // ロードデータに各変数をマージする
        $data['previousSearch'] = $isPreviousSearch;
        $data['selectedId'] = $selectedId;
        $data['search'] = $isSearch;

        // セッションから最後に検索した条件などを取得する
        $lastCondition = null;
        $lastPage = null;
        $this->restoreSessionForCondition($lastCondition, $lastPage);

        if ($isPreviousSearch &&
                $lastCondition !== null &&
                $lastPage !== null) {
            // 最後に検索した内容で検索し検索結果も一緒に返却するパターン

            $data['condition'] = $lastCondition;
            $data['page'] = $lastPage;

            // 検索を実行する
            $searchData = $this->searchData($data['condition'], $data['page']);
            // 最後に検索した条件をセッションに設定
            $this->storeSessionForCondition($searchData['condition'], $searchData['page']);
            // 検索内容と $data をマージする
            $data = array_merge($data, $searchData);
        } elseif ($isSearch) {
            // デフォルトの検索条件で検索するパターン
            // 検索を実行する
            $searchData = $this->searchData($data['condition'], $data['page']);
            // 最後に検索した条件をセッションに設定
            $this->storeSessionForCondition($searchData['condition'], $searchData['page']);
            // 検索内容と $data をマージする
            $data = array_merge($data, $searchData);
        } else {
            // ロードデータのみ返却するパターン
        }

        return $this->renderJson($data);
    }

    /**
     * 検索処理。
     */
    public function actionSearch() {

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        // リクエストパラメータ
        $params = $this->getRequestParams();

        // 条件を取得
        $condition = $params['condition'] ?? [];
        // ページ情報を取得
        $page = $params['page'] ?? [];

        // 検索を実行する
        $searchData = $this->searchData($condition, $page);

        // 最後に検索した条件をセッションに設定
        $this->storeSessionForCondition($searchData['condition'], $searchData['page']);

        return $this->renderJson($searchData);
    }

    /**
     * ロードデータ生成。
     * @return array ロードデータ
     */
    private function createLoadData() {

        $data = [
            'condition' => [
                  'system_code' => null
                , 'system_name' => null
                , 'system_value' => null
                , 'create_user_name' => null
                , 'create_datetime_from' => null
                , 'create_datetime_to' => null
                , 'update_user_name' => null
                , 'update_datetime_from' => null
                , 'update_datetime_to' => null
            ],
            'page' => [
                'currentPage' => 1,
                'showCountPerPage' => 10,
                'totalCount' => 0,
                'showPageLinkCount' => 10,
                'pageSize' => 10,
            ],
            'list' => [],
            'previousSearch' => false,
            'selectedId' => null,
            'errors' => [],
        ];
        return $data;
    }

    /**
     * 検索処理。
     * @param array $condition 検索条件
     * @param array $page ページ
     * @return array 検索結果
     */
    private function searchData($condition, $page) {

        // 検索サービス
        $service = new SystemSettingSearchService();
        // 検索結果件数を取得（ページ情報算出のため必要）
        $count = $service->searchCount($condition);

        // 総件数を設定
        $condition['totalCount'] = $count;

        $pageData = [];
        if (
                !ValUtil::isEmptyElementOfArray($page, 'currentPage') &&
                !ValUtil::isEmptyElementOfArray($page, 'showCountPerPage') &&
                !ValUtil::isEmptyElementOfArray($page, 'totalCount')
        ) {
            // ページ情報が設定されている場合は、ページ情報を計算する
            $page = new PageCalcurator($page['currentPage'], $page['showCountPerPage'], $count);

            $limit = $page->calcLimit();
            $offset = $page->calcOffset();

            $condition['limit'] = $limit;
            $condition['offset'] = $offset;

            $pageData = $page->getData();
        }

        // 検索結果を取得
        $list = $service->searchList($condition);

        $data = [
            'condition' => $condition,
            'page' => $pageData,
            'list' => $list,
        ];

        return $data;
    }

    /**
     * セッションに検索条件を保存する。
     * @param array $condition 検索条件
     * @param array $page ページ
     */
    private function storeSessionForCondition($condition, $page) {

        SessionData::setData(get_class($this) . '_LAST_CONDITION', $condition);
        SessionData::setData(get_class($this) . '_LAST_PAGE', $page);
    }

    /**
     * セッションから検索条件を読み取りする。
     * @param array $condition 検索条件
     * @param array $page ページ
     */
    private function restoreSessionForCondition(&$condition, &$page) {

        $condition = SessionData::getData(get_class($this) . '_LAST_CONDITION');
        $page = SessionData::getData(get_class($this) . '_LAST_PAGE');
    }

}
