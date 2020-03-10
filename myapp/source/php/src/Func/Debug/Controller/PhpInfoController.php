<?php

namespace App\Func\Debug\Controller;

use App\Func\Base\Controller\BaseController;
use Slim\App;

/**
 * PHPInfoコントローラー。
 */
class PHPInfoController extends BaseController {

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

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        return $this->render('/Func/Debug/Front/View/PhpInfo', []);
    }

    /**
     * ロード処理。
     */
    public function actionLoad() {

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        return $this->renderJson([]);
    }

    /**
     * phpinfo関数の結果を表示する処理。
     */
    public function actionPhpInfo() {

        // 管理者のみアクセス可能なページのためチェックを実施する
        $this->invalidAccessIfDeneiedUser();

        // phpinfo()関数の内容を取得する
        ob_start();
        phpinfo();
        $phpInfoContents = ob_get_contents();
        ob_clean();

        // レスポンスに内容を取得する
        $response = $this->container->response;
        $response = $response->withStatus(200)->withHeader('Content-type', 'text/html');

        $body = $response->getBody();
        $body->write($phpInfoContents);

        return $response;
    }

}
