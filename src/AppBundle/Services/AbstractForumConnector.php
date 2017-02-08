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
     * @param User   $user
     * @param Conversation $conversation
     *
     * @return Array<Message>|null
     */
    abstract public function getMessages(User $user, Conversation $conversation);


    /**
     * @param User $user
     * @return mixed
     */
    abstract public function getLatestForumPosts(User $user);
}
