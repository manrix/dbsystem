const path = require('path');
const merge = require('webpack-merge');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CommonConfig = require('./webpack.config');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = merge(CommonConfig, {

    devtool: false,

    output: {
        path: path.join(__dirname, './production/public'),
        filename: function(data) {
            return data.chunk.name === '../install/install' ? 'js/[name].js' : 'js/[name].js?v=[chunkhash]';
        },
        chunkFilename: 'js/[name].js?v=[chunkhash]'
    },
    
    module: {
        rules: [
            {
                test: /\.s[c|a]ss$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader', 'sass-loader']
            },
            {
                test: /\.css$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader']
            }
        ]
    },

    plugins: [
        new CleanWebpackPlugin([
            './production/public/js/*.js',
            './production/public/css/*.css',
            './production/public/install',
            './production/public/fonts/*.*',
        ]),
        new CopyWebpackPlugin([
            { from: './public/install', to: 'install' },
            { from: './.env.example', to: '../', force: true },
            { from: './composer.json', to: '../', force: true },
            { from: './composer.lock', to: '../', force: true },
            { from: './artisan', to: '../', force: true },
            { from: './node_modules/bulma/css/bulma.min.css', to: './install/css' },
            { from: './node_modules/bulma-steps/dist/css/bulma-steps.min.css', to: './install/css' },
            { from: './node_modules/bulma-steps/dist/js/bulma-steps.min.js', to: './install/js' },
        ])
    ]

});