/**
 * Created by fisa on 10/16/15.
 */

import routes from './routes.js';
import _ from 'lodash';
import controllers from './controllers.js';
import App from 'trinity/App';



let Application = new App(routes, controllers);


Application.start(function () {
    console.log('app running');
}, function (err) {
    console.error(err);
    let bar = q('.header-loader .bar');
    if (bar) {
        bar.style.backgroundColor = "#f00";
    }
});

// remove loading bar
var bars = qAll('.header-loader .bar');
if (bars.length > 0) {
    _.map(bars, function (bar) {
        bar.className += ' bar-end';
    });
    var timeoutID = null;
    timeoutID = setTimeout(function () {
        q('.header-loader').style.display = 'none';
        clearTimeout(timeoutID);
    }, 2000);
}
