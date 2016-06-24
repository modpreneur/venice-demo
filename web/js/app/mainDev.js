/**
 * Created by fisa on 10/16/15.
 */
import _ from 'lodash';
// import externalHelpers from 'babel/external-helpers';
import routes from './routes.js';
import App from 'trinity/App';
import controllers from './controllers.js';
// import Mousetrap from 'mousetrap';
// import Gateway from 'trinity/Gateway';
import {configure} from 'trinity/Gateway';

// Gateway configuration - Xdebug purposes
configure({
    timeout: 60000,
    fileTimeout: 60000
});

// // Add shortcuts @TODO: move it to some global controller
// Mousetrap.bind('ctrl+alt+n', function() {
//     var newEntryButton = q('.new-entry');
//     if(newEntryButton){
//         window.location.assign(newEntryButton.getAttribute('href'));
//     }
// });

// window.settings = {
//     environment: 'dev',
//     controllersPath: 'app/Controllers',
//     debug: true
// };
// window.App = new AppBuilder(routes, null, settings);
// App.start(function successCall(isRoute){
//     console.log('App Loaded!');
//     if(!isRoute){
//         console.log('INFO: This route doesn\'t have any controller!');
//     }
//     removeLoadingBar();
// }, function errorCall(err){
//     console.error(err);
//     var bar = q('.header-loader .bar');
//     if(bar){
//         bar.style.backgroundColor = "#f00";
//     }
// });

let Application = new App(routes, controllers, {env: 'dev'});

Application.start(function (isRoute){
    console.log('App Loaded!');
    if(!isRoute){
        console.log('INFO: This route doesn\'t have any controller!');
    }
    removeLoadingBar();
}, function (err){
    console.error(err);
    let bar = q('.header-loader .bar');
    if(bar){
        bar.style.backgroundColor = "#f00";
    }
});


function removeLoadingBar() {
    let bars = qAll('.header-loader .bar');
    if(bars.length > 0){
        _.map(bars, function(bar){
            bar.className += ' bar-end';
        });
        let timeoutID = null;
        timeoutID = setTimeout(function(){
            q('.header-loader').style.display = 'none';
            clearTimeout(timeoutID);
        }, 2000);
    }
}

