<?php

namespace App\Func\SystemSetting\Controller;

use App\Common\Exception\ServiceException;
use App\Common\Session\SessionData;
use App\Func\Base\Controller\BaseController;
use App\Func\SystemSetting\Service\SystemSettingEditService;
use App\Func\SystemSetting\Service\SystemSettingSearchService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * システム設定編集コントローラー。
 */
class EditController extends BaseController {

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

        return $this->render('/Func/SystemSetting/Front/View/SystemSettingEdit', []);
    }

    /**
     * ロード処理。
     *
     * 本アクションは、以下の処理パターンを備える。
     * ・新規登録
     * ・編集
     */
    public function actionLoad() {

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        $params = $this->getRequestParams();
        $data = null;

        if (!isset($params['system_code'])) {
            // 新規登録時
            $data = [
                'isNew' => true,
                'data' => [
                      'system_code_last' => null
                    , 'system_code' => null
                    , 'system_name' => null
                    , 'system_value' => null
                    , 'create_datetime' => null
                    , 'create_user_id' => null
                    , 'create_user_name' => null
                    , 'update_datetime' => null
                    , 'update_user_id' => null
                    , 'update_user_name' => null
                ],
                'errors' => [],
            ];

            return $this->renderJson($data);
        }

        // 編集時

        try {
            // データを取得する
            $service = new SystemSettingSearchService();
            $record = $service->findBySystemCode($params['system_code']);
            // 取得時のシステムコードを保持しておく
            $record['system_code_last'] = $record['system_code'];

            // データを返却する
            $data = [
                'isNew' => false,
                'data' => $record,
                'errors' => [],
            ];
        } catch (ServiceException $ex) {

            // エラーが発生した場合は、エラーメッセージを返却
            $data = [
                'isNew' => false,
                'data' => ['system_code' => $params['system_code']],
                'errors' => $ex->getErrors(),
            ];
        }

        return $this->renderJson($data);
    }

    /**
     * 保存処理。
     */
    public function actionSave() {

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        $params = $this->getRequestParams();

        try {
            // 保存する
            $service = new SystemSettingEditService();
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

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        $params = $this->getRequestParams();

        try {
            // 保存する
            $service = new SystemSettingEditService();
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
