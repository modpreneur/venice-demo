<?php

namespace AppBundle\Form\Content;

use AppBundle\Entity\Content\Mp3Content;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Mp3ContentType
 * @package AppBundle\Form\Content
 */
class Mp3ContentType extends \Venice\AppBundle\Form\Content\Mp3ContentType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', Mp3Content::class);
    }
}
