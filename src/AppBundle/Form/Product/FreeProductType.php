<?php

namespace AppBundle\Form\Product;

use AppBundle\Entity\Product\FreeProduct;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FreeProductType
 * @package Venice\AppBundle\Form\Product
 */
class FreeProductType extends \Venice\AppBundle\Form\Product\FreeProductType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', FreeProduct::class);
    }
}
