<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.07.15
 * Time: 12:28
 */

namespace GeneralBackend\CoreBundle\Controller\AppApi;


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

    const NEWSLETTERS_PARAMETER = "newsletters";
    const LIST_ID_PARAMETER = "listId";
    const IS_SUBSCRIBED_PARAMETER = "isSubscribed";
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
     */
    public function getNewslettersAction()
    {
        $user = $this->getUser();

//        $connectorName = $this->getParameter();
        $connector = $this->get('flofit.services.maropost_connector');

        $newsletters = $connector->getNewsletters($user);
        $data = [];

        //set custom key isSubscribed
        /** @var Newsletter $newsletter */
        foreach ($newsletters as $newsletter) {
            $array[self::LIST_ID_PARAMETER] = $newsletter->getListId();
            $array[self::TITLE_PARAMETER] = $newsletter->getTitle();
            $array[self::IS_SUBSCRIBED_PARAMETER] = $newsletter->isSubscribed();

            $data[] = $array;
        }

        return new JsonResponse($this->okResponse($data));
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

//        $connectorName = $this->getParameter('flofit.services.maropost_connector');
        $connector = $this->get('flofit.services.marop$connectorNameost_connector');

        $newsletters = json_decode($request->get(self::NEWSLETTERS_PARAMETER), true);

        if (!$newsletters || !is_array($newsletters) || empty($newsletters)) {
            return new JsonResponse($this->notOkResponse('No data'));
        }

        $listIdParameter = self::LIST_ID_PARAMETER;
        $isSubscribedParameter = self::IS_SUBSCRIBED_PARAMETER;

        foreach ($newsletters as $newsletter) {
            if (!isset($newsletter[$listIdParameter]) || !isset($newsletter[$isSubscribedParameter])) {
                return new JsonResponse(
                    $this->notOkResponse("Missing $listIdParameter or $isSubscribedParameter field.")
                );
            }
            $newsletterObject = new Newsletter(
                $user,
                $newsletter[$listIdParameter],
                null,
                $newsletter[$isSubscribedParameter]
            );

            $connector->updateNewsletter($newsletterObject, $user);
        }

        return $this->getNewslettersAction();
    }
}
