<?php

namespace AppBundle\Form;

use AppBundle\Entity\BlogArticle;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BlogArticleType
 * @package AppBundle\Form
 */
class BlogArticleType extends \Venice\AppBundle\Form\BlogArticleType
{
    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', BlogArticle::class);
    }
}
