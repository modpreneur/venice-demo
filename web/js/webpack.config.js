/**
 * Created by fisa on 4/14/16.
 */
'use strict';

let path = require('path');

const esLintPreloader = {
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
};



process.env.NODE_ENV = process.env.NODE_ENV || 'dev';

console.log(process.env.NODE_ENV);

let config = null;
switch (process.env.NODE_ENV){
    case 'dev':{
        config = require(path.join(__dirname, './env/dev.config.js'));
    } break;
    case 'production':{
        config = require(path.join(__dirname, './env/production.config.js'));
    } break;
    case 'lint':{
        config = require(path.join(__dirname, './env/dev.config.js'));
        if(config.module.preLoaders){
            config.module.preLoaders.push(esLintPreloader);
        } else {
            config.module.preLoaders = [esLintPreloader];
        }
    } break;
    default: {
        config = require(path.join(__dirname, './env/dev.config.js'));
    } break;
}

// __addTrinityJSAlias(config);
module.exports = config;

function __addTrinityJSAlias(conf){
    conf.resolve = conf.resolve || {};
    conf.resolve.alias = conf.resolve.alias || {};
    conf.resolve.alias['venice-js'] = path.join(__dirname, './venice-js');
}