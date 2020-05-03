<?php

namespace App\Middleware;

use App\Common\DB\DBObserver;
use App\Common\Log\AppLogger;
use App\Common\Util\DBUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

/**
 * DBへのクエリ発行のログを出力するミドルウェア。
 */
class DBQueryLoggingMiddleware {

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
        $this->logger = AppLogger::get();
        DBObserver::addQueryObserver('global', function($dbConnection, $statement, $params, $ext) {
            $this->logger->info(DBUtil::combineQueryStatementAndParams($statement, $params));
        });

        // --- 次のミドルウェアまたはルーティング処理を実行 ---
        $response = $handler->handle($request);

        // --- 後処理 ---

        return $response;
    }

}
