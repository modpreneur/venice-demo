<?php
/**
 * Created by PhpStorm.
 * User: ondrejbohac
 * Date: 27.10.15
 * Time: 14:21
 */

namespace AppBundle\Form\Type;

use AppBundle\Entity\AfterPurchaseSurvey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * Class AfterPurchaseSurveyType
 * @package AppBundle\Form\Type
 */
class AfterPurchaseSurveyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('male', ChoiceType::class, [
                'choices' => AfterPurchaseSurvey::MALE,
                'label' => 'What Is Your Gender?',
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('old', ChoiceType::class, [
                'choices' =>AfterPurchaseSurvey::AGES_LIST,
                'label' => 'What Is Your Age Group?',
                'multiple'=>false,
                'expanded' =>false
            ])
            ->add('biggestObstacle', ChoiceType::class, [
                'choices' =>AfterPurchaseSurvey::BIGGEST_OBSTACLE_LIST,
                'label' =>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('mainGoal', ChoiceType::class, [
                'choices' =>AfterPurchaseSurvey::MAIN_GOAL_LIST,
                'label' =>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('betterShape', ChoiceType::class, [
                'choices' =>AfterPurchaseSurvey::WHY_BETTER_SHAPE,
                'label' =>false,
                'expanded'=>true,
                'multiple'=>false
            ])

            ->add('email', HiddenType::class, ['mapped' =>false, 'required' =>false])
            ->add('redirect', HiddenType::class, ['mapped' =>false, 'required' =>false])

            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\AfterPurchaseSurvey'
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'survey';
    }

}