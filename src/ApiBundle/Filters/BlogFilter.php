<?php
/**
 * Created by PhpThunderStorm.
 * User: marek
 * Date: 20.07.18
 * Time: 12:32
 */

namespace ApiBundle\Filters;

use AppBundle\Entity\BlogArticle;
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
    public function filter(array $data, Arrayizer $arrayizer)
    {
        $blogBlogArticles = $data;

        $arrayizer->setWithout($this->getWithoutArray());
        $arrayizer->setCallbacks(
            $this->getBeforeCallback(),
            $this->getEachCallback(),
            $this->getAfterCallback()
        );

        $output = [];

        /** @var BlogArticle $blogBlogArticle */
        foreach ($blogBlogArticles as $blogBlogArticle) {
            $output[] = $arrayizer->arrayize($blogBlogArticle);
        }

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
                $propertiesArray['author'] = $author->getFirstName() . ' ' . $author->getLastName();
                $propertiesArray['authorUsername'] = $author->getUsername();
                $propertiesArray['dateWritten'] = $currentObject->getCreatedAt()->format('Y-m-d H:i:s');
            }
        };
    }

    /**
     * @return callable
     */
    public function getEachCallback()
    {
        return function ($currentObject, $propertyName, $propertyValue, & $propertiesArray) {

        };
    }

    /**
     * @return callback
     */
    public function getAfterCallback()
    {
        return function (& $arrayedObject) {

        };
    }

    /**
     * @return array
     */
    public function getWithoutArray()
    {
        return [
            '<User>.products',
            '<User>.productAccesses',
            '<User>.privacySettings',
            '<User>.groups',
            '<User>.tags.blogArticles',
            '<User>.blogArticles',
            '<BlogArticle>.publisher',
            '<BlogArticle>.products',
            '<BlogArticle>.category',
            '<BlogArticle>.categories',
            '<BlogArticle>.tags',
            '<BlogArticle>.lastAllowedDotPosition',
            '<BlogArticle>.maxCountOfCharacters',
            '<BlogArticle>.BlogArticleChild',
            '<BlogArticle>.updatedAt',
            '<BlogArticle>.createdAt',
        ];
    }
}
