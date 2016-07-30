<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:29
 */

namespace AppBundle\Entity\Product;


use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\FreeProductRepository")
 * Class FreeProduct
 * @package AppBundle\Entity\Product
 */
class FreeProduct extends \Venice\AppBundle\Entity\Product\FreeProduct
{
    /**
     * @ORM\Column(type="string")
     */
    protected $FreeProductChild;
}