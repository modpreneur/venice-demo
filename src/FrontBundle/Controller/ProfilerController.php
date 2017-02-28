<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Newsletter\Answer;
use AppBundle\Entity\Newsletter\UserAnswer;
use AppBundle\Entity\User;
use AppBundle\Form\Type\GlobalUserType;
use AppBundle\Form\Type\PrivacySettingsType;
use AppBundle\Helpers\FlashMessages;
use AppBundle\Newsletter\NewsletterOptimalization;
use AppBundle\Privacy\PrivacySettings;
use AppBundle\Services\AbstractConnector;
use AppBundle\Services\MaropostConnector;
use Doctrine\ORM\EntityManager;
use FrontBundle\Helpers\Ajax;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;



/**
 * Class ProfilerController
 * @package FrontBundle\Controller
 *
 * @Route("/profile")
 */
class ProfilerController extends Controller
{
    use Ajax;

    /**
     * @Route("/", name="core_front_user_profile_edit")
     * @Route("/", name="core_front_user_profile_edit")
     * @param Request $request
     *
     * @return Response
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
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
            'socialNetworks',
        ];

        $items = [];

        $originalUser = clone $this->getUser();

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var MaropostConnector $maropostConnector */
        $maropostConnector = $this->get('flofit.services.maropost_connector');
        $newsletterOptimizationService = $this->get('flofit.newsletter_optimalization');


        /* @todo
        if (!$user->isMaropostSynced()) {
         *
         * $newsletterOptimizationService->maropostSync($user, $maropostConnector->getUserInfo($user));
         * $user->setMaropostSynced(true);
         * $entityManager->persist($user);
         * $entityManager->flush();
         * }*/

        $session = $this->get('session');
        //$plainTextPass = $session->get('plainPassword');

        foreach ($fields as $field) {
            $form = $this
                ->createForm(GlobalUserType::class, $user, ['attr' => ['class' => 'trinity-ajax'], 'field' => $field])

                ->add(
                    'submit',
                    SubmitType::class,
                    [
                        'label' => 'Save',
                        'attr'  => [
                            'data-class-after-click' => 'progress-saving',
                            'data-disable-after-click' => true,
                            'displayClose' => true,
                        ]
                    ]
                );

            if ($request->request->has('global_user')) {
                if (array_key_exists($field, $request->request->get('global_user'))) {
                    $form->handleRequest($request);
                }
            }

            $entityManager = $this->getDoctrine()->getManager();

            /** @var User $entity */
            $entity = $form->getData();
            $errors = true;

            if ($form->isSubmitted() &&
                $field === 'profilePhotoWithDeleteButton' &&
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

                        if (is_null($usernameCheck) && $connector->updateUser($user, ['username'])) {
                            $userManager->updateUser($entity);
                        } else {
                            $this->addFlash(FlashMessages::DANGER, 'This username is used. Sorry.');

                            $user = $originalUser;
                        }

                        break;

                    case 'email':
                        $emailCheck = $userManager->findUserByUsername($form->get('email')->getData());

                        if (is_null($emailCheck) && $connector->updateUser($user, ['email'])) {
                            $userManager->updateUser($entity);
                        } else {
                            $this->addFlash(FlashMessages::DANGER, 'This email is used. Sorry.');
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
                        $this->addFlash(FlashMessages::INFO, 'User profile successfully updated');
                        $userManager->updateUser($entity);
                        break;
                    default:
                        $userManager->updateUser($entity);

                        if ($field === 'profilePhoto' &&
                            $entity->getProfilePhoto() &&
                            is_null($entity->getProfilePhoto()->getOriginalPhotoUrl())
                        ) {
                            $generator = $this->get('general_backend_core.services.profile_photo_url_generator');

                            $originalUrl = $generator->generateUrlToOriginalPhoto($entity->getProfilePhoto());
                            $croopedUrl = $generator->generateUrlToCroppedPhoto($entity->getProfilePhoto());

                            $entity->getProfilePhoto()->setOriginalPhotoUrl($originalUrl);
                            $entity->getProfilePhoto()->setCroopedPhotoUrl($croopedUrl);

                            $entityManager->persist($entity->getProfilePhoto());
                            $entityManager->flush();
                        }

                        $this->addFlash(FlashMessages::INFO, 'User profile successfully updated.');

                        break;
                }
            }

            if ($form->isSubmitted() && $request->isXmlHttpRequest()) {
                if ($errors) {
                    foreach ($form->getErrors(true) as $error) {
                        $this->addFlash(FlashMessages::WARNING, $error->getMessage());
                    }
                }

                return $this->renderJsonTrinity(
                    'VeniceFrontBundle:Core:edit.html.twig',
                    ['form' => $form->createView(), 'user' => $user, 'pageElements' => $items],
                    [$field . 'Block', 'profileHeaderUserBlock', 'profileJumbotronUserBlock', 'publicLinkBlock'],
                    $form->isValid() ? 'close' : null
                );
            }

            $items[$field] = $form->createView();
        }


        $fistNewsOptimizationForm = new NewsletterOptimalization($this->container);
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
     * @Route("/edit")
     */
    public function profileEditAction()
    {
        return $this->redirectToRoute('core_front_user_profile_edit');
    }


    /**
     * @Route("/privacy", name="core_front_user_profile_privacy")
     * @param Request $request
     *
     * @return Response
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
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
        /** @var User $user */
        $user = $this->getUser();

        $privacySettings = $this
            ->get('flofit.privacy_settings')
            ->getPrivacySettings($user);

        $disableAll = false;

        $fields = [
            'publicProfile',
            'displayEmail',
            'birthDateStyle',
            'displayLocation',
            'displayForumActivity',
            'displaySocialMedia',
        ];

        $privacyForms = [];
        foreach ($fields as $field) {
            $formRow = [];

            $form = $this
                ->createForm(
                    PrivacySettingsType::class,
                    $privacySettings,
                    [
                        'user' => $user,
                        'attr' => [
                            'class' => 'trinity-ajax',
                            'data-on-submit-callback' => 'privacyFormSubmit',
                            'data-ajax-done-callback' => 'privacyFormAjaxDone'
                        ],
                        'field' => $field
                    ]
                )
                ->add('submit', SubmitType::class, ['label' => 'Save', 'attr' => ['data-after-click' => 'Saving']]);


            if ($request->request->has('privacy_settings')) {
                if (array_key_exists($field, $request->request->get('privacy_settings'))) {
                    $form->handleRequest($request);
                }
            }

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var PrivacySettings $entity */
                $entity = $form->getData();

                if ($field === 'publicProfile' && !$entity->isPublicProfile()) {
                    $disableAll = true;
                    $entity->disableAll();
                }

                $this->get('flofit.privacy_settings')
                    ->save($entity, $user);

                $this->addFlash(FlashMessages::INFO, 'User profile successfully updated');
            }

            $formRow['label'] = PrivacySettingsType::getLabel($form, $field);
            $formRow['value'] = $form->get($field)->getData();

            $formField = $form->get($field);

            $value = $form->get($field)->getViewData();
            if (array_key_exists('choices', $formField->getConfig()->getOptions()) &&
                array_key_exists($value, $formField->getConfig()->getOptions()['choices'])
            ) {
                $formRow['value'] = $formField->getConfig()->getOptions()['choices'][$value];
            }

            $formRow['icon'] = '';
            if (array_key_exists('icon', $form->get($field)->getConfig()->getOptions()['attr'])) {
                $formRow['icon'] = $form->get($field)->getConfig()->getOptions()['attr']['icon'];
            }

            $formRow['form'] = $form->createView();
            $formRow['id']   = $field;

            if ($form->isSubmitted() && $request->isXmlHttpRequest()) {
                if (!$disableAll) {
                    return $this->renderJsonTrinity(
                        'VeniceFrontBundle:Core:privacy.html.twig',
                        ['pageElement' => $formRow, 'disabled' => false, 'user' => $user],
                        ['privacyFormBlock' . $field => 'privacyFormBlock'],
                        'close'
                    );
                }
            }
            $privacyForms[] = $formRow;
        }

        return $this->renderTrinity(
            'VeniceFrontBundle:Core:privacy.html.twig',
            ['pageElements' => $privacyForms, 'disabled' => $disableAll, 'user' => $user],
            ['privacyBlock'],
            'close'
        );
    }


    /**
     * @Route("/newsletters", name="core_front_user_profile_newsletters")
     * @return Response
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
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
     * @Route("/unsubscribe/{socialSite}")
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
     * @Route("/user-payments", name="core_front_user_order_history")
     * @param Request $request
     *
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
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
                    'viewData' => null,
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
                        'attr' =>
                        [
                            'id' => 'form'.$userInvoice->getId(),
                            'class' => 'trinity-ajax',
                            'data-on-submit-callback' => 'paymentsFormSubmit'
                        ]
                    ]
                );

                $form->add('invoice', HiddenType::class, ['data' => $userInvoice->getId()]);

                $isImmersion = isset($userInvoice->getItems()[0]) &&
                    $userInvoice->getItems()[0]->haveCategory('Platinum Club RECURING');

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
     * @Route("/p/{username}", name="core_front_user_public_profile")
     * @param User $user
     * @return Response
     */
    public function publicProfileAction(User $user)
    {
        $forumService = $this->get('flofit.prod_env_forum_connector');

        $posts = $forumService->getLatestForumPostsOfUser($user, $user, 4);
        $link = $this->getParameter('forum_send_new_message');

        $privacySettings = $this
            ->get('flofit.privacy_settings')
            ->getPrivacySettings($user);

        return $this->render(
            'VeniceFrontBundle:Core:publicProfile.html.twig',
            [
                'user' => $user,
                'sendMessageLink' => $link,
                'forumPosts'   => $posts,
                'userSettings' => $privacySettings
            ]
        );
    }
}
