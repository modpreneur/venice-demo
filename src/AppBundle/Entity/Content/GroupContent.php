<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 16:03
 */

namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\GroupContentRepository")
 * Class GroupContent
 */
class GroupContent extends \Venice\AppBundle\Entity\Content\GroupContent
{
    /**
     * @ORM\Column(type="string")
     */
    protected $GroupContentChild;
}