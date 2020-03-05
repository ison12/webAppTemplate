// webpack.config.js
const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

const prefixUri = "/myapp";

module.exports = {
    mode: 'development',
    resolve: {
        modules: [
            path.resolve('./src'),
            path.resolve('./node_modules')
        ]
    },
    entry: path.resolve(__dirname, 'src', 'main.js'),
    output: {
        path: path.resolve(__dirname, 'public', 'dist'),
        filename: 'bundle.js'
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            // this will apply to both plain `.js` files
            // AND `<script>` blocks in `.vue` files
            {
                test: /\.js$/,
                loader: 'babel-loader'
            },
            // this will apply to both plain `.css` files
            // AND `<style>` blocks in `.vue` files
            {
                test: /\.css$/,
                use: [
                    'vue-style-loader',
                    'css-loader'
                ]
            },
            {
                test: /\.(jpe?g|png|gif)$/i,
                loader: "file-loader",
                options: {
                    name: '[path][name].[ext]',
                    outputPath: 'images/',
                    publicPath: function (path) {
                        return prefixUri + '/dist/images/' + path;
                    }
                }
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf|svg)$/i,
                loader: "file-loader",
                options: {
                    name: '[path][name].[ext]',
                    outputPath: 'fonts/',
                    publicPath: function (path) {
                        return prefixUri + '/dist/fonts/' + path;
                    }
                }
            }
        ]
    },
    // Configuration for dev server
    devServer: {
        writeToDisk: true
    },
    plugins: [
        new VueLoaderPlugin()
    ]
};
