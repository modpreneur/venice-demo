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
    // devtool: 'source-map',
    module: {
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
                exclude: /(node_modules)(?!\/venice-js)/,
                // loader: require.resolve('babel-loader'),
                loader: 'babel',
                query: {
                    presets: [
                        'es2015',
                        'stage-2',
                        'react'
                    ]
                }
            },
            {
                test: /\.js$/,
                exclude: /(node_modules)(?!\/venice-js)/,
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
    // POUZE PRO VYVOJ VENICE-DEMO S VENICE-JS
    resolve: {
        root: path.join(__dirname, '../node_modules')
    },
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