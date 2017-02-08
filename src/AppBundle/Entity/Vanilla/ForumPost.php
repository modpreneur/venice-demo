<?php

namespace AppBundle\Entity\Vanilla;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ForumPost
 * @package AppBundle\Entity\Vanilla
 */
class ForumPost
{
    protected $id;

    protected $url;

    protected $name;

    protected $body;

    protected $categoryId;

    protected $categoryName;

    protected $lastDate;

    protected $countViews;

    protected $countComments;

    protected $author;

    protected $comments;

    protected $lastCommentDateTime;

    protected $authorName;


    /**
     * ForumPost constructor.
     *
     * @param      $id
     * @param      $url
     * @param      $name
     * @param      $body
     * @param      $categoryId
     * @param      $categoryName
     * @param      $lastDate
     * @param      $countViews
     * @param      $countComments
     * @param      $author
     * @param      $lastCommentDateTime
     * @param      $authorName
     */
    public function __construct(
        $id,
        $url,
        $name,
        $body,
        $categoryId,
        $categoryName,
        $lastDate,
        $countViews,
        $countComments,
        $author,
        $lastCommentDateTime = null,
        $authorName = null
    ) {
        $this->id = $id;
        $this->url = $url;
        $this->name = $name;
        $this->body = $body;
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->lastDate = $lastDate;
        $this->countViews = $countViews;
        $this->countComments = $countComments;
        $this->author = $author;
        $this->lastCommentDateTime = $lastCommentDateTime;
        $this->authorName = $authorName;
        $this->comments = new ArrayCollection();
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
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
    public function getCategoryId()
    {
        return $this->categoryId;
    }


    /**
     * @param $categoryId
     *
     * @internal param mixed $category
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }


    /**
     * @return mixed
     */
    public function getLastDate()
    {
        return $this->lastDate;
    }


    /**
     * @param mixed $lastDate
     */
    public function setLastDate($lastDate)
    {
        $this->lastDate = $lastDate;
    }


    /**
     * @return mixed
     */
    public function getCountViews()
    {
        return $this->countViews;
    }


    /**
     * @param mixed $countViews
     */
    public function setCountViews($countViews)
    {
        $this->countViews = $countViews;
    }


    /**
     * @return mixed
     */
    public function getCountComments()
    {
        return $this->countComments;
    }


    /**
     * @param mixed $countComments
     */
    public function setCountComments($countComments)
    {
        $this->countComments = $countComments;
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
     */
    public function setAuthor($author)
    {
        $this->author = $author;
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
     */
    public function setBody($body)
    {
        $this->body = $body;
    }


    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }


    /**
     * @param Comment $comment
     *
     * @return $this
     */
    public function addComment(Comment $comment)
    {
        $this->comments->add($comment);

        return $this;
    }


    /**
     * @return mixed
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }


    /**
     * @param mixed $categoryName
     *
     * @return $this
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;

        return $this;
    }


    /**
     * @return null
     */
    public function getLastCommentDateTime()
    {
        return $this->lastCommentDateTime;
    }


    /**
     * @param DateTime|null $lastCommentDateTime
     *
     * @return $this
     */
    public function setLastCommentDateTime(DateTime $lastCommentDateTime)
    {
        $this->lastCommentDateTime = $lastCommentDateTime;

        return $this;
    }


    /**
     * @return null
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }


    /**
     * @param null $authorName
     *
     * @return $this
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;

        return $this;
    }
}
