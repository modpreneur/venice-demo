<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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

    protected static $availableFields = null;

    protected $ignoredFields;

    protected $field;

    protected $entity;

    protected $options;


    /**
     * SingleItemType constructor.
     */
    public function __construct()
    {

    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->field   = $options['field'];
        $this->entity  = $options['data'];
        $this->options = $options;

        $this->ignoredFields = [];

        if (is_null(SingleItemType::$availableFields)) {
            SingleItemType::$availableFields = [];

            SingleItemType::$reflectionClass = new \ReflectionClass($this->entity);

            foreach (SingleItemType::$reflectionClass->getProperties() as $property) {
                SingleItemType::$availableFields[] = $property->getName();
            }
        }


        $thisClass = new \ReflectionClass($this);
        if (is_null($this->field)) {
            foreach (SingleItemType::$availableFields as $field) {
                if (!in_array($field, $this->ignoredFields)) {
                    $this->setField($field, $thisClass, $builder, $options);
                }
            }
        } elseif (is_array($this->field)) {
            foreach ($this->field as $field) {
                if (!in_array($field, $this->ignoredFields)) {
                    $this->setField($field, $thisClass, $builder, $options);
                }
            }
        } else {
            $this->setField($this->field, $thisClass, $builder, $options);
        }
    }


    protected function setField($field, \ReflectionClass $thisClass, FormBuilderInterface $builder, array $options)
    {
        if (!is_null($field) && !in_array($field, SingleItemType::$availableFields)) {
            //throw new \Exception("Can't change this field [$field]");
        }

        $specialFieldSetter = "set" . ucfirst($field);

        try {
            $setterMethod = $thisClass->getMethod($specialFieldSetter);

            $setterMethod->invoke($this, $builder, $options);
        } catch (\ReflectionException $e) {
            $builder->add($field);
        }
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'field'      => null
        ));
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $this->configureOptions($resolver);
    }


    public function getLabel(FormInterface $form, $field)
    {
        $item = $form->get($field);
        $label = $item->getConfig()->getOption("label");

        if (is_null($label)) {
            $label = ucfirst(strtolower(preg_replace('/(?|([a-z\d])([A-Z])|([^\^])([A-Z][a-z]))/', '$1 $2',
                $item->getName())));
        }

        return $label;
    }


    /**
     * @return string
     */
    public function getName()
    {
        $formName = SingleItemType::$reflectionClass->getShortName();

        $formName = strtolower(str_replace("\\", "_", $formName));

        if (!is_null($this->field) && !is_array($this->field)) {
            $formName .= strtolower($this->field);
        }

        return $formName;
    }
}
