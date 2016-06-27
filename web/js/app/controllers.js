'use strict';
import inherited from 'venice-js/controllers';
import $ from 'jquery';
import ExampleController from './Controllers/ExampleController';

let controllers  = {'ExampleController': ExampleController};

export default $.extend({},inherited,controllers);