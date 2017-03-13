<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use GeneralBackend\CoreBundle\Entity\GlobalUser;
use GeneralBackend\CoreBundle\Services\AmemberConnector;
use GeneralBackend\CoreBundle\Services\VanillaForumConnector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class KernelListener
 * @package AppBundle\EventListener
 */
class KernelListener
{
    /** @var ContainerInterface */
    private $serviceContainer;


    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->serviceContainer = $container;
    }


    /**
     * @param FilterResponseEvent $event
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $setting = $this
            ->serviceContainer
            ->get('trinity.settings');

        /** @var GlobalUser $user */
        $user = $this->getUser();

        if ($user === null) {
            return;
        }

        if ($event->isMasterRequest()) {
            $request  = $event->getRequest();
            $response = $event->getResponse();

            /** @var VanillaForumConnector $vanillaService */
            $vanillaService = $this->serviceContainer->get('flofit.prod_env_forum_connector');

            $cId = $setting->get('communityId', $user->getId(), 'user');
            if ($user && $cId < 0) {
                $setting->set('communityId', $vanillaService->getCommunityIdFromCookies(), $user->getId(), 'user');
            }

            if (($user === null) || (($user !== null) && $vanillaService->getCommunityIdFromCookies() != $cId)) {
                foreach ($vanillaService->createDeleteCookies() as $cookie) {
                    $response->headers->setCookie($cookie);
                }
            }

            if (!$request
                    ->cookies->has($this->serviceContainer->getParameter('forum_auth_cookie_name'))
                && !is_null($user) && $user->getCommunityId() > 0
            ) {
                foreach (
                    $vanillaService->createAuthCookies(
                        $user,
                        $this->serviceContainer->getParameter('forum_auth_cookie_domain')
                    ) as $cookie
                ) {
                    $response->headers->setCookie($cookie);
                }
            }
        }
    }


    /**
     * @return User|null
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    private function getUser()
    {
        if (null === $token = $this->serviceContainer->get('security.token_storage')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}
