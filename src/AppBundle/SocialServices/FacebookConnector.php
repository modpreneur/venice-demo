<?php

namespace AppBundle\SocialServices;

use AppBundle\Services\Connector;
use Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

/**
 * Class FacebookConnector
 */
class FacebookConnector extends Connector
{
    /**
     * code taken from SocialFeed.php
     *
     * @param $token
     *
     * @return null
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws FacebookSDKException
     */
    public function getUserIdByToken($token)
    {
        $appId     = $this->serviceContainer->getParameter('facebook_client_id');
        $appSecret = $this->serviceContainer->getParameter('facebook_client_secret');

        $fb = new Facebook\Facebook([
            'app_id'                => $appId,
            'app_secret'            => $appSecret,
            'default_graph_version' => 'v2.4',
        ]);

        try {
            $response = $fb->get('/me?fields=id,name', $token);
        } catch (FacebookResponseException $e) {
            return null;
        } catch (FacebookSDKException $e) {
            return null;
        }

        $facebookUserId = $response->getDecodedBody()["id"];

        return $facebookUserId;
    }

    public function getUserByToken($token)
    {
        $appId     = $this->serviceContainer->getParameter('facebook_client_id');
        $appSecret = $this->serviceContainer->getParameter('facebook_client_secret');

        $fb = new Facebook\Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v2.4',
        ]);

        try {
            $response = $fb->get('/me?fields=id,first_name,last_name,email,picture', $token);
        } catch (FacebookResponseException $e) {
            return null;
        } catch (FacebookSDKException $e) {
            return null;
        }

        return $response->getDecodedBody();
    }
}
