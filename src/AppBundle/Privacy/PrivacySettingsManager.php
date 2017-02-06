<?php
/**
 * @author Tomáš Jančar
 */

namespace AppBundle\Privacy;

use AppBundle\Entity\User;
use Trinity\Bundle\SettingsBundle\Manager\SettingsManagerInterface;

/**
 * Class PrivacySettingsManager
 * @package AppBundle\Privacy
 */
class PrivacySettingsManager
{
    const FIELDS = [
            'publicProfile',
            'displayEmail',
            'birthDateStyle',
            'displayLocation',
            'displayForumActivity',
            'displaySocialMedia',
        ];

    /** @var  SettingsManagerInterface */
    private $settings;


    /**
     * PrivacySettings constructor.
     *
     * @param SettingsManagerInterface $settings
     */
    public function __construct(SettingsManagerInterface $settings)
    {
        $this->settings = $settings;
    }


    /**
     * @param User $user
     * @return PrivacySettings
     */
    public function getPrivacySettings(User $user)
    {
        $setting = new PrivacySettings();

        foreach (self::FIELDS as $field) {
            $fieldF = 'set' . ucfirst($field);
            $setting->{$fieldF}($this->settings->get($field, $user->getId(), 'user_settings'));

            dump($this->settings->get($field, $user->getId(), 'user_settings'));
        }

        return $setting;
    }


    /**
     * @param PrivacySettings $entity
     * @param User $user
     */
    public function save(PrivacySettings $entity, User $user)
    {
        $setting = $this->settings;

        foreach (self::FIELDS as $field) {
            $fieldF = 'get' . ucfirst($field);
            if (!method_exists($entity, $fieldF)) {
                $fieldF = 'is' . ucfirst($field);
            }

            $value  = $entity->{$fieldF}();
            $setting->set($field, $value, $user->getId(), 'user_settings');
        }
    }
}
