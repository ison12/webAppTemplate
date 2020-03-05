<?php

namespace App\Func\Logout\Controller;

use App\Func\Base\Controller\BaseController;
use Slim\App;

/**
 * ログアウトコントローラー。
 */
class LogoutController extends BaseController {

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
     * ログアウト処理。
     */
    public function actionIndex() {

        // セッションからユーザー情報を消去する
        session_destroy();

        // ログイン画面にリダイレクトする
        return $this->redirect('/login', []);
    }

}
