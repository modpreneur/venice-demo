<?php

namespace AppBundle\Entity\Vanilla;

use AppBundle\Entity\User;

/**
 * Class Message
 * @package AppBundle\Entity\Vanilla
 */
class Message
{
    private $messageId;

    private $conversationId;

    private $body;

    private $dateInserted;

    private $author;


    /**
     * Message constructor.
     *
     * @param $messageId
     * @param $conversationId
     * @param $body
     * @param $dateInserted
     * @param $author
     */
    public function __construct($messageId, $conversationId, $author, $dateInserted, $body)
    {
        $this->messageId = $messageId;
        $this->conversationId = $conversationId;
        $this->body = $body;
        $this->author = $author;
        $this->dateInserted = $dateInserted;
    }


    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }


    /**
     * @return mixed
     */
    public function getConversationId()
    {
        return $this->conversationId;
    }


    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }


    /**
     * @return mixed
     */
    public function getDateInserted()
    {
        return $this->dateInserted;
    }


    /**
     * @return User || String
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
