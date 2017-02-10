<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Content\PdfContent;
use AppBundle\Entity\Content\VideoContent;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProductGroup;
use AppBundle\Entity\User;
use AppBundle\Services\AbstractConnector;
use AppBundle\Services\ProductsPage;
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
        $user = $this->getUser();
        $session = $this->get('session');
        $fbPixel = $request->get('fbPixel');

        if ($user->getProductAccesses()->count() === 0 && $session->get('rebuild-for-one', false)) {
            /** @var AbstractConnector $connector */
            $connector = $this->get($this->getParameter('connector_service_name'));
            $connector->fireUpdateEvent($user);

            $session->set('rebuild-for-one', true);
            return $this->redirectToRoute('downloads_dashboard');
        }

//        /** @var ProductGroup $productGroup */
//        $productGroup = $entityManager->getRepository(ProductGroup::class)
//            ->findOneBy(['handle' => ProductGroup::HANDLE_FLOFIT]);
//
//        $productsService = $this->get('flofit.products_service');
//
//        $productsService->initialSetup($productGroup->getProducts(), $this->getUser());


        $upsellProducts = $entityManager->getRepository(StandardProduct::class)
            ->findBy(['isRecommended' => 1], ['upsellOrder' => 'ASC']);

        $flofitProduct = $entityManager
            ->getRepository(Product::class)
            ->findOneBy(['handle' => ProductGroup::HANDLE_FLOFIT]);

        $platinumMixProduct = $entityManager
            ->getRepository(Product::class)
            ->findOneBy(['handle' => ProductGroup::HANDLE_PLATINUM_MIX]);

        $nutritionAndMealsProduct = $entityManager
            ->getRepository(Product::class)
            ->findOneBy(['handle' => ProductGroup::HANDLE_NUTRITION_AND_MEALS]);

        $sevenDayRipMixProduct = $entityManager
            ->getRepository(Product::class)
            ->findOneBy(['handle' => ProductGroup::HANDLE_7_DAY_RIP_MIX]);

        $flofitWorkouts = $entityManager->getRepository(VideoContent::class)
            ->getByProducts([$flofitProduct->getId(), $platinumMixProduct->getId()]);

//        dump($flofitWorkouts);
//        foreach ($flofitWorkouts as $flofitWorkout) {
//            dump($flofitWorkout->getType());
//        }
//        dump($entityManager->getRepository(PdfContent::class), get_class($entityManager->getRepository(PdfContent::class)));die();
        $flofitMealPlans = $entityManager->getRepository(PdfContent::class)
            ->getByProducts([
                $flofitProduct->getId(),
                $nutritionAndMealsProduct->getId(),
                $sevenDayRipMixProduct->getId(),
                $platinumMixProduct->getId()
            ]);

////
//        dump($flofitMealPlans);
//        foreach ($flofitMealPlans as $flofitWorkout) {
////            dump($flofitWorkout->getType());
//        }
//
//        die();

        return $this->render(
            'VeniceFrontBundle:Products:dashboard.html.twig',
            [
                'workouts' => $flofitWorkouts,
                'mealPlans' => $flofitMealPlans,
                'upsellProducts' => $upsellProducts,
                'fbPixel' => $fbPixel,
                'currentProduct' => $flofitProduct,
                'videosAndMealPlansLoadOffset' => $this->container->getParameter('downloads_number_of_product_displayed'),
            ]
        );
    }


    //     * @Route("/platinumclub/module/{module}/video", name="downloads_product_flomersion_module") unused?
    //    flomersionAction($module = 1)
    /**
     * @Route("/platinumclub/video", name="downloads_product_flomersion")
     * @Route("/platinumclub/video/{handle}", name="downloads_product_flomersion_video_play")
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function flomersionAction($handle = null)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var ProductGroup $productGroup */
        $productGroup = $entityManager->getRepository(ProductGroup::class)
            ->findOneBy(['handle' => ProductGroup::HANDLE_FLOMERSION]);

        $productsService = $this->get('flofit.products_service');
        $productsService->initialSetup($productGroup->getProducts(), $this->getUser());

        /** @var Product $product */
        $product = $productGroup->getProducts()->first();

        if ($product && !$user->hasAccessToProduct($product)) {
            return $this->redirectToRoute(
                'downloads_product_bundle_detail',
                ['handle' => $product->getHandle()]
            );
        }

        $playId = null;
        if ($handle !== null) {
            $productByHandle = $entityManager
                ->getRepository(VideoContent::class)
                ->findOneBy(['handle' => $handle]);
            if ($productByHandle) {
                $playId = $productByHandle->getId();
            }
        }

        $access = false;
        if ($product) {
            /** @var User $user */
            $user = $this->getUser();
            $access = $user->hasAccessToProduct($product);
        }

        $currentProduct = $entityManager
            ->getRepository(Product::class)
            ->findOneBy(['handle' => $productGroup::HANDLE_FLOMERSION]);

        return $this->render(
            'VeniceFrontBundle:Products:flomersion.html.twig',
            [
                'access' => $access,
                'productsService' => $productsService,
                'playId' => $playId,
                'product' => $currentProduct,
            ]
        );
    }


    /**
     * @Route("/product/platinumclub", name="downloads_platinumclub_detail_video")
     * @Route("/product/platinumclub/module/{module}", name="downloads_platinumclub_video_detail_with_module")
     * @return Response
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function productPlatinumClubAction($module = 1)
    {
        /** @var User $user */
        $user = $this->getUser();

        $bundleProduct = $this->getDoctrine()->getManager()
            ->getRepository(StandardProduct::class)
            ->findOneBy(['handle' => 'platinumclub']);

        if ($user->haveAccess($bundleProduct)) {
            return $this->redirectToRoute('downloads_product_flomersion');
        }

        /** @var ProductsPage $productsService */
        $productsService = $this->get('flofit.products_service');
        $productsService->initialSetup([$bundleProduct], $this->getUser());

        /** @var StandardProduct $upsellProducts */
        $upsellProducts = $this->getDoctrine()->getManager()
            ->getRepository(StandardProduct::class)
            ->findBy(['isRecommended' => 1], ['upsellOrder' => 'ASC']);

        $buyParams = new BillingPlan();
        $parameters = [];

        $trial = 7;

        /*
        if ($user->haveClaimYourPlatinumClubTrial()) {
            $buyParams->setBuyId(41);
            $trial = 30;
            $parameters["rebillDate"] = (new \DateTime())->add(new \DateInterval("P30D"));
        } else {

            $buyParams->setBuyId(40);
            $trial = 7;
            $parameters["rebillDate"] = (new \DateTime())->add(new \DateInterval("P7D"));
        }
        */

        $featuresService = $this->get('front.twig.flofit_features');

        $parameters['buyLinkCCT'] = $featuresService->generateOCBLinkByBuyParameters($buyParams, true, $user);
        $parameters['buyLinkCCF'] = $featuresService->generateOCBLinkByBuyParameters($buyParams, false, $user);

        $parameters['taxPriceStr'] = '$0.00';
        $parameters['totalPriceStr'] = '$0.00';

        $parameters['storedCard'] = $featuresService->getLastCard($user);
        $parameters['name'] = $bundleProduct->getName();
        $parameters['productDescription'] = 'Get a "BACKSTAGE PASS" and truly live the FLO LIFE! Just 5 monthly payments of $79 (free access for life after final payment!) Best deal ever!';
        $parameters['rebillPriceStr'] = '$79.00';


        return $this->render(
            'VeniceFrontBundle:BundleProduct:bundleProductPlatinumClubVideo.html.twig',
            [
                'productsService' => $productsService,
                'upsellProducts' => $upsellProducts,
                'bundleProduct' => $bundleProduct,
                'activeModule' => $module,
                'parameters' => $parameters,
                'trial' => $trial,
            ]
        );
    }


    /**
     * @Route("/product/{handle}", name="downloads_product_bundle_detail")
     * @Route("/product/{handle}/module/{module}", name="downloads_product_bundle_detail_with_module")
     * @param StandardProduct $product
     * @param int $module
     *
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @internal param StandardProduct $bundleProduct
     *
     */
    public function bundleProductAction(StandardProduct $product, $module = 1)
    {
        /** @var User $user */
        $user = $this->getUser();
        $productsService = $this->get('flofit.products_service');
        $productsService->initialSetup([$product], $this->getUser());

        /** @var StandardProduct $upsellProducts */
        $upsellProducts = $this->getDoctrine()->getManager()
            ->getRepository(StandardProduct::class)
            ->findBy(['isRecommended' => 1], ['upsellOrder' => 'ASC']);

        return $this->render(
            $product->getCustomTemplateName(),
            [
                'access' => $user->haveAccess($product),
                'productsService' => $productsService,
                'upsellProducts' => $upsellProducts,
                'bundleProduct' => $product,
                'activeModule' => $module,
                'allContentProducts' => $product->getContentProducts()
            ]
        );
    }


    /**
     * @Route("/vlog/detail/{handle}",name="downloads_product_vlog_detail")
     * @param VlogProduct $vlogProduct
     *
     * @todo
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \LogicException
     */
    public function vlogDetailsAction(Request $request, VlogProduct $vlogProduct)
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->haveAccess($vlogProduct)) {
            return $this->redirectToRoute('downloads_dashboard');
        }

        $authorPublicProfileLink = "";
        if ($vlogProduct->getPublisher()) {
            $authorPublicProfileLink = $this->generateUrl('core_front_user_public_profile',
                ['username' => $vlogProduct->getPublisher()->getUserName()]);
        }

        return $this->render(':DownloadsBundle/Front:vlogProductDetails.html.twig',
            [
                'vlogProduct' => $vlogProduct,
                'authorPublicProfileLink' => $authorPublicProfileLink
            ]
        );
    }


    /**
     * @Route("/pernament/post/{id}", name="vlog_permanent_post_detail")
     * @param VlogProduct $vlogProduct
     *
     * @todo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pernamentPostLink(VlogProduct $vlogProduct)
    {
        return $this->render(':DownloadsBundle/Front:vlogProductDetails.html.twig',
            [
                'vlogProduct' => $vlogProduct
            ]
        );
    }
}

