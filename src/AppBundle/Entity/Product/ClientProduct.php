<?php

namespace AppBundle\Entity\Product;


use Doctrine\ORM\Mapping as ORM;
use Venice\AppBundle\Entity\Product\Product;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\StandardProductRepository")
 *
 * Class ClientProduct
 * @package AppBundle\Entity\Product
 */
class ClientProduct extends Product
{
    /**
     * @ORM\Column(type="string")
     */
    protected $ClientProductChild;


    public function __construct()
    {
        parent::__construct();

        $this->ClientProductChild = 'standard product from application';
    }

    /**
     * @return mixed
     */
    public function getClientProductChild()
    {
        return $this->ClientProductChild;
    }

    /**
     * @param mixed $ClientProductChild
     */
    public function setClientProductChild($ClientProductChild)
    {
        $this->ClientProductChild = $ClientProductChild;
    }

    /**
     * Get the product type string
     *
     * @return string
     */
    public function getType()
    {
        return 'clientProduct';
    }
}