<?php

namespace AppBundle\Form\Content;

use AppBundle\Entity\Content\IframeContent;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class IframeContentType
 * @package AppBundle\Form\Content
 */
class IframeContentType extends \Venice\AppBundle\Form\Content\IframeContentType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', IframeContent::class);
    }
}
