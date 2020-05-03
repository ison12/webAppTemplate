<?php

namespace App\Func\User\Controller;

use App\Common\Exception\ServiceException;
use App\Common\Session\SessionData;
use App\Func\Base\Controller\BaseController;
use App\Func\User\Service\UserDeleteService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * ユーザー削除コントローラー。
 */
class DeleteController extends BaseController {

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
     * 実行処理。
     */
    public function actionExec() {

        $params = $this->getRequestParams();
        $data = $params['data'] ?? [];

        try {
            $service = new UserDeleteService();
            $service->delete($data, SessionData::getUser(), $this->request->getUri());

            // データを返却する
            $data = [
                'data' => $data,
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
     * ロードデータ生成。
     * @return array ロードデータ
     */
    private function createLoadData() {

        $params = $this->getRequestParams();

        $data = [
            'data' => [
                'id' => $params['id'] ?? null,
                'user_account' => $params['user_account'] ?? null,
                'auth_code' => null,
                'user_name' => null,
                'user_name_kana' => null,
                'password' => null,
                'password_confirm' => null,
            ],
            'errors' => [],
            'errorsOnBoard' => [],
        ];

        return $data;
    }

}
