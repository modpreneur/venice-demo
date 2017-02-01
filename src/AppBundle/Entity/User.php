<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Trinity\NotificationBundle\Annotations as N;
use Venice\AppBundle\Entity\Product\Product;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\UserRepository")
 * Class User
 *
 * @N\Source(columns="necktieId, username, email, firstName, lastName, avatar, locked, phoneNumber, website, country, region, city, addressLine1, addressLine2, postalCode")
 * Users cannot be created on client so there is no need to use POST
 * @N\Methods(types={"put", "delete"})
 */
class User extends \Venice\AppBundle\Entity\User implements \Trinity\Component\Core\Interfaces\UserInterface
{
    const PREFERRED_IMPERIAL = 'imperial';
    const PREFERRED_METRIC   = 'metric';

    const DEFAULT_PREFERRED_METRICS = self::PREFERRED_IMPERIAL;

    const DIR_PICTURES    = 'images/profile/';
    const PROFILE_PICTURE = 'empty_profile.png';
    const PROFILE_AVATAR  = 'empty_profile.png';


    /**
     * @ORM\Column(type="string")
     */
    protected $UserChild;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    protected $maropostSynced;

    private $profilePhoto = null;

    /** @var  \DateTime */
    public $dateOfBirth;

    public $location = 'x';

    public $youtubeLink = 'http://youtube.com/user';

    public $snapchatNickname = 'getSnapchatNickname';

    /**
     * @var \DateTime
     */
    private $lastPasswordChange;


    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->dateOfBirth        = new \DateTime(); //todo;
        $this->lastPasswordChange = new \DateTime();
    }


    /**
     * @return mixed
     */
    public function getUserChild()
    {
        return $this->UserChild;
    }


    /**
     * @param mixed $UserChild
     */
    public function setUserChild($UserChild)
    {
        $this->UserChild = $UserChild;
    }


    /**
     * @param Product $product
     *
     * @return bool
     */
    public function haveAccess(Product $product)
    {
        return $this->hasAccessToProduct($product);
    }


    /**
     * @return null
     */
    public function getProfilePhoto()
    {
        return $this->profilePhoto;
    }


    /**
     * @param bool $profilePhoto
     */
    public function setProfilePhoto($profilePhoto)
    {
        return true;
        //$this->profilePhoto = $profilePhoto;
    }


    /**
     * @param Product $product
     *
     * @return int
     */
    public function daysRemainingToUnlock(Product $product)
    {
        return 0;
        return $product->daysRemainingToUnlock($this);
    }


    /**
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }


    /**
     * @param \DateTime $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }


    public function getLocation()
    {
        return '';
    }


    /**
     * @return \DateTime
     */
    public function getLastPasswordChange(): \DateTime
    {
        return new \DateTime();
        return $this->lastPasswordChange;
    }


    /**
     * @param \DateTime $lastPasswordChange
     */
    public function setLastPasswordChange(\DateTime $lastPasswordChange)
    {
        $this->lastPasswordChange = $lastPasswordChange;
    }


    /**
     * @return bool
     */
    public function getPublic()
    {
        return $this->isPublic();
    }


    /**
     * @return bool
     */
    public function isMaropostSynced()
    {
        return $this->maropostSynced;
    }


    /**
     * @param bool $maropostSynced
     */
    public function setMaropostSynced(bool $maropostSynced)
    {
        $this->maropostSynced = $maropostSynced;
    }
}
