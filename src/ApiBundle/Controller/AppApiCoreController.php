<?php
/**
 * Created by PhpStorm.
 * User: ondrej
 * Date: 15.12.14
 * Time: 14:53
 */

namespace GeneralBackend\CoreBundle\Controller\AppApi;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use ApiBundle\Api;
//use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppApiUserController
 *
 * @Route("/api/core")
 * @package GeneralBackend\CoreBundle\Controller\AppApi
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
     * @param Request $request
     * @return JsonResponse
     */
    public function appLatestVersions(Request $request)
    {
        $iosVersion = $this->getParameter('app_version_ios');
        $androidVersion = $this->getParameter('app_version_android');

        $data = ['ios' => $iosVersion, 'android'=>$androidVersion];

        return new JsonResponse($this->okResponse($data));
    }
}