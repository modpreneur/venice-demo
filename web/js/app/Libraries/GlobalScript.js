/**
 * Created by fisa on 11/6/15.
 */
'use strict';
import {LayoutMenu} from 'venice-js/Libraries/LayoutLib';
import $ from 'jquery';

export default function globalFunction(){
    console.log('Global function');
    //setting active class to menu tab

    //It has to be there...! Question @RichardBures!
    LayoutMenu.setCurrent($('#sidebar'));
    LayoutMenu.unCheckRadio($('.navbar-item-input'));
}