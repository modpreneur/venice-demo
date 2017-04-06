<?php

namespace ApiBundle;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

/**
 * Class Api
 * @package ApiBundle
 */
trait Api
{
    protected $API_KEY_PARAMETER = '_key';

    protected $USER_PARAMETER = '_user';


    /**
     * @param $parameter
     *
     * @return mixed
     */
    private function getParameterFromContainer($parameter)
    {
        return $this->container->getParameter($parameter);
    }


    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkApiKey(Request $request)
    {
        $apiKeyString = $this->getRequestParameter($request, $this->API_KEY_PARAMETER);

        return $apiKeyString == $this->getParameterFromContainer('api_key');
    }


    /**
     * @param Request $request
     *
     * @return User|null|object
     */
    public function getUserFromRequest(Request $request)
    {
        $userId = $this->getRequestParameter($request, $this->USER_PARAMETER);
        if (!$userId) {
            return null;
        }

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        return $entityManager->getRepository(User::class)->find($userId);
    }


    /**
     * @param $message
     * @param null $data
     *
     * @return array
     */
    public function notOkResponse($message, $data = null)
    {
        $response = ['status' => 'not ok', 'message' => $message];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return $response;
    }


    /**
     * @param null $data
     *
     * @return array
     */
    public function okResponse($data = null)
    {
        $response = ['status' => 'ok', 'data' => $data];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return $response;
    }


    /**
     * @return array
     */
    public function badApiKeyResponse()
    {
        return $this->notOkResponse('Bad api key');
    }


    /**
     * @return array
     */
    public function missingUserResponse()
    {
        return $this->notOkResponse('Missing user');
    }


    /**
     * @param Request $request
     * @param $parameterName
     *
     * @return mixed
     */
    public function getRequestParameter(Request $request, $parameterName)
    {
        if ($request->getMethod() === 'GET') {
            return $request->query->get($parameterName);
        } elseif ($request->getMethod() === 'POST') {
            return $request->request->get($parameterName);
        } elseif ($request->getMethod() === 'PUT') {
            return $request->request->get($parameterName);
        } elseif ($request->getMethod() === 'PATCH') {
            return $request->request->get($parameterName);
        } else {
            throw new MethodNotAllowedException(['GET', 'POST', 'PUT', 'PATCH']);
        }
    }
}
