<?php

namespace AppBundle\Form\Content;

use AppBundle\Entity\Content\PdfContent;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PdfContentType.
 */
class PdfContentType extends \Venice\AppBundle\Form\Content\PdfContentType
{
    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', PdfContent::class);
    }
}
