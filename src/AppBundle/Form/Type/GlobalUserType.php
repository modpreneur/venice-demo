<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\ProfilePhoto;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * Class GlobalUserType
 * @package AppBundle\Form\Type
 */
class GlobalUserType extends SingleItemType
{
    const REMOVE_BUTTON_NAME = 'profilePictureRemoveButton';


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            if (!in_array('fullName', GlobalUserType::$availableFields)) {
                GlobalUserType::$availableFields[] = 'fullName';
                GlobalUserType::$availableFields[] = 'fullPassword';
                GlobalUserType::$availableFields[] = 'newPassword';
                GlobalUserType::$availableFields[] = 'location';
                GlobalUserType::$availableFields[] = 'socialNetworks';
                GlobalUserType::$availableFields[] = 'profilePhotoWithDeleteButton';
            }

            $this->initFields($event->getForm());
        }, 1);
    }


    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setFullName(FormInterface $form, array $options)
    {
        $form->add('fullName', HiddenType::class, [
            'data'   => '',
            'mapped' => false
        ]);
        $form->add('firstName', TextType::class);
        $form->add('lastName', TextType::class);
    }


    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setDateOfBirth(FormInterface $form, array $options)
    {
        $form->add('dateOfBirth', DateType::class, [
            'years' => range(date('Y') - 100, date('Y') - 10)
        ]);
    }


    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setFullPassword(FormInterface $form, array $options)
    {
        $form->add('current_password', PasswordType::class, [
            'label' => 'form.current_password',
            'translation_domain' => 'FOSUserBundle',
            'mapped'      => false,
            'constraints' => new UserPassword(),
        ]);

        $form->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => ['translation_domain' => 'FOSUserBundle'],
            'first_options' => ['label' => 'form.new_password'],
            'second_options' => ['label' => 'form.new_password_confirmation'],
            'invalid_message' => 'fos_user.password.mismatch',
        ]);
    }


    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setNewPassword(FormInterface $form, array $options)
    {
        $form->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => ['translation_domain' => 'FOSUserBundle'],
            'first_options'   => ['label' => 'Password'],
            'second_options'  => ['label' => 'Confirm New Password'],
            'invalid_message' => 'fos_user.password.mismatch',
        ]);
    }


    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setLocation(FormInterface $form, array $options)
    {
        $form->add('location', TextType::class);
    }


    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setPreferredUnits(FormInterface $form, array $options)
    {
        $form->add(
            'preferredUnits',
            ChoiceType::class,
            [
                'choices' => [
                    User::PREFERRED_IMPERIAL => 'Imperial',
                    User::PREFERRED_METRIC   => 'Metric'
                ]
            ]
        );
    }


    /**á
     *
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setProfilePhoto(FormInterface $form, array $options)
    {
        $form->add('profilePhotoWithDeleteButton', HiddenType::class, [
            'mapped'   => false,
            'required' => true,
            'data'     => true,
        ]);

        $form->add('profilePhoto', ProfilePhotoType::class, [
            'required' => false, 'mapped' => 'profilePhoto'
        ]);
    }


    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setProfilePhotoWithDeleteButton(FormInterface $form, array $options)
    {
        $form->add('profilePhoto', ProfilePhotoType::class, ['required' => false, 'mapped' => 'profilePhoto']);

        $form->add('profilePhotoWithDeleteButton', HiddenType::class, [
            'mapped'   => false,
            'required' => true,
        ]);

        if ($this->entity->getProfilePhoto()) {
            $form->add(self::REMOVE_BUTTON_NAME, SubmitType::class, ['label' => 'Remove photo']);
        }
    }


    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setSocialNetworks(FormInterface $form, array $options)
    {
        $form->add('socialNetworks', HiddenType::class, [
            'mapped' => false,
            'data'   => '',
        ]);

        $form->add(
            'youtubeLink',
            TextType::class,
            [
                'required' => false
            ]
        );
        $form->add(
            'snapchatNickname',
            TextType::class,
            [
                'required' => false
            ]
        );
    }


    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function setEmail(FormInterface $form, array $options)
    {
        $form->add(
            'email',
            EmailType::class,
            [
                'required' => true
            ]
        );
    }


    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'intention' => 'change_password'
        ]);
    }
}
