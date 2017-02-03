<?php

namespace FrontBundle\Helpers;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Ajax
 * @package FrontBundle\Helpers
 */
trait Ajax
{
    public static $AjaxSuccess = 200;

    public static $AjaxError = 500;

    /**
     * @param $view
     * @param array $viewParameters
     * @param $block
     *
     * @return mixed
     */
    public function renderAjax($view, array $viewParameters = [], $block)
    {
        $templateContent = $this->get('twig')->loadTemplate($view);

        return $templateContent->renderBlock($block, $viewParameters);
    }

    /**
     * @param Form $form
     *
     * @return mixed
     */
    public function renderFormTrinity(Form $form)
    {
        /** @var Controller $this */
        return $this->get('twig')->getExtension('form')->renderer->searchAndRenderBlock($form->createView(), 'widget');
    }

    /**
     * @param $view
     * @param array $viewParameters
     * @param array $block
     * @param null $hashValue
     * @param int $statusCode
     *
     * @return JsonResponse
     * @throws \InvalidArgumentException
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     */
    public function renderJsonTrinity(
        $view,
        array $viewParameters = [],
        array $block = [],
        $hashValue = null,
        $statusCode = 200
    ) {
        /** @var Controller|Ajax $this */
        /** @var \Twig_Template $templateContent */
        $templateContent = $this->get('twig')->loadTemplate($view);

        $jsonParameters = [];
        $jsonParameters['method'] = 'view';
        $jsonParameters['flashMessages'] = $this->generateFlashMessages();

        if (null !== $hashValue) {
            $jsonParameters['hashValue'] = $hashValue;
        }

        foreach ($block as $key => $part) {
            $toBlock = $part;
            if (!is_numeric($key)) {
                $toBlock = $key;
            }
            $jsonParameters[$toBlock] = $templateContent->renderBlock($part, $viewParameters);
        }

        $response = new JsonResponse($jsonParameters);

        $response->setStatusCode($statusCode);

        return $response;
    }

    /**
     * @param $view
     * @param array $viewParameters
     * @param array $block
     * @param null $hashValue
     * @param int $statusCode
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \InvalidArgumentException
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     */
    public function renderTrinity(
        $view,
        array $viewParameters = [],
        array $block = [],
        $hashValue = null,
        $statusCode = 200
    ) {
        /** @var Controller|Ajax $this */
        /** @var Request $request */
        $request = $this->get('request_stack')->getCurrentRequest();

        if ($request->isXmlHttpRequest()) {
            return $this->renderJsonTrinity($view, $viewParameters, $block, $hashValue, $statusCode);
        }

        return $this->render($view, $viewParameters);
    }

    /**
     * @param $url
     * @param int $status
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectTrinity($url, $status = 302)
    {
        /** @var Controller|Ajax $this */
        /** @var Request $request */
        $request = $this->get('request_stack')->getCurrentRequest();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['method' => 'redirect', 'url' => $url]);
        }

        return $this->redirect($url, $status);
    }

    /**
     * @param $route
     * @param array $parameters
     * @param int $status
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToRouteTrinity($route, array $parameters = [], $status = 302)
    {
        /** @var Controller|Ajax $this */
        return $this->redirectTrinity($this->generateUrl($route, $parameters), $status);
    }


    /**
     * @return string
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     */
    private function generateFlashMessages()
    {
        /** @var Controller|Ajax $this */
        $messages = '';
        $templateContent = $this->get('twig')->loadTemplate('VeniceFrontBundle:Default:flashMessage.html.twig');

        /** @var Session $session */
        $session = $this->get('session');

        foreach ($session->getFlashBag()->all() as $type => $flashMessages) {
            foreach ($flashMessages as $flashMessage) {
                $messages .= $templateContent->render(['type' => $type, 'id' => mt_rand(), 'text' => $flashMessage]);
            }
        }

        return $messages;
    }
}