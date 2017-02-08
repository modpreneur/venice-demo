<?php
namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Entity\Vanilla\Conversation;
use AppBundle\Entity\Vanilla\ForumPost;
use AppBundle\Entity\Vanilla\Message;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class AbstractForumConnector
 * @package AppBundle\Services
 */
abstract class AbstractForumConnector extends Connector
{
    private $container;


    /**
     * AbstractForumConnector constructor.
     *
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer)
    {
        parent::__construct($serviceContainer);

        $this->container = $serviceContainer;
    }


    /**
     * @param User $user
     *
     * @return Array<Conversation>
     */
    abstract public function getConversations(User $user);


    /**
     * @param User $user
     * @param string $participants users involved in a conversation. Format: username1,username2,username3,...
     *
     * @return Conversation|null
     */
    abstract public function getExistingConversation(User $user, $participants);


    /**
     * @param User $sender
     * @param string     $participants users involved in a conversation. Format: username1,username2,username3,...
     * @param string     $body body of the first message
     *
     * @return array|true returns true on success, array of error messages on failure
     */
    abstract public function createConversation(User $sender, $participants, $body);


    /**
     * @param User   $user
     * @param Conversation $conversation
     *
     * @return Array<Message>|null
     */
    abstract public function getMessages(User $user, Conversation $conversation);


    /**
     * @param User $user
     * @param Message    $message
     *
     * @return array|true returns true on success, array of error messages on failure
     */
    abstract public function sendMessage(User $user, Message $message);


    /**
     * @param User $user
     * @return mixed
     */
    abstract public function getLatestForumPosts(User $user);


    /**
     * @param User $user
     *
     * @return Array<Category>
     */
    abstract public function getCategories(User $user);


    /**
     * @param User $user
     * @param int|string|Category $category
     *
     * @return mixed
     */
    abstract public function getForumPostsByCategory(User $user, $category);


    /**
     * @param User $user
     * @param int|ForumPost $forumPost
     *
     * @return ForumPost|null
     */
    abstract public function getForumPostDetail(User $user, $forumPost);


    /**
     * @param User $user
     *
     * @return mixed
     */
    abstract public function getAllUsers(User $user);
}
