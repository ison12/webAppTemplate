<?php

namespace App\Middleware;

use App\Common\Data\User;
use App\Common\Exception\ServiceException;
use App\Common\Message\MessageManager;
use App\Common\Session\SessionData;
use App\Func\Login\Service\LoginService;
use App\View\ViewRenderer;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Psr7\Response;
use const SRC_PATH;

/**
 * 認証処理ミドルウェア。
 */
class AuthProcessMiddleware {

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
     * 認証処理。
     *
     * @param  ServerRequestInterface  $request PSR-7 request
     * @param  RequestHandlerInterface $handler PSR-15 request handler
     *
     * @return ResponseInterface レスポンス
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        // --- 前処理 ---
        if ($request->getAttribute('mustAuth', false)) {
            try {
            // 認証処理
                $user = $this->authIfParamsExists($request);
                if ($user !== null) {
                    // 認証有り
                    SessionData::setUser($user);
                }
            } catch (Exception $ex) {
                // 認証エラー時の処理
                return $this->renderAuthFailed($this->app, $ex);
            }
        }

        // --- 次のミドルウェアまたはルーティング処理を実行 ---
        $response = $handler->handle($request);

        // --- 後処理 ---

        return $response;
    }

    /**
     * パラメータに認証情報がある場合に、認証処理を実施する。
     * @param ServerRequestInterface $request リクエスト
     * @return User 認証結果ユーザー（未認証時はnullを返却）
     */
    private function authIfParamsExists(ServerRequestInterface $request): ?User {

        if ($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            // Ajax通信の場合は、認証しない
            return null;
        }

        // セキュリティを考慮してPOSTパラメータのみ受け付ける
        // （GETパラメータを受け付けるのを有りとした場合、ブラウザのアドレスバーから情報が簡単に閲覧できてしまうので）
        $postPutParams = $request->getParsedBody() ?? [];

        $account = $postPutParams['account'] ?? null;
        $password = $postPutParams['password'] ?? null;

        if ($account === null || $password === null) {
            // パラメータ不足なので、認証しない
            return null;
        }

        $user = null;

        $loginService = new LoginService();
        $loginService->auth([
                'user_account' => $account,
                'password' => $password,
                    ], $user);

        return $user;
    }

    /**
     * 認証失敗時の描画処理。
     * @param App $app アプリケーションオブジェクト
     * @param Exception $ex 例外
     * @return Response レスポンス
     */
    private function renderAuthFailed(App $app, Exception $ex): Response {

        $errorMessage = MessageManager::getInstance(SRC_PATH . 'Message/MessageConfig.php');

        $summaryMessageStr = null;
        $errorMessageStr = null;

        if ($ex instanceof ServiceException) {
            $summaryMessageStr = $errorMessage->get('error_invalid_auth_summary');
            $errorMessageStr = $ex->getErrors()[0]['message'] ?? $errorMessage->get('error_invalid_auth');
        } else {
            $summaryMessageStr = $errorMessage->get('error_invalid_auth_summary');
            $errorMessageStr = $errorMessage->get('error_invalid_auth');
        }

        $response = new Response();
        return ViewRenderer::renderGeneralError($request, $response, $app->getContainer()->get('renderer'), "auth", $summaryMessageStr, $errorMessageStr);
    }

}
