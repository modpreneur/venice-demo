<?php

namespace AppBundle\Entity\Vanilla;

/**
 * Class Conversation
 * @package AppBundle\Entity\Vanilla
 */
class Conversation
{
    private $conversationId;

    private $subject;

    private $lastBody;

    private $date;

    private $countNewMessages;

    private $countReadedMessages;

    private $participants;


    /**
     * Conversation constructor.
     *
     * @param $conversationId
     * @param $subject
     * @param $lastBody
     * @param $date
     * @param $countNewMessages
     * @param $countReadedMessages
     * @param $participants
     */
    public function __construct(
        $conversationId,
        $subject,
        $lastBody,
        $date,
        $countNewMessages,
        $countReadedMessages,
        $participants
    ) {
        $this->conversationId = $conversationId;
        $this->subject = $subject;
        $this->lastBody = $lastBody;
        $this->date = $date;
        $this->countNewMessages = $countNewMessages;
        $this->countReadedMessages = $countReadedMessages;
        $this->participants = $participants;
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
    public function getSubject()
    {
        return $this->subject;
    }


    /**
     * @return mixed
     */
    public function getLastBody()
    {
        return $this->lastBody;
    }


    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }


    /**
     * @return mixed
     */
    public function getCountNewMessages()
    {
        return $this->countNewMessages;
    }


    /**
     * @return mixed
     */
    public function getCountReadedMessages()
    {
        return $this->countReadedMessages;
    }


    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }
}
