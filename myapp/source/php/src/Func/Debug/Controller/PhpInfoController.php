<?php

namespace App\Func\Debug\Controller;

use App\Func\Base\Controller\BaseController;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * PHPInfoコントローラー。
 */
class PhpInfoController extends BaseController {

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
        $this->response = $this->response->withStatus(200)->withHeader('Content-type', 'text/html');

        $body = $this->response->getBody();
        $body->write($phpInfoContents);

        return $this->response;
    }

}
