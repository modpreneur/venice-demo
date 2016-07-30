<?php

namespace AppBundle\Form\Content;

use AppBundle\Entity\Content\GroupContent;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GroupContentType
 * @package AppBundle\Form\Content
 */
class GroupContentType extends \Venice\AppBundle\Form\Content\GroupContentType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', GroupContent::class);
    }
}
