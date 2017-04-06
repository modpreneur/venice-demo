<?php
/**
 * Created by PhpStorm.
 * User: mmate
 * Date: 17.08.2015
 * Time: 15:36
 */

namespace ApiBundle\Filters;

use AppBundle\Entity\Vanilla\ForumPost;

/**
 * Class ForumPostsFilter
 *
 *
 * @package ApiBundle\Filters
 */
class ForumPostsFilter // extends BaseFilter
{
    /**
     * @var array additional data, which is needed by filter
     */
    protected $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function filter($data, $arrayizer)
    {
        $output = [];

        $arrayizer->setCallbacks($this->getBeforeCallback(), $this->getEachCallback(), $this->getAfterCallback());
        $arrayizer->setWithout($this->getWithoutArray());

        foreach ($data as $dataRow) {
            $output[] = $arrayizer->arrayize($dataRow);
        }

        return $output;
    }

    public function getOrderOfProductsInversed($user)
    {
        $ids = [];

        return $ids;
    }

    //---------------------------------------------------------------------------------------------------------------

    /**
     * @return callable
     */
    public function getBeforeCallback()
    {
        return function ($currentObject, $properties, & $propertiesArray) {

            if ($currentObject instanceof ForumPost) {
                $propertiesArray['views'] = $currentObject->getCountViews();
                $propertiesArray['comments'] = $currentObject->getCountComments();
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
            unset($arrayedObject['firstName']);
            unset($arrayedObject['lastName']);
            unset($arrayedObject['countViews']);
            unset($arrayedObject['countComments']);
        };
    }

    /**
     * @return array
     */
    public function getWithoutArray()
    {
        return [
            '<ForumPost>.url',
            '<ForumPost>.categoryName'
        ];
    }
}