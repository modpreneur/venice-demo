<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="static_page")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class StaticPage
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    protected $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $lastChange;

    /**
     * @var string
     *
     * @ORM\Column(name="handle", type="string", length=255, nullable=true)
     */
    protected $handle;


    /**
     * StaticPage constructor.
     * @param $id
     * @param string $title
     * @param string $content
     * @param string $handle
     */
    public function __construct($id, $title, $content, $handle)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->handle = $handle;
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }


    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }


    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    /**
     * @return \DateTime
     */
    public function getLastChange()
    {
        return $this->lastChange;
    }


    /**
     * @param \DateTime $lastChange
     */
    public function setLastChange($lastChange)
    {
        $this->lastChange = $lastChange;
    }


    /**
     *  @ORM\PrePersist
     */
    public function setChangedTimestamp()
    {
        $this->lastChange = new \DateTime();
    }


    /**
     *  @ORM\PreUpdate
     */
    public function updateChangedTimestamp()
    {
        $this->lastChange = new \DateTime();
    }
}
