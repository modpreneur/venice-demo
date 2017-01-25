<?php
namespace AppBundle\SocialServices;

use GeneralBackend\CoreBundle\Entity\SocialSite;

/**
 * Class TwitterConnector
 * @package AppBundle\Login
 */
class TwitterConnector extends Connector
{
    /**
     * code taken from SocialFeed.com
     *
     * @param $token
     *
     * @return mixed
     * @throws \Exception
     */
    public function getUserIdByTokens($token, $tokenSecret)
    {
        $appId = $this->serviceContainer->getParameter("twitter_client_id");
        $appSecret = $this->serviceContainer->getParameter("twitter_client_secret");

        $settings = array(
            'oauth_access_token' => $token,
            'oauth_access_token_secret' => $tokenSecret,
            'consumer_key' => $appId,
            'consumer_secret' => $appSecret
        );
        $url = 'https://api.twitter.com/1.1/account/verify_credentials.json';
        $requestMethod = 'GET';

        $twitter = new \TwitterAPIExchange($settings);
        $user = $twitter->buildOauth($url, $requestMethod)
                         ->performRequest();

        $user = json_decode($user, true);

        if(array_key_exists("id", $user))
        {
            return $user["id"];
        }

        if(array_key_exists("errors", $user))
        {
            $this->serviceContainer->get("logger")->addCritical("Errors in " . __FILE__ . " line:" . __LINE__ . ": ", $user["errors"]);
        }

        return null;
    }
}