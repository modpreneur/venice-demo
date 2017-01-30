<?php

namespace AppBundle\Entity\SocialStream;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="social_site")
 * @ORM\Entity
 */
class SocialSite
{
    const TWITTER = 'twitter';
    const FACEBOOK = 'facebook';
    const INSTAGRAM = 'instagram';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $type; //eg. facebook, twitter

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $account;

    /**
     * @ORM\Column(type="boolean", options={"default"=true})
     * @var bool
     */
    protected $enabled;


    /**
     * SocialSite constructor.
     *
     * @param $type
     * @param $token
     * @param $tokenSecret
     */
    public function __construct($type, $token, $tokenSecret)
    {
        $this->type = $type;
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;
        $this->enabled = true;
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
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }


    /**
     * @param mixed $account
     *
     * @return $this
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }


    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }


    /**
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
