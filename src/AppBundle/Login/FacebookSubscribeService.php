<?php

namespace AppBundle\Login;

use Facebook\Facebook;
use GeneralBackend\CoreBundle\Entity\GlobalUser;
use Twig_Environment;

/**
 * Class FacebookSubscribeService
 * @package AppBundle\Login
 */
class FacebookSubscribeService extends AbstractSubscibeService
{
    const PARAMETER_APP_ID = "appId";
    const PARAMETER_APP_SECRET = "appSecret";

    /**
     * @return bool
     */
    public function haveSubscription(GlobalUser $user)
    {
        if(is_null($user->getFacebookId()) || is_null($user->getFacebookAccessToken()))
            return false;

        $facebook = new Facebook(
            array(
                "app_id"=>$this->parameters[self::PARAMETER_APP_ID],
                "app_secret"=>$this->parameters[self::PARAMETER_APP_SECRET],
        ));
        try
        {
            $data = $facebook->get("/me?fields=id,name",$user->getFacebookAccessToken())->getHttpStatusCode();
            return $data == 200;
        }
        catch(\Exception $exception)
        {
           return false;
        }
    }

    /**
     * @return array
     */
    protected function loadParameters()
    {
        return array(
            self::PARAMETER_APP_ID=>$this->serviceContainer->getParameter("facebook_client_id"),
            self::PARAMETER_APP_SECRET=>$this->serviceContainer->getParameter("facebook_client_secret")
        );
    }

    public function renderSubscribeButton()
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/FacebookSubscribeService/generateSubscribeButton.html.twig");
        //return $this->render("@ModernEntrepreneurGeneralBackendCore/FacebookSubscribeService/generateSubscribeButton.html.twig");
    }

    public function renderLogInButton()
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/FacebookSubscribeService/generateLogInButton.html.twig");
    }
     public function renderRemoveButton()
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/FacebookSubscribeService/generateRemoveButton.html.twig");
    }

    public function renderCommentsBlock($parameters)
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/FacebookSubscribeService/comments.html.twig",array("permanentLink"=>$parameters["permanentLink"]));
    }

    public function unsubscribe(GlobalUser $user)
    {
        //return $this->render("@ModernEntrepreneurGeneralBackendCore/TwitterSubscribeService/comments.html.twig",array("permanentLink"=>$parameters["permanentLink"]));
        $user->setFacebookAccessToken(null);
        $user->setFacebookId(null);
        $em = $this->serviceContainer->get("doctrine")->getManager();
        $em->persist($user);
        $em->flush();

        return true;
        //var_dump(json_decode($response));
    }
}