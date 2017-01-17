<?php

namespace FrontBundle\Controller;

use AppBundle\Services\VanillaForumConnector;
use FrontBundle\Helpers\Ajax;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\User;
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
     */
    public function indexAction(Request $request)
    {
        $logger = $this->get('logger');

       // $socialService = $this->get('general_backend_core.services.social_feed');


        return $this->render(
            'VeniceFrontBundle:Front:index.html.twig',
            [
                "socialPosts" => [],
                "messages" => [],
                "forumPosts" => '',
                "blogPosts" => [],
                "productPosts" => [],
                "communityInboxUrl" => $this->container->getParameter('forum_read_conversation_url'),
                "communityForumUrl" => $this->container->getParameter('forum_url'),
                "workoutGuide"      => null,
                "nutritionGuide"    => null,
                "displayMobileAdv" => false,
                "displayQuickStartGuide" => null,
                "firstLogin" => new \DateTime(),
            ]
        );
    }


    /**
     * @Route("profile/unsubscribe/{socialSite}")
     * @param $socialSite
     *
     * @return Response
     * @internal param GlobalUser $user
     */
    public function unsubscribeAction($socialSite)
    {
        $subscribeService = $this->get('general_backend_core.services.'.$socialSite.'_subscribe_service');
        $user = $this->getUser();
        $subscribeService->unsubscribe($user);
        return $this->redirectToRoute("core_front_user_profile_edit");
    }


    /**
     * @Route("/p/{username}", name="core_front_user_public_profile")
     * @param User $user
     * @return Response
     */
    public function publicProfileAction(User $user)
    {
        $forumService = $this->get("general_backend_core.services.forum_connector");

        $forumService->setCustomAuthUser($user);

        $posts = $forumService->getLatestForumPostsOfUser($user, $user, 4);
        $link = $this->getParameter("forum_send_new_message");

        return $this->render(":CoreBundle/Front/core:publicProfile.html.twig",array("user" => $user,"sendMessageLink" => $link,"forumPosts"=>$posts));
    }

    /**
     * @Route("/profile", name="core_front_user_profile_edit")
     * @Route("/profile/", name="core_front_user_profile_edit")
     * @param Request $request
     * @return Response
     */
    public function profileAction(Request $request)
    {
        $fields = array("profilePhotoWithDeleteButton","fullName", "username", "email", "fullPassword", "dateOfBirth", "preferredUnits", "location", "socialNetworks");
        $items = array();
        $originalUser = clone $this->getUser();

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var GlobalUser $user */
        $user = $this->getUser();

        $maropostConnector = $this->get('general_backend_core.services.maropost_connector');
        $newsletterOptimizationService = $this->get('newsletter_optimalization');

        if (!$user->isMaropostSynced()) {
            $newsletterOptimizationService->maropostSync($user, $maropostConnector->getUserInfo($user));
            $user->setMaropostSynced(true);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $session = $this->get('session');
        $plainTextPass = $session->get('plainPassword');
        if (!is_null($plainTextPass)) {
            $amemberConnector = $this->get('modern_entrepreneur_global_backend_core.amember_connector');
            $amemberConnector->changeUserPassword($user, $plainTextPass);

            $session->remove('plainPassword');
        }

        foreach ($fields as $field) {
            $formType = new GlobalUserType($user, $field);

            $form = $this
                ->createForm($formType, $user, array("attr"=>array("class"=>"trinity-ajax")))
                ->add("submit", "submit", array("label" => "Save","attr" => array("data-class-after-click" => "progress-saving","data-disable-after-click"=>true,"displayClose"=>true)));

            $form->handleRequest($request);

            $entityManager = $this->getDoctrine()->getManager();
            /** @var GlobalUser $entity */
            $entity = $form->getData();
            $errors = true;

            if($form->isSubmitted() && $field == "profilePhotoWithDeleteButton" && $form->has(GlobalUserType::REMOVE_BUTTON_NAME) && $form->get(GlobalUserType::REMOVE_BUTTON_NAME)->isClicked())
            {
                $profilePhoto = $entity->getProfilePhoto();

                $entityManager->remove($profilePhoto);

                $entity->setProfilePhoto(null);

                $entityManager->flush();
                $errors = false;
            }

            if ($form->isSubmitted() && $form->isValid())
            {
                $userManager = $this->get('fos_user.user_manager');

                /** @var AbstractConnector $connector */
                $connector = $this->get($this->getParameter("connector_service_name"));

                switch ($field)
                {
                    case "fullPassword":
                        if ($connector->changeUserPassword($user, $form->get("plainPassword")->getData())) {
                            $userManager->updatePassword($user);
                        }
                        break;

                    case "username":
                        $usernameCheck = $userManager->findUserByUsername($form->get("username")->getData());

                        if (is_null($usernameCheck) && $connector->updateUser($user,array("username"))) {
                            $userManager->updateUser($entity);
                        } else {
                            $this->addFlash(FlashMessages::DANGER,"This username is used. Sorry.");

                            $user = $originalUser;
                        }

                        break;

                    case "email":
                        $emailCheck = $userManager->findUserByUsername($form->get("email")->getData());

                        if (is_null($emailCheck) && $connector->updateUser($user,array("email"))) {
                            $userManager->updateUser($entity);
                        } else {
                            $this->addFlash(FlashMessages::DANGER,"This email is used. Sorry.");

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
                                ->getRepository('ModernEntrepreneurNewsletterOptimalizationBundle:Answer')
                                ->findOneBy(array('tag' => $newTag));
                            $userAnswer = new UserAnswer($user, $answer, $answer->getQuestion());
//                            $userAnswer->setUser($user);
//                            $userAnswer->setAnswer($answer);
//                            $userAnswer->setQuestion($answer->getQuestion());

                            $entityManager->persist($userAnswer);
                            $entityManager->flush();

                            // modify maropost tag
                            $maropostConnector->removeTags($user, array($oldTag));
                            $maropostConnector->addTags($user, array($newTag));
                        }

                        // update user anyway
                        $this->addFlash(FlashMessages::INFO,"User profile successfully updated");
                        $userManager->updateUser($entity);
                        break;
                    default:
                        $userManager->updateUser($entity);

                        if($field == "profilePhotoWithDeleteButton" && $entity->getProfilePhoto() && is_null($entity->getProfilePhoto()->getOriginalPhotoUrl()))
                        {
                            $generator = $this->get("general_backend_core.services.profile_photo_url_generator");

                            $originalUrl = $generator->generateUrlToOriginalPhoto($entity->getProfilePhoto());
                            $croopedUrl = $generator->generateUrlToCroppedPhoto($entity->getProfilePhoto());

                            $entity->getProfilePhoto()->setOriginalPhotoUrl($originalUrl);
                            $entity->getProfilePhoto()->setCroopedPhotoUrl($croopedUrl);

                            $entityManager->persist($entity->getProfilePhoto());
                            $entityManager->flush();
                        }

                        $this->addFlash(FlashMessages::INFO,"User profile successfully updated");

                        break;
                }
            }

            if ($form->isSubmitted() && $request->isXmlHttpRequest())
            {
                if($errors)
                {
                    foreach($form->getErrors(true) as $error)
                    {
                        $this->addFlash(FlashMessages::WARNING, $error->getMessage());
                    }
                }

                return $this->renderJsonTrinity(":CoreBundle/Front/core:edit.html.twig",
                    array("form"=>$form->createView(),"user"=>$user),
                    array($field."Block","profileHeaderUserBlock","profileJumbotronUserBlock","publicLinkBlock"),
                    $form->isValid()? "close": null);
            }

            $items[$field] = $form->createView();
        }

        $fistNewsOptimizationForm = new NewsletterOptimalization($this->container);
        $firstFormCustomizeNewsletter = $fistNewsOptimizationForm->createForm(1, $this->getUser()); // group  1 in db

        return $this->render(
            ':CoreBundle/Front/core:edit.html.twig',
            array(
                'pageElements' => $items,
                'user' => $this->getUser(),
                'fistNewsOptimizationForm' => $firstFormCustomizeNewsletter->createView()
            )
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
        return $this->redirectToRoute("core_front_user_profile_edit");
    }

    /**
     * @Route("/profile/privacy", name="core_front_user_profile_privacy")
     * @param Request $request
     * @return Response
     * @internal param Request $request
     */
    public function privacyAction(Request $request)
    {
        /** @var GlobalUser $user */
        $user = $this->getUser();
        $privacySettings = $user->getPrivacySettings();
        $disableAll = false;

        $fields = array(
            "publicProfile",
            "displayEmail",
            "birthDateStyle",
            "displayLocation",
            "displayForumActivity",
            "displaySocialMedia"
        );
        $privacyForms = array();
        foreach ($fields as $field)
        {
            $formRow = array();
            $formType = new PrivacySettingsType($this->getUser(), $field);

            $form = $this
                ->createForm($formType, $privacySettings,array("attr"=>array("class"=>"trinity-ajax","data-on-submit-callback"=>"privacyFormSubmit","data-ajax-done-callback"=>"privacyFormAjaxDone")))
                ->add("submit", "submit", array("label" => "Save", "attr" => array("data-after-click" => "Saving")));

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                /** @var PrivacySettings $entity */
                $entity = $form->getData();

                if($field == "publicProfile" && !$entity->isPublicProfile())
                {
                    $disableAll = true;
                    $entity->disableAll();
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($entity);
                $entityManager->flush($entity);

                $this->addFlash(FlashMessages::INFO,"User profile successfully updated");
            }

            $formRow["label"] = $formType->getLabel($form, $field);
            $formRow["value"] = $form->get($field)->getData();

            $formField = $form->get($field);


            $value = $form->get($field)->getViewData();
            if(array_key_exists("choices",$formField->getConfig()->getOptions()) &&
                array_key_exists($value,$formField->getConfig()->getOptions()["choices"]))
            {
                $formRow["value"] = $formField->getConfig()->getOptions()["choices"][$value];
            }

            $formRow["icon"] = "";
            if(array_key_exists("icon",$form->get($field)->getConfig()->getOptions()["attr"])){
                $formRow["icon"] = $form->get($field)->getConfig()->getOptions()["attr"]["icon"];
            }

            $formRow["form"] = $form->createView();
            $formRow["id"] = $field;

            if ($form->isSubmitted() && $request->isXmlHttpRequest())
            {
                if(!$disableAll)
                    return $this->renderJsonTrinity(
                        ":CoreBundle/Front/core:privacy.html.twig",
                        array("pageElement"=>$formRow, "disabled" => false, "user"=>$user),
                        array("privacyFormBlock".$field=>"privacyFormBlock"),
                        "close");
            }
            $privacyForms[] = $formRow;
        }

        return $this->renderTrinity(
            ":CoreBundle/Front/core:privacy.html.twig",
            array("pageElements" => $privacyForms, "disabled" => $disableAll, "user"=>$user),
            array("privacyBlock"),
            "close"
        );
    }

    /**
     * @Route("/profile/user-payments", name="core_front_user_order_history")
     * @return Response
     * @internal param AmemberConnector $amemberConnector
     */
    public function orderHistoryAction(Request $request)
    {
        /** @var GlobalUser $user */
        $user = $this->getUser();

        $connector = $this->get($this->getParameter("connector_service_name"));
        $userInvoices = $connector->getUserInvoices($user);

        $flofitFeaturesService = $this->get("general_backend_core.services.flofit_features");

        $digitalParameters = new CBBuyParameters();
        $digitalParameters->setBuyId(63);

        $shippingParameters = new CBBuyParameters();
        $shippingParameters->setBuyId(64);

        $digitalProductLink = $flofitFeaturesService->generateOCBLinkByBuyParameters($digitalParameters, true,$user);
        $digitalShippingProductLink = $flofitFeaturesService->generateOCBLinkByBuyParameters($shippingParameters, true, $user, array(), 'ocb-shipping');
        if(count($userInvoices) == 0)
        {
            return $this->render(":CoreBundle/Front/core:orderHistory.html.twig",
                array(
                    'viewData' =>null,
                    'digitalProductBuyLink' => $digitalProductLink,
                    'digitalShippingProductLink' => $digitalShippingProductLink,
                )
            );
        }
        $viewData = array();

        foreach($userInvoices as $userInvoice)
        {
            /** @var Invoice $userInvoice */
            $row = array();
            $row["invoice"] = $userInvoice;

            if($userInvoice->getStatus() == Invoice::INVOICE_STATUS_RECURRING)
            {
                $form = $this->createFormBuilder(null,
                    array("attr"=>
                        array(
                            "id"=>"form".$userInvoice->getInvoiceId(),
                            "class"=>"trinity-ajax",
                            "data-on-submit-callback"=>"paymentsFormSubmit"
                        )
                    ));

                $form->add("invoice","hidden",array("data"=>$userInvoice->getInvoiceId()));

                $isImmersion = isset($userInvoice->getInvoiceItems()[0]) && $userInvoice->getInvoiceItems()[0]->haveCategory("Platinum Club RECURING")? "1":"0";

                $jsOnClickAction = "return openCancelPopup(this, \"".$userInvoice->getInvoiceItemNames()."\", {$isImmersion});";

                $form->add(
                    $userInvoice->getInvoiceId(),
                    "button",
                    array("label"=>"Cancel","attr"=>array("onClick"=>$jsOnClickAction))
                );

                $form = $form->getForm();
                $form->handleRequest($request);

                $parameters = $request->request->all();

                if($form->isSubmitted() && isset($parameters["form"]["invoice"]) && $parameters["form"]["invoice"] == $userInvoice->getInvoiceId())
                {
                    $connector->cancelInvoice($userInvoice,$user);

                    $userInvoice->setCanceled();

                    $this->addFlash(FlashMessages::SUCCESS, "Invoice successfully canceled.");

                    return $this->renderJsonTrinity(
                        ":CoreBundle/Front/core:orderHistory.html.twig",
                        array(
                            "orderHistoryData"=>$row
                        ),
                        array(
                            "orderHistory".$userInvoice->getInvoiceId()=>"orderHistory"
                        )
                    );
                }

                $row["form"] = $form->createView();
            }

            $viewData[] = $row;
        }


        return $this->render(
            ":CoreBundle/Front/core:orderHistory.html.twig",
            array(
                "viewData" => $viewData,
                "digitalProductBuyLink" => $digitalProductLink,
                "digitalShippingProductLink" => $digitalShippingProductLink
            )
        );
    }

    /**
     * @Route("/profile/newsletters", name="core_front_user_profile_newsletters")
     * @param Request $request
     * @return Response
     * @internal param Request $request
     */
    public function newslettersAction()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var GlobalUser $user */
        $user = $this->getUser();

        $maropostConnector = $this->get('general_backend_core.services.maropost_connector');
        $newsletterOptimizationService = $this->get('newsletter_optimalization');

        if (!$user->isMaropostSynced()) {
            $newsletterOptimizationService->maropostSync($user, $maropostConnector->getUserInfo($user));
            $user->setMaropostSynced(true);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $secondNewsOptimizationForm = new NewsletterOptimalization($this->container);
        $secondFormCustomizeNewsletter = $secondNewsOptimizationForm->createForm(2, $this->getUser()); // group 2 in DB

        return $this->render(
            ':CoreBundle/Front/core:newsletters.html.twig',
            array('secondNewsOptimizationForm' => $secondFormCustomizeNewsletter->createView())
        );
    }

    /**
     * @Route("/profile/login", name="core_front_login_profile")
     */
    public function loginAction()
    {
        return $this->render(":CoreBundle/Front/core:login.html.twig");
    }
}
