<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Trinity\NotificationBundle\Annotations as N;

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
}

