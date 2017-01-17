<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Venice\AppBundle\Entity\Product\Product;

/**
 * Class ProductPostsService
 * @package AppBundle\Services
 */
class ProductPostsService
{
    /**
     * @var
     */
    protected $serviceContainer;

    /** @var  EntityManager */
    protected $entityManager;


    /**
     * ProductPostsService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param null $limit
     *
     * @return array|Product[]
     */
    public function getLatestProductPosts($limit = null)
    {
        $em = $this->entityManager;

        $ProductPosts = $em->getRepository(Product::class)
            ->findBy([], ['orderNumber' => 'ASC'], $limit);

        return $ProductPosts;
    }
}


