<?php

namespace App\Func\Base\Controller;

use App\Common\DB\DBObserver;
use App\Common\Exception\ServiceException;
use App\Common\Log\AppLogger;
use App\Common\Message\MessageManager;
use App\Common\ResponseCache\ResponseCache;
use App\Common\Session\SessionData;
use App\Common\Util\DBUtil;
use App\Constant\CommonConstant;
use App\Func\Login\Service\LoginService;
use SebastianBergmann\RecursionContext\Exception;
use Slim\App;
use Slim\Exception\NotFoundException;
use Slim\Http\Response;
use Slim\Http\Stream;
use const SRC_PATH;

/**
 * 基本となるコントローラークラス。
 * コントローラクラスは、本クラスを継承すること。
 */
class BaseController {

    /**
     * @var bool 認証を要するかどうかのフラグ、true：要認証、false、不要
     */
    protected $needAuth = true;

    /**
     * @var App Appオブジェクト
     */
    protected $app;

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
     * @var Response コンストラクタ内で中断された際のレスポンスオブジェクトを取得する
     */
    protected $_responseIfAbortInConstructor;

    /**
     * @var bool アクションメソッドを継続して呼び出すかどうかのフラグ
     */
    protected $_isContinueActionMethod = true;

    /**
     * コンストラクタ内で中断された際のレスポンスオブジェクトを取得する。
     * @return Response コンストラクタ内で中断された際のレスポンスオブジェクトを取得する
     */
    public function getResponseIfAbortInConstructor() {
        return $this->_responseIfAbortInConstructor;
    }

    /**
     * アクションメソッドを継続して呼び出すかどうかのフラグを取得する。
     * @return bool アクションメソッドを継続して呼び出すかどうかのフラグ
     */
    public function isContinueActionMethod() {
        return $this->_isContinueActionMethod;
    }

    /**
     * コンストラクタ。
     * @param App $app アプリケーションオブジェクト
     */
    public function __construct(App $app) {
        $this->app = $app;
        $this->container = $app->getContainer();
        $this->layoutName = 'Base';
        $this->requestParams = null;

        $this->logger = AppLogger::get();
        DBObserver::addQueryObserver('global', function($dbConnection, $statement, $params, $ext) {
            $this->logger->info(DBUtil::combineQueryStatementAndParams($statement, $params));
        });

        if ($this->needAuth) {
            // 認証を必要とする場合は、ここで認証関連の処理をチェックする
            // 認証を実施する（パラメータが存在する場合）
            if ($this->isContinueActionMethod()) {
                if (!$this->authIfParamsExists()) {
                    // アクションメソッドを継続して呼び出さない
                    $this->_isContinueActionMethod = false;
                }
            }
            // 認証をチェックする
            if ($this->isContinueActionMethod()) {
                if (!$this->checkAuth()) {
                    // アクションメソッドを継続して呼び出さない
                    $this->_isContinueActionMethod = false;
                }
            }
        }
    }

    /**
     * リクエストパラメータを取得する。
     * @return array リクエストパラメータ
     */
    protected function getRequestParams(): array {

        if ($this->requestParams !== null) {
            return $this->requestParams;
        }

        $request = $this->container->request;

        $getParams = $request->getQueryParams() ?? [];
        $postPutParams = $request->getParsedBody() ?? [];

        $allParams = array_merge($getParams, $postPutParams);
        $this->requestParams = $allParams;

        return $allParams;
    }

    /**
     * 描画メソッド。
     * @param string $contentsViewPath コンテンツビューのパス … /srcディレクトリからのパスを指定する（例：/Func/Login/Front/View/Login）
     * @param array $contentsData データ
     * @param string $layoutName レイアウトページ名 … /src/View/Layout下のレイアウトページを指定する（例：Base.phpの場合は、"Base"と指定する）
     * @param array $importWidget インポートするWidget
     * @return type
     */
    protected function render(string $contentsViewPath, array $contentsData, string $layoutName = null) {

        if (!file_exists(SRC_PATH . $contentsViewPath . '.vue')) {
            // ファイルが存在しないため、例外を発行する（基本的には発生しないエラー）
            throw new \Exception("コンテンツビューファイル未存在エラー contentsViewPath={$contentsViewPath}");
        }

        $contentsViewName = basename($contentsViewPath);

        // レイアウトページの設定
        if ($layoutName === null) {
            $layoutName = $this->layoutName;
        }
        $layoutPhp = '/View/Layout/' . $layoutName . '.php';

        // コンテナから関連するオブジェクトを取得
        $container = $this->container;
        $request = $container->request;
        $response = $container->response;
        $renderer = $container->renderer;
        $environment = $container->settings['environment'] ?? null;
        $baseUrl = $request->getUri()->getBasePath();

        // リクエストパラメーターを取得する
        $requestParams = $this->getRequestParams();
        // セッションからログインユーザー情報を取得する
        $user = SessionData::getUser();

        // レイアウトページのデータオブジェクト
        $layoutDataObj = [
            '__environment' => $environment,
            '__baseUrl' => $baseUrl,
            '__requestParams' => $requestParams,
            '__user' => $user,
            '__contentsViewPath' => $contentsViewPath,
            '__contentsViewName' => $contentsViewName,
            '__contentsData' => $contentsData,
        ];

        return $renderer->render($response
                        , $layoutPhp
                        , $layoutDataObj);
    }

    /**
     * Json出力メソッド。
     * @param mixed $data The data
     * @param int $status The HTTP status code.
     * @param int $encodingOptions Json encoding options
     * @return type
     */
    protected function renderJson($data, int $status = null, int $encodingOptions = 0) {

        $response = $this->container->response;

        // セッションからログインユーザー情報を取得する
        $user = SessionData::getUser();
        // 情報をマージする
        $data = array_merge($data, ['user' => ((array) $user)]);

        return $response->withJson($data, $status, $encodingOptions);
    }

    /**
     * ファイル出力メソッド。
     * @param string $filePath ファイルパス
     * @param string $contentType コンテントタイプ
     * @param Response $response レスポンス
     * @return type
     */
    protected function renderFile($filePath, $contentType, $response = null) {

        $container = $this->container;
        if ($response === null) {
            $response = $container->response;
        }

        if (!file_exists($filePath)) {
            // 404エラー
            $response = $response->withHeader("Content-Type", $contentType)->withStatus(404);
            return $response;
        }

        $fh = fopen($filePath, 'rb');

        $stream = new Stream($fh); // create a stream instance for the response body

        $response = $response->withHeader("Content-Type", $contentType)
                ->withHeader('Content-Disposition', 'attachment;filename="' . basename($filePath) . '"')
                ->withHeader('Content-Length', filesize($filePath))
                ->write($stream);

        $stream->close();

        return $response;
    }

    /**
     * ファイル出力メソッド。
     * キャッシュが存在する場合は、HTTPステータスコードの304を返却する。
     * @param string $filePath ファイルパス
     * @param string $contentType コンテントタイプ
     * @return type
     */
    protected function renderFileUseCacheStrategy($filePath, $contentType) {

        $container = $this->container;

        $request = $container->request;
        $response = $container->response;

        if (!file_exists($filePath)) {
            // 404エラー
            $response = $response->withHeader("Content-Type", $contentType)->withStatus(404);
            return $response;
        }

        $ifNoneMatch = $request->getHeaderLine('If-None-Match');
        $ifModifiedSince = $request->getHeaderLine('If-Modified-Since');

        /*
         * ブラウザにキャッシュさせる
          private = 特定ユーザーだけが使えるようにキャッシュしてよい（主にブラウザ）
          max-age = キャッシュの有効期限を設定する（秒数）
          must-revalidate = キャッシュが期限切れだった場合、オリジンサーバでの確認無しにキャッシュを利用してはならない
         */
        $response = $response->withHeader('Cache-Control', 'private, max-age=' . (86400 * 30) . ', must-revalidate');

        // キャッシュが有効かどうかを判定する
        $cacheInfo = ResponseCache::existsCacheFile($filePath, $ifNoneMatch, $ifModifiedSince);
        if ($cacheInfo['exists']) {
            $response = $response->withStatus(304);
            return $response;
        }

        $response = $response->withHeader('ETag', $cacheInfo['eTag']);
        $response = $response->withHeader('Last-Modified', gmdate('D, d M Y H:i:s T', $cacheInfo['lastModified']));

        return $this->renderFile($filePath, $contentType, $response);
    }

    /**
     * リダイレクトメソッド。
     * @param string $page ページ
     * @return type
     */
    protected function redirect(string $page) {

        $container = $this->container;

        $request = $container->request;
        $response = $container->response;

        $baseUrl = $request->getUri()->getBasePath();
        return $response->withRedirect($baseUrl . $page);
    }

    /**
     * パラメータに認証情報がある場合に、認証処理を実施する。
     */
    protected function authIfParamsExists() {

        $request = $this->container->request;

        if ($request->isXhr()) {
            // Ajax通信の場合は、認証しない
            return true;
        }

        $reqParams = $this->getRequestParams();

        $account = $reqParams['account'] ?? null;
        $password = $reqParams['password'] ?? null;

        if ($account !== null && $password !== null) {
            // アカウントとパスワードがリクエストパラメータに存在する場合は、認証処理を実施する

            $user = null;

            $errorMessage = MessageManager::getInstance(SRC_PATH . 'Message/Message.php');

            try {
                $loginService = new LoginService();
                $loginService->auth([
                    'user_account' => $account,
                    'password' => $password,
                        ], $user);
            } catch (ServiceException $ex) {
                // サービス例外
                // メッセージを取得
                $summaryMessageStr = $errorMessage->get('error_invalid_auth_summary');
                $errorMessageStr = $ex->getErrors()[0]['message'] ?? $errorMessage->get('error_invalid_auth');

                // 汎用エラーを出力
                $this->_responseIfAbortInConstructor = $this->renderGeneralError("auth", $summaryMessageStr, $errorMessageStr);

                return false;
            } catch (Exception $ex) {
                // 認証例外
                // メッセージを取得
                $summaryMessageStr = $errorMessage->get('error_invalid_auth_summary');
                $errorMessageStr = $errorMessage->get('error_invalid_auth');

                // 汎用エラーを出力
                $this->_responseIfAbortInConstructor = $this->renderGeneralError("auth", $summaryMessageStr, $errorMessageStr);

                return false;
            }

            SessionData::setUser($user);
        }

        return true;
    }

    /**
     * 認証状態をチェックする。
     */
    protected function checkAuth() {

        $environment = $this->container->settings['environment'] ?? null;

        if ($environment === 'development') {
            // 開発の場合はスキップする
            return true;
        }

        $user = SessionData::getUser();
        if ($user === null || $user === null) {
            // ユーザー情報が見つからないので、未認証である旨のエラーを発行する

            $errorMessage = MessageManager::getInstance(SRC_PATH . 'Message/Message.php');

            $summaryMessageStr = $errorMessage->get('error_unauth_summary');
            $errorMessageStr = $errorMessage->get('error_unauth');

            $this->_responseIfAbortInConstructor = $this->renderGeneralError("unauth", $summaryMessageStr, $errorMessageStr);
            return false;
        }

        return true;
    }

    /**
     * 汎用的なエラー情報を出力する。
     * @param string $type エラー種類
     * @param string $summary サマリメッセージ
     * @param string $message メッセージ
     */
    protected function renderGeneralError($type, $summary, $message) {

        $renderer = $this->container->renderer;
        $request = $this->container->request;
        $response = $this->container->response;

        if ($request->isXhr()) {

            $data = ['type' => $type, 'summary' => $summary, 'message' => $message];
            return $this->renderJson($data, 500, 0);
        } else {

            $data = $renderer->fetch('/View/Error/Error.php', ['type' => $type, 'summary' => $summary, 'message' => $message]);
            return $response
                            ->withStatus(500)
                            ->withHeader('Content-Type', 'text/html')
                            ->write($data);
        }
    }

    /**
     * 管理者ではない場合に不正なアクセスのため、ページが見つからないエラーを発生させる。
     */
    protected function invalidAccessIfDeneiedUser() {

        $user = SessionData::getUser();

        if ($user->authority !== CommonConstant::AUTH_ADMIN) {
            // 404エラー
            $request = $this->container->request;
            $response = $this->container->response;

            throw new NotFoundException($request, $response);
        }
    }

}
