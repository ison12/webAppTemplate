# 概要
VueJsでシングルファイルコンポーネント（*.vue）を使用する（Vue CLIを使わない）。  
シングルページアプリケーション的ではなく、一般的なマルチページ的なWebアプリケーション構成とする。

# ディレクトリ構成

    ルート
        + public                ... 公開ディレクトリ
            + dist
                + bundle.js     ... 出力ファイル
            + index.html        ... エントリポイントとなるページ
        + src                   ... ソースディレクトリ
            + vue               ... VueJs関連
                + app           ... アプリケーション関連コンポーネント
                    + App
                        + App.css
                        + App.html
                        + App.js
                        + App.vue
                    .
                    .
                + func          ... 機能別コンポーネント
                    .
                    .
            + main.js           ... Webpackのエントリファイル
        + package.json          ... NPMパッケージ設定ファイル
        + webpack.config.js     ... WebPack設定ファイル

# インストール手順
1. NPMをインストール
1. Xamppをインストール  
Xamppのhttpd.confファイルを編集して、"ルート/public"ディレクトリを公開先として指定
1. NetBeansをインストール

# Node Package Managerでインストールするもの
    # 初期化
    npm init

    # webpack関連
    npm install -D webpack webpack-cli
    npm install -D webpack-dev-server

    # トランスパイラ
    npm install -D babel-loader @babel/core @babel/preset-env
    # CSSローダー
    npm install -D css-loader

    # VueJs本体
    npm install -D vue
    # SFCビルドに必要なモジュール
    npm install -D vue-loader vue-template-compiler

# ポイントのメモ

webpack.config.js

    ...
    devServer: {
        // vueファイルなどの変更時にファイルを書き込むようにする
        writeToDisk: true
    },
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