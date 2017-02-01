<?php

namespace FrontBundle\Controller;


use AppBundle\Entity\Newsletter\Answer;
use AppBundle\Entity\Newsletter\UserAnswer;
use AppBundle\Entity\User;
use AppBundle\Form\Type\GlobalUserType;
use AppBundle\Helpers\FlashMessages;
use AppBundle\Newsletter\NewsletterOptimalization;
use AppBundle\Services\AbstractConnector;
use AppBundle\Services\MaropostConnector;
use Doctrine\ORM\EntityManager;
use FrontBundle\Helpers\Ajax;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Trinity\Bundle\SettingsBundle\Manager\SettingsManager;

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
            'socialNetworks'
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
         * $newsletterOptimizationService->maropostSync($user, $maropostConnector->getUserInfo($user));
         * $user->setMaropostSynced(true);
         * $entityManager->persist($user);
         * $entityManager->flush();
         * }*/

        $session = $this->get('session');
        $plainTextPass = $session->get('plainPassword');

        foreach ($fields as $field) {
            $formType = new GlobalUserType($user, $field);

            $form = $this
                ->createForm(GlobalUserType::class, $user, ['attr' => ['class' => 'trinity-ajax'], 'field' => $field])
                ->add(
                    'submit',
                    SubmitType::class,
                    [
                        'label' => 'Save',
                        'attr' => [
                            'data-class-after-click' => 'progress-saving',
                            'data-disable-after-click' => true,
                            'displayClose' => true,
                        ]
                    ]
                );

            $form->handleRequest($request);

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

                        if ($field === 'profilePhotoWithDeleteButton' &&
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

                        $this->addFlash(FlashMessages::INFO, 'User profile successfully updated');

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
                    ['form' => $form->createView(), 'user' => $user],
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
                'user' => $this->getUser(),
                'fistNewsOptimizationForm' => $firstFormCustomizeNewsletter->createView()
            ]
        );
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

        /** @var SettingsManager $settings */
        $settings = $this->get('trinity.settings');

        /** @var User $user */
        $user = $this->getUser();


        $privacySettings = $settings->get('userSettings', $user->getId(), 'user-settings');
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
                        'attr' => [
                            'class' => 'trinity-ajax',
                            'data-on-submit-callback' => 'privacyFormSubmit',
                            'data-ajax-done-callback' => 'privacyFormAjaxDone'
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
                $entityManager->flush($entity);

                $this->addFlash(FlashMessages::INFO, 'User profile successfully updated');
            }

            $formRow['label'] = $formType->getLabel($form, $field);
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
            $formRow['id'] = $field;

            if ($form->isSubmitted() && $request->isXmlHttpRequest()) {
                if (!$disableAll) {
                    return $this->renderJsonTrinity(
                        'SecurityBundle:Collector:security.html.twig',
                        ['pageElement' => $formRow, 'disabled' => false, 'user' => $user],
                        ['privacyFormBlock' . $field => 'privacyFormBlock'],
                        'close'
                    );
                }
            }
            $privacyForms[] = $formRow;
        }

        return $this->renderTrinity(
            'SecurityBundle:Collector:security.html.twig',
            ['pageElements' => $privacyForms, 'disabled' => $disableAll, 'user' => $user],
            ['privacyBlock'],
            'close'
        );
    }


    /**
     * @Route("/profile/newsletters", name="core_front_user_profile_newsletters")
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

}
