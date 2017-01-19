<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProductGroup;
use AppBundle\Entity\User;

use AppBundle\Services\AbstractConnector;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\AppBundle\Entity\Product\Product;

/**
 * Class DownloadsController
 *
 * @Route("/products")
 *
 * @package GeneralBackend\DownloadsBundle\Controller\Front
 */
class ProductsController extends Controller
{
    /**
     * @Route("/flofit", name="downloads_dashboard")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \LogicException
     */
    public function dashboardAction(Request $request)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user    = $this->getUser();
        $session = $this->get('session');
        $fbPixel = $request->get('fbPixel');

        if ($user->getProductAccesses()->count() === 0 && $session->get('rebuild-for-one', false)) {
            /** @var AbstractConnector $connector */
            $connector = $this->get($this->getParameter('connector_service_name'));
            $connector->fireUpdateEvent($user);

            $session->set('rebuild-for-one', true);
            return $this->redirectToRoute('downloads_dashboard');
        }

        /** @var ProductGroup $productGroup */
        $productGroup = $entityManager->getRepository(ProductGroup::class)
            ->findOneBy(['handle' => ProductGroup::HANDLE_FLOFIT]);

        $productsService = $this->get('flofit.products_service');

        $productsService->initialSetup($productGroup->getProducts(), $this->getUser());

        $upsellProducts = $entityManager->getRepository(StandardProduct::class)
            ->findBy(['isRecommended' => 1], ['upsellOrder' => 'ASC']);

        $currentProduct = $entityManager->getRepository(Product::class)->findOneBy(['handle' => $productGroup::HANDLE_FLOFIT]);

        return $this->render(
            'VeniceFrontBundle:Products:dashboard.html.twig',
            [
                'productsService' => $productsService,
                'upsellProducts'  => $upsellProducts,
                'fbPixel'         => $fbPixel,
                'currentProduct'  => $currentProduct,
            ]
        );
    }


    //     * @Route("/platinumclub/module/{module}/video", name="downloads_product_flomersion_module") unused?
    //    flomersionAction($module = 1)
    /**
     * @Route("/platinumclub/video", name="downloads_product_flomersion")
     * @Route("/platinumclub/video/{handle}", name="downloads_product_flomersion_video_play")
     * @return Response
     */
    public function flomersionAction($handle = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        /** @var ProductGroup $productGroup */
        $productGroup = $entityManager->getRepository("ModernEntrepreneurGeneralBackendDownloadsBundle:ProductGroup")
            ->findOneBy(array("handle" => ProductGroup::HANDLE_FLOMERSION));

        $productsService = $this->get("modern_entrepreneur_global_backend_downloads.products_service");
        $productsService->initialSetup($productGroup->getProducts(), $this->getUser());

        /** @var Product $product */
        $product = $productGroup->getProducts()->first();

        if (!$product->haveAccess($user)) {
            return $this->redirectToRoute("downloads_product_bundle_detail",
                array("handle" => $product->getRootProduct()->getHandle()));
        }

        $playId = null;
        if ($handle !== null) {
            $productByHandle = $entityManager
                ->getRepository('ModernEntrepreneurGeneralBackendDownloadsBundle:VideoProduct')
                ->findOneBy(array('handle' => $handle));
            if ($productByHandle) {
                $playId = $productByHandle->getId();
            }
        }


        $access = false;
        if (!is_null($product)) {
            /** @var GlobalUser $user */
            $user = $this->getUser();
            $access = $product->haveAccess($user);
        }

        return $this->render('DownloadsBundle/Front/flomersion.html.twig',
            array(
                "access" => $access,
                "productsService" => $productsService,
                'playId' => $playId
//                "modules" => range(1,6),
//                "activeModule" => $module
            ));
    }


    /**
     * @Route("/product/platinumclub", name="downloads_platinumclub_detail_video")
     * @Route("/product/platinumclub/module/{module}", name="downloads_platinumclub_video_detail_with_module")
     * @return Response
     */
    public function productPlatinumClubAction($module = 1)
    {
        /** @var User $user */
        $user = $this->getUser();

        $bundleProduct = $this->getDoctrine()->getManager()
            ->getRepository("ModernEntrepreneurGeneralBackendDownloadsBundle:BundleProduct")
            ->findOneBy(array("handle" => "platinumclub"));

        if ($user->haveAccess($bundleProduct)) {
            return $this->redirectToRoute("downloads_product_flomersion");
        }

        $productsService = $this->get("modern_entrepreneur_global_backend_downloads.products_service");
        $productsService->initialSetup($bundleProduct->getSubProducts(), $this->getUser());

        /** @var StandardProduct $upsellProducts */
        $upsellProducts = $this->getDoctrine()->getManager()
            ->getRepository("ModernEntrepreneurGeneralBackendDownloadsBundle:BundleProduct")
            ->findBy(array("isRecommended" => 1), array("upsellOrder" => "ASC"));

        $buyParams = new CBBuyParameters();
        $parameters = array();
        if ($user->haveClaimYourPlatinumClubTrial()) {
            $buyParams->setBuyId(41);
            $trial = 30;
            $parameters["rebillDate"] = (new \DateTime())->add(new \DateInterval("P30D"));
        } else {

            $buyParams->setBuyId(40);
            $trial = 7;
            $parameters["rebillDate"] = (new \DateTime())->add(new \DateInterval("P7D"));
        }

        $featuresService = $this->get("general_backend_core.services.flofit_features");

        $parameters["buyLinkCCT"] = $featuresService->generateOCBLinkByBuyParameters($buyParams, true, $user);
        $parameters["buyLinkCCF"] = $featuresService->generateOCBLinkByBuyParameters($buyParams, false, $user);

        $parameters["taxPriceStr"] = "$0.00";
        $parameters["totalPriceStr"] = "$0.00";

        $parameters["storedCard"] = $featuresService->getLastCard($user);
        $parameters["name"] = $bundleProduct->getName();
        $parameters["productDescription"] = "Get a \"BACKSTAGE PASS\" and truly live the FLO LIFE! Just 5 monthly payments of $79 (free access for life after final payment!) Best deal ever!";
        $parameters["rebillPriceStr"] = "$79.00";


        return $this->render(":DownloadsBundle/Front/bundleProduct:bundleProductPlatinumClubVideo.html.twig",
            array(
                "productsService" => $productsService,
                "upsellProducts" => $upsellProducts,
                "bundleProduct" => $bundleProduct,
                "activeModule" => $module,
                "parameters" => $parameters,
                "trial" => $trial
            ));
    }


    /**
     * @Route("/product/{handle}", name="downloads_product_bundle_detail")
     * @Route("/product/{handle}/module/{module}", name="downloads_product_bundle_detail_with_module")
     * @param BundleProduct $bundleProduct
     *
     * @return Response
     */
    public function bundleProductAction(BundleProduct $bundleProduct, $module = 1)
    {
        /** @var User $user */
        $user = $this->getUser();

        $productsService = $this->get("modern_entrepreneur_global_backend_downloads.products_service");
        $productsService->initialSetup($bundleProduct->getSubProducts(), $this->getUser());

        /** @var BundleProduct $upsellProducts */
        $upsellProducts = $this->getDoctrine()->getManager()
            ->getRepository("ModernEntrepreneurGeneralBackendDownloadsBundle:BundleProduct")
            ->findBy(array("isRecommended" => 1), array("upsellOrder" => "ASC"));

        return $this->render($bundleProduct->getCustomTemplateName(),
            array(
                "access" => $user->haveAccess($bundleProduct),
                "productsService" => $productsService,
                "upsellProducts" => $upsellProducts,
                "bundleProduct" => $bundleProduct,
                "activeModule" => $module
            ));
    }


    /**
     * @Route("/vlog/detail/{handle}",name="downloads_product_vlog_detail")
     * @param VlogProduct $vlogProduct
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function vlogDetailsAction(Request $request, VlogProduct $vlogProduct)
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->haveAccess($vlogProduct)) {
            return $this->redirectToRoute("downloads_dashboard");
        }

        $authorPublicProfileLink = "";
        if ($vlogProduct->getPublisher()) {
            $authorPublicProfileLink = $this->generateUrl('core_front_user_public_profile',
                array('username' => $vlogProduct->getPublisher()->getUserName()));
        }

        return $this->render(":DownloadsBundle/Front:vlogProductDetails.html.twig",
            array(
                "vlogProduct" => $vlogProduct,
                "authorPublicProfileLink" => $authorPublicProfileLink
            )
        );
    }


    /**
     * @Route("/pernament/post/{id}", name="vlog_permanent_post_detail")
     * @param VlogProduct $vlogProduct
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pernamentPostLink(VlogProduct $vlogProduct)
    {
        return $this->render(":DownloadsBundle/Front:vlogProductDetails.html.twig",
            array(
                "vlogProduct" => $vlogProduct
            )
        );
    }
}

