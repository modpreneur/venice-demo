<?php

namespace AppBundle\Entity\Vanilla;

/**
 * Class Comment
 * @package AppBundle\Entity\Vanilla
 */
class Comment
{
    protected $id;

    protected $discussionId;

    protected $author;

    protected $body;

    protected $lastEdit;


    /**
     * Comment constructor.
     *
     * @param $id
     * @param $discussionId
     * @param $author
     * @param $body
     * @param $lastEdit
     *
     */
    public function __construct($id, $discussionId, $author, $body, $lastEdit)
    {
        $this->id = $id;
        $this->discussionId = $discussionId;
        $this->author = $author;
        $this->body = $body;
        $this->lastEdit = $lastEdit;
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
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getDiscussionId()
    {
        return $this->discussionId;
    }


    /**
     * @param mixed $discussionId
     *
     * @return $this
     */
    public function setDiscussionId($discussionId)
    {
        $this->discussionId = $discussionId;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }


    /**
     * @param mixed $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
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
     *
     * @return $this
     */
    public function setLastEdit($lastEdit)
    {
        $this->lastEdit = $lastEdit;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }


    /**
     * @param mixed $author
     *
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }
}
