<?php
/**
 * Created by PhpThunderStorm.
 * User: marek
 * Date: 20.07.18
 * Time: 12:32
 */

namespace ApiBundle\Filters;

use AppBundle\Entity\BlogArticle;
use ApiBundle\Filters\BaseFilter;
use AppBundle\Services\Arrayizer;

/**
 * Class BlogFilter
 * @package ApiBundle\Filters
 */
class BlogFilter
{
    /**
     * @param $data
     * @param Arrayizer $arrayizer
     * @return array
     */
    public function filter($data, $arrayizer)
    {
        $blogBlogArticles = $data;

        $arrayizer->setWithout($this->getWithoutArray());
        $arrayizer->setCallbacks(
            $this->getBeforeCallback(),
            $this->getEachCallback(),
            $this->getAfterCallback()
        );

        $output = [];

        foreach ($blogBlogArticles as $blogBlogArticle) {
            $output[] = $arrayizer->arrayize($blogBlogArticle);
        }

        var_dump($output);
        die;

        return $output;
    }

    /**
     * @return callable
     */
    public function getBeforeCallback()
    {
        /**
         * @param $currentObject
         * @param $properties
         * @param $propertiesArray
         */
        return function ($currentObject, $properties, & $propertiesArray) {
            if ($currentObject instanceof BlogArticle) {
                $author = $currentObject->getPublisher();
                $propertiesArray['author'] = $author->getFirstName() . " " . $author->getLastName();
                $propertiesArray['authorUsername'] = $author->getUsername();
            }
        };
    }

    /**
     * @return callable
     */
    public function getEachCallback()
    {
        return function($currentObject, $propertyName, $propertyValue, & $propertiesArray){

        };
    }

    /**
     * @return callback
     */
    public function getAfterCallback()
    {
        return function(& $arrayedObject) {

        };
    }

    /**
     * @return array
     */
    public function getWithoutArray()
    {
        return [
            '<User>.products',
            '<User>.privacySettings',
            '<User>.groups',
            '<User>.tags.BlogArticles',
            '<BlogArticle>.publisher',
            '<BlogArticle>.category',
            '<BlogArticle>.lastAllowedDotPosition',
            '<BlogArticle>.maxCountOfCharacters',
        ];
    }
}