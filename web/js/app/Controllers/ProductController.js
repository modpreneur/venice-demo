/**
 * Created by Jakub Fajkus on 10.12.15.
 */


import Events from 'trinity/utils/Events';
import VeniceForm from '../Libraries/VeniceForm';
import TrinityTab from 'trinity/components/TrinityTab';
import {handleHandleGeneration} from '../Libraries/GlobalLib';
import _ from 'lodash';
import Controller from 'trinity/Controller';
import $ from 'jquery';

export default class ProductController extends Controller {

    //indexAction($scope) {
    //    $scope.productGrid = GridBuilder.build(q.id('product-grid'), this.request.query);
    //}


    newAction($scope) {
        $scope.form = new VeniceForm($('form[name="free_product"]')[0], VeniceForm.formType.NEW);
        ProductController._handleHandleGeneration();
    }

    tabsAction($scope) {
        $scope.trinityTab = new TrinityTab();
        let unlisteners = [];
        //On tabs load
        $scope.trinityTab.addListener('tab-load', function (e) {

            $scope.veniceForms = $scope.veniceForms || {};
            switch(e.id){
                // Edit
                case 'tab2': {
                    let form = e.element.q('form');
                    $scope.veniceForms[e.id] = new VeniceForm(form);

                    unlisteners.push(ProductController._handleHandleGeneration());

                    $scope.veniceForms['tab2'].success(()=>{
                        $scope.trinityTab.reload('tab1');
                    });
                } break;
            }
        }, this);

        $scope.trinityTab.addListener('tab-unload', function(e) {
            switch(e.id){
                // Edit
                case 'tab2': {
                    _.each(unlisteners,(unListener)=>{unListener();});
                } break;
                default : break;
            }
            // All
            if($scope.veniceForms[e.id]){
                $scope.veniceForms[e.id].detach();
            }
        });
    }

    /**
     *
     * @param $scope
     */
    newContentProductAction($scope) {
        $scope.form = new VeniceForm(q('form[name="content_product_type_with_hidden_product"]'), VeniceForm.formType.NEW);
    }

    static _handleHandleGeneration() {
        return handleHandleGeneration($('#product_form').attr('data-slugify'),$('#free_product_name')[0], $('#free_product_handle')[0]);
    }
}