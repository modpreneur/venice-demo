<?php

namespace AppBundle\Form\Notification;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Product\StandardProduct;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BillingPlanType
 */
class BillingPlanType extends \Venice\AppBundle\Form\Notification\BillingPlanType
{
    /**
     * {@inheritdoc}
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', BillingPlan::class);
        $resolver->setDefault('standardProductClass', StandardProduct::class);
    }
}
