<?php
/**
 * Created by PhpStorm.
 * User: ondrejbohac
 * Date: 15.06.15
 * Time: 11:29
 */

namespace GeneralBackend\CoreBundle\Entity;


use AppBundle\Entity\User;

/**
 * Class Newsletter
 * @package GeneralBackend\CoreBundle\Entity
 */
class Newsletter
{
    protected $user;
    protected $listId;
    protected $title;
    protected $isSubscribed;

    /**
     * Newsletter constructor.
     *
     * @param User $user
     * @param $listId
     * @param $title
     * @param $isSubscribed
     */
    public function __construct(User $user, $listId, $title, $isSubscribed)
    {
        $this->user = $user;
        $this->listId = $listId;
        $this->title = $title;
        $this->isSubscribed = $isSubscribed;
    }

    public function getListId()
    {
        return $this->listId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function isSubscribed()
    {
        return $this->isSubscribed;
    }

    /**
     * @param $isSubscribed
     */
    public function setSubscription($isSubscribed)
    {
        $this->isSubscribed = $isSubscribed;
    }

    public function unsubscribe()
    {
        $this->isSubscribed = false;
    }

    public function subscribe()
    {
        $this->isSubscribed = true;
    }
}