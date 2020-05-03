// webpack production setting
const merge = require('webpack-merge');
const common = require('./webpack.common.js');

module.exports = merge(common.settings, {
    mode: 'production'
});
