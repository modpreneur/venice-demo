/**
 * Created by Jakub Fajkus on 28.12.15.
 */

import Controller from 'trinity/Controller';
import TrinityTab from 'trinity/components/TrinityTab';
import _ from 'lodash';
import VeniceForm from '../Libraries/VeniceForm';

export default class ContetntController extends Controller {

    /**
     * @param $scope
     */
    tabsAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.trinityTab = new TrinityTab();

        //On tabs load
        $scope.trinityTab.addListener('tab-load', function(e) {
            if(e.id === 'tab2') {
                let form = e.element.q('form');
                $scope.veniceForms = $scope.veniceForms || {};
                $scope.veniceForms[e.id] = new VeniceForm(form);
                $scope.veniceForms['tab2'].success(()=>{
                    $scope.trinityTab.reload('tab1');
                });
            }
        }, this);
    }
}