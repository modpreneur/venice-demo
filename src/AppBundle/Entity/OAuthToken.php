<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 9:27.
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\OAuthTokenRepository")
 * Class OAuthToken
 */
class OAuthToken extends \Venice\AppBundle\Entity\OAuthToken
{
    /**
     * @ORM\Column(type="string")
     */
    protected $OAuthTokenChild;

    /**
     * @return mixed
     */
    public function getOAuthTokenChild()
    {
        return $this->OAuthTokenChild;
    }

    /**
     * @param mixed $OAuthTokenChild
     */
    public function setOAuthTokenChild($OAuthTokenChild)
    {
        $this->OAuthTokenChild = $OAuthTokenChild;
    }
}
