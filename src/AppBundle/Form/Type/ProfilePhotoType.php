<?php

namespace AppBundle\Form\Type;

use FlofitEntities\Bundle\FlofitEntitiesBundle\FlofitEntities\CoreBundle\ProfilePhoto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProfilePhotoType
 * @package AppBundle\Form\Type
 */
class ProfilePhotoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image_file',FileType::class, array(
                'required'=>false,
                'constraints' => array(
//                    new Assert\NotBlank()
                )))
            ->add('cropStartX',HiddenType::class, array(
                'constraints' => array(
//                    new Assert\NotBlank()
                )))
            ->add('cropStartY',HiddenType::class,  array(
                'constraints' => array(
//                    new Assert\NotBlank()
                )))

            ->add('cropSize',HiddenType::class,  array(
                'constraints' => array(
//                    new Assert\NotBlank()
                )))
        ;
    }


    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => ProfilePhoto::class,
            'allow_extra_fields' => true // For API - changeProfilePhotoAction
        ]);
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'globaluser_profilePhoto';
    }
}
