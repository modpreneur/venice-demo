<?php
/**
 * Created by PhpStorm.
 * User: ondrejbohac
 * Date: 07.07.15
 * Time: 11:03
 */

namespace ApiBundle;


use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

trait Api
{
    protected $API_KEY_PARAMETER = '_key';
    protected $USER_PARAMETER = '_user';


    private function getParameterFromContainer($parameter)
    {
        return $this->container->getParameter($parameter);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function checkApiKey(Request $request)
    {
        $apiKeyString = $this->getRequestParameter($request, $this->API_KEY_PARAMETER);

        return $apiKeyString == $this->getParameterFromContainer('api_key');
    }

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

    public function notOkResponse($message, $data = null)
    {
        $response = ['status' => 'not ok', 'message' => $message];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return $response;
    }

    public function okResponse($data = null)
    {
        $response = ['status' => 'ok', 'data' => $data];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return $response;
    }

    public function badApiKeyResponse()
    {
        return $this->notOkResponse('Bad api key');
    }

    public function missingUserResponse()
    {
        return $this->notOkResponse('Missing user');
    }

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