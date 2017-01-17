<?php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Connector
 * @package AppBundle\Services
 */
class Connector
{
    /**
     * Connector constructor.
     *
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;

        //$this->curl = $serviceContainer->get("anchovy.curl");
    }


    public function getJson($url)
    {
        $response = $this->curl->setMethod("GET")->setURL($url)->execute();

        $decoded = json_decode($response, true);

        return is_null($decoded) ? [] : $decoded;
    }


    public function putAndGetJson($url, $parameters = array())
    {
        $response = $this->curl->setMethod("PUT", $parameters)->setURL($url)->execute();

        $decoded = json_decode($response, true);

        return is_null($decoded) ? [] : $decoded;
    }


    public function postAndGetJson($url, $parameters = array(), $options = array())
    {
        foreach ($options as $key => $option) {
            $this->curl->setOption($key, $option);
        }

        $response = $this->curl->setMethod("POST", $parameters)->setURL($url)->execute();

        $decoded = json_decode($response, true);

        return is_null($decoded) ? [] : $decoded;
    }


    public function postJson($url, $parameters = array())
    {
        $jsonData = json_encode($parameters);

        $request = $this->curl->setMethod("POST", array())->setURL($url);
        $request->setOption("CURLOPT_POSTFIELDS", $jsonData);
        $request->setOption("CURLOPT_RETURNTRANSFER", true);

        $request->setOption("CURLOPT_HTTPHEADER", array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            )
        );

        $response = $request->execute();

        $decoded = json_decode($response, true);

        return is_null($decoded) ? [] : $decoded;
    }
}
