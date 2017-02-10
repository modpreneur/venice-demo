<?php

namespace AppBundle\Entity\SocialStream;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="social_post", options={"collate":"utf8mb4_general_ci", "charset":"utf8mb4"})
 * @ORM\Entity
 */
class SocialPost
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $type; // eg. facebook, twitter

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $author; // name...

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $dateTime;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $message;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $profilePic;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $url;


    /**
     * SocialPost constructor.
     *
     * @param $type
     * @param $author
     * @param $dateTime
     * @param $message
     * @param $profilePic
     * @param $url
     */
    public function __construct($type, $author, $dateTime, $message, $profilePic, $url)
    {
        $this->type = $type;
        $this->author = $author;
        $this->dateTime = $dateTime;
        $this->message = $message;
        $this->profilePic = $profilePic;
        $this->url = $url;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getDateTime()
    {
        return $this->dateTime;
    }


    /**
     * @param mixed $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }


    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


    /**
     * @return mixed
     */
    public function getProfilePic()
    {
        return $this->profilePic;
    }


    /**
     * @param mixed $profilePic
     */
    public function setProfilePic($profilePic)
    {
        $this->profilePic = $profilePic;
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
}
