<?php

namespace AppBundle\Form;

use AppBundle\Entity\ProductAccess;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProductAccessType.
 */
class ProductAccessType extends \Venice\AppBundle\Form\ProductAccessType
{
    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', ProductAccess::class);
    }
}
