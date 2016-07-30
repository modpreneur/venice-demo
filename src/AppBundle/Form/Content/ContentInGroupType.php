<?php

namespace AppBundle\Form\Content;

use AppBundle\Entity\Content\ContentInGroup;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContentInGroupType
 * @package AppBundle\Form\Content
 */
class ContentInGroupType extends \Venice\AppBundle\Form\Content\ContentInGroupType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', ContentInGroup::class);
    }
}
