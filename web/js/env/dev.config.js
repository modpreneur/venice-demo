'use strict';

let path = require('path');
let webpack = require('webpack');

module.exports = {
    entry: path.join(__dirname, '../app/mainDev.js'),
    output: {
        path: path.join(__dirname, '../dist'),
        filename: 'venice.bundle.js'
    },
    // Bigger file but faster compiling
    devtool: 'eval-cheap-module-source-map',
    module: {
        /*preLoaders: [
            {
                test: /\.jsx?$/,
                loader: 'eslint-loader',
                exclude: /(node_modules)/,
                query: {
                    parserOptions: {
                        ecmaVersion: 6,
                        sourceType: 'module',
                        ecmaFeatures: {
                            jsx: true,
                            experimentalObjectRestSpread: true
                        }
                    },
                    rules:{
                        semi: 2,
                        quotes: [1, 'single']
                    }
                }
            }
        ],*/
        loaders: [
            {
                test: /\.css$/,
                loader: 'style!css-loader'
            },
            {
                test: /\.less$/,
                loader: 'style!css-loader!less-loader'
            },
            {
                test: /\.jsx$/,
                exclude: /(node_modules)/,
                loader: 'babel',
                query: {
                    presets: [
                        'es2015',
                        'react',
                        'stage-2'
                    ]
                }
            },
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                loader: 'babel',
                query: {
                    presets: [
                        'es2015',
                        'stage-2'
                    ]
                }
            }
        ]
    },
    plugins: [
        new webpack.DefinePlugin({
            DEVELOPMENT: true
        })
    ],
    resolveLoader: {
        root: path.join(__dirname, './node_modules')
    },
    // For faster build 
    externals: {
        'lodash': '_',
        'jquery': '$',
        'react': 'React',
        'react-dom': 'ReactDOM',
        'history' : 'History',
        'flux': 'Flux',
        'moment': 'moment'
    }
};
