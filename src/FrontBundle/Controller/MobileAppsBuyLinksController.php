<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class StaticPagesController
 *
 * @Route("/mobile-apps")
 *
 * @package GeneralBackend\CoreBundle\Controller\Front
 */
class MobileAppsBuyLinksController extends Controller
{

// TODO: PETRBEL DELETE THIS OLD RUSTY CONTROLLER AFTER TESTING OF NEW ONE
//    /**
//     * @Route("/buy-link/extend-trial", name="buy_link_extend_trial_old")
//     */
//    public function buyLink36Action(Request $request)
//    {
//        /** @var User $user */
//        $user = $this->getUser();
//        if (!$user) {
//            $userId = $request->get('user_id', false);
//
//            /** @var EntityManager $entityManager */
//            $entityManager = $this->getDoctrine()->getManager();
//
//            /** @var User $user */
//            $user = $entityManager->getRepository(User::class)
//                ->findOneBy(['id' => $userId]);
//        }
//
//        $browser = $this->get('general_backend_core.services.browser');
//        $vtid = 'introfreebe2408';
//        $splitVariant = $request->cookies->get('flosplitvar');
//
//        if ($splitVariant) {
//            if ($splitVariant === 'control') {
//                $vtid = 'introfreebecon';
//            }
//            if ($splitVariant === 'io') {
//                $vtid = 'introfreebeio';
//            }
//            if ($splitVariant === 'an') {
//                $vtid = 'introfreebean';
//            }
//        }
//
//        $vtid = ''; //($browser->isMobile() || $browser->isTablet()) ? 'mo' . $vtid : $vtid;
//
//        if (!$user) {
//            return $this->redirect("http://41.flofit.pay.clickbank.net/?vtid={$vtid}&cbskin=13358&cbfid=26406");
//        } elseif (!$user->getTrialStart() || !$user->getTrialEnd()) {
//            return $this->redirect("http://41.flofit.pay.clickbank.net/?vtid={$vtid}&cbskin=13358&cbfid=26406&email={$user->getEmail()}&name={$user->getFirstName()} {$user->getLastName()}");
//        }
//
//        $userTrialStart = $user->getTrialStart();
//        $now = new \DateTime();
//        $daysToTrial = $now->diff($userTrialStart, true)->d + 1;
//
//        if ($user->hasProductOfferId('introFree')) {
//            $productId = $this->productsExtend895($daysToTrial);
////            $vtid = ($browser->isMobile() || $browser->isTablet()) ? 'mointroFree' : 'introFree';
//            $cbfid = '27220';
//        }
//        if ($user->hasProductOfferId('introFreePa')) {
//            $productId = $this->productsExtend895($daysToTrial);
////            $vtid = ($browser->isMobile() || $browser->isTablet()) ? 'mointroFreePa' : 'introFreePa';
//            $cbfid = '26600';
//        } elseif ($user->hasProductOfferId('introFreeHp')) {
//            $productId = $this->productsExtend1295($daysToTrial);
////            $vtid = ($browser->isMobile() || $browser->isTablet()) ? 'mointroFreeHp' : 'introFreeHp';
//            $cbfid = '26601';
//        } else {
//            $productId = $this->productsExtend895($daysToTrial);
//            $cbfid = '27220';
//        }
//
////        return $this->redirect("http://{$productId}.flofit.pay.clickbank.net/?cbfid={$cbfid}&vtid={$vtid}&cbskin=13358&email={$user->getEmail()}&name={$user->getFirstName()} {$user->getLastName()}");
//        return $this->redirect("http://{$productId}.flofit.pay.clickbank.net/?cbfid={$cbfid}&vtid=hello&cbskin=13358&email={$user->getEmail()}&name={$user->getFirstName()} {$user->getLastName()}");
//    }
//
//
//    /**
//     * @Route("/buy-link/after-trial", name="buy_link_after_trial_old")
//     */
//    public function buyLinkAfterTrialAction(Request $request)
//    {
//        $choice = $request->get('user_choice', false);
//        /** @var User $user */
//        $user = $this->getUser();
//        if (!$user) {
//            $userId = $request->get('user_id', false);
//
//            /** @var EntityManager $entityManager */
//            $entityManager = $this->getDoctrine()->getManager();
//
//            /** @var User $user */
//            $user = $entityManager->getRepository(User::class)
//                ->findOneBy(['id' => $userId]);
//        }
//
//        $browser = $this->get('general_backend_core.services.browser');
//
//        $browser = $this->get('general_backend_core.services.browser');
//
//        $vtid = ($browser->isMobile() || $browser->isTablet()) ? 'mo' : '';
//
//        $cbfId = 26406;//21773;
//        if (!$user || !$choice) {
//            return $this->redirect("http://30.flofit.pay.clickbank.net/?vtid={$vtid}&cbskin=13358&cbfid={$cbfId}");
//        }
//
//
//        if ($user->hasProductOfferId('introFree')) {
//            $prodCbf = $this->productsAfter895($choice);
//            $vtid .= 'introFree';
//        }
//        if ($user->hasProductOfferId('introFreePa')) {
//            $prodCbf = $this->productsAfter895($choice);
//            $vtid .= 'introFreePa';
//        } elseif ($user->hasProductOfferId('introFreeHp')) {
//            $prodCbf = $this->productsAfter1295($choice);
//            $vtid .= 'introFreeHp';
//        } else {
//            $prodCbf = $this->productsAfter495($choice);
//            $vtid .= 'introfrafbe2408';
//        }
//
//        $productId = $prodCbf['product'];
//        $cbfid = $prodCbf['cbfid'];
//
//        //upgrade popup 67 na cb
//        return $this->redirect("http://{$productId}.flofit.pay.clickbank.net/?vtid={$vtid}&cbskin={$cbfid}&cbfid={$cbfid}&email={$user->getEmail()}&name={$user->getFirstName()} {$user->getLastName()}");
//    }
//
//
//    /**
//     * @Route("/buy-link/upgrade-trial", name="buy_link_upgrade_trial_old")
//     */
//    public function buyLinkTrialUpgradeAction(Request $request)
//    {
//        /** @var User $user */
//        $user = $this->getUser();
//        if (!$user) {
//            $userId = $request->get('user_id', false);
//
//            /** @var EntityManager $entityManager */
//            $entityManager = $this->getDoctrine()->getManager();
//
//            /** @var User $user */
//            $user = $entityManager->getRepository('ModernEntrepreneurGeneralBackendCoreBundle:User')
//                ->findOneBy(['id' => $userId]);
//        }
//
//        if (!$user) {
//            return $this->redirect('http://25.flofit.pay.clickbank.net?cbfid=26406&vtid=introfreebe2408&cbskin=13358');
//        } else {
//            return $this->redirect('http://25.flofit.pay.clickbank.net?cbfid=26406&vtid=introfreebe2408&cbskin=13358&email=' . $user->getEmail() . '&name=' . $user->getFullName());
//        }
//    }
//
//
//    /**
//     * @param $daysToTrial
//     *
//     * @return int
//     */
//    private function productsExtend495($daysToTrial)
//    {
//        switch ($daysToTrial) {
//            case 1:
//                return 424; // 14
//            case 2:
//                return 423; // 13
//            case 3:
//                return 422; // 12
//            case 4:
//                return 421; // 11
//            case 5:
//                return 420; // 10
//            case 6:
//                return 439; //  9
//            case 7:
//                return 438; //  8
//            default:
//                return 435; //
//        }
//    }
//
//
//    /**
//     * @param $daysToTrial
//     *
//     * @return int
//     */
//    private function productsExtend895($daysToTrial)
//    {
//        switch ($daysToTrial) {
//            case 1:
//                return 443; // 14
//            case 2:
//                return 444; // 13
//            case 3:
//                return 445; // 12
//            case 4:
//                return 450; // 11
//            case 5:
//                return 447; // 10
//            case 6:
//                return 448; //  9
//            case 7:
//                return 449; //  8
//            default:
//                return 446; //
//        }
//    }
//
//
//    /**
//     * @param $daysToTrial
//     *
//     * @return int
//     */
//    private function productsExtend1295($daysToTrial)
//    {
//        switch ($daysToTrial) {
//            case 1:
//                return 451; // 14
//            case 2:
//                return 452; // 13
//            case 3:
//                return 453; // 12
//            case 4:
//                return 454; // 11
//            case 5:
//                return 455; // 10
//            case 6:
//                return 456; //  9
//            case 7:
//                return 457; //  8
//            default:
//                return 458; //
//        }
//    }
//
//
//    /**
//     * @param $choice
//     *
//     * @return array
//     */
//    private function productsAfter495($choice)
//    {
//        switch ($choice) {
//            case 1:
//                return ['product' => 435, 'cbfid' => 27220]; // 4.95 trial
//            case 2:
//                return ['product' => 426, 'cbfid' => 26406];
//            case 3:
//                return ['product' => 427, 'cbfid' => 26406];
//            default:
//                return ['product' => 427, 'cbfid' => 26406];
//        }
//    }
//
//
//    /**
//     * @param $choice
//     *
//     * @return array
//     */
//    private function productsAfter895($choice)
//    {
//        switch ($choice) {
//            case 1:
//                return ['product' => 446, 'cbfid' => 26600]; // 8.95 trial
//            case 2:
//                return ['product' => 459, 'cbfid' => 26406]; // $67
//            case 3:
//                return ['product' => 20, 'cbfid' => 26406]; // Fe shipping product - $97
//            default:
//                return ['product' => 20, 'cbfid' => 26406]; // Fe shipping product - $97
//        }
//    }
//
//
//    /**
//     * @param $choice
//     *
//     * @return array
//     */
//    private function productsAfter1295($choice)
//    {
//        switch ($choice) {
//            case 1:
//                return ['product' => 458, 'cbfid' => 26601]; // 12.95 trial
//            case 2:
//                return ['product' => 460, 'cbfid' => 26406]; // $97
//            case 3:
//                return ['product' => 208, 'cbfid' => 26406]; // Fe shipping product - $197
//            default:
//                return ['product' => 208, 'cbfid' => 26406]; // Fe shipping product - $197
//        }
//    }
}
