<?php

namespace AppBundle\Services;

use AppBundle\Entity\SocialStream\SocialPost;
use AppBundle\Entity\SocialStream\SocialSite;
use MetzWeb\Instagram\Instagram;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SocialFeed
 * @package AppBundle\Services
 */
class SocialFeed
{
    //protected $feedCount;
    /**
     * @var SocialPost[]
     */
    protected $feed = [];

    /**
     * @var
     */
    protected $serviceContainer;


    /**
     * @param $container ContainerInterface
     */
    public function __construct($container)
    {
        $this->serviceContainer = $container;
    }


    /**
     * @param int $count
     *
     * @return SocialPost[]
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Facebook\Exceptions\FacebookSDKException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \InvalidArgumentException
     */
    public function getLatestPosts($count)
    {
        $this->feed = [];

        $sites = $this->getSocialSites();

        //foreach site get posts / max $limit
        foreach ($sites as $site) {
            if ($site->isEnabled()) {
                array_merge($this->feed, $this->getPostsFromSite($site, $count));
            }
        }

        // order by time DESC
        usort($this->feed, function ($b, $a) { // $b, $a because of DESC
            return strtotime($a->getDateTime()) - strtotime($b->getDateTime());
        });

        $entityManager = $this
            ->serviceContainer
            ->get('doctrine')
            ->getManager();

        foreach ($this->feed as $post) {
            $entityManager->persist($post);
        }

        $entityManager->flush();
        return $this->feed;
    }


    /**
     * @param $config
     *
     * @return SocialPost[]|array
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function getLatestPostsFromCache($config)
    {
        $entityManager = $this->serviceContainer->get('doctrine')->getManager();

        $this->feed = $entityManager
            ->getRepository(SocialPost::class)
            ->findBy([], ['dateTime' => 'DESC']);

        return $this->feed;
    }


    public function removeAllCachedPosts()
    {
        $entityManager = $this->serviceContainer->get('doctrine')->getManager();

        $posts = $entityManager->getRepository(SocialPost::class)
            ->findAll();

        foreach ($posts as $post) {
            $entityManager->remove($post);
        }
    }


    /**
     * @param SocialSite $site
     * @param int $limit
     *
     * @return array
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    private function getPostsFromSite($site, $limit)
    {
        switch ($site->getType()) {
            case 'facebook':
                return $this->getFacebook($site->getAccount(), $limit);
            case 'twitter':
                return $this->getTwitter($site->getAccount(), $limit);
            case 'instagram':
                return $this->getInstagram($site->getAccount(), $limit);
        }

        return [];
    }


    /**
     *
     * @param $account
     * @param $limit
     *
     * @return SocialPost[]
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    private function getFacebook($account, $limit)
    {
        //array for return value
        $socialPosts = [];
        $url = '';

        $appId = $this->serviceContainer->getParameter('facebook_client_id');
        $appSecret = $this->serviceContainer->getParameter('facebook_client_secret');

        $fb = new \Facebook\Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v2.4',
        ]);

        try {
            // Returns a `Facebook\FacebookResponse` object
//            $response = $fb->get('/me?fields=feed.limit('.$limit.'),name,picture,id', $token);
            $response = $fb->get('/' . $account . '?fields=posts,feed.limit(10),name,picture,id',
                $fb->getApp()->getAccessToken());
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            return $socialPosts;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return $socialPosts;
        } catch (\Exception $e) {
            return $socialPosts;
        }

        $fBposts = $response->getGraphUser();
        $decodedPosts = json_decode($fBposts);

        foreach ($decodedPosts->{'posts'} as $post) {
            $a = new \ReflectionClass($post);

            $type = 'facebook';
            $author = $decodedPosts->{'name'};
            if (isset($post->{'created_time'})) {
                //$dateTime = $post->{"created_time"}->{"date"};
                $dateTime = new \DateTime($post->{'created_time'}->{'date'});
                $dateTime = $dateTime->format('Y-m-d H:i:s');
            } else { // if couldn't find time, drop. TODO: maybe could be forced in FB api
                continue;
            }
            $message = "";
            if (isset($post->{'message'})) {
                $message .= $post->{'message'};
            }

            $message = preg_replace('/[\\\]\S{5}/u', '', $message);

            if (isset($post->{'story'})) {
                $message .= "\n" . $post->{'story'};
            }
            $profilePic = $decodedPosts->{'picture'}->{'url'};

            if (isset($post->{'id'})) {
                $url = 'https://www.facebook.com/' . $post->{'id'};
            } else {
                $url = 'https://www.facebook.com/' . $decodedPosts->{'id'};
            }

            $socialPost = new SocialPost($type, $author, $dateTime, $message, $profilePic, $url);
            //return value
            array_push($socialPosts, $socialPost);
            // add to feed
            array_push($this->feed, $socialPost);
        }

        return $socialPosts;
    }


    /**
     * @param int $limit
     *
     * @return SocialPost[]
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Exception
     */
    private function getTwitter($account, $limit)
    {
        $socialPosts = [];

        $appId = $this->serviceContainer->getParameter('twitter_client_id');
        $appSecret = $this->serviceContainer->getParameter('twitter_client_secret');
        $token = $this->serviceContainer->getParameter('twitter_client_token');
        $tokenSecret = $this->serviceContainer->getParameter('twitter_client_token_secret');

        $settings = [
            'oauth_access_token' => $token,
            'oauth_access_token_secret' => $tokenSecret,
            'consumer_key' => $appId,
            'consumer_secret' => $appSecret
        ];
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

        $getField = '?screen_name=' . $account . '&count=' . $limit;
        $requestMethod = 'GET';

        //$twitter = new \Twitter\twitter\TwitterAPIExchange($settings);
        $twitter = new \TwitterAPIExchange($settings);
        $posts = $twitter->setGetfield($getField)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $decodedPosts = json_decode($posts);

        if (isset($decodedPosts->{'errors'})) {
            return $socialPosts;
        }

        foreach ($decodedPosts as $post) {
            $type = 'twitter';
            $author = $post->{'user'}->{'name'};
            //$dateTime = $post->{"created_at"};
            $dateTime = new \DateTime($post->{'created_at'});
            $dateTime = $dateTime->format('Y-m-d H:i:s');
            $message = $post->{'text'};
            $profilePic = $post->{'user'}->{'profile_image_url_https'};
            $url = 'https://twitter.com/' . $post->{'user'}->{'screen_name'};

            $socialPost = new SocialPost($type, $author, $dateTime, $message, $profilePic, $url);

            //return value
            array_push($socialPosts, $socialPost);
            // add to feed
            array_push($this->feed, $socialPost);
        }

        return $socialPosts;
    }


    /**
     * @param $account
     * @param $limit
     *
     * @return array
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function getInstagram($account, $limit)
    {
        $appId = $this->serviceContainer->getParameter('instagram_client_id');
        $appSecret = $this->serviceContainer->getParameter('instagram_client_secret');

        $instagram = new Instagram(
            [
                'apiKey' => $appId,
                'apiSecret' => $appSecret,
                'apiCallback' => ""
            ]
        );

        try {
            $user = $instagram->searchUser($account, 1);
            $posts = $instagram->getUserMedia($user->data[0]->id, $limit);

            $socialPosts = [];
            foreach ($posts->data as $post) {
                $socialPosts[] = new SocialPost('instagram', $user->data[0]->username, new \DateTime(),
                    $post->caption->text, $user->data[0]->profile_picture, $post->link);
            }
            return $socialPosts;
        } catch (\Exception $e) {
            return [];
        }
    }


    /**
     * @return SocialSite[]
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \InvalidArgumentException
     */
    private function getSocialSites()
    {
        $em = $this->serviceContainer->get('doctrine')->getManager();
        $sites = $em->getRepository(SocialSite::class)->findAll();

        return $sites;
    }


    /**
     * @return int
     */
    public function countPosts()
    {
        return count($this->feed);
    }
}
