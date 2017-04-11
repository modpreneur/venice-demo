<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Repositories\OAuthTokenRepository;
use AppBundle\Entity\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Venice\AppBundle\Entity\OAuthToken;

/**
 * Class OAuthController
 * @package ApiBundle\Controller
 *
 * @Route("/oauth")
 *
 * Replaces the functionality of old FLO FIT's FOSOAuthServer
 * Redirects requests to Necktie
 */
class OAuthController extends Controller
{
    /**
     * @Route("/v2/token", name="app_api_oauth", options={})
     * @Method({"POST"})
     * @internal param Request $request
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function oauthAction(Request $request)
    {
        $url = $this->getParameter('necktie_url') . '/oauth/v2/token';

        $entityManager = $this->getDoctrine()->getManager();
        $client = new Client();

        $necktieRequestBody = $request->request->all();

        $grantType = (array_key_exists('refresh_token', $necktieRequestBody) ? 'refresh_token' : 'password');

        $necktieRequestBody['grant_type'] = $grantType;
        if ($grantType === 'password') {
            $username = $necktieRequestBody['username'];
        }

        $refreshToken = $necktieRequestBody['refresh_token'];

        $necktieRequestBody = json_encode($necktieRequestBody);

        try {
            $response = $client->request(
                'POST',
                $url,
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => $necktieRequestBody,
                ]
            );
        } catch (RequestException $exception) {
            $response = $exception->getResponse();

            return new JsonResponse($response->getBody()->getContents(), $response->getStatusCode(), [], true);
        }

        $responseBody = $response->getBody()->getContents();

        $resArray = json_decode($responseBody, true);

        if ($resArray === null) {
            return new JsonResponse('Invalid necktie response - cannot parse json', 500);
        }

        $gwHelper = $this->get('venice.app.necktie_gateway_helper');
        $token = $gwHelper->createOAuthTokenFromArray($resArray);

        if ($token === null) {
            return new JsonResponse('Invalid necktie response - cannot parse token', 500);
        }

        // We don't need to do this when processing a refresh token
        if ($grantType === 'password') {
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

            if (!$user) {
                return new JsonResponse('User not found', 404);
            }

            $user->addOAuthToken($token);
            $token->setUser($user);
            $entityManager->persist($user);
            $entityManager->persist($token);
            $entityManager->flush();
        }
//        elseif ($grantType === 'refresh_token' & null !== $refreshToken) {



        return new JsonResponse($responseBody, $response->getStatusCode(), [], true);
    }
}
