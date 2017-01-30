<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\BlogArticle;
use AppBundle\Form\Type\GlobalUserType;
use AppBundle\Newsletter\NewsletterOptimalization;
use AppBundle\Services\AbstractConnector;
use AppBundle\Services\MaropostConnector;
use Doctrine\ORM\EntityManager;
use FlofitEntities\Bundle\FlofitEntitiesBundle\FlofitEntities\CoreBundle\Vanilla\Message;
use FlofitEntities\Bundle\FlofitEntitiesBundle\FlofitEntities\NewsletterOptimalizationBundle\Answer;
use FlofitEntities\Bundle\FlofitEntitiesBundle\FlofitEntities\NewsletterOptimalizationBundle\UserAnswer;
use FrontBundle\Helpers\Ajax;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @Route("/profile", name="core_front_user_profile_edit")
     * @Route("/profile/", name="core_front_user_profile_edit")
     * @param Request $request
     *
     * @return Response
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \OutOfBoundsException
     * @throws \LogicException
     */
    public function profileAction(Request $request)
    {
        $fields = [
            'profilePhotoWithDeleteButton',
            'fullName',
            'username',
            'email',
            'fullPassword',
            'dateOfBirth',
            'preferredUnits',
            'location',
            'socialNetworks'
        ];

        $items  = [];

        $originalUser = clone $this->getUser();

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var MaropostConnector $maropostConnector */
        $maropostConnector = $this->get('flofit.services.maropost_connector');

        /* @todo
        $newsletterOptimizationService = $this->get('flofit.newsletter_optimalization');
        if (!$user->isMaropostSynced()) {
            $newsletterOptimizationService->maropostSync($user, $maropostConnector->getUserInfo($user));
            $user->setMaropostSynced(true);
            $entityManager->persist($user);
            $entityManager->flush();
        }*/

        $session = $this->get('session');
        $plainTextPass = $session->get('plainPassword');
        if (null !== $plainTextPass) {
            //$amemberConnector = $this->get('modern_entrepreneur_global_backend_core.amember_connector');
            //$amemberConnector->changeUserPassword($user, $plainTextPass);

            // change password necktie

            // @todo
            //$session->remove('plainPassword');
        }

        foreach ($fields as $field) {
//            $formType = new GlobalUserType($user, $field);

            $form = $this
                ->createForm(GlobalUserType::class, $user, ['attr' => ['class' => 'trinity-ajax'], 'field' => $field])
                ->add(
                    'submit',
                    SubmitType::class,
                    [
                        'label' => 'Save',
                        'attr'  => [
                            'data-class-after-click'   => 'progress-saving',
                            'data-disable-after-click' => true,
                            'displayClose'             => true,
                        ]
                    ]
                );

            $form->handleRequest($request);

            $entityManager = $this->getDoctrine()->getManager();

            /** @var User $entity */
            $entity = $form->getData();
            $errors = true;

            if ($field === 'profilePhotoWithDeleteButton' &&
                $form->isSubmitted() &&
                $form->has(GlobalUserType::REMOVE_BUTTON_NAME) &&
                $form->get(GlobalUserType::REMOVE_BUTTON_NAME)->isClicked()
            ) {
                $profilePhoto = $entity->getProfilePhoto();

                $entityManager->remove($profilePhoto);

                $entity->setProfilePhoto(null);

                $entityManager->flush();
                $errors = false;
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $userManager = $this->get('fos_user.user_manager');

                /** @var AbstractConnector $connector */
                $connector = $this->get($this->getParameter('connector_service_name'));

                switch ($field) {
                    case 'fullPassword':
                        if ($connector->changeUserPassword($user, $form->get('plainPassword')->getData())) {
                            $userManager->updatePassword($user);
                        }
                        break;

                    case 'username':
                        $usernameCheck = $userManager->findUserByUsername($form->get('username')->getData());

                        if (null === $usernameCheck && $connector->updateUser($user, ['username'])) {
                            $userManager->updateUser($entity);
                        } else {
                            $this->addFlash('danger', 'This username is used. Sorry.');

                            $user = $originalUser;
                        }

                        break;

                    case 'email':
                        $emailCheck = $userManager->findUserByUsername($form->get('email')->getData());

                        if (null === $emailCheck && $connector->updateUser($user, ['email'])) {
                            $userManager->updateUser($entity);
                        } else {
                            $this->addFlash('danger', 'This email is used. Sorry.');
                            $user = $originalUser;
                        }

                        break;
                    case 'dateOfBirth':
                        // get age tags (categories)
                        $oldTag = $maropostConnector->getAgeTag($originalUser->getAge());
                        $newTag = $maropostConnector->getAgeTag($entity->getAge());
                        if ($oldTag !== $newTag) {
                            // modify user-answer
                            $answer = $entityManager
                                ->getRepository(Answer::class)
                                ->findOneBy(['tag' => $newTag]);
                            $userAnswer = new UserAnswer($user, $answer, $answer->getQuestion());
//                            $userAnswer->setUser($user);
//                            $userAnswer->setAnswer($answer);
//                            $userAnswer->setQuestion($answer->getQuestion());

                            $entityManager->persist($userAnswer);
                            $entityManager->flush();

                            // modify maropost tag
                            $maropostConnector->removeTags($user, [$oldTag]);
                            $maropostConnector->addTags($user, [$newTag]);
                        }

                        // update user anyway
                        $this->addFlash('info', 'User profile successfully updated');
                        $userManager->updateUser($entity);
                        break;
                    default:
                        $userManager->updateUser($entity);

                        if ($field === 'profilePhotoWithDeleteButton' &&
                            $entity->getProfilePhoto() &&
                            null === $entity->getProfilePhoto()->getOriginalPhotoUrl()
                        ) {
                            $generator = $this->get('general_backend_core.services.profile_photo_url_generator');

                            $originalUrl = $generator->generateUrlToOriginalPhoto($entity->getProfilePhoto());
                            $croppedUrl  = $generator->generateUrlToCroppedPhoto($entity->getProfilePhoto());

                            $entity->getProfilePhoto()->setOriginalPhotoUrl($originalUrl);
                            $entity->getProfilePhoto()->setCroopedPhotoUrl($croppedUrl);

                            $entityManager->persist($entity->getProfilePhoto());
                            $entityManager->flush();
                        }

                        $this->addFlash('info', 'User profile successfully updated');

                        break;
                }
            }

            if ($form->isSubmitted() && $request->isXmlHttpRequest()) {
                if ($errors) {
                    foreach ($form->getErrors(true) as $error) {
                        $this->addFlash('warning', $error->getMessage());
                    }
                }

                return $this->renderJsonTrinity(
                    'VeniceFrontBundle:Core:edit.html.twig',
                    ['form'=>$form->createView(),'user' => $user],
                    [$field.'Block', 'profileHeaderUserBlock', 'profileJumbotronUserBlock', 'publicLinkBlock'],
                    $form->isValid()? 'close': null
                );
            }

            $items[$field] = $form->createView();
        }

        $fistNewsOptimizationForm     = new NewsletterOptimalization($this->container);
        $firstFormCustomizeNewsletter = $fistNewsOptimizationForm->createForm(1, $this->getUser()); // group  1 in db

        return $this->render(
            'VeniceFrontBundle:Core:edit.html.twig',
            [
                'pageElements' => $items,
                'user'         => $this->getUser(),
                'fistNewsOptimizationForm' => $firstFormCustomizeNewsletter->createView()
            ]
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
     * @Route("/profile/edit")
     */
    public function profileEditAction()
    {
        return $this->redirectToRoute('core_front_user_profile_edit');
    }

    /**
     * @Route("/profile/privacy", name="core_front_user_profile_privacy")
     * @param Request $request
     *
     * @return Response
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @internal param Request $request
     */
    public function privacyAction(Request $request)
    {
        /** @var GlobalUser $user */
        $user = $this->getUser();
        $privacySettings = $user->getPrivacySettings();
        $disableAll = false;

        $fields = [
            'publicProfile',
            'displayEmail',
            'birthDateStyle',
            'displayLocation',
            'displayForumActivity',
            'displaySocialMedia'
        ];
        $privacyForms = [];
        foreach ($fields as $field) {
            $formRow = [];
            $formType = new PrivacySettingsType($this->getUser(), $field);

            $form = $this
                ->createForm(
                    $formType,
                    $privacySettings,
                    [
                        'attr'=> [
                            'class'=>'trinity-ajax',
                            'data-on-submit-callback'=>'privacyFormSubmit',
                            'data-ajax-done-callback'=>'privacyFormAjaxDone'
                        ]
                    ]
                )
                ->add('submit', 'submit', ['label' => 'Save', 'attr' => ['data-after-click' => 'Saving']]);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var PrivacySettings $entity */
                $entity = $form->getData();

                if ($field === 'publicProfile' && !$entity->isPublicProfile()) {
                    $disableAll = true;
                    $entity->disableAll();
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($entity);
                $entityManager->flush();

                $this->addFlash('info', 'User profile successfully updated');
            }

            $formRow['label'] = $formType->getLabel($form, $field);
            $formRow['value'] = $form->get($field)->getData();

            $formField = $form->get($field);


            $value = $form->get($field)->getViewData();
            if (array_key_exists('choices', $formField->getConfig()->getOptions()) &&
                array_key_exists($value, $formField->getConfig()->getOptions()['choices'])) {
                    $formRow['value'] = $formField->getConfig()->getOptions()['choices'][$value];
            }

            $formRow['icon'] = '';
            if (array_key_exists('icon', $form->get($field)->getConfig()->getOptions()['attr'])) {
                $formRow['icon'] = $form->get($field)->getConfig()->getOptions()['attr']['icon'];
            }

            $formRow['form'] = $form->createView();
            $formRow['id'] = $field;

            if (!$disableAll && $form->isSubmitted() && $request->isXmlHttpRequest()) {
                return $this->renderJsonTrinity(
                    'SecurityBundle:Collector:security.html.twig',
                    ['pageElement'=>$formRow, 'disabled' => false, 'user'=>$user],
                    ['privacyFormBlock'.$field=>'privacyFormBlock'],
                    'close'
                );
            }
            $privacyForms[] = $formRow;
        }

        return $this->renderTrinity(
            'SecurityBundle:Collector:security.html.twig',
            ['pageElements' => $privacyForms, 'disabled' => $disableAll, 'user'=>$user],
            ['privacyBlock'],
            'close'
        );
    }

    /**
     * @Route("/profile/user-payments", name="core_front_user_order_history")
     * @param Request $request
     *
     * @return Response
     * @throws \LogicException
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @internal param AmemberConnector $amemberConnector
     */
    public function orderHistoryAction(Request $request)
    {
        /** @var GlobalUser $user */
        $user = $this->getUser();

        $connector = $this->get($this->getParameter('connector_service_name'));
        $userInvoices = $connector->getUserInvoices($user);

        $flofitFeaturesService = $this->get('front.twig.flofit_features');

        $digitalParameters = new CBBuyParameters();
        $digitalParameters->setBuyId(63);

        $shippingParameters = new CBBuyParameters();
        $shippingParameters->setBuyId(64);

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
     * @Route("/profile/newsletters", name="core_front_user_profile_newsletters")
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @internal param Request $request
     */
    public function newslettersAction()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $maropostConnector = $this->get('flofit.services.maropost_connector');
        $newsletterOptimizationService = $this->get('flofit.newsletter_optimalization');

        if (!$user->isMaropostSynced()) {
            $newsletterOptimizationService->maropostSync($user, $maropostConnector->getUserInfo($user));
            $user->setMaropostSynced(true);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $secondNewsOptimizationForm = new NewsletterOptimalization($this->container);
        $secondFormCustomizeNewsletter = $secondNewsOptimizationForm->createForm(2, $this->getUser()); // group 2 in DB

        return $this->render(
            'VeniceFrontBundle:Core:newsletters.html.twig',
            ['secondNewsOptimizationForm' => $secondFormCustomizeNewsletter->createView()]
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
