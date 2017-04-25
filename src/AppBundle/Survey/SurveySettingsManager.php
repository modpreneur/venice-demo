<?php
namespace AppBundle\Survey;

use AppBundle\Entity\AfterPurchaseSurvey;
use AppBundle\Entity\User;
use Trinity\Bundle\SettingsBundle\DependencyInjection\TrinitySettingsExtension;
use Trinity\Bundle\SettingsBundle\Manager\SettingsManagerInterface;

/**
 * Created by PhpStorm.
 * User: marek
 * Date: 25/04/17
 * Time: 15:42
 */
class SurveySettingsManager
{
    /**
     * @var SettingsManagerInterface
     */
    private $settings;

    /**
     * SurveySettingsManager constructor.
     *
     * @param $settings
     */
    public function __construct(SettingsManagerInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param User $user
     *
     * @return AfterPurchaseSurvey
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     */
    public function getSurvey(User $user)
    {
        $setting = new AfterPurchaseSurvey();

        foreach (AfterPurchaseSurvey::FIELDS as $field) {
            $setter = 'set' . \ucfirst($field);

            if ($this->settings->has($field, $user->getId(), 'after_purchase_survey')) {
                $setting->{$setter}($this->settings->get($field, $user->getId(), 'after_purchase_survey'));
            } else {
                // If the survey hasn't been filled in
                return null;
            }
        }

        return $setting;
    }

    /**
     * @param AfterPurchaseSurvey $entity
     * @param User $user
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveSurvey(AfterPurchaseSurvey $entity, User $user)
    {
        foreach (AfterPurchaseSurvey::FIELDS as $field) {
            $fieldF = 'get' . \ucfirst($field);
            if (!\method_exists($entity, $fieldF)) {
                $fieldF = 'is' . \ucfirst($field);
            }

            $value  = $entity->{$fieldF}();
            $this->settings->set($field, $value, $user->getId(), 'after_purchase_survey');
        }
    }
}
