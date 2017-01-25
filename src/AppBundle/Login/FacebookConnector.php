<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.08.15
 * Time: 14:41
 */

namespace GeneralBackend\CoreBundle\Services;

use Facebook;

/**
 * Class FacebookConnector
 * @package GeneralBackend\CoreBundle\Services
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
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function getUserIdByToken($token)
    {
        $appId = $this->serviceContainer->getParameter("facebook_client_id");
        $appSecret = $this->serviceContainer->getParameter("facebook_client_secret");

        $fb = new Facebook\Facebook([
            'app_id'                => $appId,
            'app_secret'            => $appSecret,
            'default_graph_version' => 'v2.4',
        ]);

        try {
            $response = $fb->get('/me?fields=id,name', $token);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return null;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return null;
        }

        $facebookUserId = $response->getDecodedBody()["id"];

        return $facebookUserId;
    }

    public function getUserByToken($token)
    {
        $appId = $this->serviceContainer->getParameter("facebook_client_id");
        $appSecret = $this->serviceContainer->getParameter("facebook_client_secret");

        $fb = new Facebook\Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v2.4',
        ]);

        try {
            $response = $fb->get('/me?fields=id,first_name,last_name,email,picture', $token);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return null;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return null;
        }

        return $response->getDecodedBody();
    }
}
