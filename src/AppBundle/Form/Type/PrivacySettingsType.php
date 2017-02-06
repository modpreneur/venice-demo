<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use AppBundle\Privacy\PrivacySettings;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PrivacySettingsType
 * @package AppBundle\Form\Type
 */
class PrivacySettingsType extends SingleItemType
{
    private $classicChoice = ['Public' => true, 'Private' => false];

    /** @var User */
    protected $user;


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder, $options) {
            $this->initFields($event->getForm());
        }, 1);
    }


    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver); // TODO: Change the autogenerated stub

        $resolver->setDefault('user', null);
        $resolver->setDefault('data_class', PrivacySettings::class);
    }


    /**
     * @param FormInterface $builder
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setBirthDateStyle(FormInterface $builder, array $options)
    {
        $user = $options['user'];

        if (is_null($user->getAge())) {
            $builder->add(
                'birthDateStyle',
                ChoiceType::class,
                [
                    'choices' => [
                        'Birth date is not set.' => 0
                    ],
                    'disabled' => true,
                    'attr' => ['icon' => 'ff-time']
                ]
            );
        } else {
            $builder->add(
                'birthDateStyle',
                ChoiceType::class,
                [
                    'choices' => [
                        'Private' => PrivacySettings::FORMAT_BIRTH_DATE_NONE,
                        $this->user->getAge() . ' years old' => PrivacySettings::FORMAT_BIRTH_DATE_AGE,
                        $this->user->getDateOfBirth()->format("jS \\o\\f F") => PrivacySettings::FORMAT_BIRTH_DATE_DAY,
                        $this->user->getDateOfBirth()->format('m/d/Y') => PrivacySettings::FORMAT_BIRTH_DATE_FULL,
                    ],
                    'attr' => ['icon' => 'ff-time']
                ]
            );
        }
    }


    /**
     * @param FormInterface $builder
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setPublicProfile(FormInterface $builder, array $options)
    {
        $builder->add(
            'publicProfile',
            ChoiceType::class,
            ['choices' => $this->classicChoice, 'attr' => ['icon' => 'ff-person']]
        );
    }


    /**
     * @param FormInterface $builder
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setDisplayEmail(FormInterface $builder, array $options)
    {
        $builder->add(
            'displayEmail',
            ChoiceType::class,
            ['choices' => $this->classicChoice, 'attr' => ['icon' => 'ff-sign-mail']]
        );
    }


    /**
     * @param FormInterface $builder
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setDisplayLocation(FormInterface $builder, array $options)
    {
        $builder->add(
            'displayLocation',
            ChoiceType::class,
            ['choices' => $this->classicChoice, 'attr' => ['icon' => 'ff-map-pointer']]
        );
    }


    /**
     * @param FormInterface $builder
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setDisplayForumActivity(FormInterface $builder, array $options)
    {
        $builder->add(
            'displayForumActivity',
            ChoiceType::class,
            ['choices' => $this->classicChoice, 'attr' => ['icon' => 'ff-chat-conversation']]
        );
    }


    /**
     * @param FormInterface $builder
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setDisplayProgressGraph(FormInterface $builder, array $options)
    {
        $builder->add(
            'displayProgressGraph',
            ChoiceType::class,
            ['choices' => $this->classicChoice, 'attr' => ['icon' => 'ff-graph']]
        );
    }


    /**
     * @param FormInterface $builder
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setDisplayFullName(FormInterface $builder, array $options)
    {
        $builder->add(
            'displayFullName',
            ChoiceType::class,
            ['choices' => $this->classicChoice, 'attr' => ['icon' => 'ff-graph']]
        );
    }


    /**
     * @param FormInterface $builder
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setDisplaySocialMedia(FormInterface $builder, array $options)
    {
        $builder->add(
            'displaySocialMedia',
            ChoiceType::class,
            ['choices' => $this->classicChoice, 'attr' => ['icon' => 'ff-social-stream']]
        );
    }
}
