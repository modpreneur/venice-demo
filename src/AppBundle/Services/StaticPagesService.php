<?php

namespace AppBundle\Services;

use AppBundle\Entity\StaticPage;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StaticPagesService
 * @package AppBundle\Services
 */
class StaticPagesService
{
    /**
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->serviceContainer = $container;
    }


    /**
     * @param $pageHandle
     * @return mixed
     */
    public function getPageBody($pageHandle)
    {
        $em   = $this->serviceContainer->get('doctrine')->getManager();
        $page = $em->getRepository(StaticPage::class)->findOneBy(['handle' => $pageHandle]);
        return $page;
    }


    /**
     * @return mixed
     */
    public function getPages(array $handles = [])
    {
        $em = $this->serviceContainer->get('doctrine')->getManager();
        if (empty($handles)) {
            $pages = $em->getRepository(StaticPage::class)->findAll();
        } else {
            $pages = $em->getRepository(StaticPage::class)->findBy(['handle' => $handles]);
        }
        return $pages;
    }
}
