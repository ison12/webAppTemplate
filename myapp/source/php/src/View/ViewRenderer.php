<?php

namespace App\View;

use App\Common\App\AppContext;
use App\Common\ResponseCache\ResponseCache;
use App\Common\Session\SessionData;
use Exception;
use GuzzleHttp\Psr7\LazyOpenStream;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\PhpRenderer;
use const SRC_PATH;

/**
 * ビュー描画クラス。
 * ビューへの描画処理（レスポンスに対する出力）をまとめたクラス。
 */
class ViewRenderer {

    /**
     * コンストラクタ。
     */
    private function __construct() {

    }

    /**
     * 描画メソッド。
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param PhpRenderer $renderer レンダーオブジェクト
     * @param string $contentsViewPath コンテンツビューのパス … /srcディレクトリからのパスを指定する（例：/Func/Login/Front/View/Login）
     * @param array $contentsData データ
     * @param string $layoutName レイアウトページ名 … /src/View/Layout下のレイアウトページを指定する（例：Base.phpの場合は、"Base"と指定する）
     * @param array $importWidget インポートするWidget
     * @return Response レスポンスオブジェクト
     */
    public static function render(Request $request, Response $response, PhpRenderer $renderer, string $contentsViewPath, array $contentsData, string $layoutName = null): Response {

        $app = AppContext::get();
        $container = $app->getContainer();

        if (!file_exists(SRC_PATH . $contentsViewPath . '.vue')) {
            // ファイルが存在しないため、例外を発行する（基本的には発生しないエラー）
            throw new Exception("コンテンツビューファイル未存在エラー contentsViewPath={$contentsViewPath}");
        }

        $contentsViewName = basename($contentsViewPath);

        // レイアウトページの設定
        $layoutPhp = '/View/Layout/' . $layoutName . '.php';

        // コンテナから関連するオブジェクトを取得
        $environment = $container->get('config')['environment'] ?? null;
        $baseUrl = $app->getBasePath();

        // リクエストパラメーターを取得する
        $requestParams = ViewRenderer::getRequestParams($request);
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
     * @param Response $response レスポンスオブジェクト
     * @param mixed $data The data
     * @param int $status The HTTP status code.
     * @param int $encodingOptions Json encoding options
     * @return Response レスポンスオブジェクト
     */
    public static function renderJson(Response $response, $data, int $status = null, int $encodingOptions = 0): Response {

        // セッションからログインユーザー情報を取得する
        $user = SessionData::getUser();
        // 情報をマージする
        $data = array_merge($data, ['user' => ((array) $user)]);

        $response = $response->withHeader('Content-Type', 'application/json')->withStatus($status === null ? 200 : $status);

        $payload = json_encode($data, $encodingOptions);
        $response->getBody()->write($payload);

        return $response;
    }

    /**
     * ファイル出力メソッド。
     * @param Response $response レスポンスオブジェクト
     * @param string $filePath ファイルパス
     * @param string $contentType コンテントタイプ
     * @return Response レスポンスオブジェクト
     */
    public static function renderFile(Response $response, $filePath, $contentType): Response {

        if (!file_exists($filePath)) {
            // 404エラー
            $response = $response->withHeader("Content-Type", $contentType)->withStatus(404);
            return $response;
        }

        $stream = new LazyOpenStream($filePath, 'rb'); // create a stream instance for the response body

        $response = $response->withHeader("Content-Type", $contentType)
                ->withHeader('Content-Disposition', 'attachment;filename="' . basename($filePath) . '"')
                ->withHeader('Content-Length', filesize($filePath))
                ->withBody($stream);

        return $response;
    }

    /**
     * ファイル出力メソッド。
     * キャッシュが存在する場合は、HTTPステータスコードの304を返却する。
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param string $filePath ファイルパス
     * @param string $contentType コンテントタイプ
     * @return Response レスポンスオブジェクト
     */
    public static function renderFileUseCacheStrategy(Request $request, Response $response, $filePath, $contentType): Response {

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

        return self::renderFile($response, $filePath, $contentType, $response);
    }

    /**
     * リダイレクトメソッド。
     * @param Response $response レスポンスオブジェクト
     * @param string $page ページ
     * @return Response レスポンスオブジェクト
     */
    public static function redirect(Response $response, string $page): Response {

        $app = AppContext::get();

        $baseUrl = $app->getBasePath();
        return $response->withHeader('Location', $baseUrl . $page)
                        ->withStatus(302);
    }

    /**
     * 汎用的なエラー情報を出力する。
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param string $type エラー種類
     * @param string $summary サマリメッセージ
     * @param string $message メッセージ
     * @return Response レスポンスオブジェクト
     */
    public static function renderGeneralError(Request $request, Response $response, PhpRenderer $renderer, $type, $summary, $message): Response {

        if ($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {

            $data = ['type' => $type, 'summary' => $summary, 'message' => $message];
            return self::renderJson($response, $data, 500, 0);
        } else {

            $data = $renderer->fetch('/View/Error/Error.php', ['type' => $type, 'summary' => $summary, 'message' => $message]);
            $response->getBody()->write($data);

            return $response
                            ->withStatus(500)
                            ->withHeader('Content-Type', 'text/html');
        }
    }

    /**
     * リクエストパラメータを取得する。
     * @param Request リクエストオブジェクト
     * @return array リクエストパラメータ
     */
    public static function getRequestParams(Request $request): array {

        $getParams = $request->getQueryParams() ?? [];
        $postPutParams = $request->getParsedBody() ?? [];

        $reqParams = array_merge($getParams, $postPutParams);

        return $reqParams;
    }

}
