<?php

namespace AppBundle\Form\Product;

use AppBundle\Entity\Product\StandardProduct;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StandardProductType
 * @package AppBundle\Form\Product
 */
class StandardProductType extends \Venice\AppBundle\Form\Product\StandardProductType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', StandardProduct::class);
    }
}
