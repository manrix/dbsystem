const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const TerserPlugin = require('terser-webpack-plugin');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');

const DEV_MODE = process.env.NODE_ENV === 'dev';

module.exports = {
    entry: {
        app: './src/js/app',
        'font-awesome': './src/js/plugins/font-awesome',
    },

    optimization: {
        minimizer: [
            new TerserPlugin({
                exclude: [/\.min\.js$/gi]
            }),
            new OptimizeCSSAssetsPlugin()
        ],
        splitChunks: {
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/](vue|axios|buefy|chart.js|vue-axios|vue-breadcrumbs|vue-router|vuex)[\\/]/,
                    name: 'vendor',
                    chunks: 'all',
                },
            }
        }
    },

    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                }
            },
            {
                test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
                loader: 'url-loader',
                options: {
                    limit: 10000,
                    name: DEV_MODE ? '[path][name].[ext]' : '[path][name].[ext]?v=[hash]',
                    publicPath: (file) => {
                        return file.replace('src', '..');
                    },
                    outputPath: (file) => {
                        return file.split("src/")[1];
                    }
                }
            },
            {
                test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
                loader: 'url-loader',
                options: {
                    limit: 10000,
                    name: '/fonts/[name].[ext]',
                    publicPath: '..'
                }
            },
            {
                test: /\.vue$/,
                use: 'vue-loader'
            },
        ]
    },

    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js'
        },
        extensions: ['.js', '.vue', '.json'],
    },

    plugins: [
        new MiniCssExtractPlugin({
            filename: DEV_MODE ? 'css/[name].css' : 'css/[name].css?v=[contenthash]'
        }),
        new VueLoaderPlugin(),
        new WebpackAssetsManifest({
            output: 'mix-manifest.json',
            customize(entry, original, manifest, asset) {
                if (entry.key.includes('install') || entry.key.includes('fonts/')) {
                    return false;
                }

                return {
                    key: `/${entry.key}`,
                    value: entry.value,
                };
            },
        })
    ]
};