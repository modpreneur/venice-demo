<?php

namespace AppBundle\SocialServices;

use AppBundle\Entity\User;
use AppBundle\Services\AbstractSubscibeService;
use Twig_Environment;

/**
 * Class TwitterSubscribeService
 * @package AppBundle\Login
 */
class TwitterSubscribeService extends AbstractSubscibeService
{
    const PARAMETER_APP_ID     = 'appId';

    const PARAMETER_APP_SECRET = 'appSecret';


    /**
     * @param User $user
     *
     * @return bool
     */
    public function haveSubscription(User $user)
    {
        //debug
//        $this->serviceContainer->get("ladybug")->log($user->getTwitterId(),$user->getTwitterAccessToken());
        if (is_null($user->getTwitterId()) || is_null($user->getTwitterAccessToken())) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    protected function loadParameters()
    {
        return [
            self::PARAMETER_APP_ID=>$this->serviceContainer->getParameter('twitter_client_id'),
            self::PARAMETER_APP_SECRET=>$this->serviceContainer->getParameter('twitter_client_secret')
        ];
    }


    /**
     * @return \Symfony\Bundle\TwigBundle\TwigEngine
     * @throws \Twig_Error
     * @throws \RuntimeException
     */
    public function renderSubscribeButton()
    {
        return $this->render('VeniceFrontBundle:TwitterSubscribeService:generateSubscribeButton.html.twig');
    }


    /**
     * @return \Symfony\Bundle\TwigBundle\TwigEngine
     * @throws \Twig_Error
     * @throws \RuntimeException
     */
    public function renderRemoveButton()
    {
        return $this->render('VeniceFrontBundle:TwitterSubscribeService:generateRemoveButton.html.twig');
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
            'VeniceFrontBundle:TwitterSubscribeService:comments.html.twig',
            ['permanentLink' => $parameters['permanentLink']]
        );
    }


    /**
     * @param User $user
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function unsubscribe(User $user)
    {
        $user->setTwitterAccessToken(null);
        $user->setTwitterId(null);
        $em = $this->serviceContainer->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return true;
        //var_dump(json_decode($response));
    }
}
