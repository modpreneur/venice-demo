/**
 * Created by Jakub Fajkus on 10.12.15.
 */
import Controller from 'trinity/Controller';
import VeniceForm from '../Libraries/VeniceForm';
import {handleHandleGeneration} from '../Libraries/GlobalLib';
import TrinityTab from 'trinity/components/TrinityTab';
import Events from 'trinity/utils/Events';
import $ from 'jquery';

export default class BlogArticleController extends Controller {

    /**
     * Tabs action
     * @param $scope
     */
    tabsAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.trinityTab = new TrinityTab();

        //On tabs load
        $scope.trinityTab.addListener('tab-load', function (e) {

            $scope.veniceForms = $scope.veniceForms || {};
            if(e.id == 'tab2') {
                let form = e.element.q('form');

                $scope.veniceForms[e.id] = new VeniceForm(form);
                let article = $('#blog_article_content');
                article.froalaEditor(JSON.parse(article[0].getAttribute('data-settings')));

                BlogArticleController._handleHandleGeneration();
            }
        }, this);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newAction($scope) {
        $scope.form = new VeniceForm($('form[name="blog_article"]')[0], VeniceForm.formType.NEW);

        let article = $('#blog_article_content');
        console.log(article);
        // article.froalaEditor(JSON.parse(article[0].getAttribute('data-settings')));
        //
        BlogArticleController._handleHandleGeneration();
    }

    static _handleHandleGeneration() {
        return handleHandleGeneration($('#blog-article-form').attr('data-slugify'),$('#blog_article_title')[0], $('#blog_article_handle')[0]);
    }
}