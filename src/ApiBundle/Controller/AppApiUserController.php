<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppApiUserController
 *
 * @Route("/api/user")
 */
class AppApiUserController extends FOSRestController
{
    /**
     *  Get user's profile
     *
     * Responses:
     * =========
     *
     * Everything ok:
     * --------------------
     *
     *     {
     *           "status": "ok",
     *           "data": {
     *               "id": 2,
     *               "firstName": "Pepa",
     *               "lastName": "Uzivatel",
     *               "avatarPhoto": "",
     *               "email": "admin",
     *               "username": "admin",
     *               "preferredUnits": "imperial",
     *               "dateOfBirth": "1982-11-27",
     *               "facebookId": null,
     *               "facebookAccessToken": null,
     *               "twitterId": null,
     *               "twitterAccessToken": null,
     *               "youtubeLink": null,
     *               "snapchatNickname": null,
     *               "lastPasswordChange": "24.06.2015 17:14",
     *               "location": "New York",
     *               "privacySettings": {
     *                   "id": 3,
     *                   "publicProfile": true,
     *                   "displayFullName": false,
     *                   "displayEmail": false,
     *                   "birthDateStyle": 1,
     *                   "displayLocation": false,
     *                   "displayForumActivity": false,
     *                   "displayProgressGraph": false
     *               },
     *               "profilePhotoOriginal": "http://localhost/FlofitVenice/web/media/cache/profile_picture/images/profile-photo/3/55e2e56c31d12.jpeg",
     *               "profilePhotoCropped": "http://localhost/FlofitVenice/web/media/cache/profile_picture/rc/AocWOMfB/images/profile-photo/3/55e2e56c31d12.jpeg",
     *               "cropStartX": 0,
     *               "cropStartX": 0,
     *               "cropSize": 0
     *           }
     *       }
     *
     *
     * @Route("/profile", name="api_get_user_profile")
     *
     * @param Request $request
     *
     * @return array|JsonResponse
     * @throws \LogicException
     */
    public function getUserProfileAction(Request $request)
    {

        dump($this->getUser());
        return [];

        /** @var GlobalUser $user */
        $user = $this->getUser();
        $arrayizer = $this->get('general_backend_core.services.arrayizer');
        $arrayizer->setWithout(['user', 'id']);

        $data = $this->getUserAsArray($user);

        return new JsonResponse($this->okResponse($data));
    }
}
