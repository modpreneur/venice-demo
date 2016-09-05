<?php

namespace AppBundle\Form\ContentProduct;

use AppBundle\Entity\ContentProduct;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContentProductTypeWithHiddenProduct.
 */
class ContentProductTypeWithHiddenProduct extends \Venice\AppBundle\Form\ContentProduct\ContentProductTypeWithHiddenProduct
{
    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', ContentProduct::class);
    }
}
