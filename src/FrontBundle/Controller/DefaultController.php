<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\BlogArticle;
use FrontBundle\Helpers\Ajax;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Entity\Invoice;
use Venice\FrontBundle\Controller\FrontController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController
 * @package FrontBundle\Controller
 *
 * @Route("/front")
 */
class DefaultController extends FrontController
{
    use Ajax;

    /**
     * @param Request $request
     *
     * @Route("/", name="front_index")
     * @Route("/", name="landing_page")
     *
     * @return Response
     * @throws \LogicException
     */
    public function indexAction(Request $request)
    {
        $logger = $this->get('logger');

        $socialService = $this->get('flofit.services.social_feed');

        $postsCount = $this->getParameter('social_stream_number_of_posts_downloaded');
        $socialStream = $socialService->getLatestPostsFromCache($postsCount);
        
        if (count($socialStream) === 0) {
            $socialStream = $socialService->getLatestPosts($postsCount);
        }

       // $socialService = $this->get('general_backend_core.services.social_feed');

        $entityManager = $this->getDoctrine()->getManager();

        return $this->render(
            'VeniceFrontBundle:Front:index.html.twig',
            [
                'socialPosts' => $socialStream,
                'messages' => [],
                'forumPosts' => '',
                'blogArticles' => [$entityManager->getRepository(BlogArticle::class)->findBy([], ['id'=>'DESC'], 2)],
                'productPosts' => [],
                'communityInboxUrl' => $this->container->getParameter('forum_read_conversation_url'),
                'communityForumUrl' => $this->container->getParameter('forum_url'),
                'workoutGuide'      => null,
                'nutritionGuide'    => null,
                'displayMobileAdv'  => false,
                'displayQuickStartGuide' => null,
                'firstLogin' => new \DateTime(),
            ]
        );
    }

    /**
     * @Route("profile/unsubscribe/{socialSite}")
     * @param $socialSite
     *
     * @return Response
     * @throws \LogicException
     * @internal param GlobalUser $user
     */
    public function unsubscribeAction($socialSite)
    {
        $subscribeService = $this->get('general_backend_core.services.'.$socialSite.'_subscribe_service');
        $user = $this->getUser();
        $subscribeService->unsubscribe($user);
        return $this->redirectToRoute('core_front_user_profile_edit');
    }


    /**
     * @Route("/p/{username}", name="core_front_user_public_profile")
     * @param User $user
     * @return Response
     */
    public function publicProfileAction(User $user)
    {
        $forumService = $this->get('general_backend_core.services.forum_connector');

        $forumService->setCustomAuthUser($user);

        $posts = $forumService->getLatestForumPostsOfUser($user, $user, 4);
        $link = $this->getParameter('forum_send_new_message');

        return $this->render(
            'VeniceFrontBundle:Core:publicProfile.html.twig',
            ['user' => $user,'sendMessageLink' => $link,'forumPosts' => $posts]
        );
    }



    /**
     * override FOSUSERBUNDLE - user show action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        return $this->redirectToRoute('core_front_user_profile_edit');
    }


    /**
     * @Route("/profile/user-payments", name="core_front_user_order_history")
     * @param Request $request
     *
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @internal param AmemberConnector $amemberConnector
     */
    public function orderHistoryAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $connector = $this->get($this->getParameter('connector_service_name'));
        $userInvoices = $connector->getUserInvoices($user);

        $flofitFeaturesService = $this->get('front.twig.flofit_features');

        $digitalParameters = new BillingPlan();
        //$digitalParameters->setBuyId(63);

        $shippingParameters = new BillingPlan();
        //$shippingParameters->setBuyId(64);

        $digitalProductLink = $flofitFeaturesService->generateOCBLinkByBuyParameters($digitalParameters, true, $user);
        $digitalShippingProductLink = $flofitFeaturesService->generateOCBLinkByBuyParameters(
            $shippingParameters,
            true,
            $user,
            [],
            'ocb-shipping'
        );
        if (count($userInvoices) === 0) {
            return $this->render(
                'VeniceFrontBundle:Core:orderHistory.html.twig',
                [
                    'viewData' =>null,
                    'digitalProductBuyLink' => $digitalProductLink,
                    'digitalShippingProductLink' => $digitalShippingProductLink,
                ]
            );
        }
        $viewData = [];

        foreach ($userInvoices as $userInvoice) {
            /** @var Invoice $userInvoice */
            $row = [];
            $row['invoice'] = $userInvoice;

            if ($userInvoice->getStatus() === Invoice::INVOICE_STATUS_RECURRING) {
                $form = $this->createFormBuilder(
                    null,
                    [
                        'attr'=>
                        [
                            'id'=>'form'.$userInvoice->getInvoiceId(),
                            'class'=>'trinity-ajax',
                            'data-on-submit-callback'=>'paymentsFormSubmit'
                        ]
                    ]
                );

                $form->add('invoice', HiddenType::class, ['data'=>$userInvoice->getInvoiceId()]);

                $isImmersion = isset($userInvoice->getInvoiceItems()[0]) &&
                    $userInvoice->getInvoiceItems()[0]->haveCategory('Platinum Club RECURING');

                $jsOnClickAction = 'return openCancelPopup(this, \''
                    .$userInvoice->getInvoiceItemNames()
                    .'\', {$isImmersion});';

                $form->add(
                    $userInvoice->getInvoiceId(),
                    ButtonType::class,
                    ['label'=>'Cancel','attr'=> ['onClick'=>$jsOnClickAction]]
                );

                $form = $form->getForm();
                $form->handleRequest($request);

                $parameters = $request->request->all();

                if (isset($parameters['form']['invoice']) &&
                    $form->isSubmitted() &&
                        $parameters['form']['invoice'] === $userInvoice->getInvoiceId()
                ) {
                    $connector->cancelInvoice($userInvoice, $user);

                    $userInvoice->setCanceled();

                    $this->addFlash('success', 'Invoice successfully canceled.');

                    return $this->renderJsonTrinity(
                        'VeniceFrontBundle:Core:orderHistory.html.twig',
                        ['orderHistoryData'=>$row],
                        ['orderHistory'.$userInvoice->getInvoiceId()=>'orderHistory']
                    );
                }

                $row['form'] = $form->createView();
            }

            $viewData[] = $row;
        }


        return $this->render(
            'VeniceFrontBundle:Core:orderHistory.html.twig',
            [
                'viewData' => $viewData,
                'digitalProductBuyLink' => $digitalProductLink,
                'digitalShippingProductLink' => $digitalShippingProductLink
            ]
        );
    }



    /**
     * @Route("/profile/login", name="core_front_login_profile")
     */
    public function loginAction()
    {
        return $this->render('VeniceFrontBundle:Core:login.html.twig');
    }
}
