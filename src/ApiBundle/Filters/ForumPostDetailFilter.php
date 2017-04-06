<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 21.08.15
 * Time: 13:47
 */

namespace ApiBundle\Filters;

use AppBundle\Entity\Vanilla\ForumPost;
use AppBundle\Services\Arrayizer;

/**
 * Class ForumPostDetailFilter
 * @package ApiBundle\Filters
 */
class ForumPostDetailFilter // extends BaseFilter
{
    protected $data;

    /**
     * @param $post
     * @param Arrayizer $arrayizer
     *
     * @return array
     */
    public function filter($post, $arrayizer)
    {
        $output = [];

        $arrayizer->setCallbacks($this->getBeforeCallback(), $this->getEachCallback(), $this->getAfterCallback());
        $arrayizer->setWithout($this->getWithoutArray());

        /** @var  $comment */
        foreach ($post[0]->getComments() as $comment) {
            $arrayedComment = $arrayizer->arrayize($comment);
            $arrayedComment["postId"] = $arrayedComment["discussionId"];
            unset($arrayedComment["discussionId"]);

            $output[] = $arrayedComment;
        }

        return $output;
    }

    /**
     * @return callable
     */
    public function getBeforeCallback()
    {
        return function ($currentObject, $properties, & $propertiesArray) {
            if ($currentObject instanceof ForumPost) {
                $propertiesArray['views'] = $currentObject->getCountViews();
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
            '<ForumPost>.categoryName',
        ];
    }
}