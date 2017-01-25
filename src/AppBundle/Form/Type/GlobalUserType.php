<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * Class GlobalUserType
 * @package AppBundle\Form\Type
 */
class GlobalUserType extends SingleItemType
{
    const REMOVE_BUTTON_NAME = "profilePictureRemoveButton";


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (!in_array("fullName", GlobalUserType::$availableFields)) {
            GlobalUserType::$availableFields[] = "fullName";
            GlobalUserType::$availableFields[] = "fullPassword";
            GlobalUserType::$availableFields[] = "newPassword";
            GlobalUserType::$availableFields[] = "location";
            GlobalUserType::$availableFields[] = "socialNetworks";
            GlobalUserType::$availableFields[] = "profilePhotoWithDeleteButton";
        }
    }


    public function setFullName(FormBuilderInterface $builder, array $options)
    {
        $builder->add("firstName");
        $builder->add("lastName");
    }


    public function setDateOfBirth(FormBuilderInterface $builder, array $options)
    {
        $builder->add("dateOfBirth", DateType::class, array(
            'years' => range(date('Y') - 100, date('Y') - 10)
        ));
    }


    public function setFullPassword(FormBuilderInterface $builder, array $options)
    {
        $builder->add('current_password', PasswordType::class, array(
            'label' => 'form.current_password',
            'translation_domain' => 'FOSUserBundle',
            'mapped' => false,
            'constraints' => new UserPassword(),
        ));
        $builder->add('plainPassword', RepeatedType::class, array(
            'type'            => PasswordType::class,
            'options'         => array('translation_domain' => 'FOSUserBundle'),
            'first_options'   => array('label' => 'form.new_password'),
            'second_options'  => array('label' => 'form.new_password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
        ));
    }


    public function setNewPassword(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array('translation_domain' => 'FOSUserBundle'),
            'first_options' => array('label' => 'Password'),
            'second_options' => array('label' => 'Confirm New Password'),
            'invalid_message' => 'fos_user.password.mismatch',
        ));
    }


    public function setLocation(FormBuilderInterface $builder, array $options)
    {
        $builder->add("location");
    }


    public function setPreferredUnits(FormBuilderInterface $builder, array $options)
    {
        $builder->add("preferredUnits", ChoiceType::class,
            array(
                "choices" => array(
                    User::PREFERRED_IMPERIAL => "Imperial",
                    User::PREFERRED_METRIC   => "Metric"
                )
            )
        );
    }


    public function setProfilePhoto(FormBuilderInterface $builder, array $options)
    {
        $builder->add("profilePhoto", ProfilePhotoType::class);
    }


    public function setProfilePhotoWithDeleteButton(FormBuilderInterface $builder, array $options)
    {
        $builder->add("profilePhoto", ProfilePhotoType::class, array("required" => false));

        if ($this->entity->getProfilePhoto()) {
            $builder->add(self::REMOVE_BUTTON_NAME, "submit", array("label" => "Remove photo"));
        }
    }


    public function setSocialNetworks(FormBuilderInterface $builder, array $options)
    {
        $builder->add("youtubeLink", TextType::class,
            array(
                "required" => false
            ));
        $builder->add("snapchatNickname", TextType::class,
            array(
                "required" => false
            ));
    }


    public function setEmail(FormBuilderInterface $builder, array $options)
    {
        $builder->add("email", EmailType::class,
            array(
                "required" => true
            )
        );
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'intention' => 'change_password'
        ));
    }
}
