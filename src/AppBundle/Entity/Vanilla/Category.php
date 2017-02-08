<?php

namespace AppBundle\Entity\Vanilla;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Category
 * @package AppBundle\Entity\Vanilla
 */
class Category
{
    protected $id;
    protected $countOfDiscussions;
    protected $countOfComments;
    protected $name;
    protected $description;
    protected $lastEdit;
    protected $discussions;

    /**
     * Category constructor.
     *
     * @param $id
     * @param $countOfDiscussions
     * @param $countOfComments
     * @param $name
     * @param $description
     * @param $lastEdit
     */
    public function __construct($id, $countOfDiscussions, $countOfComments, $name, $description, $lastEdit)
    {
        $this->id = $id;
        $this->countOfDiscussions = $countOfDiscussions;
        $this->countOfComments = $countOfComments;
        $this->name = $name;
        $this->description = $description;
        $this->lastEdit = $lastEdit;
        $this->discussions = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCountOfDiscussions()
    {
        return $this->countOfDiscussions;
    }

    /**
     * @param mixed $countOfDiscussions
     */
    public function setCountOfDiscussions($countOfDiscussions)
    {
        $this->countOfDiscussions = $countOfDiscussions;
    }

    /**
     * @return mixed
     */
    public function getCountOfComments()
    {
        return $this->countOfComments;
    }

    /**
     * @param mixed $countOfComments
     */
    public function setCountOfComments($countOfComments)
    {
        $this->countOfComments = $countOfComments;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLastEdit()
    {
        return $this->lastEdit;
    }

    /**
     * @param mixed $lastEdit
     */
    public function setLastEdit($lastEdit)
    {
        $this->lastEdit = $lastEdit;
    }

    /**
     * @return mixed
     */
    public function getDiscussions()
    {
        return $this->discussions;
    }


    public function addDiscussion(Discussion $discussion)
    {
        $this->discussions->add($discussion);

        return $this;
    }
}