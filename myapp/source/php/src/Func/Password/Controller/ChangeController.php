<?php

namespace App\Func\Password\Controller;

use App\Common\Exception\ServiceException;
use App\Func\Base\Controller\BaseController;
use App\Func\Password\Service\PasswordChangeService;
use Slim\App;
use Slim\Exception\NotFoundException;

/**
 * パスワード変更コントローラー。
 */
class ChangeController extends BaseController {

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

        $params = $this->getRequestParams();

        $service = new PasswordChangeService();
        if (!$service->validateData($params ?? [])) {
            // データが無効なので404ページエラーを返却する
            throw new NotFoundException($this->container->request, $this->container->response);
        }

        return $this->render('/Func/Password/Front/View/PasswordChange', []);
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
            $service = new PasswordChangeService();
            $service->change($params['data'], $this->container->request->getUri());

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
                'id' => $params['id'] ?? null,
                'user_account' => $params['user_account'] ?? null,
                'auth_code' => null,
                'password' => null,
                'password_confirm' => null,
            ],
            'errors' => [],
            'errorsOnBoard' => [],
        ];

        return $data;
    }

}
