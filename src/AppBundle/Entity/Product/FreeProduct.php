<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:29.
 */
namespace AppBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\FreeProductRepository")
 * Class FreeProduct
 */
class FreeProduct extends \Venice\AppBundle\Entity\Product\FreeProduct
{
    /**
     * @ORM\Column(type="string")
     */
    protected $FreeProductChild;

    /**
     * @return mixed
     */
    public function getFreeProductChild()
    {
        return $this->FreeProductChild;
    }

    /**
     * @param mixed $FreeProductChild
     */
    public function setFreeProductChild($FreeProductChild)
    {
        $this->FreeProductChild = $FreeProductChild;
    }
}
