<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Venice\AppBundle\Entity\User;

/**
 * Class VanillaForumConnector
 * @package AppBundle\Services
 */
class VanillaForumConnector extends AbstractForumConnector
{
    const API_SEARCH = "/api/search";
    const API_CATEGORIES = "/api/categories";
    const API_CONVERSATIONS = "/api/conversations";
    const API_CONVERSATION = "/api/conversations/:id";
    const API_NEW_MESSAGE = "/api/conversations/:id/messages";
    const API_ALL_USERS = "/api/users/summary";
    const API_FLAG_CONVERSATION_AS_READ = "/messages/:id";
    const API_DISCUSSIONS = "/api/discussions";
    const API_DISCUSSIONS_FIND = "/api/discussions/:id";

    private $secret;

    private $forumUrl;

    private $entityManager;

    private $customAuthUser;


    /**
     * VanillaForumConnector constructor.
     *
     * @param ContainerInterface $serviceContainer
     * @param EntityManager $entityManager
     */
    public function __construct($serviceContainer, EntityManager $entityManager)
    {
        parent::__construct($serviceContainer);

        $this->forumUrl = $this->serviceContainer->getParameter("forum_url");
        $this->secret = $this->serviceContainer->getParameter("forum_secret_key");
        $this->entityManager = $entityManager;
        $this->customAuthUser = null;
    }


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


    protected function createCookieString()
    {
        $cookiesString = "";
        $cookies = $this->createAuthCookies(is_null($this->customAuthUser) ? $this->getUser() : $this->customAuthUser);

        foreach ($cookies as $cookie) {
            /** @var Cookie $cookie */
            $cookiesString .= $cookie->__toString();
        }

        return $cookiesString;
    }


    public function getJson($url)
    {
        $cookiesString = $this->createCookieString();

        $response = $this->curl->setMethod("GET")->setOption("CURLOPT_COOKIE", $cookiesString)->setURL($url)->execute();

        $decoded = json_decode($response, true);

        return is_null($decoded) ? array() : $decoded;
    }


    public function putAndGetJson($url, $parameters = array())
    {
        $cookiesString = $this->createCookieString();

        $response = $this->curl->setMethod("PUT", $parameters)->setOption("CURLOPT_COOKIE",
            $cookiesString)->setURL($url)->execute();

        $decoded = json_decode($response, true);

        return is_null($decoded) ? array() : $decoded;
    }


    public function postAndGetJson($url, $parameters = array(), $options = array())
    {
        $cookiesString = $this->createCookieString();

        $response = $this->curl->setMethod("POST", $parameters)->setOption("CURLOPT_COOKIE",
            $cookiesString)->setURL($url)->execute();

        $decoded = json_decode($response, true);

        return is_null($decoded) ? array() : $decoded;
    }


    public function postJson($url, $parameters = array())
    {
        $cookiesString = $this->createCookieString();

        $jsonData = json_encode($parameters);

        $curl = curl_init($url);

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
        );

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_COOKIE, $cookiesString);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

//        $request = $this->curl->setMethod("POST",array())->setURL($url);
//        $request->setOption("CURLOPT_POSTFIELDS", $jsonData);
//        $request->setOption("CURLOPT_RETURNTRANSFER", true);
//        $request->setOption("CURLOPT_COOKIE",$cookiesString);
//        $request->setOption("CURLOPT_HTTPHEADER", array(
//                'Content-Type: application/json',
//                'Content-Length: '.strlen($jsonData))
//        );
//        $response = $request->execute();

        $decoded = json_decode($response, true);

        return is_null($decoded) ? array() : $decoded;
    }


    private function createToken($requestValues)
    {
        ksort($requestValues, SORT_STRING);
        $imploded = implode("-", $requestValues);

        return hash_hmac("sha256", strtolower($imploded), $this->secret);
    }


    private function createHash($data, $key)
    {
        if (isset($key[63])) {
            $Key = pack("H32", md5($key));
        } else {
            $Key = str_pad($key, 64, chr(0));
        }

        $InnerPad = (substr($Key, 0, 64) ^ str_repeat(chr(0x36), 64));
        $OuterPad = (substr($Key, 0, 64) ^ str_repeat(chr(0x5C), 64));

        $hash = md5($OuterPad . pack("H32", md5($InnerPad . $data)));

        return $hash;
    }


    private function createCookieValue(User $user, $exp)
    {
        $keyData = $user->getCommunityId() . "-" . $exp;

        $key = $this->createHash($keyData, $this->serviceContainer->getParameter("forum_auth_cookie_salt"));
        $hash = $this->createHash($keyData, $key);

        $value = implode('|', array($keyData, $hash, \time(), $user->getCommunityId(), $exp));

        return $value;
    }


    private function getCookies()
    {
        $request = $this->serviceContainer->get("request");

        $cookie = array();

        $cookieName = $this->serviceContainer->getParameter("forum_auth_cookie_name");

        if ($request->cookies->has($cookieName)) {
            $cookie[] = $request->cookies->get($cookieName);
        }

        if ($request->cookies->has($cookieName) . "-Volatile") {
            $cookie[] = $request->cookies->get($cookieName . "-Volatile");
        }

        return $cookie;
    }


    public function getCookieNames()
    {
        $cookieName = $this->serviceContainer->getParameter("forum_auth_cookie_name");

        return array($cookieName, $cookieName . "-Volatile");
    }


    public function createAuthCookies(User $user, $domain = null, $exp = null)
    {
        $cookies = array();

        if (is_null($exp)) {
            $exp = \time() + 60 * 60 * 24 * 2;
        }

        $cookies[] = new Cookie($this->serviceContainer->getParameter("forum_auth_cookie_name"),
            $this->createCookieValue($user, $exp), $exp, null, $domain);
        $cookies[] = new Cookie($this->serviceContainer->getParameter("forum_auth_cookie_name") . "-Volatile",
            $this->createCookieValue($user, $exp), $exp, null, $domain);

        return $cookies;
    }


    public function createDeleteCookies($domain = null, $exp = null)
    {
        $cookies = array();

        $exp = time() - 36000;

        $cookies[] = new Cookie($this->serviceContainer->getParameter("forum_auth_cookie_name"), "", $exp, null,
            $domain);
        $cookies[] = new Cookie($this->serviceContainer->getParameter("forum_auth_cookie_name") . "-Volatile", "", $exp,
            null, $domain);

        return $cookies;
    }


    public function getCommunityIdFromCookies()
    {
        $request = $this->serviceContainer->get("request");
        $cookieName = $this->serviceContainer->getParameter("forum_auth_cookie_name");

        if (!$request->cookies->has($cookieName)) {
            return 0;
        }

        /** @var  $cookie */
        $cookie = $request->cookies->get($cookieName);

        return substr($cookie, 0, strpos($cookie, "-"));
    }


    private function createUrl($user, $endpointUrl)
    {
        $timestamp = (new \DateTime())->getTimestamp();

        $parameters = array(
            "email" => $user->getEmail(),
            "username" => $user->getUsername(),
            "timestamp" => $timestamp
        );

        $url = $this->forumUrl . $endpointUrl . "&username={$user->getUsername()}&email={$user->getEmail()}&timestamp={$timestamp}&token={$this->createToken($parameters)}";

        return $url;
    }


    public function getCategories(User $user)
    {
        $categoriesUrl = self::API_CATEGORIES;
        $url = $this->createUrl($user, $categoriesUrl);

        $categories = $this->getJson($url)["Categories"];

        $categoriesObjects = [];
        foreach ($categories as $category) {
            $categoriesObjects[] = new Category(
                $category["CategoryID"],
                $category["CountDiscussions"],
                $category["CountComments"],
                $category["Name"],
                $category["Description"],
                $category["DateUpdated"]
            );
        }

        return $categoriesObjects;
    }


    /**
     * @param User $user
     * @param bool $raw
     *
     * @return mixed json array
     */
    public function getLatestForumPosts(User $user, $raw = false)
    {
        $forumPosts = array();

        $url = $this->createUrl($user, self::API_DISCUSSIONS);
        $forum = $this->getJson($url);

        if ($raw == true) {
            return $forum;
        }

        if (!array_key_exists("Discussions", $forum)) {
            return [];
        }

        foreach ($forum{"Discussions"} as $discussion) {
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
            $author = $this->entityManager->getRepository("ModernEntrepreneurGeneralBackendCoreBundle:User")->findOneBy(["username" => $username]);
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


    public function getLatestForumPostsOfUser(User $user, User $author = null, $count = null)
    {
        if (is_null($author)) {
            $author = $user;
        }

        $posts = $this->getLatestForumPosts($user);
        $filteredPosts = [];
        $filteredCount = 0;

        /** @var ForumPost $post */
        foreach ($posts as $post) {
            if ($post->getAuthor() == $author->getUsername()) {
                $filteredPosts[] = $post;

                if (++$filteredCount >= $count) {
                    break;
                }
            }
        }

        return $filteredPosts;
    }


    /**
     * @param User $user
     * @param int|string|Category $category
     *
     * @return mixed
     */
    public function getForumPostsByCategory(User $user, $category)
    {
        $forumPosts = $this->getLatestForumPosts($user);

        if (is_object($category)) {
            if ($category->getId() != null) {
                $category = $category->getId();
            } else {
                if ($category->getName() != null) {
                    $category = $category->getName();
                } else {
                    throw new InvalidArgumentException("Category object has to contain id or name");
                }
            }
        }

        //it is category id
        if (is_numeric($category)) {
            /** @var ForumPost $post */
            foreach ($forumPosts as $key => $post) {
                if ($category != $post->getCategoryId()) {
                    unset($forumPosts[$key]);
                }
            }

        } //it is category name
        else {
            if (is_string($category)) {
                /** @var ForumPost $post */
                foreach ($forumPosts as $key => $post) {
                    if ($category != $post->getCategoryName()) {
                        unset($forumPosts[$key]);
                    }
                }
            } else {
                throw new InvalidArgumentException;
            }
        }

        return $forumPosts;
    }


    /**
     * Discussion aka ForumPost
     *
     *
     * @param User $user
     * @param ForumPost|int $forumPost
     * @param bool $raw
     *
     * @return mixed
     */
    public function getForumPostDetail(User $user, $forumPost, $raw = false)
    {
        if (is_object($forumPost) && $forumPost->getId() != null) {
            $forumPostId = $forumPost->getId();
        } else {
            if (is_numeric($forumPost)) {
                $forumPostId = $forumPost;
            } else {
                throw new InvalidArgumentException("Discussion parameter ");
            }
        }

        $postUrl = $this->createUrl($user, self::API_DISCUSSIONS_FIND);
        $postUrl = str_replace(":id", $forumPostId, $postUrl);
        $response = $this->getJson($postUrl);

        if ($raw === true) {
            return $response;
        }

        if (array_key_exists("Code", $response) && $response["Code"] == 404) {
            return null;
        }

        $postArray = $response["Discussion"];
        $comments = $response["Comments"];

        $forumPostObject = new ForumPost(
            $postArray["DiscussionID"],
            $postArray["Url"],
            $postArray["Name"],
            $postArray["Body"],
            $postArray["CategoryID"],
            $postArray["Category"],
            $postArray["LastDate"],
            $postArray["CountViews"],
            $postArray["CountComments"],
            $postArray["InsertName"]
        );

        foreach ($comments as $commentArray) {
            $comment = new Comment(
                $commentArray["CommentID"],
                $commentArray["DiscussionID"],
                $commentArray["InsertName"],
                $commentArray["Body"],
                ($commentArray["DateUpdated"]) ?: $commentArray["DateInserted"]
            );

            $forumPostObject->addComment($comment);
        }

        return $forumPostObject;

    }


    public function getAllUsers(User $user, $raw = false)
    {
        $url = $this->createUrl($user, self::API_ALL_USERS);
        $response = $this->getJson($url);

        if ($raw === true) {
            return $response;
        }

        if (!array_key_exists("UserData", $response)) {
            return [];
        }

        $usernames = [];
        foreach ($response["UserData"] as $userArray) {
            if (array_key_exists("Name", $userArray)) {
                $usernames[] = $userArray["Name"];
            }
        }

        $users = $this
            ->serviceContainer
            ->get("doctrine.orm.entity_manager")
            ->getRepository("ModernEntrepreneurGeneralBackendCoreBundle:User")
            ->getUsersByUsernames($usernames);

        return $users;
    }


    public function getConversations(User $user, $raw = false)
    {
        $url = $this->createUrl($user, self::API_CONVERSATIONS);

        $conversations = $this->getJson($url);
        if ($raw == true) {
            return $conversations;
        }

        $convParsed = array();
        $entityManager = $this->serviceContainer->get("doctrine")->getManager();

        if (!array_key_exists("Conversations", $conversations)) {
            return array();
        }

        foreach ($conversations["Conversations"] as $conversation) {
            $participants = array();
            foreach ($conversation["Participants"] as $participant) {
                if ($participant["Name"] != $user->getUsername()) {
                    $participantEnt = $entityManager->getRepository("ModernEntrepreneurGeneralBackendCoreBundle:User")
                        ->findOneBy(array("username" => $participant["Name"]));

                    if (is_null($participantEnt)) {
                        $participants[] = $participant["Name"];
                    } else {
                        $participants[] = $participantEnt;
                    }
                }
            }

            $convParsed[] = new Conversation(
                $conversation["ConversationID"],
                $conversation["Subject"],
                $conversation["LastBody"],
                $conversation["DateUpdated"],
                $conversation["CountNewMessages"],
                $conversation["CountReadMessages"],
                $participants
            );
        }

        return $convParsed;
    }


    /**
     * @param User $user
     * @param string $participants
     *
     * @return Conversation|null
     */
    public function getExistingConversation(User $user, $participants)
    {
        $conversations = $this->getConversations($user);

        /** @var Conversation $conversation */
        foreach ($conversations as $conversation) {
            $participantsArray = [];
            foreach ($conversation->getParticipants() as $obj) {
                if ($obj instanceof User) {
                    $participantsArray[] = $obj->getUsername();
                } else {
                    $participantsArray[] = $obj;
                }
            }

            $participantsString = implode(",", $participantsArray);

            if ($participantsString == $participants) {
                return $conversation;
            }
        }

        return null;
    }


    public function createConversation(User $sender, $participants, $body)
    {
        $url = $this->createUrl($sender, self::API_CONVERSATIONS);

        $response = $this->postJson($url, ["To" => $participants, "Body" => $body]);

        if (!array_key_exists("Code", $response)) {
            return true;
        } else {
            return $response;
        }
    }


    public function deleteConversation(User $user, Conversation $conversation)
    {
        $url = $this->createUrl($user, self::API_CONVERSATION);
        $url = str_replace(":id", $conversation->getConversationId(), $url);

        $cookiesString = $this->createCookieString();

        $response = $this->curl->setMethod("DELETE")->setOption("CURLOPT_COOKIE",
            $cookiesString)->setURL($url)->execute();

        return $response;
    }


    public function sendMessage(User $user, Message $message)
    {
        $url = $this->createUrl($user, self::API_NEW_MESSAGE);
        $url = str_replace(":id", $message->getConversationId(), $url);

        $response = $this->postJson($url, array("Body" => $message->getBody()));
        if (array_key_exists("Messages", $response)) {
            return true;
        } else {
            return $response;
        }
    }


    public function getMessages(User $user, Conversation $conversation, $raw = false)
    {
        $url = $this->createUrl($user, self::API_CONVERSATION);
        $url = str_replace(":id", $conversation->getConversationId(), $url);
        $messages = $this->getJson($url);

        if ($raw == true) {
            return $messages;
        }

        if (!array_key_exists("Messages", $messages)) {
            return null;
        }

        $entityManager = $this->serviceContainer->get("doctrine")->getManager();

        $msgParsed = array();

        foreach ($messages["Messages"] as $message) {
            $author = $entityManager->getRepository("ModernEntrepreneurGeneralBackendCoreBundle:User")
                ->findOneBy(array("username" => $message["InsertName"]));
            if (is_null($author)) {
                $author = $message["InsertName"];
            }

            $msgParsed[] = new Message(
                $message["MessageID"],
                $message["ConversationID"],
                $author,
                $message["DateInserted"],
                $message["Body"]
            );
        }

        return $msgParsed;
    }


    /**
     * @param null $customAuthUser
     *
     * @return $this
     */
    public function setCustomAuthUser(User $customAuthUser)
    {
        $this->customAuthUser = $customAuthUser;

        return $this;
    }
}