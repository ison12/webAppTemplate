<?php

namespace App\Func\Logout\Controller;

use App\Func\Base\Controller\BaseController;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * ログアウトコントローラー。
 */
class LogoutController extends BaseController {

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
     * ログアウト処理。
     */
    public function actionIndex() {

        // セッションからユーザー情報を消去する
        session_destroy();

        // ログイン画面にリダイレクトする
        return $this->redirect('/login', []);
    }

}
