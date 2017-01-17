<?php

namespace FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ShippingProductController
 *
 * @Route()
 * @package GeneralBackend\DownloadsBundle\Controller\Front
 */
class ShippingProductController extends Controller
{
    /**
     * @Route("/physical-product", name="core_front_staticpages_social_physical_product")
     * @return Response
     */
    public function physicalProductAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $physicalProduct = $entityManager->getRepository("ModernEntrepreneurGeneralBackendDownloadsBundle:ShippingProduct")
            ->findOneBy(array("handle"=>"physical-product"));

        if(is_null($physicalProduct))
            return $this->redirectToRoute("landing_page");

        return $this->render(":DownloadsBundle/Front/shippingProduct:physicalProduct.html.twig",
            array(
                "physicalProduct"=>$physicalProduct
            ));
    }
}