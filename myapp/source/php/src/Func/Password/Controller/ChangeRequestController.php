<?php

namespace App\Func\Password\Controller;

use App\Common\Exception\ServiceException;
use App\Func\Base\Controller\BaseController;
use App\Func\Password\Service\PasswordChangeRequestService;
use Slim\App;

/**
 * パスワード変更リクエストコントローラー。
 */
class ChangeRequestController extends BaseController {

    /**
     * @var bool 認証を要するかどうかのフラグ、true：要認証、false、不要
     */
    protected $needAuth = false;

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
            $service->changeRequest($params['data'], $this->container->request->getUri());

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
                'password' => null,
            ],
            'errors' => [],
            'errorsOnBoard' => [],
        ];

        return $data;
    }

}
