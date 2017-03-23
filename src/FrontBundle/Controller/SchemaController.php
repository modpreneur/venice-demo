<?php

namespace FrontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class SchemaController
 * @package FrontBundle\Controller
 *
 * @Route("/schema")
 */
class SchemaController extends Controller
{
    /**
     * @Route("/login", name="schema-login-page")
     */
    public function loginAction()
    {
        return $this->render('VeniceFrontBundle:Schema:login.html.twig');
    }


    /**
     * @Route("/request", name="schema-request-page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resettingAction()
    {
        return $this->render('VeniceFrontBundle:Schema:request.html.twig');
    }

    /**
     * @Route("/check-email", name="schema-check-email-page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkEmailAction()
    {
        return $this->render('VeniceFrontBundle:Schema:check_email.html.twig');
    }

    /**
     * @Route("/already-requested", name="schema-already-requested-page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function alreadyRequestedAction()
    {
        return $this->render('VeniceFrontBundle:Schema:passwordAlreadyRequested.html.twig');
    }

    /**
     * @Route("/change-password", name="schema-change-password-page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordChangeAction()
    {
        return $this->render('VeniceFrontBundle:Schema:change_password.html.twig');
    }

    /**
     * @Route("/new", name="schema-new-page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        return $this->render('VeniceFrontBundle:Schema:new.html.twig');
    }
}
