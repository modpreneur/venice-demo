<?php

namespace ApiBundle\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @internal param Request $request
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function oauthAction(Request $request)
    {
        $url = $this->getParameter('necktie_url') . '/oauth/v2/token';
        $clientId = $this->getParameter('mobile_app_client_id');
        $clientSecret = $this->getParameter('mobile_app_client_secret');

        $client = new Client();

        $necktieRequestBody = $request->request->all();
        $necktieRequestBody['client_id'] = $clientId;
        $necktieRequestBody['client_secret'] = $clientSecret;

        $grantType = (array_key_exists('refresh_token', $necktieRequestBody)? 'refresh_token' : 'password');

        $necktieRequestBody['grant_type'] = $grantType;

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
        }

        $responseBody = $response->getBody()->getContents();

        return new JsonResponse($responseBody, $response->getStatusCode(), [], true);
    }
}
