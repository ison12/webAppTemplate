<?php

namespace App\Func\Top\Controller;

use App\Func\Base\Controller\BaseController;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * トップコントローラー。
 */
class TopController extends BaseController {

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

        return $this->render('/Func/Top/Front/View/Top', []);
    }

    /**
     * ロード処理。
     */
    public function actionLoad() {
        return $this->renderJson([]);
    }

}
