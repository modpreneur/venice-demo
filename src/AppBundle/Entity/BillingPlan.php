<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 13:13
 */

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
}