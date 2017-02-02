<?php

namespace AppBundle\SocialServices;

use AppBundle\Entity\User;
use AppBundle\Services\AbstractSubscibeService;
use Facebook\Facebook;

/**
 * Class FacebookSubscribeService
 * @package AppBundle\Login
 */
class FacebookSubscribeService extends AbstractSubscibeService
{
    const PARAMETER_APP_ID = 'appId';
    const PARAMETER_APP_SECRET = 'appSecret';


    /**
     * @return bool
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function haveSubscription(User $user)
    {
        if (is_null($user->getFacebookId()) || is_null($user->getFacebookAccessToken())) {
            return false;
        }

        $facebook = new Facebook(
            [
                'app_id' => $this->parameters[self::PARAMETER_APP_ID],
                'app_secret' => $this->parameters[self::PARAMETER_APP_SECRET],
            ]
        );
        try {
            $data = $facebook->get('/me?fields=id,name', $user->getFacebookAccessToken())->getHttpStatusCode();
            return $data === 200;
        } catch (\Exception $exception) {
            return false;
        }
    }


    /**
     * @return array
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    protected function loadParameters()
    {
        return [
            self::PARAMETER_APP_ID => $this->serviceContainer->getParameter('facebook_client_id'),
            self::PARAMETER_APP_SECRET => $this->serviceContainer->getParameter('facebook_client_secret')
        ];
    }


    public function renderSubscribeButton()
    {
        return $this->render('VeniceFrontBundle:FacebookSubscribeService/generateSubscribeButton.html.twig');
        //return $this->render("@ModernEntrepreneurGeneralBackendCore/FacebookSubscribeService/generateSubscribeButton.html.twig");
    }


    public function renderLogInButton()
    {
        return $this->render('VeniceFrontBundle:FacebookSubscribeService/generateLogInButton.html.twig');
    }


    public function renderRemoveButton()
    {
        return $this->render('VeniceFrontBundle:FacebookSubscribeService/generateRemoveButton.html.twig');
    }


    /**
     * @param $parameters
     *
     * @return \Symfony\Bundle\TwigBundle\TwigEngine
     * @throws \Twig_Error
     * @throws \RuntimeException
     */
    public function renderCommentsBlock($parameters)
    {
        return $this->render(
            'VeniceFrontBundle:FacebookSubscribeService:comments.html.twig',
            ['permanentLink' => $parameters['permanentLink']]
        );
    }


    /**
     * @param User $user
     *
     * @return bool
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \InvalidArgumentException
     */
    public function unsubscribe(User $user)
    {
        //return $this->render("@ModernEntrepreneurGeneralBackendCore/TwitterSubscribeService/comments.html.twig",array("permanentLink"=>$parameters["permanentLink"]));
        $user->setFacebookAccessToken(null);
        $user->setFacebookId(null);
        $em = $this->serviceContainer->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return true;
        //var_dump(json_decode($response));
    }
}