<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 14:49
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\ContentProductRepository")
 * Class ContentProduct
 *
 * @package AppBundle\Entity
 */
class ContentProduct extends \Venice\AppBundle\Entity\ContentProduct
{
    /**
     * @ORM\Column(type="string")
     */
    protected $ContentProductChild;
}