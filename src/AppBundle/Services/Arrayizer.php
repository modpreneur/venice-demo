<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.07.15
 * Time: 13:48
 */

namespace AppBundle\Services;

use Doctrine\Common\Collections\Collection;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class Arrayizer
 *
 * NOTE: All entities should have all properties at least protected,
 * so child object would have access to parent's properties.
 *
 * @package GeneralBackend\CoreBundle\Services
 */
class Arrayizer
{

    /**
     * @var array array of strings
     */
    protected $without;

    /**
     * this callback receives data:
     *  $currentObject - object, which is being converted to array
     *  $properties - array of all objects properties
     *  &$propertiesArray - array, which will contain object's data. When this callback is called, it's empty
     *
     * @var callback callback, which is called for each object once
     */
    protected $before;

    /**
     * this callback receives data:
     *  $currentObject - object, which is being converted to array
     *  $propertyName - name of the current property being converted
     *  $propertyValue - value of the current property being converted
     *  &$propertiesArray - array, which will contain object's data.
     *      This array is being filled in the moment of calling this callback
     * @var callback callback, which is called for each property of object
     */
    protected $each;

    /**
     * this callback receives data:
     *  $propertiesArray - object converted to array
     * @var callback callback, which is called for each object once
     */
    protected $after;

    /**
     * Arrayizer constructor.
     */
    public function __construct()
    {
        $this->without = [];
        $this->before = null;
        $this->each = null;
        $this->after = null;
    }

    /**
     * @param array $without
     */
    public function setWithout(array $without = [])
    {
        $this->without = $without;
    }

    /**
     * @param callable $before
     * @param callable $each
     * @param callable $after
     */
    public function setCallbacks(callable $before = null, callable $each = null, callable $after = null)
    {
        $this->before = $before;
        $this->each = $each;
        $this->after = $after;
    }

    /**
     * Convert given object to array
     *
     * @param $entity object to convert
     * @return array
     */
    public function arrayize($entity)
    {
        return $this->process($entity, $this->without);
    }

    /**
     *
     * @param $entity
     * @param array $without
     * @return array
     */
    protected function process($entity, array $without = [])
    {
        if ($entity == null) {
            return [];
        }
        /** @var string $className name of the entity class with full namespace */
        $className = get_class($entity);

        /* modify array $without: delete current class name from the beginning of each string */
        /** @var array $newWithout array with modified without parameters, which is passed to child toArray() call*/
        $newWithout = $this->modifyWithoutArray($without, $entity);

        /* create a reflection of current class */
        /** @var array $propertiesArray array of object's properties, converted to array*/
        $propertiesArray = [];

        /** @var ReflectionClass $reflection reflection of current object*/
        $reflection = new ReflectionClass($entity);

        /*  properties of the current entity*/
        $properties = $reflection->getProperties();

        /*
         * if there is an before callback function, call it
         * this gives an chance to work with the object before it's converted to array
         * this callback is called only once
         * $propertiesArray is passed by reference, so it is possible to modify it, e.g. add an custom array key
         */
        if ($this->before) {
            call_user_func_array($this->before, array($currentObject = $entity, $properties, &$propertiesArray));
        }

        foreach ($properties as $property) {
            /** @var ReflectionProperty $properties */
            $propertyName = $property->getName();
            $property->setAccessible(true);
            $propertyValue = $property->getValue($entity);

            /*
             * if there is an each callback function, call it
             * this gives an chance to work with object properties
             * this callback is called with every property of the object
             * $propertiesArray is passed by reference, so it is possible to modify it, e.g. add an custom array key
             */
            if ($this->each) {
                call_user_func_array(
                    $this->each,
                    array($currentObject = $entity, $propertyName, $propertyValue, &$propertiesArray)
                );
            }

            /*
             * if the current class name and property matches with without array OR
             *    the current property matches with without array
             *
             * skip current property
             */
            if (in_array($className . '.' . $propertyName, $without) || in_array($propertyName, $without)) {
                continue;
            }

            $continueCycle = false;
            foreach ($without as $withoutString) {
                $slug = $this->getSlug($withoutString);
                /* if there is an slug, e.g. <Product> */
                if ($slug) {
                    // explode an string like Class1.Class2.property and make an array['Class1', 'Class2', 'property']
                    $exploded = explode('.', $withoutString);

                    /*
                     * if there were used absolute class names, e.g. Namespace\ClassName or Namespace\ClassName.field
                     *  skip current iteration
                     */
                    if (strpos(strtolower($exploded[0]), $slug) !== false  && $exploded[1] == $propertyName) {
                        /* skip current property iteration */
                        $continueCycle = true;
                        break;
                    }
                }
            }

            if ($continueCycle) {
                continue;
            }

            /* if the property object implements Collection interface */
            if ($propertyValue instanceof Collection || is_array($propertyValue)) {
                /* call process on each item in collection */
                foreach ($propertyValue as $collectionItem) {
                    $propertiesArray[$propertyName][] = $this->process($collectionItem, $newWithout);
                }
            } /* if the property object, convert it to a string*/
            elseif ($propertyValue instanceof \DateTime) {
                $propertiesArray[$propertyName] = $propertyValue->format('Y-m-d H:i:s');
            } /* if is it an other object, call the toArray method on it */
            elseif (is_object($propertyValue)) {
                $propertiesArray[$propertyName] = $this->process($propertyValue, $newWithout);
            } /* it is not an object, so simply copy it's value to array */
            else {
                $propertiesArray[$propertyName] = $propertyValue;
            }
        }

        /*
         * if there is an after callback function, call it
         * this gives a chance to work with the arrayed object
         * this callback is called only once for each object
         * $propertiesArray is passed by reference, so it is possible to modify it,
         *   e.g. add an custom array key , rename it or delete keys, which are not needed
         */
        if ($this->after) {
            /* call after function and pass in arrayed object */
            call_user_func_array($this->after, array(& $propertiesArray));
        }

        return $propertiesArray;
    }

    /**
     * Get slug value
     *
     * @param  classname
     * @return bool false - if the slug was not found
     * @return string value of the slug
     */
    protected function getSlug($string)
    {
        $left = strpos($string, '<');
        $right = strpos($string, '>');

        if ($left === false || $right === false) {
            return false;

        }

        if ($left < $right) {
            return strtolower(substr($string, $left + 1, $right - $left - 1));
        }
    }

    /**
     * Delete current class name from the beginning of each string in $without array
     *
     * @param $without array of strings
     * @return array modified array of strings
     */
    protected function modifyWithoutArray($without, $entity)
    {
        $className = get_class($this);
        $newWithout = [];

        foreach ($without as $withoutString) {
            /** @var array $exploded array of exploded without parameters*/
            $exploded = explode('.', $withoutString);

            /** @var string $slug shorthand for class name*/
            $slug = $this->getSlug($withoutString);

            /*
             * if the without option contains current class name at the first position OR
             *    there is an slug in the current class name
             *
             * delete class name at the first position(it was current class name and it was already used)
             */
            if ($exploded[0] == $className || strpos(strtolower($exploded[0]), $slug) !== false) {
                unset($exploded[0]);
            }

            /* create a new without option, which is ready to be passed to child toArray method */
            $newWithout[] = implode('.', $exploded);
        }

        return $newWithout;
    }
}
