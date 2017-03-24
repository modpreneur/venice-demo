<?php
/**
 * Created by PhpStorm.
 * User: polki
 * Date: 10.3.17
 * Time: 18:25
 */

namespace FrontBundle\Controller;

use AppBundle\Entity\Product\StandardProduct;
use FlofitEntities\Bundle\FlofitEntitiesBundle\FlofitEntities\DownloadsBundle\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Services\BuyUrlGenerator as BuyUrlGen;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class BuyLinkController
 *
 * @Route("/buy")
 *
 * @package FrontBundle\Controller
 */
class BuyLinkController extends Controller
{

    private $FLOFIT_67dollars = 41; // FLO FIT Online Workout System + Limited Time Bonuses: Get Access Now!
    private $FLOFIT_37dollars = 30;
    private $FLOFIT_67dollarsPlusDVDs = 25;
    private $CBID_to_necktieId = [
        1   => 546,
        10  => 571,
        11  => 572,
        12  => 573,
        13  => 574,
        14  => 575,
        15  => 583,
        16  => 584,
        17  => 581,
        18  => 582,
        19  => 585,
        2   => 548,
        20  => 588,
        200 => 547,
        201 => 549,
        202 => 551,
        203 => 553,
        204 => 558,
        206 => 563,
        207 => 565,
        208 => 601,
        209 => 600,
        21  => 586,
        22  => 587,
        23  => 589,
        24  => 590,
        25  => 591,
        27  => 592,
        28  => 593,
        29  => 594,
        3   => 550,
        30  => 595,
        31  => 596,
        32  => 597,
        33  => 598,
        34  => 599,
        35  => 603,
        36  => 602,
        37  => 604,
        38  => 616,
        39  => 617,
        4   => 552,
        40  => 618,
        400 => 559,
        401 => 560,
        402 => 561,
        403 => 562,
        404 => 557,
        405 => 566,
        406 => 568,
        407 => 569,
        41  => 626,
        414 => 577,
        415 => 578,
        416 => 579,
        417 => 580,
        418 => 605,
        419 => 606,
        42  => 627,
        420 => 607,
        421 => 608,
        422 => 609,
        423 => 610,
        424 => 611,
        425 => 612,
        426 => 613,
        427 => 614,
        428 => 619,
        429 => 620,
        43  => 628,
        435 => 621,
        438 => 622,
        439 => 623,
        44  => 629,
        440 => 624,
        441 => 625,
        443 => 633,
        444 => 634,
        445 => 635,
        446 => 640,
        447 => 637,
        448 => 638,
        449 => 639,
        45  => 630,
        450 => 636,
        451 => 641,
        452 => 642,
        453 => 643,
        454 => 644,
        455 => 645,
        456 => 646,
        457 => 647,
        458 => 648,
        459 => 649,
        46  => 631,
        460 => 650,
        47  => 632,
        5   => 555,
        51  => 651,
        52  => 652,
        54  => 653,
        6   => 564,
        7   => 567,
        8   => 556,
        9   => 570,
    ];



    /**
     * @Route("/buy-link/testtrialshow", name="buy_link_show_test")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function testshowAction(Request $request)
    {

        $settings = $this->get('trinity.settings');
        $dateFrom = $settings->get('trialStart', $this->getUser()->getId(), 'user');
        $dateTo = $settings->get('trialEnd', $this->getUser()->getId(), 'user');

        return new JsonResponse(['url' => 'OK', 'start' => $dateFrom, 'end' => $dateTo]);
    }
    /**
     * @Route("/buy-link/testtrialclear", name="buy_link_clear_test")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function testclearAction(Request $request)
    {

        $settings = $this->get('trinity.settings');

        $settings->clear($this->getUser(), 'user');
        $settings->clear($this->getUser()->getId(), 'user');

        return new JsonResponse(['url' => 'OK']);
    }

    /**
     * @Route("/buy-link/test7days", name="buy_link_7_test")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function test7Action(Request $request)
    {
        $dateFrom = new \DateTime('2016-01-01');
        $dateTo = new \DateTime('2016-01-08');
        $settings = $this->get('trinity.settings');
        $settings->set('trialStart', $dateFrom, $this->getUser()->getId(), 'user');
        $settings->set('trialEnd', $dateTo, $this->getUser()->getId(), 'user');

        return new JsonResponse(['url' => 'OK', 'start' => $dateFrom, 'end' => $dateTo]);
    }

    /**
     * @Route("/buy-link/test14days", name="buy_link_14_test")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function test14Action(Request $request)
    {
        $dateFrom = new \DateTime('2016-01-01');
        $dateTo = new \DateTime('2016-01-15');
        $settings = $this->get('trinity.settings');
        $settings->set('trialStart', $dateFrom, $this->getUser()->getId(), 'user');
        $settings->set('trialEnd', $dateTo, $this->getUser()->getId(), 'user');

        return new JsonResponse(['url' => 'OK', 'start' => $dateFrom, 'end' => $dateTo]);
    }

    /**
     * @Route("/buy-link/test2days", name="buy_link_2_test")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function test2Action(Request $request)
    {
        $dateFrom = new \DateTime('2017-03-18');
        $dateTo = new \DateTime('2017-03-25');
        $settings = $this->get('trinity.settings');
        $settings->set('trialStart', $dateFrom, $this->getUser()->getId(), 'user');
        $settings->set('trialEnd', $dateTo, $this->getUser()->getId(), 'user');

        return new JsonResponse(['url' => 'OK', 'start' => $dateFrom, 'end' => $dateTo]);
    }

    /**
     * @Route("/buy-link/extend-trial", name="buy_link_extend_trial")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function buyLink36Action(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            $userId = $request->get('user_id', false);

            /** @var User $user */
            $user = $em->getRepository(User::class)
                ->findOneBy(['id' => $userId]);
        }

        /** @var BuyUrlGen $generator */
        $generator = $this->get('venice.app.buy_url_generator');

        $settings = $this->get('trinity.settings');


        if (!$user ||
            !$settings->get('trialStart', $user->getId(), 'user') ||
            !$settings->get('trialEnd', $user->getId(), 'user')
        ) {
            /** @var \AppBundle\Entity\BillingPlan $billingPlan */
            $billingPlan = $em->getRepository(\AppBundle\Entity\BillingPlan::class)
                ->findOneBy(['necktieId' => $this->CBID_to_necktieId[
                    $this->FLOFIT_67dollars
                ]]);

            $product = $billingPlan->getProduct(); // if not null
            $url = $generator->generateBuyUrl($product, $billingPlan->getId());
//            return new JsonResponse(['url' => $url]);
            return $this->redirect($url);
        }

        $userTrialStart = $settings->get('trialStart', $user->getId(), 'user');
        $now = new \DateTime();
        $daysToTrial = $now->diff($userTrialStart, true)->d + 1;

        if ($settings->get('productOfferId', $user->getId(), 'user') == 'introFreePa') {
            $productId = $this->productsExtend895($daysToTrial);
        } elseif ($settings->get('productOfferId', $user->getId(), 'user') == 'introFreeHp') {
            $productId = $this->productsExtend1295($daysToTrial);
        } else {
            $productId = $this->productsExtend895($daysToTrial);
        }

        /** @var \AppBundle\Entity\BillingPlan $billingPlan */
        $billingPlan = $em->getRepository(\AppBundle\Entity\BillingPlan::class)
            ->findOneBy(['necktieId' => $this->CBID_to_necktieId[
                $productId
            ]]);

        $product = $billingPlan->getProduct(); // if not null
        $url = $generator->generateBuyUrl($product, $billingPlan->getId());
//        return new JsonResponse(['url' => $url]);
////        return $this->redirect("http://{$productId}.flofit.pay.clickbank.net/?cbfid={$cbfid}&vtid={$vtid}&cbskin=13358&email={$user->getEmail()}&name={$user->getFirstName()} {$user->getLastName()}");
//        return $this->redirect("http://{$productId}.flofit.pay.clickbank.net/?cbfid={$cbfid}&vtid=hello&cbskin=13358&email={$user->getEmail()}&name={$user->getFirstName()} {$user->getLastName()}");
        return $this->redirect($url);
    }

    /**
     * @Route("/buy-link/after-trial", name="buy_link_after_trial")
     */
    public function buyLinkAfterTrialAction(Request $request)
    {
        $choice = $request->get('user_choice', false);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            $userId = $request->get('user_id', false);

            /** @var User $user */
            $user = $em->getRepository(User::class)
                ->findOneBy(['id' => $userId]);
        }

        /** @var BuyUrlGen $generator */
        $generator = $this->get('venice.app.buy_url_generator');

        $settings = $this->get('trinity.settings');


        if (!$user || !$choice) {
            /** @var \AppBundle\Entity\BillingPlan $billingPlan */
            $billingPlan = $em->getRepository(\AppBundle\Entity\BillingPlan::class)
                ->findOneBy(['necktieId' => $this->CBID_to_necktieId[
                    $this->FLOFIT_37dollars
                ]]);

            $product = $billingPlan->getProduct(); // if not null
            $url = $generator->generateBuyUrl($product, $billingPlan->getId());
//            return new JsonResponse(['url' => $url]);
            return $this->redirect($url);
//            return $this->redirect("http://30.flofit.pay.clickbank.net/?vtid={$vtid}&cbskin=13358&cbfid={$cbfId}");
        }


        if ($settings->get('productOfferId', $user->getId(), 'user') == 'introFreePa') {
            $prodCbf = $this->productsAfter895($choice);
        } elseif ($settings->get('productOfferId', $user->getId(), 'user') == 'introFreeHp') {
            $prodCbf = $this->productsAfter1295($choice);
        } else {
            $prodCbf = $this->productsAfter495($choice);
        }

        $productId = $prodCbf['product'];
        $cbfid = $prodCbf['cbfid'];

        /** @var \AppBundle\Entity\BillingPlan $billingPlan */
        $billingPlan = $em->getRepository(\AppBundle\Entity\BillingPlan::class)
            ->findOneBy(['necktieId' => $this->CBID_to_necktieId[
                $productId
            ]]);

        $product = $billingPlan->getProduct(); // if not null
        $url = $generator->generateBuyUrl($product, $billingPlan->getId());
//        return new JsonResponse(['url' => $url]);
        return $this->redirect($url);

        //upgrade popup 67 na cb
//        return $this->redirect("http://{$productId}.flofit.pay.clickbank.net/?vtid={$vtid}&cbskin={$cbfid}&cbfid={$cbfid}&email={$user->getEmail()}&name={$user->getFirstName()} {$user->getLastName()}");
    }

    /**
     * @Route("/buy-link/upgrade-trial", name="buy_link_upgrade_trial")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function buyLinkTrialUpgradeAction(Request $request)
    {
        /** @var EntityManager $entityManager */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            $userId = $request->get('user_id', false);

            /** @var User $user */
            $user = $em->getRepository('ModernEntrepreneurGeneralBackendCoreBundle:User')
                ->findOneBy(['id' => $userId]);
        }

        /** @var BuyUrlGen $generator */
        $generator = $this->get('venice.app.buy_url_generator');


        /** @var \AppBundle\Entity\BillingPlan $billingPlan */
        $billingPlan = $em->getRepository(\AppBundle\Entity\BillingPlan::class)
            ->findOneBy(['necktieId' => $this->CBID_to_necktieId[
                $this->FLOFIT_67dollarsPlusDVDs
            ]]);
        $product = $billingPlan->getProduct(); // if not null
        $url = $generator->generateBuyUrl($product, $billingPlan->getId());
//        return new JsonResponse(['url' => $url]);
        return $this->redirect($url);
//        if (!$user) {
//            return $this->redirect('http://25.flofit.pay.clickbank.net?cbfid=26406&vtid=introfreebe2408&cbskin=13358');
//        } else {
//            return $this->redirect('http://25.flofit.pay.clickbank.net?cbfid=26406&vtid=introfreebe2408&cbskin=13358&email=' . $user->getEmail() . '&name=' . $user->getFullName());
//        }
    }


    /**
     * @param $daysToTrial
     *
     * @return int
     */
    private function productsExtend495($daysToTrial)
    {
        switch ($daysToTrial) {
            case 1:
                return 424; // 14
            case 2:
                return 423; // 13
            case 3:
                return 422; // 12
            case 4:
                return 421; // 11
            case 5:
                return 420; // 10
            case 6:
                return 439; //  9
            case 7:
                return 438; //  8
            default:
                return 435; //
        }
    }


    /**
     * @param $daysToTrial
     *
     * @return int
     */
    private function productsExtend895($daysToTrial)
    {
        switch ($daysToTrial) {
            case 1:
                return 443; // 14
            case 2:
                return 444; // 13
            case 3:
                return 445; // 12
            case 4:
                return 450; // 11
            case 5:
                return 447; // 10
            case 6:
                return 448; //  9
            case 7:
                return 449; //  8
            default:
                return 446; //
        }
    }


    /**
     * @param $daysToTrial
     *
     * @return int
     */
    private function productsExtend1295($daysToTrial)
    {
        switch ($daysToTrial) {
            case 1:
                return 451; // 14
            case 2:
                return 452; // 13
            case 3:
                return 453; // 12
            case 4:
                return 454; // 11
            case 5:
                return 455; // 10
            case 6:
                return 456; //  9
            case 7:
                return 457; //  8
            default:
                return 458; //
        }
    }


    /**
     * @param $choice
     *
     * @return array
     */
    private function productsAfter495($choice)
    {
        switch ($choice) {
            case 1:
                return ['product' => 435, 'cbfid' => 27220]; // 4.95 trial
            case 2:
                return ['product' => 426, 'cbfid' => 26406];
            case 3:
                return ['product' => 427, 'cbfid' => 26406];
            default:
                return ['product' => 427, 'cbfid' => 26406];
        }
    }


    /**
     * @param $choice
     *
     * @return array
     */
    private function productsAfter895($choice)
    {
        switch ($choice) {
            case 1:
                return ['product' => 446, 'cbfid' => 26600]; // 8.95 trial
            case 2:
                return ['product' => 459, 'cbfid' => 26406]; // $67
            case 3:
                return ['product' => 20, 'cbfid' => 26406]; // Fe shipping product - $97
            default:
                return ['product' => 20, 'cbfid' => 26406]; // Fe shipping product - $97
        }
    }


    /**
     * @param $choice
     *
     * @return array
     */
    private function productsAfter1295($choice)
    {
        switch ($choice) {
            case 1:
                return ['product' => 458, 'cbfid' => 26601]; // 12.95 trial
            case 2:
                return ['product' => 460, 'cbfid' => 26406]; // $97
            case 3:
                return ['product' => 208, 'cbfid' => 26406]; // Fe shipping product - $197
            default:
                return ['product' => 208, 'cbfid' => 26406]; // Fe shipping product - $197
        }
    }
}