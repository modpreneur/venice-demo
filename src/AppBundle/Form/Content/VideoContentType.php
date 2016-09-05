<?php

namespace AppBundle\Form\Content;

use AppBundle\Entity\Content\VideoContent;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class VideoContentType.
 */
class VideoContentType extends \Venice\AppBundle\Form\Content\VideoContentType
{
    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', VideoContent::class);
    }
}
