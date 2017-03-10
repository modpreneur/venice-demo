<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Venice\AppBundle\Entity\Product\Product;
use Xiidea\EasyAuditBundle\Annotation\ORMSubscribedEvents;

/**
 * ProductGroup
 *
 * @ORM\Table(name="product_group")
 * @ORM\Entity
 * @ORMSubscribedEvents()
 */
class ProductGroup
{
    const HANDLE_FLOFIT     = 'flo-fit-workout-program';
    const HANDLE_FLOMERSION = 'platinumclub';
    const HANDLE_PLATINUM_MIX = 'platinum-mix';
    const HANDLE_NUTRITION_AND_MEALS = 'nutrition-and-meals';
    const HANDLE_7_DAY_RIP_MIX = '7-day-rip-mix';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="handle", type="string", length=100)
     */
    private $handle;

    /**
     * @var ArrayCollection<Product>
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product\StandardProduct", mappedBy="group")
     */
    private $products;

    /**
     * @var integer
     *
     * @ORM\Column(name="order_number", type="integer")
     */
    private $orderNumber;


    /**
     * ProductGroup constructor.
     */
    public function __construct()
    {
        $this->products    = new ArrayCollection();
        $this->orderNumber = 0;
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ProductGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set handle
     *
     * @param string $handle
     * @return ProductGroup
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;

        return $this;
    }


    /**
     * Get handle
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }


    /**
     * @var ArrayCollection<Product>
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }


    /**
     * @param Product $products
     * @return $this
     */
    public function addProduct(Product $products)
    {
        $this->products->add($products);

        return $this;
    }


    /**
     * @param Product $products
     * @return $this
     */
    public function removeProducts(Product $products)
    {
        $this->products->removeElement($products);

        return $this;
    }


    /**
     * @param int $orderNumber
     * @return $this
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }


    /**
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }
}
