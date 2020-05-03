<?php

namespace App\Middleware;

use App\Common\Message\MessageManager;
use App\Common\Session\SessionData;
use App\View\ViewRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Psr7\Response;
use const SRC_PATH;

/**
 * 認証チェックミドルウェア。
 */
class AuthCheckMiddleware {

    /**
     * @var App アプリケーションオブジェクト
     */
    private $app;

    /**
     * コンストラクタ。
     * @param App $app アプリケーションオブジェクト
     */
    public function __construct(App $app) {
        $this->app = $app;
    }

    /**
     * 認証チェック。
     *
     * @param  ServerRequestInterface  $request PSR-7 request
     * @param  RequestHandlerInterface $handler PSR-15 request handler
     *
     * @return ResponseInterface レスポンスオブジェクト
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        // --- 前処理 ---
        if ($request->getAttribute('mustAuth', false)) {
            if (!$this->checkAuth($this->app)) {
                // 未認証の場合、エラーを表示する
                return $this->renderAuthCheckFailed($request);
            }
        }

        // --- 次のミドルウェアまたはルーティング処理を実行 ---
        $response = $handler->handle($request);

        // --- 後処理 ---

        return $response;
    }

    /**
     * 認証チェックの実施
     * @param App $app アプリケーションオブジェクト
     * @return bool true 問題なし、false 未認証のため問題あり
     */
    private function checkAuth(App $app) {

        $container = $app->getContainer();

        $environment = $container->get('config')['environment'] ?? null;

        if ($environment === 'development') {
            // 開発の場合はスキップする
            return true;
        }

        $user = SessionData::getUser();
        if ($user === null) {
            // ユーザー情報が見つからない
            return false;
        }

        return true;
    }

    /**
     * 認証失敗時の描画処理。
     * @param App $app アプリケーションオブジェクト
     * @param Request $request リクエストオブジェクト
     * @return Response レスポンス
     */
    private function renderAuthCheckFailed(App $app, ServerRequestInterface $request): Response {

        $errorMessage = MessageManager::getInstance(SRC_PATH . 'Message/MessageConfig.php');
        $summaryMessageStr = $errorMessage->get('error_unauth_summary');
        $errorMessageStr = $errorMessage->get('error_unauth');

        $response = new Response();
        return ViewRenderer::renderGeneralError($request, $response, $app->getContainer()->get('renderer'), "unauth", $summaryMessageStr, $errorMessageStr);
    }

}
