<?php

namespace ApiBundle\Api;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Trinity\Bundle\SettingsBundle\Entity\Setting;


/**
 * Class ApiListener
 * @package ApiBundle\Api
 */
class ApiListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var EntityManager
     */
    private $entityManager;


    /**
     * ApiListener constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     * @param EntityManager $entityManager
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        EntityManager $entityManager
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->entityManager = $entityManager;
    }


    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        try {
            $accessToken = $request->headers->get('Authorization');
            $split = explode(' ', $accessToken);

            if (count($split) < 1) {
                return;
            }

            if (strtolower($split[0]) !== 'bearer') {
                return;
            }

            $accessToken = $split[1];

            $len = strlen($accessToken);
            $settings    = $this
                ->entityManager
                ->getRepository(Setting::class)
                ->findOneBy(['value' => 's:'.$len.':"'.$accessToken.'";']);

            if ($settings === null) {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                $event->setResponse($response);

                return;
            }

            $userId = $settings->getOwnerId();
            $user   = $this->entityManager->getRepository(User::class)->find($userId);

            if ($user === null) {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                $event->setResponse($response);

                return;
            }

            $token = new ApiToken();
            $token->setUser($user);

            $authToken = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($authToken);

            return;
        } catch (\Exception $exception) {
            $this->tokenStorage->setToken(null);

            return;
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $event->setResponse($response);
    }
}
