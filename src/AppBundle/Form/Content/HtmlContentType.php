<?php

namespace AppBundle\Form\Content;

use AppBundle\Entity\Content\HtmlContent;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HtmlContentType
 * @package AppBundle\Form\Content
 */
class HtmlContentType extends \Venice\AppBundle\Form\Content\HtmlContentType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', HtmlContent::class);
    }
}
