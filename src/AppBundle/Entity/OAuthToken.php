<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 9:27
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\OAuthTokenRepository")
 * Class OAuthToken
 * @package AppBundle\Entity
 */
class OAuthToken extends \Venice\AppBundle\Entity\OAuthToken
{
    /**
     * @ORM\Column(type="string")
     */
    protected $OAuthTokenChild;
}