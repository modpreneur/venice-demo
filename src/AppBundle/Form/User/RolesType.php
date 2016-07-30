<?php

namespace AppBundle\Form\User;

use AppBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RolesType
 * @package AppBundle\Form\User
 */
class RolesType extends \Venice\AppBundle\Form\User\RolesType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', User::class);
    }
}
