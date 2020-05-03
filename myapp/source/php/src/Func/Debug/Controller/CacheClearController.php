<?php

namespace App\Func\Debug\Controller;

use App\Func\Base\Controller\BaseController;
use App\Func\Debug\Service\CacheClearService;
use Exception;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * キャッシュクリアコントローラー。
 */
class CacheClearController extends BaseController {

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
     * DBキャッシュのクリアを実施する。
     */
    public function actionDbCacheClear() {

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        $ret = [
            'errors' => null
        ];

        try {
            $service = new CacheClearService();
            $service->clearDBCache();
        } catch (Exception $exc) {
            $ret['errors'] = $exc->getMessage();
        }

        return $this->renderJson($ret);
    }

}
