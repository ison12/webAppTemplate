<?php

namespace App\Func\Top\Controller;

use App\Func\Base\Controller\BaseController;
use Slim\App;

/**
 * トップコントローラー。
 */
class TopController extends BaseController {

    /**
     * @var bool 認証を要するかどうかのフラグ、true：要認証、false、不要
     */
    protected $needAuth = true;

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

        return $this->render('/Func/Top/Front/View/Top', []);
    }

    /**
     * ロード処理。
     */
    public function actionLoad() {
        return $this->renderJson([]);
    }

}
