<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $clientId = $request->request->get('client_id');
        $clientSecret = $request->request->get('client_secret');

        $client = new Client();

        $necktieRequestBody = $request->request->all();
        $necktieRequestBody['client_id'] = $clientId;
        $necktieRequestBody['client_secret'] = $clientSecret;

        $grantType = (array_key_exists('refresh_token', $necktieRequestBody)? 'refresh_token' : 'password');

        $necktieRequestBody['grant_type'] = $grantType;
        $username = $necktieRequestBody['username'];
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

        $accessToken = $resArray['access_token'];
        $refreshToken = $resArray['refresh_token'];

        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        $this->get('trinity.settings')
            ->set('api_token', $accessToken, $user->getId(), 'user');

        $this->get('trinity.settings')
            ->set('api_refresh_token', $refreshToken, $user->getId(), 'user');

        return new JsonResponse($responseBody, $response->getStatusCode(), [], true);
    }
}
