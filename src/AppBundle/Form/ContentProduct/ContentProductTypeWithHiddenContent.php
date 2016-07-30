<?php

namespace AppBundle\Form\ContentProduct;

use AppBundle\Entity\ContentProduct;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContentProductTypeWithHiddenContent
 * @package AppBundle\Form\ContentProduct
 */
class ContentProductTypeWithHiddenContent extends \Venice\AppBundle\Form\ContentProduct\ContentProductTypeWithHiddenContent
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', ContentProduct::class);
    }
}
