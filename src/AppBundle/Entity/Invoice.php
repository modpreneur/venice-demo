<?php

namespace AppBundle\Entity;

use Venice\AppBundle\Entity\Invoice as VeniceInvoice;

/**
 * Class Invoice
 * @package AppBundle\Entity
 */
class Invoice extends VeniceInvoice
{
    const INVOICE_STATUS_PENDING   = 0;
    const INVOICE_STATUS_NORMAL    = 1;
    const INVOICE_STATUS_RECURRING = 2;
    const INVOICE_STATUS_CANCELED  = 3;
    const INVOICE_STATUS_REFUNDED  = 4;
    const INVOICE_STATUS_COMPLETED = 5;


    /** @var  int */
    protected $status;

    /** @var  User */
    protected $user;

    protected $firstPrice;

    protected $secondTotal;

    protected $rebillTimes;

    protected $stringPrice;


    /**
     * Invoice constructor.
     *
     * @param User $user
     * @param string $status
     * @param int $invoiceId
     * @param $firstTotal
     * @param $secondTotal
     * @param $rebillTimes
     * @param \DateTime $startedDate
     * @param array $invoiceItems
     */
    public function __construct(
        User $user,
        string $status,
        int $invoiceId,
        $firstTotal,
        $secondTotal,
        $rebillTimes,
        \DateTime $startedDate,
        $invoiceItems = []
    ) {

        parent::__construct();

        $this->user   = $user;
        $this->status = $status;
        $this->firstPrice  = $firstTotal;
        $this->secondTotal = $secondTotal;
        $this->rebillTimes = $rebillTimes;

        $this->setId($invoiceId);
        $this->setTransactionTime($startedDate);
        $this->setTotalPrice($firstTotal + $rebillTimes * $secondTotal);


        foreach ($invoiceItems as $invoiceItem) {
            $this->items->add($invoiceItem);
        }

        $this->stringPrice = '$' . $this->getTotalPrice();

        // @todo @JakubFajkus @fík
    }


    /**
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }


    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    /**
     * @return mixed
     */
    public function getStringPrice()
    {
        return $this->stringPrice;
    }


    /**
     * @param mixed $stringPrice
     */
    public function setStringPrice($stringPrice)
    {
        $this->stringPrice = $stringPrice;
    }
}
