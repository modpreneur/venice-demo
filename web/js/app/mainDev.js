/**
 * Created by fisa on 10/16/15.
 */
import _ from 'lodash';
import routes from './routes.js';
import App from 'trinity/App';
import controllers from './controllers.js';
import {configure} from 'trinity/Gateway';

// Gateway configuration - Xdebug purposes
configure({
    timeout: 60000,
    fileTimeout: 60000
});

let Application = new App(routes, controllers, {env: 'dev'});

Application.start(isRoute => {
    console.log('App Loaded!');
    if(!isRoute){
        console.log('INFO: This route doesn\'t have any controller!');
    }
    removeLoadingBar();
}, err => {
    console.error(err);
    let bar = document.querySelector('.header-loader .bar');
    if(bar){
        bar.style.backgroundColor = '#f00';
    }
});


function removeLoadingBar() {
    let bars = document.querySelectorAll('.header-loader .bar');
    if(bars.length > 0){
        _.map(bars, bar => {
            bar.className += ' bar-end';
        });
        let timeoutID = null;
        timeoutID = setTimeout(function(){
            document.querySelector('.header-loader').style.display = 'none';
            clearTimeout(timeoutID);
        }, 2000);
    }
}

