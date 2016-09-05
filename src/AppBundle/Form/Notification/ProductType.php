<?php

namespace AppBundle\Form\Notification;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Product\StandardProduct;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProductType.
 */
class ProductType extends \Venice\AppBundle\Form\Notification\ProductType
{
    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', StandardProduct::class);
        $resolver->setDefault('billingPlanClass', BillingPlan::class);
    }
}
