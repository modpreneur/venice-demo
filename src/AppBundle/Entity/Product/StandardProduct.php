<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:32.
 */
namespace AppBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Trinity\NotificationBundle\Annotations as N;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\StandardProductRepository")
 * @N\Source(columns="necktieId, name, description, defaultBillingPlan")
 * Creating products on client is not allowed because creating billing plans is not allowed
 * @N\Methods(types={"put", "delete"})
 * @N\Url(postfix="product")
 *
 * Class StandardProduct
 */
class StandardProduct extends \Venice\AppBundle\Entity\Product\StandardProduct
{
    public function __construct()
    {
        parent::__construct();

        $this->StandardProductChild = 'standard product from application';
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $StandardProductChild;

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
}
