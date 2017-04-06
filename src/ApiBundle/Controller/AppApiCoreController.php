<?php

namespace ApiBundle\Controller;

use ApiBundle\Api;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

//use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class AppApiUserController
 *
 * @Route("/api/core")
 */
class AppApiCoreController extends FOSRestController
{
    use Api;


    /**
     * Get latest versions of applications and return ios, android
     *
     * Responses:
     * =========
     *
     * Everything ok:
     * --------------------
     *       {
     *           "status": "ok",
     *           "data": {
     *               "ios": "",
     *               "android": ""
     *           }
     *       }
     *
     * @Get("/latest-versions", name="api_get_latest_versions")
     * ApiDoc(
     *  resource=false,
     *  description="Get latest versions of mobile applications (ios, android)",
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function appLatestVersions(Request $request)
    {
        $iosVersion = $this->getParameter('app_version_ios');
        $androidVersion = $this->getParameter('app_version_android');

        $data = ['ios' => $iosVersion, 'android' => $androidVersion];

        return new JsonResponse($this->okResponse($data));
    }
}