<?php

namespace AppBundle\Login;

//use Facebook;
use HappyR\Google\ApiBundle;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GoogleConnector
 * @package AppBundle\Login
 */
class GoogleConnector extends Connector
{
    /**
     * code taken from SocialFeed.php
     *
     * @param $token
     *
     * @return null
     */
    public function getUserIdByToken($token)
    {
        $appId = $this->serviceContainer->getParameter("facebook_client_id");
        $appSecret = $this->serviceContainer->getParameter("facebook_client_secret");



        return false;
    }

    public function getUserByToken($token) {
//        $appId = $this->serviceContainer->getParameter("google_client_id");
//        $appSecret = $this->serviceContainer->getParameter("google_client_secret");
        $client = new \Google_Client();
        $client->addScope('email profile');
//        $client->setAccessToken($token);
//            $request = new Request('https://www.googleapis.com/oauth2/v3/tokeninfo');
        $ticket = $client->verifyIdToken($token);
        if ($ticket) {
            return $ticket;
        } else {
            return false;
        }

//        $api = new ApiBundle\Services\GoogleClient();
//        $api->
//        $client = $api->getGoogleClient();
//        $client->addScope('email profile');
//        $client->setAccessToken($token);
//        $client->get

//        return $response->getDecodedBody();
    }
}