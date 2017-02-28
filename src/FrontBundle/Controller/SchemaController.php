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
     * @Route("/new", name="schema-new-page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        return $this->render('VeniceFrontBundle:Schema:new.html.twig');
    }
}
