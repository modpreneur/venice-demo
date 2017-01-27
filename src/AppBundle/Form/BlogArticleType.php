<?php

namespace AppBundle\Form;

use AppBundle\Entity\BlogArticle;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BlogArticleType.
 */
class BlogArticleType extends \Venice\AppBundle\Form\BlogArticleType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('BlogArticleChild');
        $builder->add('commentsOn', CheckboxType::class, [
            'attr' => ['disable_widget_label' => true],
            'required' => false,
        ]);
        $builder->add('published', CheckboxType::class, [
            'attr' => ['disable_widget_label' => true ],
            'required' => false,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', BlogArticle::class);
    }
}
