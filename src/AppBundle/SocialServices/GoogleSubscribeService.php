<?php

namespace AppBundle\SocialServices;

use AppBundle\Entity\User;
use AppBundle\Services\AbstractSubscibeService;
use Twig_Environment;

/**
 * Class GoogleSubscribeService
 * @package AppBundle\Login
 */
class GoogleSubscribeService extends AbstractSubscibeService
{
    const PARAMETER_APP_ID = "appId";
    const PARAMETER_APP_SECRET = "appSecret";

    /**
     * @return bool
     */
    public function haveSubscription(User $user)
    {
        if (is_null($user->getGoogleId()) || is_null($user->getGoogleAccessToken())) {
            return false;
        } else {
            return true;
        }
        // TODO: check on google if is unsubscribed
    }

    /**
     * @return array
     */
    protected function loadParameters()
    {
        return array(
            self::PARAMETER_APP_ID=>$this->serviceContainer->getParameter("google_client_id"),
            self::PARAMETER_APP_SECRET=>$this->serviceContainer->getParameter("google_client_secret")
        );
    }

    public function renderSubscribeButton()
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/GoogleSubscribeService/generateSubscribeButton.html.twig");
    }

    public function renderLogInButton()
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/GoogleSubscribeService/generateLogInButton.html.twig");
    }
    public function renderRemoveButton()
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/GoogleSubscribeService/generateRemoveButton.html.twig");
    }

//    public function renderCommentsBlock($parameters)
//    {
//        return $this->render("@ModernEntrepreneurGeneralBackendCore/FacebookSubscribeService/comments.html.twig",array("permanentLink"=>$parameters["permanentLink"]));
//    }

    public function renderHeader()
    {
        return $this->render("@ModernEntrepreneurGeneralBackendCore/GoogleSubscribeService/init.html.twig");
    }

    public function unsubscribe(User $user)
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