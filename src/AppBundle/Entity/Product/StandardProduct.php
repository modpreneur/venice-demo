<?php

namespace AppBundle\Entity\Product;

use AppBundle\Entity\ProductGroup;
use Doctrine\ORM\Mapping as ORM;
use Trinity\NotificationBundle\Annotations as N;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\StandardProductRepository")
 * @N\Source(columns="necktieId, name")
 * Creating products on client is not allowed because creating billing plans is not allowed
 * @N\Methods(types={"put", "delete"})
 * @N\Url(postfix="product")
 * Class StandardProduct
 */
class StandardProduct extends \Venice\AppBundle\Entity\Product\StandardProduct
{
    /**
     * @ORM\Column(type="string")
     */
    protected $StandardProductChild;

    /**
     * @var ProductGroup
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProductGroup", inversedBy="products")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;

    /**
     * @var int
     * @ORM\Column(name="upsell_order", type="integer")
     */
    protected $upsellOrder;

    /**
     * @var string
     * @ORM\Column(name="upsel_miniature", type="string", length=255)
     */
    protected $upselMiniature;

    /**
     * @var string
     * @ORM\Column(name="upsel_miniature_mobile", type="string", length=255)
     */
    protected $upselMiniatureMobile;

    /**
     * @var bool
     * @ORM\Column(name="is_recommended", type="boolean")
     */
    protected $isRecommended;

    /**
     * @var string
     * @ORM\Column(name="short_description", type="text")
     */
    protected $shortDescription;

    /**
     * @var string
     * @ORM\Column(name="short_name", type="string", length=20)
     */
    protected $shortName;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $customTemplateName;


    /**
     * StandardProduct constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->StandardProductChild = 'standard product from application';
        $this->customTemplateName = 'VeniceFrontbundle:BundleProduct:bundleProductPlatinumMix.html.twig';
    }


    /**
     * @return mixed
     */
    public function getStandardProductChild()
    {
        return $this->StandardProductChild;
    }


    /**
     * @param mixed $StandardProductChild
     */
    public function setStandardProductChild($StandardProductChild)
    {
        $this->StandardProductChild = $StandardProductChild;
    }


    /**
     * @return ProductGroup
     */
    public function getGroup()
    {
        return $this->group;
    }


    /**
     * @param ProductGroup $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }


    /**
     * @return int
     */
    public function getUpsellOrder()
    {
        return $this->upsellOrder;
    }


    /**
     * @param int $upsellOrder
     */
    public function setUpsellOrder($upsellOrder)
    {
        $this->upsellOrder = $upsellOrder;
    }


    /**
     * @return bool
     */
    public function isIsRecommended()
    {
        return $this->isRecommended;
    }


    /**
     * @param bool $isRecommended
     */
    public function setIsRecommended($isRecommended)
    {
        $this->isRecommended = $isRecommended;
    }


    /**
     * @return string
     */
    public function getUpselMiniature()
    {
        return $this->upselMiniature;
    }


    /**
     * @param string $upselMiniature
     */
    public function setUpselMiniature($upselMiniature)
    {
        $this->upselMiniature = $upselMiniature;
    }


    /**
     * @return string
     */
    public function getUpselMiniatureMobile()
    {
        return $this->upselMiniatureMobile;
    }


    /**
     * @param string $upselMiniatureMobile
     */
    public function setUpselMiniatureMobile($upselMiniatureMobile)
    {
        $this->upselMiniatureMobile = $upselMiniatureMobile;
    }


    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }


    /**
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }


    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }


    /**
     * @param string $shortName
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }


    /**
     * @return string
     */
    public function getCustomTemplateName(): string
    {
        return $this->customTemplateName;
    }


    /**
     * @param string $customTemplateName
     */
    public function setCustomTemplateName(string $customTemplateName)
    {
        $this->customTemplateName = $customTemplateName;
    }


    public function getbuyCBParameters()
    {
        return $this->getDefaultBillingPlan();
    }


    /**
     * @todo
     * @return int
     */
    public function daysRemainingToUnlock()
    {
        return 0;
    }

}
