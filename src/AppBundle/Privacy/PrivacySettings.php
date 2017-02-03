<?php

namespace AppBundle\Privacy;

use AppBundle\Entity\User;

/**
 * Class PrivacySettings
 * @package AppBundle\Privacy
 */
class PrivacySettings
{
    const FORMAT_BIRTH_DATE_NONE = 0;
    const FORMAT_BIRTH_DATE_AGE = 1;
    const FORMAT_BIRTH_DATE_DAY = 2;
    const FORMAT_BIRTH_DATE_FULL = 3;

    /**
     * @var User
     */
    private $user;

    /**
     * @var boolean
     *
     */
    private $publicProfile;

    /**
     * @var boolean
     *
     */
    private $displayFullName;

    /**
     * @var boolean
     *
     */
    private $displayEmail;

    /**
     * @var integer
     *
     */
    private $birthDateStyle;

    /**
     * @var boolean
     *
     */
    private $displayLocation;

    /**
     * @var boolean
     *
     */
    private $displaySocialMedia;

    /**
     * @var boolean
     *
     */
    private $displayForumActivity;

    /**
     * @var boolean
     *
     */
    private $displayProgressGraph;


    /**
     * PrivacySettings constructor.
     */
    public function __construct()
    {
        $this->publicProfile = true;
        $this->displayFullName = true;
        $this->displayEmail = true;
        $this->birthDateStyle = self::FORMAT_BIRTH_DATE_AGE;
        $this->displayLocation = true;
        $this->displayForumActivity = true;
        $this->displayProgressGraph = true;
        $this->displaySocialMedia = true;
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }


    /**
     * @return boolean
     */
    public function isPublicProfile()
    {
        return $this->publicProfile;
    }


    /**
     * @param boolean $publicProfile
     *
     * @return $this
     */
    public function setPublicProfile($publicProfile)
    {
        $this->publicProfile = $publicProfile;

        return $this;
    }


    /**
     * Set displayFullName
     *
     * @param boolean $displayFullName
     *
     * @return PrivacySettings
     */
    public function setDisplayFullName($displayFullName)
    {
        $this->displayFullName = $displayFullName;

        return $this;
    }


    /**
     * Get displayFullName
     *
     * @return boolean
     */
    public function getDisplayFullName()
    {
        return $this->displayFullName;
    }


    /**
     * Set displayEmail
     *
     * @param boolean $displayEmail
     *
     * @return PrivacySettings
     */
    public function setDisplayEmail($displayEmail)
    {
        $this->displayEmail = $displayEmail;

        return $this;
    }


    /**
     * Get displayEmail
     *
     * @return boolean
     */
    public function getDisplayEmail()
    {
        return $this->displayEmail;
    }


    /**
     * Set birthDateStyle
     *
     * @param integer $birthDateStyle
     *
     * @return PrivacySettings
     */
    public function setBirthDateStyle($birthDateStyle)
    {
        if ($birthDateStyle > self::FORMAT_BIRTH_DATE_FULL) {
            $this->birthDateStyle = self::FORMAT_BIRTH_DATE_FULL;
        } else {
            if ($birthDateStyle < 0) {
                $this->birthDateStyle = 0;
            } else {
                if ($birthDateStyle == null) {
                    $this->birthDateStyle = self::FORMAT_BIRTH_DATE_NONE;
                } else {
                    $this->birthDateStyle = $birthDateStyle;
                }
            }
        }

        return $this;
    }


    /**
     * Get birthDateStyle
     *
     * @return integer
     */
    public function getBirthDateStyle()
    {
        return $this->birthDateStyle;
    }


    /**
     * Set displayLocation
     *
     * @param boolean $displayLocation
     *
     * @return PrivacySettings
     */
    public function setDisplayLocation($displayLocation)
    {
        $this->displayLocation = $displayLocation;

        return $this;
    }


    /**
     * Get displayLocation
     *
     * @return boolean
     */
    public function getDisplayLocation()
    {
        return $this->displayLocation;
    }


    /**
     * Set displayForumActivity
     *
     * @param boolean $displayForumActivity
     *
     * @return PrivacySettings
     */
    public function setDisplayForumActivity($displayForumActivity)
    {
        $this->displayForumActivity = $displayForumActivity;

        return $this;
    }


    /**
     * Get displayForumActivity
     *
     * @return boolean
     */
    public function getDisplayForumActivity()
    {
        return $this->displayForumActivity;
    }


    /**
     * Set displayProgressGraph
     *
     * @param boolean $displayProgressGraph
     *
     * @return PrivacySettings
     */
    public function setDisplayProgressGraph($displayProgressGraph)
    {
        $this->displayProgressGraph = $displayProgressGraph;

        return $this;
    }


    /**
     * @return boolean
     */
    public function isDisplaySocialMedia()
    {
        return $this->displaySocialMedia;
    }


    /**
     * @param boolean $displaySocialMedia
     */
    public function setDisplaySocialMedia($displaySocialMedia)
    {
        $this->displaySocialMedia = $displaySocialMedia;
    }


    /**
     * Get displayProgressGraph
     *
     * @return boolean
     */
    public function getDisplayProgressGraph()
    {
        return $this->displayProgressGraph;
    }


    public function disableAll()
    {
        $this->setDisplayFullName(false);
        $this->setDisplayEmail(false);
        $this->setBirthDateStyle(self::FORMAT_BIRTH_DATE_NONE);
        $this->setDisplayLocation(false);
        $this->setDisplayForumActivity(false);
        $this->setDisplayProgressGraph(false);
        $this->setDisplaySocialMedia(false);
    }
}
