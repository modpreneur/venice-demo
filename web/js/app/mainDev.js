'use strict';

import _ from 'lodash';
import $ from 'jquery';
import 'Base64'; // Adds widow.atob() and window.btoa() IE9 support
import 'trinity/devTools';
import routes from './routes';
import controllers from './controllers';
import App from 'trinity/App';
import {configure} from 'trinity/Gateway';

// Gateway configuration - Xdebug purposes
configure({
    timeout: 60000,
    fileTimeout: 60000
});

// Extends jquery
$.id = document.getElementById.bind(document);
// Externals for debug
window.$ = $;
window._ = _;

let Application = new App(routes, controllers, {
    env: 'dev',
    globalController: 'Global'
});

Application.start(function (){
    removeLoadingBar();
}, function (err){
    console.error(err);
    let bar = $('.header-loader .bar')[0];
    if(bar){
        bar.style.backgroundColor = "#f00";
    }
});


function removeLoadingBar() {
    let $bars = $('.header-loader .bar');
    if(!_.isEmpty($bars)){
        $bars.addClass('bar-end');

        let timeoutID = null;
        timeoutID = setTimeout(()=>{
            $('.header-loader')[0].style.display = 'none';
            clearTimeout(timeoutID);
        }, 5000);
    }
}