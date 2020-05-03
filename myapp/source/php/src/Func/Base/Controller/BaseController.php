<?php

namespace App\Func\Base\Controller;

use App\Common\Log\AppLogger;
use App\Common\Session\SessionData;
use App\Constant\CommonConstant;
use App\View\ViewRenderer;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * 基本となるコントローラークラス。
 * コントローラクラスは、本クラスを継承すること。
 */
class BaseController {

    /**
     * @var App Appオブジェクト
     */
    protected $app;

    /**
     * @var Request HTTPリクエストオブジェクト
     */
    protected $request;

    /**
     * @var Response HTTPレスポンスオブジェクト
     */
    protected $response;

    /**
     *
     * @var type
     */
    protected $container;

    /**
     * @var AppLogger ロガー
     */
    protected $logger;

    /**
     * @var string レイアウト名
     */
    protected $layoutName;

    /**
     * @var array リクエストパラメータ
     */
    protected $requestParams;

    /**
     * コンストラクタ。
     * @param App $app アプリケーションオブジェクト
     * @param Request $request HTTPリクエスト
     * @param Response $response HTTPレスポンス
     */
    public function __construct(App $app, Request $request, Response $response) {

        $this->app = $app;
        $this->request = $request;
        $this->response = $response;

        $this->container = $app->getContainer();
        $this->layoutName = 'Base';
        $this->requestParams = null;

        $this->logger = AppLogger::get();
    }

    /**
     * リクエストパラメータを取得する。
     * @return array リクエストパラメータ
     */
    protected function getRequestParams(): array {

        if ($this->requestParams !== null) {
            return $this->requestParams;
        }

        $this->requestParams = ViewRenderer::getRequestParams($this->request);
        return $this->requestParams;
    }

    /**
     * 描画メソッド。
     * @param string $contentsViewPath コンテンツビューのパス … /srcディレクトリからのパスを指定する（例：/Func/Login/Front/View/Login）
     * @param array $contentsData データ
     * @param string $layoutName レイアウトページ名 … /src/View/Layout下のレイアウトページを指定する（例：Base.phpの場合は、"Base"と指定する）
     * @param array $importWidget インポートするWidget
     * @return Response レスポンスオブジェクト
     */
    protected function render(string $contentsViewPath, array $contentsData, string $layoutName = null): Response {

        if ($layoutName === null) {
            $layoutName = $this->layoutName;
        }
        return ViewRenderer::render($this->request, $this->response, $this->container->get('renderer'), $contentsViewPath, $contentsData, $layoutName);
    }

    /**
     * Json出力メソッド。
     * @param mixed $data The data
     * @param int $status The HTTP status code.
     * @param int $encodingOptions Json encoding options
     * @return Response レスポンスオブジェクト
     */
    protected function renderJson($data, int $status = null, int $encodingOptions = 0): Response {

        return ViewRenderer::renderJson($this->response, $data, $status, $encodingOptions);
    }

    /**
     * ファイル出力メソッド。
     * @param string $filePath ファイルパス
     * @param string $contentType コンテントタイプ
     * @return Response レスポンスオブジェクト
     */
    protected function renderFile($filePath, $contentType): Response {

        return ViewRenderer::renderFile($this->response, $filePath, $contentType);
    }

    /**
     * ファイル出力メソッド。
     * キャッシュが存在する場合は、HTTPステータスコードの304を返却する。
     * @param string $filePath ファイルパス
     * @param string $contentType コンテントタイプ
     * @return Response レスポンスオブジェクト
     */
    protected function renderFileUseCacheStrategy($filePath, $contentType): Response {

        return ViewRenderer::renderFileUseCacheStrategy($this->request, $this->response, $filePath, $contentType);
    }

    /**
     * リダイレクトメソッド。
     * @param string $page ページ
     * @return Response レスポンスオブジェクト
     */
    protected function redirect(string $page): Response {

        return ViewRenderer::redirect($this->response, $page);
    }

    /**
     * 汎用的なエラー情報を出力する。
     * @param string $type エラー種類
     * @param string $summary サマリメッセージ
     * @param string $message メッセージ
     * @return Response レスポンスオブジェクト
     */
    protected function renderGeneralError($type, $summary, $message): Response {

        return ViewRenderer::renderGeneralError($this->request, $this->response, $this->container->get('renderer'), $type, $summary, $message);
    }

    /**
     * 管理者ではない場合に不正なアクセスのため、ページが見つからないエラーを発生させる。
     */
    protected function invalidAccessIfDeneiedUser() {

        $user = SessionData::getUser();

        $environment = $this->container->get('config')['environment'];
        if ($environment === 'development') {
            return;
        }

        if ($user->authority !== CommonConstant::AUTH_ADMIN) {
            // 404エラー
            throw new HttpNotFoundException($this->request);
        }
    }

}
