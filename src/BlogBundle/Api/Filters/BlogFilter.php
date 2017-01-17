<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 20.07.15
 * Time: 12:32
 */

namespace GeneralBackend\BlogBundle\Api\Filters;

use GeneralBackend\BlogBundle\Entity\Post;
use GeneralBackend\CoreBundle\Api\Filters\BaseFilter;
use GeneralBackend\CoreBundle\Services\Arrayizer;

class BlogFilter extends BaseFilter
{
    /**
     * @param $data
     * @param Arrayizer $arrayizer
     * @return array
     */
    public function filter($data, $arrayizer)
    {
        $blogPosts = $data;

        $arrayizer->setWithout($this->getWithoutArray());
        $arrayizer->setCallbacks(
            $this->getBeforeCallback(),
            $this->getEachCallback(),
            $this->getAfterCallback()
        );

        $output = [];

        foreach ($blogPosts as $blogPost)
        {
            $output[] = $arrayizer->arrayize($blogPost);
        }

        return $output;
    }

    /**
     * @return callable
     */
    public function getBeforeCallback()
    {
        return function($currentObject, $properties, & $propertiesArray){
            if($currentObject instanceof Post)
            {
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
            '<GlobalUser>.products',
            '<GlobalUser>.privacySettings',
            '<GlobalUser>.groups',
            '<GlobalUser>.tags.posts',
            '<Post>.publisher',
            '<Post>.category',
            '<Post>.lastAllowedDotPosition',
            '<Post>.maxCountOfCharacters',
        ];
    }
}