# 説明
サーバーサイドはPHP、クライアントサイドはVueJsを使用。  

VueJsでシングルファイルコンポーネント（*.vue）を使用する（Vue CLIを使わない）。  
シングルページアプリケーションではなく、従来どおりのWebアプリケーション（ページ切り替えが発生する）とする。

# ディレクトリ構成

    ルート
        + config
            + appConfig.php     ... アプリケーション設定ファイル
        + public                ... Webサーバー公開ディレクトリ
            + assets            ... リソースディレクトリ（画像やフォントファイルなど）
            + dist              ... Webpackの出力ディレクトリ
                + bundle.js
            + .htaccess         ... Apache設定ファイル
            + index.php         ... Webリクエストのエントリポイントページ
        + src                   ... ソースディレクトリ
            + Cache             ... キャッシュ関連モジュール
            + Command           ... 単体実行用のモジュール（定期実行バッチなど）
            + Common            ... 共通系のモジュール
            + Constant          ... 定数系のモジュール
            + Dao               ... DAOモジュール
            + Front             ... フロントエンド系の共通モジュール（jsファイルなど）
            + Func              ... 機能別モジュール
                + [機能]
                    + Controller ... 機能別コントローラー
                    + Front      ... 機能別フロントエンド系のモジュール
                        + View
                            + [ビューコンポーネント].html
                            + [ビューコンポーネント].css
                            + [ビューコンポーネント].js
                            + [ビューコンポーネント].vue
                    + Service   ... 機能別ビジネスロジック
                .
                .
            + Mail              ... メール送信モジュール
            + Message
                + Message.php           ... 共通メッセージファイル
                + ValidateMessage.php   ... 入力チェック用メッセージファイル
            + View              ... サーバーサイドビュー
            + bootstrap.php     ... PHP起動時に実行する共通処理
            + main.js           ... メインのJSファイル (Webpackのエントリファイル)
            + main.css          ... メインのCSSファイル
        + tests                 ... ユニットテストディレクトリ
        + composer.json         ... Composerパッケージ設定ファイル
        + composer.lock
        + package-lock.json
        + package.json          ... NPMパッケージ設定ファイル
        + webpack.common.js     ... WebPack設定ファイル
        + webpack.dev.js        ... WebPack設定ファイル（開発環境）
        + webpack.prod.js       ... WebPack設定ファイル（本番環境）

# 環境構築時に実施したことのメモ
## Composerでインストールするもの
Composer.jsonを編集して、Composerの install(dev) を実施。

    "require": {
        "php": "7.2.*",
        "psr/simple-cache": "~1.0",
        "slim/slim": "^3.1",
        "slim/php-view": "^2.0",
        "monolog/monolog": "^1.17",
        "phpmailer/phpmailer": "~6.0"
    },

## NPM (Node Package Manager)でインストールするもの
コマンドプロンプトを起動して、NPMコマンドで以下を導入

    # 初期化
    npm init

    # webpack関連
    npm install -D webpack webpack-cli webpack-dev-server
    npm install -D webpack-merge

    # トランスパイラ
    npm install -D babel-loader @babel/core @babel/preset-env
    # CSSローダー
    npm install -D css-loader
    # ファイルローダー
    npm install -D file-loader

    # VueJs本体
    npm install -D vue
    npm install -D vuex
    # SFCビルドに必要なモジュール
    npm install -D vue-loader vue-template-compiler

    # Bootstrapの依存パッケージ
    npm install -D jquery
    npm install -D popper.js
    # Bootstrap CSS Framework
    npm install -D bootstrap

    # jQuery UI
    npm install -D jquery-ui

    # CSSノーマライズ
    npm install -D normalize.css

    # Webアイコンフォント
    npm install -D open-iconic

## ポイント

Webpackの設定ファイルを開発用と本番用で分割。  
参考：https://webpack.js.org/guides/production/

HMRの有効化設定（プロキシでdist以外のリクエストはApacheに転送する）  
webpack.dev.js

    ...
    devServer: {
        // 生成されたファイルをディスクに書き込むかどうか
        writeToDisk: false,
        // ブラウザの表示有無
        open: true,
        // ブラウザの表示ページ
        openPage: prefixUri + '/login',
        // HMR (Hot Module Replacement) の有無
        hot: true,
        // Webページリロードの有無
        //inline: true,
        // Webサーバーの公開ディレクトリ
        contentBase: path.join(__dirname, 'public'),
        // Webサーバーの公開ディレクトリの監視有無
        watchContentBase: true,
        // Webサーバーの公開URL
        publicPath: prefixUri + "/",
        // プロキシ設定
        proxy: [
            {
                context: [
                    // すべてのリクエストを対象とする
                    '**',
                    // dist以下は対象外とする
                    '!' + prefixUri + '/dist/**'
                ],
                // Apache Webサーバーに転送する
                target: 'http://localhost:5555'
            }
        ]
    }
    ...

main.js

    // グローバル変数の登録（windowオブジェクトに設定しないと、他のJavascriptコードから読み取れない）
    ...
    window.AppFuncs = AppFuncs;
    ...

App.vue  
ファイルを分割

    <template src="./App.html"></template>
    <script src="./App.js"></script>
    <style src="./App.css" scoped></style>


# 参考ページなど
* https://jp.vuejs.org/v2/guide/single-file-components.html
* https://vue-loader.vuejs.org/
* https://webpack.js.org/
* https://qiita.com/rifutan/items/a55f132d4dae7e2f1941
* https://qiita.com/riversun/items/d27f6d3ab7aaa119deab#3hmrhot-module-replacement%E3%81%AE%E8%A8%AD%E5%AE%9A
