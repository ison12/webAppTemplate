<?php

namespace App\Func\Password\Controller;

use App\Common\Exception\ServiceException;
use App\Func\Base\Controller\BaseController;
use App\Func\Password\Service\PasswordChangeRequestService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * パスワード変更依頼コントローラー。
 */
class ChangeRequestController extends BaseController {

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

        return $this->render('/Func/Password/Front/View/PasswordChangeRequest', []);
    }

    /**
     * ロード処理。
     */
    public function actionLoad() {

        $data = $this->createLoadData();
        return $this->renderJson($data);
    }

    /**
     * 実行処理。
     */
    public function actionExec() {

        $params = $this->getRequestParams();

        try {
            $service = new PasswordChangeRequestService();
            $service->changeRequest($params['data'], $this->request->getUri());

            // データを返却する
            $data = [
                'data' => $params['data'],
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
                'user_account' => $params['user_account'] ?? null,
            ],
            'errors' => [],
            'errorsOnBoard' => [],
        ];

        return $data;
    }

}
