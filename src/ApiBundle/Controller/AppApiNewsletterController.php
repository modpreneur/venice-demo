<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.07.15
 * Time: 12:28
 */

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use GeneralBackend\CoreBundle\Entity\Newsletter;
use ApiBundle\Api;
//use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tree\Fixture\Closure\News;

/**
 * @Route("/api/newsletters")
 *
 *
 * Class AppApiNewsletterController
 * @package GeneralBackend\CoreBundle\Controller\AppApi
 */
class AppApiNewsletterController extends FOSRestController
{
    use Api;

    const NEWSLETTERS_PARAMETER   = 'newsletters';
    const LIST_ID_PARAMETER       = 'listId';
    const IS_SUBSCRIBED_PARAMETER = 'isSubscribed';


    /**
     * Get newsletters for user
     *
     * Responses:
     * =========
     *
     * Everything ok:
     * --------------------
     *
     *       {
     *           "status": "ok",
     *           "data": [
     *           {
     *               "listId": 1,
     *               "title": "Title 1",
     *               "isSubscribed": true
     *           },
     *           {
     *               "listId": 2,
     *               "title": "Title 2",
     *               "isSubscribed": false
     *           },
     *           {
     *               "listId": 3,
     *               "title": "Title 3",
     *               "isSubscribed": true
     *           }
     *           ]
     *       }
     *
     * ApiDoc(
     *  resource=true,
     *  description="Get all newsletters for user"
     * )
     *
     * @Get("", name="api_newsletters_get")
     *
     * @return JsonResponse
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function getNewslettersAction()
    {
        $user = $this->getUser();

        $newsletters = $this
            ->get('venice.app.necktie_gateway')
            ->getNewsletters($user);

        return new JsonResponse($this->okResponse($newsletters));
    }


    const TITLE_PARAMETER = 'title';

    /**
     * Edit user newsletters
     *
     * Everything ok:
     * --------------------
     *
     *       {
     *           "status": "ok",
     *           "data": [
     *           {
     *               "listId": 1,
     *               "title": "Title 1",
     *               "isSubscribed": true
     *           },
     *           {
     *               "listId": 2,
     *               "title": "Title 2",
     *               "isSubscribed": false
     *           },
     *           {
     *               "listId": 3,
     *               "title": "Title 3",
     *               "isSubscribed": true
     *           }
     *           ]
     *       }
     *
     * Missing listId or isSubscribed field
     * -------------------------------------
     *      {
     *          "status": "not ok",
     *          "message": "Missing listId or isSubscribed field.."
     *       }
     *
     *
     * ApiDoc(
     * resource=true,
     *  description="Edit newsletters for the user",
     *  parameters={
     *      {"name"="newsletters","dataType"="array","required"=true,"description"="newsletters","format"="[{'listId':1,'isSubscribed':false},{'listId':2,'isSubscribed':true}]"},
     * }
     * )
     *
     * @Post("", name="api_newsletters_edit")
     * @param Request $request
     * @return JsonResponse
     */
    public function editNewslettersAction(Request $request)
    {
        $user = $this->getUser();

        $newsletters = json_decode($request->get(self::NEWSLETTERS_PARAMETER), true);

        if (!$newsletters || !is_array($newsletters) || empty($newsletters)) {
            return new JsonResponse($this->notOkResponse('No data'));
        }

        $res = $this
            ->get('venice.app.necktie_gateway')
            ->updateNewsletters($user, $newsletters);

        return $this->getNewslettersAction();
    }
}
