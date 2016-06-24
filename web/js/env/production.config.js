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
        // preLoaders: [
        //     {
        //         test: /\.jsx?$/,
        //         loader: 'eslint-loader?{rules:{quotes:["error", "double"]}}',
        //         exclude: /(node_modules)/,
        //         query: {
        //             parserOptions: {
        //                 ecmaVersion: 6,
        //                 sourceType: 'module',
        //                 ecmaFeatures: {
        //                     jsx: true,
        //                     experimentalObjectRestSpread: true
        //                 }
        //             },
        //             rules:{
        //                 semi: 2,
        //                 quotes: ['error', 'double']
        //             }
        //         }
        //     }
        // ],
        loaders: [
            // {
            //     test: /\.css$/,
            //     loader: ExtractTextPlugin.extract('style-loader', 'css-loader')
            // },
            // {
            //     test: /\.less$/,
            //     loader: ExtractTextPlugin.extract('style-loader', 'css-loader!less-loader')
            // },
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
                exclude: /(node_modules)/,
                loader: 'babel',
                query: {
                    presets: [
                        'es2015',
                        'react',
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
        // new ExtractTextPlugin('venice.styles.min.css', {
        //     allChunks: true
        // }),
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