<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:36.
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Trinity\NotificationBundle\Annotations as N;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\UserRepository")
 * Class User
 *
 * @N\Source(columns="necktieId, username, email, firstName, lastName, avatar, locked, phoneNumber, website, country, region, city, addressLine1, addressLine2, postalCode")
 * Users cannot be created on client so there is no need to use POST
 * @N\Methods(types={"put", "delete"})
 */
class User extends \Venice\AppBundle\Entity\User
{
    /**
     * @ORM\Column(type="string")
     */
    protected $UserChild;


    private $profilePhoto = null;


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


    public function haveAccess($e)
    {
        return true;
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
    public function setProfilePhoto( $profilePhoto)
    {
        $this->profilePhoto = $profilePhoto;
    }


    public function daysRemainingToUnlock($x)
    {
        return 10;
    }
}
