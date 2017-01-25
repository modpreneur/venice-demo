<?php

namespace AppBundle\Services;

use FlofitEntities\Bundle\FlofitEntitiesBundle\FlofitEntities\CoreBundle\Newsletter;
use Venice\AppBundle\Entity\Invoice;
use Venice\AppBundle\Entity\User;


/**
 * Class AbstractConnector
 * @package AppBundle\Services
 */
abstract class AbstractConnector extends Connector
{
    protected $serviceContainer;


    /**
     * AbstractConnector constructor.
     *
     * @param $serviceContainer
     */
    public function __construct($serviceContainer)
    {
        parent::__construct($serviceContainer);

        $this->serviceContainer = $serviceContainer;
    }


    /**
     * @param BuyParameters $parameters
     * @param User $user
     * @param               $userStoredCC
     *
     * @return string
     */
     abstract function generateBuyLink(BuyParameters $parameters, User $user, $userStoredCC);


    /**
     * @param User $user
     *
     * @return Invoice[]
     */
    abstract function getUserInvoices(User $user);


    /**
     * @param Invoice $invoice
     * @param User $user
     *
     * @return bool
     */
    abstract function cancelInvoice(Invoice $invoice, User $user);


    /**
     * @param User $user
     *
     * @return Newsletter[]
     */
    abstract function getNewsletters(User $user);


    /**
     * @param Newsletter $newsletter
     * @param User $user
     *
     * @return bool
     */
    abstract function updateNewsletter(Newsletter $newsletter, User $user);


    /**
     * @param User $user
     *
     * @return LastPaymentInfo
     */
    abstract function getLastPaymentInfo(User $user);


    /**
     * @param User $user
     *
     * @return bool
     */
    abstract function loginInBaseService(User $user);


    /**
     * @param User $user
     *
     * @return bool
     */
    abstract function updateUser(User $user, array $propertyNames);


    /**
     * @param User $user
     * @param            $plainTextPassword
     *
     * @return bool
     */
    abstract function changeUserPassword(User $user, $plainTextPassword);


    /**
     * @param User $user
     *
     * @return bool
     */
    abstract function fireUpdateEvent(User $user);
}
