<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SingleItemType
 * @package AppBundle\Form\Type
 */
abstract class SingleItemType extends AbstractType
{
    /** @var  \ReflectionClass */
    protected static $reflectionClass;

    protected static $availableFields = [];

    protected $ignoredFields = [];

    protected $field;

    protected $entity;

    protected $options;


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->field   = $options['field'];
        $this->entity  = $options['data'];
        $this->options = $options;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder, $options) {
            $user = $event->getData();

            SingleItemType::$reflectionClass = new \ReflectionClass($user);

            foreach (SingleItemType::$reflectionClass->getProperties() as $property) {
                SingleItemType::$availableFields[] = $property->getName();
            }
        }, 10);
    }


    /**
     * @param FormInterface $form
     *
     * @throws \Exception
     */
    public function initFields(FormInterface $form)
    {
        $config = $form->getConfig()->getOptions();

        //dump(SingleItemType::$availableFields);
        $thisClass = new \ReflectionClass($this);
        if (is_null($this->field)) {
            foreach (SingleItemType::$availableFields as $field) {
                if (!in_array($field, $this->ignoredFields)) {
                    $this->setField($field, $thisClass, $form, $config);
                }
            }
        } elseif (is_array($this->field)) {
            foreach ($this->field as $field) {
                if (!in_array($field, $this->ignoredFields)) {
                    $this->setField($field, $thisClass, $form, $config);
                }
            }
        } else {
            $this->setField($this->field, $thisClass, $form, $config);
        }
    }


    /**
     * @param $field
     * @param \ReflectionClass $thisClass
     * @param FormInterface $builder
     * @param array $options
     *
     * @throws \Exception
     */
    protected function setField($field, \ReflectionClass $thisClass, $builder, array $options)
    {
        if (!is_null($field) && !in_array($field, SingleItemType::$availableFields)) {
            throw new \Exception("Can't change this field [$field]");
        }

        $specialFieldSetter = 'set' . ucfirst($field);

        try {
            $setterMethod = $thisClass->getMethod($specialFieldSetter);

            $setterMethod->invoke($this, $builder, $options);
        } catch (\ReflectionException $e) {
            $builder->add($field);
        }
    }


    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'field'      => null
        ]);
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $this->configureOptions($resolver);
    }


    /**
     * @param FormInterface $form
     * @param $field
     *
     * @return mixed|string
     */
    public static function getLabel(FormInterface $form, $field)
    {
        $item  = $form->get($field);
        $label = $item->getConfig()->getOption('label');

        if (is_null($label)) {
            $label = ucfirst(
                strtolower(
                    preg_replace(
                        '/(?|([a-z\d])([A-Z])|([^\^])([A-Z][a-z]))/',
                        '$1 $2',
                        $item->getName()
                    )
                )
            );
        }

        return $label;
    }


    /**
     * @return string
     */
    public function getName()
    {
        $formName = SingleItemType::$reflectionClass->getShortName();

        $formName = strtolower(str_replace("\\", '_', $formName));

        if (!is_null($this->field) && !is_array($this->field)) {
            $formName .= strtolower($this->field);
        }

        return $formName;
    }
}
