<?php

namespace AppBundle\Entity\SocialStream;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="social_stream_reload_log")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SocialStream\SocialStreamReloadLogRepository")
 */
class SocialStreamReloadLog
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="timestamp", type="datetime")
     */
    protected $timestamp;

    /**
     * @var boolean
     * @ORM\Column(name="reload_ran", type="boolean", options={"default"=0})
     */
    protected $reloadRan;

    /**
     * @var string
     * @ORM\Column(name="from_ip", type="string", length=45)
     */
    protected $fromIP;

    /**
     * @var int
     * @ORM\Column(name="loaded_posts", type="integer", options={"default"=0})
     */
    protected $loadedPosts;

    /**
     * @var int
     * @ORM\Column(name="loading_duration", type="integer", options={"default"=0})
     */
    protected $loadingDuration;

    /**
     * SocialStreamReloadLog constructor.
     */
    public function __construct()
    {
        $this->timestamp = new \DateTime();
        $this->reloadRan = false;
        $this->loadedPosts = 0;
        $this->loadingDuration = 0;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     * @return $this
     */
    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isReloadRan()
    {
        return $this->reloadRan;
    }

    /**
     * @param boolean $reloadRan
     * @return $this
     */
    public function setReloadRan($reloadRan)
    {
        $this->reloadRan = $reloadRan;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromIP()
    {
        return $this->fromIP;
    }

    /**
     * @param string $fromIP
     * @return $this
     */
    public function setFromIP($fromIP)
    {
        $this->fromIP = $fromIP;

        return $this;
    }

    /**
     * @return int
     */
    public function getLoadedPosts()
    {
        return $this->loadedPosts;
    }

    /**
     * @param int $loadedPosts
     * @return $this
     */
    public function setLoadedPosts($loadedPosts)
    {
        $this->loadedPosts = $loadedPosts;

        return $this;
    }

    /**
     * @return int
     */
    public function getLoadingDuration()
    {
        return $this->loadingDuration;
    }

    /**
     * @param int $loadingDuration
     * @return $this
     */
    public function setLoadingDuration($loadingDuration)
    {
        $this->loadingDuration = $loadingDuration;

        return $this;
    }
}