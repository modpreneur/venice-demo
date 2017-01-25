<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\BillingPlanRepository")
 * Class BillingPlan
 */
class BillingPlan extends \Venice\AppBundle\Entity\BillingPlan
{
    /**
     * @ORM\Column(type="string")
     */
    protected $BillingPlanChild;

    /**
     * @return mixed
     */
    public function getBillingPlanChild()
    {
        return $this->BillingPlanChild;
    }

    /**
     * @param mixed $BillingPlanChild
     */
    public function setBillingPlanChild($BillingPlanChild)
    {
        $this->BillingPlanChild = $BillingPlanChild;
    }
}
