<?php
/**
 * Created by PhpStorm.
 * User: ondrejbohac
 * Date: 02.12.15
 * Time: 9:33
 */

namespace FrontBundle\Controller;

use AppBundle\Entity\AfterPurchaseSurvey;
use AppBundle\Entity\User;
use AppBundle\Form\Type\AfterPurchaseSurveyType;
use AppBundle\Services\MaropostConnector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SurveyController
 * @package FrontBundle\Controller
 */
class SurveyController extends Controller
{
    /**
     * @Route("/survey", name="core_after_purchase_survey")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \OutOfBoundsException
     */
    public function afterBuySurveyAction(Request $request)
    {
        $surveyManager = $this->get('flofit.survey_settings_manager');

        if (null === $request->get('email', null) && null === $this->getUser()) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        /** @var  $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $email = $request->get('email');
        /** @var User $user */
        $user = $this->getUser();
        if (null === $user || ($user && strtolower($user->getEmail()) === strtolower($email))) {
            $user = $entityManager->getRepository(User::class)
                ->findOneBy(['email' =>$email]);
        }

        if (null === $user) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $survey = $surveyManager->getSurvey($user);

        if (null !== $survey) {
            return $this->redirectToRoute('landing_page');
        }

        $survey = new AfterPurchaseSurvey();

        $form = $this->createForm(AfterPurchaseSurveyType::class, $survey, [
            'action' =>$this->generateUrl('core_after_purchase_survey_form')]);

        $form->get('email')->setData(str_replace(' ', '+', $request->get('email')));
        $form->get('redirect')->setData($request->get('redirect', ''));

        return $this->render('VeniceFrontBundle:Survey:afterPurchase.html.twig', [
            'afterPurchaseForm' => $form->createView(),
            'skipRedirect' => null === $request->get('redirect', null) ?
                $this->generateUrl('landing_page') : $request->get('redirect')]);
    }

    /**
     * @Route("/survey/form", name="core_after_purchase_survey_form")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     */
    public function afterBuySurveyForm(Request $request): RedirectResponse
    {
        $surveyManager = $this->get('flofit.survey_settings_manager');

        if (\strtoupper($request->getMethod()) !== 'POST') {
            return $this->redirectToRoute('core_after_purchase_survey');
        }

        $survey = new AfterPurchaseSurvey();

        $form = $this->createForm(AfterPurchaseSurveyType::class, $survey);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->redirectToRoute('core_after_purchase_survey');
        }

        $email = $form->get('email')->getData();

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);
        if (null === $user) {
            $user = $this->getUser();
        }

        $survey->setUser($user);

        $survey = $form->getData();

        $surveyManager->saveSurvey($survey, $user);

        $maropostConnector = $this->get('flofit.services.maropost_connector');

        $tags = [
            MaropostConnector::$age[$survey->getOld()],
            MaropostConnector::$gender[$survey->isMale()]
        ];

        $maropostConnector->addTags($user, $tags);

        $redirect = $form->get('redirect')->getData();

        if (\strlen($redirect) > 0) {
            return $this->redirect($redirect);
        }

        return $this->redirectToRoute('landing_page');
    }
}
