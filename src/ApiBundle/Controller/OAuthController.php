<?php

namespace ApiBundle\Controller;

use GuzzleHttp\Client;
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
     * @internal param Request $request
     *
     * @param Request $request
     *
     * @return
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function oauthAction(Request $request)
    {
        // use okResponse?
        $url = $this->getParameter('necktie_url') . '/oauth/v2/tokennigga';
        $clientId = $this->getParameter('mobile_app_client_id');
        $clientSecret = $this->getParameter('mobile_app_client_secret');

        $client = new Client();

        $necktieRequestBody = $request->request->all();
        $necktieRequestBody['client_id'] = $clientId;
        $necktieRequestBody['client_secret'] = $clientSecret;
        $necktieRequestBody['grant_type'] = 'password';

        $necktieRequestBody = \GuzzleHttp\json_encode($necktieRequestBody);

        $response = $client->request(
            'POST',
            $url,
            [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $necktieRequestBody,
            ]
        );

        $response = $response->getBody()->getContents();

        return new JsonResponse($response, 200, [], true);
    }
}
