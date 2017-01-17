<?php

namespace AppBundle\Services;

use Venice\AppBundle\Entity\User;

/**
 * Class DevForumConnector
 * @package AppBundle\Services
 */
class DevForumConnector extends AbstractForumConnector
{
    public function getConversations(User $user)
    {
        return array();
    }

    public function getMessages(User $user, Conversation $conversation)
    {
        return array();
    }

    /**
     * @return Array<Message>
     */
    public function getLastMessages(User $user)
    {
        return array();
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function sendMessage(User $user, Message $message)
    {
        return array();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getLatestForumPosts(User $user)
    {
        return array();
    }

    /**
     * @param User $user
     * @param string     $participants users involved in a conversation. Format: username1,username2,username3,...
     *
     * @return Conversation|null
     */
    public function getExistingConversation(User $user, $participants)
    {
        return null;
    }

    /**
     * @param User $sender
     * @param string     $participants users involved in a conversation. Format: username1,username2,username3,...
     * @param string     $body         body of the first message
     *
     * @return array|true returns true on success, array of error messages on failure
     */
    public function createConversation(User $sender, $participants, $body)
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @return Array<Category>
     */
    public function getCategories(User $user)
    {
        return array();
    }

    /**
     * @param User          $user
     * @param int|string|Category $category
     *
     * @return mixed
     */
    public function getForumPostsByCategory(User $user, $category)
    {
        return array();
    }

    /**
     * @param User    $user
     * @param int|ForumPost $forumPost
     *
     * @return ForumPost|null
     */
    public function getForumPostDetail(User $user, $forumPost)
    {
        return null;
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function getAllUsers(User $user)
    {
        return array();
    }
}