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
     * @var ProfilePhoto
     *
     * @ORM\OneToOne(targetEntity="ProfilePhoto", inversedBy="user", cascade={"persist","merge","remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="profile_photo_id", referencedColumnName="id", nullable=true)
     */
    protected $profilePhoto;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $UserChild;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    protected $maropostSynced;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_of_birth_v", type="date", nullable=true, options={"default" = null})
     */
    public $dateOfBirth;


    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $location;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $youtubeLink;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $snapchatNickname;

    /**
     * @var \DateTime
     */
    private $lastPasswordChange;


    /**
     * User constructor.
     */
    public function __construct()
    {
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
     * Set profilePhoto
     *
     * @param ProfilePhoto $profilePhoto
     * @return User
     */
    public function setProfilePhoto(ProfilePhoto $profilePhoto = null)
    {
        $this->profilePhoto = $profilePhoto;

        if ($profilePhoto) {
            $this->profilePhoto->setUser($this);
        }

        return $this;
    }


    /**
     * Get profilePhoto
     *
     * @return ProfilePhoto
     */
    public function getProfilePhoto()
    {
        return $this->profilePhoto;
    }


    /**
     * @param Product $product
     *
     * @return int
     */
    public function daysRemainingToUnlock(Product $product)
    {
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


    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }


    /**
     * @return \DateTime
     */
    public function getLastPasswordChange(): \DateTime
    {
        return new \DateTime(); // @todo
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


    /**
     * @return int
     */
    public function getAge()
    {
        $date = $this->getDateOfBirth();

        if (!$date) {
            return null;
        }

        $now  = new \DateTime();
        return $now->diff($date)->y;
    }
}
