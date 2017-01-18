<?php

namespace AppBundle\Form\Product;

use AppBundle\Entity\Product\StandardProduct;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StandardProductType.
 */
class StandardProductType extends \Venice\AppBundle\Form\Product\StandardProductType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('upsellOrder', IntegerType::class);
    }


    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', StandardProduct::class);
    }
}
