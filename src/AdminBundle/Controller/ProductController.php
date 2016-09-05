<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.02.16
 * Time: 15:55.
 */
namespace AdminBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Venice\AdminBundle\Controller\ProductController as VeniceProductController;

/**
 * Class ProductController.
 */
class ProductController extends VeniceProductController
{
    /**
     * @Route("/admin/product")
     * @Route("/admin/product/")
     *
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     * @throws \LogicException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }
}
