/* global __dirname */

// webpack development setting
const path = require('path');
const merge = require('webpack-merge');
const common = require('./webpack.common.js');

const prefixUri = "/myapp";

module.exports = merge(common, {
    mode: 'development',
    devtool: 'inline-source-map',
    // Configuration for dev server
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
});
