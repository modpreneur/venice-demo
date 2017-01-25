<?php

namespace FrontBundle\Twig;

use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * PopupService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
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
        );
    }


    public function afterTrialExpires(User $user)
    {
        if (!$user) {
            return ''; // no user
        }

        $twigTemplater = $this->container->get('templating');
        if (count($user->getProducts()) == 0) {
            return $twigTemplater->render('VeniceFrontBundle:Core/trial:pop-trial-flow.html.twig');
        }

        $trialStart = $user->getTrialStart();
        $trialEnd = $user->getTrialEnd();

        if ($trialStart === null || $trialEnd === null) {
            return '';
        }

        $now = new \DateTime();

        if ($now->diff($trialEnd)->invert) {
            // trial expired
            if ($trialStart->diff($trialEnd)->d === 7) {
                // was 7 day trial
                if (count($user->getProducts()) > 0) {
                    $flofit = $this->container->get('doctrine.orm.entity_manager')
                        ->getRepository(StandardProduct::class)
                        ->findOneBy(array('handle' => 'flofit'));

                    if (null !== $flofit && !$user->haveAccess($flofit) && !$user->isTrial7daysEndPopupShown()) {
                        $user->setTrial7daysEndPopupShown(true);

                        return $twigTemplater->render('VeniceFrontBundle:Core/trial:pop-trial-flow.html.twig');
                    }
                } else {
                    return $twigTemplater->render('VeniceFrontBundle:Core/trial:pop-trial-flow.html.twig');
                }
            } elseif ($trialStart->diff($trialEnd)->d === 14 && !$user->isTrialEndPopupShown()) {
                // was 14 day trial
                $user->setTrialEndPopupShown(true);

                return $twigTemplater->render('VeniceFrontBundle:Core/trial:after-fourteen-days.html.twig');
            }
        }

        return '';
    }


    public function beforeTrialExpires(User $user, $showAlways = false)
    {
        if (!$user) {
            return ''; // no user
        }

        $trialStart = $user->getTrialStart();
        $trialEnd = $user->getTrialEnd();

        if (($trialStart === null || $trialEnd === null) && $showAlways == false) {
            return ''; // not a trial -> no trial dates
        }
        $now = new \DateTime();

        if ((!$user->isTrialExtendPopupShown()) &&
            $trialStart->diff($trialEnd)->d === 7 &&
            $trialStart->diff($now)->d >= 4 &&
            $trialStart->diff($now)->d <= 7
        ) {
            $user->setTrialExtendPopupShown(true);
            $twigTemplater = $this->container->get('templating');
            return $twigTemplater->render(':CoreBundle/Front/core/trial:two-days-before.html.twig');
        } else {
            if ($showAlways) {
                $twigTemplater = $this->container->get('templating');
                return $twigTemplater->render(':CoreBundle/Front/core/trial:two-days-before.html.twig');
            }

            return ''; // trial didnt expired or was shown or is not two days before expiration
        }
    }


    public function productPopup(Product $product)
    {
        $twigTemplater = $this->container->get('templating');

        return $twigTemplater->render(
            'VeniceFrontBundle:Core/trial:product-popup.html.twig',
            ['product' => $product]
        );
    }


    public function getName()
    {
        return 'popupService';
    }
}
