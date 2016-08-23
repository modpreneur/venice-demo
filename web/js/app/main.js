'use strict';

import $ from 'jquery';
import 'Base64'; // Adds widow.atob() and window.btoa() IE9 support
import routes from './routes.js';
import controllers from './controllers.js';
import App from 'trinity/App';
import globalScript from './Libraries/GlobalScript';

// Extends jquery
$.id = document.getElementById.bind(document);

let Application = new App(routes, controllers);

Application.addPreBOOTScript(globalScript);

Application.start(function() {
    removeLoadingBar();
}, function (err){
    console.error(err);
    let bar = $('.header-loader .bar')[0];
    if(bar){
        bar.style.backgroundColor = "#f00";
    }
});



if(DEVELOPMENT){
    require('trinity/Gateway').configure({
        timeout: 60000,
        fileTimeout: 60000
    });
    window.$ = $;
    window._ = require('lodash');
}

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
