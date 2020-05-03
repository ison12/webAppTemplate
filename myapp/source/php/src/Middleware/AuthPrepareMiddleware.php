<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

/**
 * 認証チェックおよび認証処理の前準備を実施するためのミドルウェア。
 */
class AuthPrepareMiddleware {

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
        if ($this->mustAuth($this->app, $request)) {
            $request = $request->withAttribute('mustAuth', true);
        } else {
            $request = $request->withAttribute('mustAuth', false);
        }

        // --- 次のミドルウェアまたはルーティング処理を実行 ---
        $response = $handler->handle($request);

        // --- 後処理 ---

        return $response;
    }

    /**
     * 認証が必要かどうかの判定を実施。
     *
     * @param App $app アプリケーションオブジェクト
     * @param  ServerRequestInterface  $request PSR-7 request
     *
     * @return bool true 認証が必要、false 認証不要
     */
    private function mustAuth(App $app, ServerRequestInterface $request): bool {

        $basePath = $app->getBasePath();
        if ($basePath === null) {
            $basePath = '';
        }

        $configPublicPage = $app->getContainer()->get('config')['publicPage'] ?? [];
        $configPublicPageEle = $configPublicPage;

        $uri = $request->getUri();
        $uriPath = mb_substr($uri->getPath(), mb_strlen($basePath));
        $uriPath = trim(mb_strtolower($uriPath), '/');

        $uriPathList = explode('/', $uriPath);

        $isFind = false;

        foreach ($uriPathList as $uriPathFragment) {

            if (isset($configPublicPageEle[$uriPathFragment])) {
                // 公開ページの一部のパスにマッチ

                $judge = $configPublicPageEle[$uriPathFragment];

                if (is_array($judge)) {
                    // 多段階層なので、次の階層へ移動
                    $configPublicPageEle = $judge;
                    continue;
                } else if ($judge === true) {
                    // 公開ページとして判定
                    $isFind = true;
                    break;
                }

                break;
            }
        }

        if ($isFind) {
            // 公開ページの場合は、認証不要
            return false;
        }

        return true;
    }

}
