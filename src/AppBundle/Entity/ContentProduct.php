<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 14:49.
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\ContentProductRepository")
 * Class ContentProduct
 */
class ContentProduct extends \Venice\AppBundle\Entity\ContentProduct
{
    /**
     * @ORM\Column(type="string")
     */
    protected $ContentProductChild;

    /**
     * @return mixed
     */
    public function getContentProductChild()
    {
        return $this->ContentProductChild;
    }

    /**
     * @param mixed $ContentProductChild
     */
    public function setContentProductChild($ContentProductChild)
    {
        $this->ContentProductChild = $ContentProductChild;
    }
}
