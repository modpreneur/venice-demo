<?php

namespace AppBundle\Services;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;


/**
 * Class DevConnector
 * @package AppBundle\Services
 */
class DevConnector extends AbstractConnector
{
    /**
     * @param BillingPlan $parameters
     * @param User $user
     * @param               $userStoredCC
     *
     * @return string
     */
    public function generateBuyLink(BillingPlan $parameters, User $user, $userStoredCC)
    {
        return "";
    }


    /**
     * @param User $user
     *
     * @return Invoice[]
     */
    public function getUserInvoices(User $user)
    {
        $invoiceItem = 'Test upgrade';
        //$invoiceItem->setCategories(array(new InvoiceItemCategory(1, "PRODUCT TRIAL")));


        return [
            new Invoice($user, Invoice::INVOICE_STATUS_NORMAL, 1, 100, 0, 0, new \DateTime(),
                ['Test product']
            ),
            new Invoice($user, Invoice::INVOICE_STATUS_RECURRING, 5, 100, 0, 0, new \DateTime(), [$invoiceItem]),
            new Invoice($user, Invoice::INVOICE_STATUS_RECURRING, 2, 100, 10, 10, new \DateTime(),
                ['Test product recuring']
            ),
            new Invoice($user, Invoice::INVOICE_STATUS_CANCELED, 3, 100, 10, 10, new \DateTime(),
                ['Test product cancel']
            ),
            new Invoice($user, Invoice::INVOICE_STATUS_REFUNDED, 4, 100, 10, 10, new \DateTime(),
                ['Test product refunded']
            )
        ];
    }


    /**
     * @param Invoice $invoice
     * @param User $user
     *
     * @return bool
     */
    public function cancelInvoice(Invoice $invoice, User $user)
    {
        return true;
    }


    /**
     * @param User $user
     *
     * @return []
     */
    public function getNewsletters(User $user)
    {
        return [

        ];
    }


    /**
     * @param  $newsletter
     * @param User $user
     *
     * @return bool
     */
    public function updateNewsletter($newsletter, User $user)
    {
        return true;
    }


    /**
     * @param User $user
     *
     * @return
     */
    public function getLastPaymentInfo(User $user)
    {
        //return new LastPaymentInfo(1, 111, 'TEST PAYMENT');
        return null;
    }


    /**
     * @param User $user
     *
     * @return bool
     */
    public function loginInBaseService(User $user)
    {
        return true;
    }


    /**
     * @param User $user
     *
     * @return bool
     */
    public function updateUser(User $user, array $propertyNames)
    {
        return true;
    }


    /**
     * @param User $user
     * @param            $plainTextPassword
     *
     * @return bool
     */
    public function changeUserPassword(User $user, $plainTextPassword)
    {
        return true;
    }


    /**
     * @param User $user
     *
     * @return bool
     */
    public function fireUpdateEvent(User $user)
    {
        return true;
    }
}
