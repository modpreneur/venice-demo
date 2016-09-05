<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\ProductAccessRepository")
 * ProductAccess
 *
 * @UniqueEntity(fields={"user", "product"}, errorPath="product")
 */
class ProductAccess extends \Venice\AppBundle\Entity\ProductAccess
{
    /**
     * @ORM\Column(type="string")
     */
    protected $ProductAccessChild;

    /**
     * @return mixed
     */
    public function getProductAccessChild()
    {
        return $this->ProductAccessChild;
    }

    /**
     * @param mixed $ProductAccessChild
     */
    public function setProductAccessChild($ProductAccessChild)
    {
        $this->ProductAccessChild = $ProductAccessChild;
    }
}
