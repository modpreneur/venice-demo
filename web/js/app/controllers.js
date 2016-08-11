'use strict';

import inherited from 'venice-js/controllers';
import $ from 'jquery';
import ExampleController from './Controllers/ExampleController';
import GlobalController from './Controllers/GlobalController';

let controllers  = {
    'ExampleController': ExampleController,
    'GlobalController' : GlobalController
};

export default $.extend({}, inherited, controllers);