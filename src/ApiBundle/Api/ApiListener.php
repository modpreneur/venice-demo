<?php

namespace ApiBundle\Api;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use AppBundle\Entity\OAuthToken;


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

            $token = $this->entityManager->getRepository(OAuthToken::class)->findOneBy(['accessToken' => $accessToken]);

            if ($token === null) {
                $response = new Response('The token is not known to the flofit');
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                $event->setResponse($response);

                return;
            }

            $user = $token->getUser();

            if ($user === null) {
                $response = new Response('No user for the token');
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
    }
}
