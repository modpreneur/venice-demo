<?php

namespace AppBundle\Form\Notification;

use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProductAccessType
 */
class ProductAccessType extends \Venice\AppBundle\Form\Notification\ProductAccessType
{
    /**
     * {@inheritdoc}
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', ProductAccess::class);
        $resolver->setDefault('standardProductClass', StandardProduct::class);
        $resolver->setDefault('userClass', User::class);
    }
}