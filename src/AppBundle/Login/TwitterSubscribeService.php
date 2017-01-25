<?php

namespace AppBundle\Login;

use AppBundle\Entity\User;
use Twig_Environment;

/**
 * Class TwitterSubscribeService
 * @package AppBundle\Login
 */
class TwitterSubscribeService extends AbstractSubscibeService
{
    const PARAMETER_APP_ID = "appId";
    const PARAMETER_APP_SECRET = "appSecret";

    /**
     * @return bool
     */
    public function haveSubscription(User $user)
    {
        //debug
//        $this->serviceContainer->get("ladybug")->log($user->getTwitterId(),$user->getTwitterAccessToken());
        if(is_null($user->getTwitterId()) || is_null($user->getTwitterAccessToken()))
            return false;

        return true;
    }

    /**
     * @return array
     */
    protected function loadParameters()
    {
        return array(
            self::PARAMETER_APP_ID=>$this->serviceContainer->getParameter("twitter_client_id"),
            self::PARAMETER_APP_SECRET=>$this->serviceContainer->getParameter("twitter_client_secret")
        );
    }

    public function renderSubscribeButton()
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/TwitterSubscribeService/generateSubscribeButton.html.twig");
    }

    public function renderRemoveButton()
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/TwitterSubscribeService/generateRemoveButton.html.twig");
    }

    public function renderCommentsBlock($parameters)
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/TwitterSubscribeService/comments.html.twig",array("permanentLink"=>$parameters["permanentLink"]));
    }

    public function unsubscribe(GlobalUser $user)
    {
        //return $this->render("@ModernEntrepreneurGeneralBackendCore/TwitterSubscribeService/comments.html.twig",array("permanentLink"=>$parameters["permanentLink"]));
        $user->setTwitterAccessToken(null);
        $user->setTwitterId(null);
        $em = $this->serviceContainer->get("doctrine")->getManager();
        $em->persist($user);
        $em->flush();

        return true;
        //var_dump(json_decode($response));
    }
}
