/**
 * Created by Jakub on 21.12.15.
 */

import Events from 'trinity/utils/Events';
import Controller from 'trinity/Controller';
import TrinityTab from 'trinity/components/TrinityTab';
import Collection from 'trinity/Collection';
import _ from 'lodash';
import VeniceForm from '../Libraries/VeniceForm';
import $ from 'jquery';
import {slugify, handleHandleGeneration} from '../Libraries/GlobalLib';
import FormChanger from '../Libraries/FormChanger';

export default class ContetntController extends Controller {

    newAction($scope) {
        let select = $('#entity_type_select')[0],
            jqFormDiv = $('#javascript-inserted-form'),
            url = jqFormDiv.attr('data-changer'),
            unlisteners = [];

        let func = () => {
            _.each(unlisteners,(unListener)=>{unListener();});
            let type = select.options[select.selectedIndex].value;
            FormChanger.refreshForm(jqFormDiv[0], url.replace('contentType', type), () => {

                $scope.form = new VeniceForm($(`form[name="${type}_content"]`)[0], VeniceForm.formType.NEW);
                if (type == 'html' || type == 'iframe') {
                    let jqueryContElem = $(`#${type}_content_html`);
                    jqueryContElem.froalaEditor(JSON.parse(jqueryContElem[0].getAttribute('data-settings')));
                }
                unlisteners.push(
                    ContetntController._handleHandleGeneration()
                );
            });
        };
        func();

        //render new form after change
        Events.listen(select, 'change', func);
    }

    tabsAction($scope) {
        $scope.trinityTab = new TrinityTab();
        let unlisteners= [];
        //On tabs load
        $scope.trinityTab.addListener('tab-load', e => {
            let form = e.element.q('form');
            if(form){
                $scope.veniceForms = $scope.veniceForms || {};
                $scope.veniceForms[e.id] = new VeniceForm(form);
            }

            //Edit tab
            if (e.id === 'tab2') {
                // Collection
                $scope.collection = _.map($('[data-prototype]'), (node)=> {
                    return new Collection(node, {addFirst: false, label: true});
                });

                let formName = form.getAttribute('name');
                let contentType = formName.substr(0, formName.indexOf('_'));
                let jqFormCont = $(`#${contentType}_content_html`);

                if(contentType == 'html' || contentType == 'iframe') {
                    jqFormCont.froalaEditor(JSON.parse(jqFormCont[0].getAttribute('data-settings')));
                }

                $scope.veniceForms['tab2'].success(()=>{
                    $scope.trinityTab.reload('tab1');
                });
                unlisteners.push(ContetntController._handleHandleGeneration());
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
     * New standard product action
     * @param $scope
     */
    newContentProductAction($scope) {
        //Attach VeniceForm
        $scope.form = new VeniceForm($('form[name="content_product_type_with_hidden_content"]')[0], VeniceForm.formType.NEW);
    }

    contentProductTabsAction($scope) {
        $scope.trinityTab = new TrinityTab();
        //On tabs load
        $scope.trinityTab.addListener('tab-load',  e => {
            if(e.id == 'tab2') {
                $scope.veniceForms = $scope.veniceForms || {};
                $scope.veniceForms[e.id] = new VeniceForm(e.element.q('form'));
                $scope.veniceForms['tab2'].success(()=> {
                    $scope.trinityTab.reload('tab1');
                });
            }
        }, this);
    }

    static _handleHandleGeneration() {
        return handleHandleGeneration($('#javascript-inserted-form').attr('data-slugify'),$('#group_content_name')[0], $('#group_content_handle')[0])
    }
}