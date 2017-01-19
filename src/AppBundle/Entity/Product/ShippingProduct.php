<?php

namespace AppBundle\Entity\Product;

use AppBundle\Entity\ProductGroup;
use Doctrine\ORM\Mapping as ORM;
use Trinity\NotificationBundle\Annotations as N;

/**
 * Class ShippingProduct
 * @package AppBundle\Entity\Product
 *
 *
 * @ORM\Entity()
 */
class ShippingProduct extends StandardProduct
{
    /**
     * @var
     * @ORM\Column(name="shipping_price", type="float")
     */
    protected $shippingPrice;

    /**
     * @return mixed
     */
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

    /**
     * @param mixed $shippingPrice
     * @return $this
     */
    public function setShippingPrice($shippingPrice)
    {
        $this->shippingPrice = $shippingPrice;

        return $this;
    }
}
