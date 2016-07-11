'use strict';

let path = require('path');
let webpack = require('webpack');
let ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
    entry: path.join(__dirname, '../app/main.js'),
    output: {
        path: path.join(__dirname, '../dist'),
        filename: 'venice.bundle.min.js'
    },
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
                test: /\.jsx?$/,
                exclude: /(node_modules)(?!\/venice-js)/,
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
    plugins:[
        new webpack.DefinePlugin({
            // React libraries
            "process.env": {
                NODE_ENV: JSON.stringify("production")
            },
            DEVELOPMENT: false
        }),
        new webpack.optimize.DedupePlugin(),
        new webpack.optimize.UglifyJsPlugin({
            sourceMap: true,
            comments: false
        })

    ],
    resolve:{

    },
    resolveLoader: {
        root: path.join(__dirname, './node_modules')
    }
};