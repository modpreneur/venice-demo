<?php

namespace FrontBundle\Twig;

use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Trinity\Bundle\SettingsBundle\Manager\SettingsManager;
use Venice\AppBundle\Entity\Product\Product;

/**
 * Class PopupService
 * @package FrontBundle\Twig
 */
class PopupService extends \Twig_Extension
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @var SettingsManager
     */
    private $settings;


    /**
     * PopupService constructor.
     *
     * @param ContainerInterface $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->settings = $container->get('trinity.settings');
    }


    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function(
                'afterTrialExpires',
                [
                    $this,
                    'afterTrialExpires'
                ],
                [
                    'is_safe' => ['html']
                ]
            ),
            new \Twig_Function(
                'beforeTrialExpires',
                [
                    $this,
                    'beforeTrialExpires'
                ],
                ['is_safe' => ['html']]
            ),
            new \Twig_Function(
                'productPopup',
                [
                    $this,
                    'productPopup'
                ],
                ['is_safe' => ['html']]
            )
        ];
    }


    /**
     * @param User $user
     *
     * @todo check !!!
     *
     * @return string
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function afterTrialExpires(User $user)
    {
        if (!$user) {
            return ''; // no user
        }

        $twigTemplater = $this->container->get('templating');

        if (count($user->getProductAccesses()) === 0) {
            return $twigTemplater->render('VeniceFrontBundle:Core/trial:pop-trial-flow.html.twig');
        }

        $trialStart = $this->settings->get('trialStart', $user->getId(), 'user');
        $trialEnd   = $this->settings->get('trialEnd', $user->getId(), 'user');

        if ($trialStart === null || $trialEnd === null) {
            return '';
        }

        $now = new \DateTime();

        /** @var SettingsManager $settings */
        $settings = $this->settings;

        $trialEndPopupShown = $settings->get('trialEndPopupShown', $user->getId(), 'user');

        if ($now->diff($trialEnd)->invert) {
            // trial expired
            if ($trialStart->diff($trialEnd)->d === 7) {
                // was 7 day trial
                if (count($user->getProductAccesses()) > 0) {
                    $flofit = $this->container->get('doctrine.orm.entity_manager')
                        ->getRepository(StandardProduct::class)
                        ->findOneBy(['handle' => 'flofit']);

                    $isTrial7daysEndPopupShown = $settings
                        ->get('trial7daysEndPopupShown', $user->getId(), 'user');

                    if (null !== $flofit && !$user->haveAccess($flofit) && !$isTrial7daysEndPopupShown) {
                        $settings->set('trial7daysEndPopupShown', true, $user->getId(), 'user');

                        return $twigTemplater->render('VeniceFrontBundle:Core/trial:pop-trial-flow.html.twig');
                    }
                } else {
                    return $twigTemplater->render('VeniceFrontBundle:Core/trial:pop-trial-flow.html.twig');
                }
            } elseif ($trialStart->diff($trialEnd)->d === 14 && !$trialEndPopupShown) {
                // was 14 day trial
                $settings->set('trialEndPopupShown', true, $user->getId(), 'user');
                return $twigTemplater->render('VeniceFrontBundle:Core/trial:after-fourteen-days.html.twig');
            }
        }

        return '';
    }


    /**
     * @param User $user
     * @param bool $showAlways
     *
     * @return string
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function beforeTrialExpires(User $user, $showAlways = false)
    {
        if (!$user) {
            return ''; // no user
        }

        /** @var SettingsManager $settings */
        $settings = $this->settings;

        /** @var \Datetime $trialStart */
        $trialStart = $settings->get('trialStart', $user->getId(), 'user');

        /** @var \DateTime $trialEnd */
        $trialEnd = $settings->get('trialEnd', $user->getId(), 'user');

        if (($trialStart === null || $trialEnd === null) && $showAlways === false) {
            return ''; // not a trial -> no trial dates
        }

        $now = new \DateTime();

        if ((!$settings->get('trialExtendPopupShown', $user->getId(), 'user')) &&
            $trialStart->diff($trialEnd)->d === 7 &&
            $trialStart->diff($now)->d >= 4 &&
            $trialStart->diff($now)->d <= 7
        ) {
            $settings->set('trialExtendPopupShown', true, $user->getId(), 'user');
            $twigTemplater = $this->container->get('templating');
            return $twigTemplater->render('VeniceFrontBundle:Core/trial:two-days-before.html.twig');
        } else {
            if ($showAlways) {
                $twigTemplater = $this->container->get('templating');
                return $twigTemplater->render('VeniceFrontBundle:Core/trial:two-days-before.html.twig');
            }

            return ''; // trial didnt expired or was shown or is not two days before expiration
        }
    }


    /**
     * @param Product $product
     *
     * @return mixed
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function productPopup(Product $product)
    {
        $twigTemplater = $this->container->get('templating');

        return $twigTemplater->render(
            'VeniceFrontBundle:Core/trial:product-popup.html.twig',
            ['product' => $product]
        );
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'popupService';
    }
}
