<?php
namespace AppBundle\Form;

use AppBundle\Entity\BillingPlan;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BillingPlanType
 * @package Venice\AppBundle\Form
 */
class BillingPlanType extends \Venice\AppBundle\Form\BillingPlanType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', BillingPlan::class);
    }
}
