<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 15:43
 */

namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\Mp3ContentRepository")
 * Class Mp3Content
 *
 * @package AppBundle\Entity\Content
 */
class Mp3Content extends \Venice\AppBundle\Entity\Content\Mp3Content
{
    /**
     * @ORM\Column(type="string")
     */
    protected $Mp3ContentChild;
}