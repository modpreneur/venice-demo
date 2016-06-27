/**
 * Created by fisa on 10/16/15.
 */

import routes from './routes.js';
import _ from 'lodash';
import controllers from './controllers.js';
import App from 'trinity/App';

let Application = new App(routes, controllers);

Application.start(() => {
    console.log('App Loaded!');
    removeLoadingBar();
}, err => {
    console.error(err);
    let bar = document.querySelector('.header-loader .bar');
    if(bar){
        bar.style.backgroundColor = "#f00";
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