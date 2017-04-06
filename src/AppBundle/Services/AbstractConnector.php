<?php

namespace AppBundle\Services;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;


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
     * @param BillingPlan $parameters
     * @param User $user
     * @param               $userStoredCC
     *
     * @return string
     */
     abstract public function generateBuyLink(BillingPlan $parameters, User $user, $userStoredCC);


    /**
     * @param User $user
     *
     * @return Invoice[]
     */
    abstract public function getUserInvoices(User $user);


    /**
     * @param Invoice $invoice
     * @param User $user
     *
     * @return bool
     */
    abstract public function cancelInvoice(Invoice $invoice, User $user);


    /**
     * @param User $user
     *
     * @return []
     */
    abstract public function getNewsletters(User $user);


    /**
     * @param  $newsletter
     * @param User $user
     *
     * @return bool
     */
    abstract public function updateNewsletter($newsletter, User $user);


    /**
     * @param User $user
     *
     * @return
     */
    abstract public function getLastPaymentInfo(User $user);


    /**
     * @param User $user
     *
     * @return bool
     */
    abstract public function loginInBaseService(User $user);


    /**
     * @param User $user
     *
     * @return bool
     */
    abstract public function updateUser(User $user, array $propertyNames);


    /**
     * @param User $user
     * @param            $plainTextPassword
     *
     * @return bool
     */
    abstract public function changeUserPassword(User $user, $plainTextPassword);


    /**
     * @param User $user
     *
     * @return bool
     */
    abstract public function fireUpdateEvent(User $user);
}
