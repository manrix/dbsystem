const path = require('path');
const merge = require('webpack-merge');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CommonConfig = require('./webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = merge(CommonConfig, {

    devtool: 'eval',

    output: {
        path: path.resolve(__dirname, './public'),
        filename: 'js/[name].js'
    },
    
    module: {
        rules: [
            {
                test: /\.s[c|a]ss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true
                        }
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true
                        }
                    }
                ]
            },
            {
                test: /\.css$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true
                        }
                    }
                ]
            }
        ]
    }

});