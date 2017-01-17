<?php

namespace AppBundle\Services;

use Venice\AppBundle\Entity\Invoice;
use Venice\AppBundle\Entity\User;


/**
 * Class DevConnector
 * @package AppBundle\Services
 */
class DevConnector extends AbstractConnector
{
    /**
     * @param BuyParameters $parameters
     * @param User $user
     * @param               $userStoredCC
     *
     * @return string
     */
    public function generateBuyLink(BuyParameters $parameters, User $user, $userStoredCC)
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
        $invoiceItem = new InvoiceItem("Test upgrade");
        $invoiceItem->setCategories(array(new InvoiceItemCategory(1, "PRODUCT TRIAL")));

        return array(
            new Invoice($user, Invoice::INVOICE_STATUS_NORMAL, 1, 100, 0, 0, new \DateTime(),
                array(new InvoiceItem("Test product"))),
            new Invoice($user, Invoice::INVOICE_STATUS_RECURRING, 5, 100, 0, 0, new \DateTime(), array($invoiceItem)),
            new Invoice($user, Invoice::INVOICE_STATUS_RECURRING, 2, 100, 10, 10, new \DateTime(),
                array(new InvoiceItem("Test product recuring"))),
            new Invoice($user, Invoice::INVOICE_STATUS_CANCELED, 3, 100, 10, 10, new \DateTime(),
                array(new InvoiceItem("Test product cancel"))),
            new Invoice($user, Invoice::INVOICE_STATUS_REFUNDED, 4, 100, 10, 10, new \DateTime(),
                array(new InvoiceItem("Test product refunded")))
        );
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
     * @return Newsletter[]
     */
    public function getNewsletters(User $user)
    {
        return array(
            new Newsletter($user, 1, "Test newsletter 1", true),
            new Newsletter($user, 2, "Test newsletter 2", false)
        );
    }


    /**
     * @param Newsletter $newsletter
     * @param User $user
     *
     * @return bool
     */
    public function updateNewsletter(Newsletter $newsletter, User $user)
    {
        return true;
    }


    /**
     * @param User $user
     *
     * @return LastPaymentInfo
     */
    public function getLastPaymentInfo(User $user)
    {
        return new LastPaymentInfo(1, 111, "TEST PAYMENT");
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
