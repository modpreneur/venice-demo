<?php

namespace AppBundle\Services;

use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Connector
 * @package AppBundle\Services
 */
class Connector
{
    protected $curl;


    /**
     * @return Client
     */
    protected function getClient()
    {
        return new Client(['cookies' => true,
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json']
        ]);
    }


    /**
     * Connector constructor.
     *
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;

        //$this->curl = $serviceContainer->get('anchovy.curl');
    }


    /**
     * @param $url
     *
     * @return array|mixed
     */
    public function getJson($url)
    {
        $response = $this->getClient()->get($url, [ 'content-type' => 'application/json', 'Accept' => 'application/json']);
        $body = $response->getBody();
        $decoded = json_decode($body, true);

        return is_null($decoded) ? [] : $decoded;
    }


    /**
     * @param $url
     * @param array $parameters
     *
     * @return array|mixed
     */
    public function putAndGetJson($url, $parameters = [])
    {
        $response = $this->getClient()->put($url. $parameters);
        $body = $response->getBody();
        $decoded = json_decode($response, true);

        return is_null($decoded) ? [] : $decoded;
    }


    /**
     * @param $url
     * @param array $parameters
     * @param array $options
     *
     * @return array|mixed
     */
    public function postAndGetJson($url, $parameters = [], $options = [])
    {
        $response = $this->getClient()->post($url);
        $decoded = json_decode($response, true);

        return is_null($decoded) ? [] : $decoded;
    }


    /**
     * @param $url
     * @param array $parameters
     *
     * @return array|mixed
     */
    public function postJson($url, $parameters = [])
    {
        $response = $this->getClient()->post($url, $parameters);

        $decoded = json_decode($response->getBody(), true);

        return is_null($decoded) ? [] : $decoded;
    }
}
