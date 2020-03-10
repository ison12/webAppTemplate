<?php

namespace App\Func\Debug\Controller;

use App\Func\Base\Controller\BaseController;
use App\Func\Debug\Service\CacheClearService;
use Exception;
use Slim\App;

/**
 * キャッシュクリアコントローラー。
 */
class CacheClearController extends BaseController {

    /**
     * コンストラクタ。
     * @param App $app アプリケーションオブジェクト
     */
    public function __construct(App $app) {
        parent::__construct($app);
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
