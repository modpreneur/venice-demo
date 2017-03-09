<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Entity\Vanilla\Conversation;
use AppBundle\Entity\Vanilla\ForumPost;
use AppBundle\Entity\Vanilla\Message;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class VanillaForumConnector
 * @package AppBundle\Services
 */
class VanillaForumConnector extends AbstractForumConnector
{
    const API_SEARCH = '/api/search';
    const API_CATEGORIES = '/api/categories';
    const API_CONVERSATIONS = '/api/conversations';
    const API_CONVERSATION = '/api/conversations/:id';
    const API_NEW_MESSAGE = '/api/conversations/:id/messages';
    const API_ALL_USERS = '/api/users/summary';
    const API_FLAG_CONVERSATION_AS_READ = '/messages/:id';
    const API_DISCUSSIONS = '/api/discussions';
    const API_DISCUSSIONS_FIND = '/api/discussions/:id';

    private $secret;

    private $forumUrl;

    /** @var EntityManager */
    private $entityManager;

    private $customAuthUser;


    /**
     * VanillaForumConnector constructor.
     *
     * @param ContainerInterface $serviceContainer
     * @param EntityManager $entityManager
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function __construct($serviceContainer, EntityManager $entityManager)
    {
        parent::__construct($serviceContainer);

        $this->forumUrl = $this->serviceContainer->getParameter('forum_url');
        $this->secret = $this->serviceContainer->getParameter('forum_secret_key');
        $this->entityManager = $entityManager;
        $this->customAuthUser = null;
    }


    /**
     * @return mixed|null
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    private function getUser()
    {
        if (null === $token = $this->serviceContainer->get('security.token_storage')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }


    /**
     * @return string
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    protected function createCookieString()
    {
        $cookiesString = '';
        $cookies = $this->createAuthCookies(is_null($this->customAuthUser) ? $this->getUser() : $this->customAuthUser);

        foreach ($cookies as $cookie) {
            /** @var Cookie $cookie */
            $cookiesString .= $cookie->__toString();
        }

        return $cookiesString;
    }


    /**
     * @param $url
     *
     * @return array|mixed
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function getJson($url)
    {
        $cookiesString = $this->createCookieString();

        $newCookie = \GuzzleHttp\Cookie\SetCookie::fromString($cookiesString);
        $newCookie->setDomain($this->serviceContainer->getParameter('forum_url'));

        $cookieJar = new CookieJar(true);
        $cookieJar->setCookie($newCookie);

        $response = $this->getClient()->get($url, ['cookies' => $cookieJar]);
        $decoded = json_decode($response->getBody(), true);

        return is_null($decoded) ? [] : $decoded;
    }


    /**
     * @param $requestValues
     *
     * @return string
     */
    private function createToken($requestValues)
    {
        ksort($requestValues, SORT_STRING);
        $imploded = implode('-', $requestValues);

        return hash_hmac('sha256', strtolower($imploded), $this->secret);
    }


    /**
     * @param $data
     * @param $key
     *
     * @return string
     */
    private function createHash($data, $key)
    {
        if (isset($key[63])) {
            $Key = pack('H32', md5($key));
        } else {
            $Key = str_pad($key, 64, chr(0));
        }

        $InnerPad = (substr($Key, 0, 64) ^ str_repeat(chr(0x36), 64));
        $OuterPad = (substr($Key, 0, 64) ^ str_repeat(chr(0x5C), 64));

        $hash = md5($OuterPad . pack('H32', md5($InnerPad . $data)));

        return $hash;
    }


    /**
     * @param User $user
     * @param $exp
     *
     * @return string
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    private function createCookieValue(User $user, $exp)
    {
        $keyData = $user->getCommunityId() . '-' . $exp;

        $key = $this->createHash($keyData, $this->serviceContainer->getParameter('forum_auth_cookie_salt'));
        $hash = $this->createHash($keyData, $key);

        return  implode('|', [$keyData, $hash, \time(), $user->getCommunityId(), $exp]);
    }


    /**
     * @return array
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    private function getCookies()
    {
        $request = $this->serviceContainer->get('request');

        $cookie = [];


        if ($request->cookies->has($cookieName) . '-Volatile') {
            $cookie[] = $request->cookies->get($cookieName . '-Volatile');
        }

        return $cookie;
    }


    /**
     * @return array
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function getCookieNames()
    {
        $cookieName = $this->serviceContainer->getParameter('forum_auth_cookie_name');

        return [$cookieName, $cookieName . '-Volatile'];
    }


    /**
     * @param User $user
     * @param null $domain
     * @param null $exp
     *
     * @return array
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function createAuthCookies(User $user, $domain = null, $exp = null)
    {
        $cookies = [];

        if (is_null($exp)) {
            $exp = \time() + 60 * 60 * 24 * 2;
        }

        $cookies[] = new Cookie(
            $this
                ->serviceContainer
                ->getParameter('forum_auth_cookie_name'),
            $this->createCookieValue($user, $exp),
            $exp,
            null,
            $domain
        );


        $cookies[] = new Cookie(
            $this->serviceContainer->getParameter('forum_auth_cookie_name') . '-Volatile',
            $this->createCookieValue($user, $exp),
            $exp,
            null,
            $domain
        );

        return $cookies;
    }


    /**
     * @return int|string
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function getCommunityIdFromCookies()
    {
        $request = $this->serviceContainer->get('request');
        $cookieName = $this->serviceContainer->getParameter('forum_auth_cookie_name');

        if (!$request->cookies->has($cookieName)) {
            return 0;
        }

        /** @var  $cookie */
        $cookie = $request->cookies->get($cookieName);

        return substr($cookie, 0, strpos($cookie, '-'));
    }


    /**
     * @param $user
     * @param $endpointUrl
     *
     * @return string
     */
    private function createUrl(User $user, $endpointUrl)
    {
        $timestamp = (new \DateTime())->getTimestamp();

        $parameters = [
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'timestamp' => $timestamp
        ];

        $url =
            $this->forumUrl .
            $endpointUrl .
            "&username={$user->getUsername()}&email={$user->getEmail()}" .
            "&timestamp={$timestamp}&token={$this->createToken($parameters)}";

        return $url;
    }


    /**
     * @param User $user
     * @param bool $raw
     *
     * @return mixed json array
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function getLatestForumPosts(User $user, $raw = false)
    {
        var_dump(2);
        exit;

        $forumPosts = [];

        dump(1);

        $url = $this->createUrl($user, self::API_DISCUSSIONS);
        $forum = $this->getJson($url);

        if ($raw === true) {
            return $forum;
        }

        if (!array_key_exists('Discussions', $forum)) {
            return [];
        }

        dump(1);

        foreach ($forum{'Discussions'} as $discussion) {
            $id = $discussion{'DiscussionID'};
            $url = $discussion{'Url'};
            $name = $discussion{'Name'};
            $body = $discussion{'Body'};
            $categoryId = $discussion{'CategoryID'};
            $categoryName = $discussion{'Category'};
            $lastDate = $discussion{'LastDate'};
            $countViews = $discussion{'CountViews'};
            $countComments = $discussion{'CountComments'};
            $username = $discussion{'FirstName'};
            $lastCommentDateTime = $discussion{'DateLastComment'};
            $author = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
            $forumPosts[] = new ForumPost(
                $id,
                $url,
                $name,
                $body,
                $categoryId,
                $categoryName,
                $lastDate,
                $countViews,
                $countComments,
                $username,
                $lastCommentDateTime,
                (!is_null($author)) ? $author->getFullName() : $username
            );
        }

        return $forumPosts;
    }


    /**
     * @param User $user
     * @param bool $raw
     *
     * @return array|mixed
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \InvalidArgumentException
     */
    public function getConversations(User $user, $raw = false)
    {
        $url = $this->createUrl($user, self::API_CONVERSATIONS);
        $conversations = $this->getJson($url);
        if ($raw === true) {
            return $conversations;
        }

        $convParsed = [];
        $entityManager = $this->serviceContainer->get('doctrine')->getManager();

        if (!array_key_exists('Conversations', $conversations)) {
            return [];
        }

        foreach ($conversations['Conversations'] as $conversation) {
            $participants = [];
            foreach ($conversation['Participants'] as $participant) {
                if ($participant['Name'] != $user->getUsername()) {
                    $participantEnt = $entityManager->getRepository(User::class)
                        ->findOneBy(['username' => $participant['Name']]);

                    if (is_null($participantEnt)) {
                        $participantModernEntrepreneurGeneralBackendCoreBundles[] = $participant['Name'];
                    } else {
                        $participants[] = $participantEnt;
                    }
                }
            }

            $convParsed[] = new Conversation(
                $conversation['ConversationID'],
                $conversation['Subject'],
                $conversation['LastBody'],
                $conversation['DateUpdated'],
                $conversation['CountNewMessages'],
                $conversation['CountReadMessages'],
                $participants
            );
        }

        return $convParsed;
    }


    /**
     * @param User $user
     * @param Conversation $conversation
     * @param bool $raw
     *
     * @return array|mixed|null
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \InvalidArgumentException
     */
    public function getMessages(User $user, Conversation $conversation, $raw = false)
    {
        $url = $this->createUrl($user, self::API_CONVERSATION);
        $url = str_replace(':id', $conversation->getConversationId(), $url);
        $messages = $this->getJson($url);

        if ($raw === true) {
            return $messages;
        }

        if (!array_key_exists('Messages', $messages)) {
            return null;
        }

        $entityManager = $this->serviceContainer->get('doctrine')->getManager();

        $msgParsed = [];

        foreach ($messages['Messages'] as $message) {
            $author = $entityManager->getRepository(User::class)
                ->findOneBy(['username' => $message['InsertName']]);
            if (is_null($author)) {
                $author = $message['InsertName'];
            }

            $msgParsed[] = new Message(
                $message['MessageID'],
                $message['ConversationID'],
                $author,
                $message['DateInserted'],
                $message['Body']
            );
        }

        return $msgParsed;
    }
}
