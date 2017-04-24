<?php
namespace ApiBundle\Controller;

use ApiBundle\Api;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProfilePhoto;
use AppBundle\Entity\User;
use AppBundle\Form\Type\PrivacySettingsType;
use AppBundle\Form\Type\GlobalUserType;
use AppBundle\Privacy\PrivacySettings;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParameterBag;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Nette\Utils\DateTime;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppApiUserController
 *
 * @Route("/api/user")
 */
class AppApiUserController extends FOSRestController
{
    use Api;

    const USERNAME_PARAMETER = 'username';
    const PASSWORD_PARAMETER = 'password';
    const NEW_PASSWORD_PARAMETER = 'newPassword';
    const DATE_FORMAT = 'Y-m-d';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';


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
     * @Get("/profile", name="api_get_user_profile")
     * ApiDoc(
     *  resource=true,
     *  description="Get user's profile info",
     * )
     *
     * @param Request $request
     *
     * @return array|JsonResponse
     */
    public function getUserProfileAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $arrayizer = $this->get('flofit.services.arrayizer');
        $arrayizer->setWithout(['user', 'id']);

        $data = $this->getUserAsArray($user);

        return new JsonResponse($this->okResponse($data));
    }

    /**
     * @param User $user
     *
     * @return array
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     */
    private function getUserAsArray(User $user)
    {
        $arrayizer = $this->get('flofit.services.arrayizer');
        $arrayizer->setWithout(['user', 'id']);

        $data = [];
        $data['id'] = $user->getId();
        $data['firstName'] = $user->getFirstName();
        $data['lastName'] = $user->getLastName();
//        $data['avatarPhoto'] = $user->getAvatar();
        $data['avatarPhoto'] = '';
        $data['email'] = $user->getEmail();
        $data['username'] = $user->getUsername();
        $data['preferredUnits'] = $user->getPreferredUnits();
        $data['dateOfBirth'] = ($user->getDateOfBirth()) ? $user->getDateOfBirth()->format(self::DATE_FORMAT) : null;
        //$data['birthDateStyle'] = $user->getPrivacySettings()->getBirthDateStyle();
        $data['birthDateStyle'] = $this->get('trinity.settings')
            ->get('birthDateStyle', $user->getId(), 'user_settings');
        $data['facebookId'] = $user->getFacebookId();
        //$data['googleId']   = $user->getGoogleId();
        $data['googleId']   = '';
        //$data['twitterId']  = $user->getTwitterId();
        $data['twitterId']    = '';

        $data['facebookAccessToken'] = $this
            ->get('trinity.settings')->get('facebookAccessToken', $user->getId(), 'user_settings');
        $data['twitterAccessToken']  = $this
            ->get('trinity.settings')->get('twitterAccessToken', $user->getId(), 'user_settings');

        $data['lastPasswordChange']  = ($user->getLastPasswordChange()) ? $user->getLastPasswordChange()->format(
            self::DATE_TIME_FORMAT
        ) : null;
        $data['location'] = $user->getLocation();
        $data['youtubeLink']      = $this
            ->get('trinity.settings')->get('youtubeLink', $user->getId(), 'user_settings');

        $data['snapchatNickname'] = $this
            ->get('trinity.settings')->get('snapchatNickname', $user->getId(), 'user_settings');

        $data['privacySettings'] = $arrayizer->arrayize($this->getUserPrivateSettings());

        if ($user->getProfilePhoto()) {
            $profilePhoto = $user->getProfilePhoto();
            $data['profilePhotoOriginal'] = $profilePhoto->getOriginalPhotoUrl();
            $data['profilePhotoCropped'] = $profilePhoto->getCroopedPhotoUrl();
            $data['cropStartX'] = $profilePhoto->getCropStartX();
            $data['cropStartY'] = $profilePhoto->getCropStartY();
            $data['cropSize'] = $profilePhoto->getCropSize();
        } else {
            $data['profilePhotoOriginal'] =
                'http://my.flofit.com/Resources/public/images/site/default-profile-photo.png';
            $data['profilePhotoCropped'] = $data['profilePhotoOriginal'];
            $data['cropStartX'] = 0;
            $data['cropStartY'] = 0;
            $data['cropSize'] = 0;
        }
        $sth = new ArrayCollection();
        $sth->count();
        if ($user->getProducts()->count() > 0) {
            $data['hasAccess'] = true;
        } else {
            $data['hasAccess'] = false;
        }
        $flofit = $this->container->get('doctrine.orm.entity_manager')
            ->getRepository(StandardProduct::class)
            ->findOneBy(['handle' => 'flofit']);
        if (null !== $flofit && $user->haveAccess($flofit)) {
            $data['hasCore'] = true;
        } else {
            $data['hasCore'] = false;
        }

        $trialStart = $this->get('trinity.settings')
            ->get('trialStart', $user->getId(), 'user');

        $trialEnd = $this->get('trinity.settings')
            ->get('trialEnd', $user->getId(), 'user');

        $data['trialStart'] = $trialStart ? $trialStart->format(self::DATE_FORMAT) : null;
        $data['trialEnd']   = $trialEnd   ? $trialEnd->format(self::DATE_FORMAT) : null;

        return $data;
    }

    /**
     * Get user privacy settings
     *
     * Birth date style values:
     * ------------------------
     *   FORMAT_BIRTH_DATE_NONE = 0
     *   FORMAT_BIRTH_DATE_AGE = 1, e.g. 29
     *   FORMAT_BIRTH_DATE_DAY = 2, 17th of January
     *   FORMAT_BIRTH_DATE_FULL = 3, 1990-10-15
     *
     * Responses:
     * =========
     *
     * Everything ok:
     * --------------------
     *
     *       {
     *           "status": "ok",
     *           "data": {
     *               "id": 8,
     *               "publicProfile": false,
     *               "displayFullName": false,
     *               "displayEmail": false,
     *               "birthDateStyle": 1,
     *               "displayLocation": true,
     *               "displayForumActivity": false,
     *               "displayProgressGraph": true
     *               "displayProgressGraph": true
     *           }
     *       }
     *
     *
     * ApiDoc(
     *  resource=true,
     *  description="Get user's privacy settings",
     * )
     *
     * @Get("/privacy", name="api_get_user_privacy")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \LogicException
     */
    public function getPrivacyAction(Request $request)
    {
        $arrayizer = $this->get('flofit.services.arrayizer');
        $arrayizer->setWithout(['user']);

        $arrayizer->setCallbacks(
            function ($currentObject, $properties, & $propertiesArray) {
                if ($currentObject instanceof PrivacySettings) {
                    $propertiesArray['id']=$this->getUser()->getId();
                }
            }
        );

        $privacySettings = $this->getUserPrivateSettings();

        return new JsonResponse($this->okResponse($arrayizer->arrayize($privacySettings)));
    }


    /**
     * Get user public information
     *
     * Everything ok
     * =============
     *       {
     *           "status": "ok",
     *           "data": {
     *               "firstName": "Martin",
     *               "lastName": "Matejka",
     *               "username": "martin",
     *               "profilePhotoCropped": "",
     *               "id": 14,
     *               "email": "matejka@modpreneur.com",
     *               "facebookId": null,
     *               "twitterId": null,
     *               "youtubeLink": null,
     *               "snapchatNickname": null,
     *               "dateOfBirth": null
     *           }
     *       }
     *
     * No user found
     * =============
     *     {
     *         "status": "not ok",
     *         "message": "No user with username: admi found"
     *     }
     *
     * User has no public profile
     * ===========================
     *       {
     *           "status": "not ok",
     *           "message": "User has no public profile"
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Get user's public profile",
     *  requirements={
     *      {"name"="username","dataType"="string","description"="username of user which public profile we want"}
     *  },
     * )
     *
     * @Get("/profile/public/{username}", name="api_get_user_profile_public")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     */
    public function getUserPublicProfileAction(Request $request, $username)
    {
        $user = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (null === $user) {
            return new JsonResponse($this->notOkResponse("No user with username: {$username} found"));
        }

        $privacySettings = $this->getUserPrivateSettings();
        $arrayedUser = $this->getUserAsArray($user);
        $userInfo = [];

        $fields = [
            'birthDateStyle',
            'facebookId',
            'twitterId',
            'youtubeLink',
            'snapchatNickname'
        ];


        $userInfo['id'] = $user->getId();
        $userInfo['firstName'] = $user->getFirstName();
        $userInfo['email']     = $user->getEmail();
        $userInfo['lastName']  = $user->getLastName();
        $userInfo['username']  = $user->getUsername();
        $userInfo['profilePhotoCropped']  = $user->getProfilePhoto();
        $userInfo['dateOfBirth']  = $user->getDateOfBirth();



        if ($privacySettings->isPublicProfile()) {
            foreach ($fields as $field) {
                $userInfo[$field] = $this->get('trinity.settings')->get($field, $user->getId(), 'user_settings');
            }
        } else {
            foreach ($fields as $field) {
                $userInfo[$field] = null;
            }

            $userInfo['username'] = $arrayedUser['username'];
            $userInfo['id'] = $arrayedUser['id'];
        }

        if (!$privacySettings->getDisplayEmail()) {
            $userInfo['email'] = null;
        }

        if (!$privacySettings->getDisplayFullName()) {
            $userInfo['lastName'] = null;
            $userInfo['firstName'] = null;
        }

        if (!$privacySettings->getDisplayLocation()) {
            $userInfo['location'] = null;
        }

        if (!$privacySettings->isDisplaySocialMedia()) {
            $userInfo['facebookId'] = null;
            $userInfo['twitterId'] = null;
            $userInfo['youtubeLink'] = null;
            $userInfo['snapchatNickname'] = null;
        }

        return new JsonResponse($this->okResponse($userInfo));
    }

    /**
     * Edit user's privacy settings
     *
     * This input is validated by form
     *
     * Responses:
     * =========
     *
     * Everything ok:
     * --------------------
     *  Return user privacy
     *
     * Wrong parameter value
     * ---------------------------------------
     *
     *       {
     *           "status": "not ok",
     *           "message": {
     *               "displayProgressGraph": [
     *                   "This value is not valid."
     *               ]
     *           }
     *       }
     *
     *       {
     *          "status": "not ok",
     *          "message": "Invalid value in privacysettingsdisplayprogressgraph [displayProgressGraph]."
     *       }
     *
     *
     * @Patch("/privacy", name="api_edit_privacy")
     * ApiDoc(
     *  resource=true,
     *  description="Edit user's privacy settings",
     *  parameters={
     *      {"name"="_method","dataType"="string","required"=true,"description"="method specification", "format"="PATCH"},
     *      {"name"="privacysettingspublicprofile[publicProfile]","dataType"="int","required"=false,"description"="is profile public", "format"="0 OR 1 "},
     *      {"name"="privacysettingsdisplayfullname[displayFullName]","dataType"="int","required"=false,"description"="display full name to public", "format"="0 OR 1 "},
     *      {"name"="privacysettingsdisplayemail[displayEmail]","dataType"="int","required"=false,"description"="display email to public", "format"="0 OR 1 "},
     *      {"name"="privacysettingsbirthdatestyle[birthDateStyle]","dataType"="int","required"=false,"description"="set birthday date style", "format"="0, 1, 2, 3"},
     *      {"name"="privacysettingsdisplaylocation[displayLocation]","dataType"="int","required"=false,"description"="display location to public", "format"="0 OR 1 "},
     *      {"name"="privacysettingsdisplayforumactivity[displayForumActivity]","dataType"="int","required"=false,"description"="display forum activity to public", "format"="0 OR 1 "},
     *      {"name"="privacysettingsdisplayprogressgraph[displayProgressGraph]","dataType"="int","required"=false,"description"="display progress graph to public", "format"="0 OR 1 "},
     *      {"name"="privacysettingsdisplaysocialmedia[displaySocialMedia]","dataType"="int","required"=false,"description"="display social media to public", "format"="0 OR 1 "}
     *  }
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \LogicException
     */
    public function editPrivacyAction(Request $request)
    {
        $formFields = [
            'publicProfile',
            'displayEmail',
            'displayLocation',
            'displayForumActivity',
            'displayProgressGraph',
            'displayFullName',
            'birthDateStyle',
            'displaySocialMedia'
        ];


        $user = $this->getUser();
        $privacySettings = $this->get('flofit.privacy_settings')
            ->getPrivacySettings($user);

        foreach ($formFields as $formField) {
            $formName = 'privacysettings' . strtolower($formField);
            $formData = $request->get($formName);

            if ($formData !== null) {
                if (array_key_exists($formField, $formData) && $formData[$formField] === null) {
                    return new JsonResponse($this->notOkResponse("Invalid value in $formName [$formField]."));
                }

                if ((int)$formData[$formField] >= 0 || (int)$formData[$formField] <= 3) {
                    if ($formField === 'birthDateStyle') {
                        $privacySettings->setBirthDateStyle((int)$formData[$formField]);
                    } else {
                        $privacySettings->{'set' . ucfirst($formField)}((int)$formData[$formField] === 1);
                    }
                }
            }
        }

        $this->get('flofit.privacy_settings')
            ->save($privacySettings, $user);

        return $this->redirectToRoute('api_get_user_privacy');
    }


    /**
     * Helper method for getting errors from form as associative array
     *
     * @param FormInterface $form
     *
     * @return array
     */
    protected function getFormErrors(FormInterface $form)
    {
        $errors = [];
        foreach ($form->all() as $formElement) {
            $currentErrors = $formElement->getErrors(true);

            foreach ($currentErrors as $currentError) {
                $errors[$formElement->getName()][] = $currentError->getMessage();
            }
        }

        return $errors;
    }

    /**
     * Edit user's profile
     *
     * This input is validated by form
     *
     * Responses:
     * =========
     *
     *  Everything ok:
     * --------------------
     *  Return user profile
     *
     * Bad parameters:
     * ---------------
     *       {
     *           "status": "not ok",
     *           "message": [
     *               "This form should not contain extra fields."
     *           ]
     *       }
     *
     * Invalid values:
     * ----------------
     *       {
     *           "status": "not ok",
     *           "message": {
     *               "preferredUnits": "This value is not valid.",
     *               "dateOfBirth": "This value is not valid."
     *           }
     *       }
     *
     *
     * ApiDoc(
     *  resource=true,
     *  description="Edit user' profile",
     *  parameters={
     *      {"name"="_method","dataType"="string","required"=true,"description"="method specification", "format"="PATCH"},
     *      {"name"="User[firstName]","dataType"="string","required"=false,"description"="user first name"},
     *      {"name"="User[lastName]","dataType"="string","required"=false,"description"="user last name"},
     *      {"name"="User[username]","dataType"="string","required"=false,"description"="username"},
     *      {"name"="User[preferredUnits]","dataType"="string","required"=false,"description"=" user preferred units", "format"="imperial/metric"},
     *      {"name"="User[dateOfBirth][day]","dataType"="int","required"=false,"description"="day of the birthday"},
     *      {"name"="User[dateOfBirth][month]","dataType"="int","required"=false,"description"="month of the birthday"},
     *      {"name"="User[dateOfBirth][year]","dataType"="int","required"=false,"description"="year of the birthday"},
     *      {"name"="User[email]","dataType"="string","required"=false,"description"="user's email"},
     *      {"name"="User[location]","dataType"="string","required"=false,"description"="user's location"},
     *      {"name"="User[facebook_id]","dataType"="string","required"=false,"description"="user's facebook id"},
     *      {"name"="User[twitter_id]","dataType"="string","required"=false,"description"="user's twitter id"},
     *      {"name"="User[facebook_access_token]","dataType"="string","required"=false,"description"="user's facebook token"},
     *      {"name"="User[twitter_access_token]","dataType"="string","required"=false,"description"="user's twitter token"},
     *  }
     * )
     *
     * @Patch("/profile", name="api_edit_user_profile")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \LogicException
     */
    public function editProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $form = $this->createForm(
            GlobalUserType::class,
            $user,
            ['csrf_protection' => false, 'method' => 'patch']
        );

        $newUsername = null;
        if (array_key_exists('username', $request->request->get('globaluser'))) {
            $newUsername = $request->get('globaluser')['username'];
        }

        if ($newUsername !== null) {
            $existingUser = $em->getRepository(User::class)
                ->findOneBy(['username' => $newUsername]);

            if ($existingUser !== null && $existingUser !== $user) {
                return new JsonResponse($this->notOkResponse('Username is already taken'));
            }
        }

        $requestBody = $request->request->all()['globaluser'];

        $request->request->set('global_user', $requestBody);
        $request->request->remove('globaluser');

        Request::enableHttpMethodParameterOverride();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('api_get_user_profile');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $output = $this->notOkResponse($this->getFormErrors($form));
            $output['additional'] = 'The form was posted, but it is not valid.';

            return new JsonResponse($output);
        }

        return new JsonResponse($this->notOkResponse(''));
    }

    /**
     * Change user password
     *
     * Responses:
     * =========
     *
     *  Everything ok:
     * --------------------
     *
     *           {
     *               "status": "ok",
     *               "data": null
     *           }
     *
     * Missing parameters:
     * -------------------
     *       {
     *           "status": "not ok",
     *           "message": "No password or new password."
     *       }
     *
     * Bad user password
     * -----------------------------------------
     *
     *       {
     *           "status": "not ok",
     *           "message": "Bad user password."
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Change user's password",
     *  parameters={
     *      {"name"="password","dataType"="string","required"=true,"description"="old user's password"},
     *      {"name"="newPassword","dataType"="string","required"=true,"description"="new user's password"},
     *  }
     * )
     *
     * @Post("/change-password", name="api_user_change_password")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function changePasswordAction(Request $request)
    {
        if (!$request->get(self::PASSWORD_PARAMETER) || !$request->get(self::NEW_PASSWORD_PARAMETER)) {
            return new JsonResponse($this->notOkResponse('No password or new password.'));
        }

        /** @var User $user */
        $user = $this->getUser();
        $oldPassword = $request->get(self::PASSWORD_PARAMETER);
        $newPassword = $request->get(self::NEW_PASSWORD_PARAMETER);

        $client =  new Client();

        $necktieRequestBody = [
            'oldPassword' => $oldPassword,
            'newPassword' => $newPassword
        ];

        $necktieRequestHeaders = [
            'Authorization' => 'Bearer ' . $user->getLastAccessToken(),
            'Content-Type' => 'application/json',
        ];

        try {
            $response = $client->request(
                'PUT',
                $this->getParameter('necktie_url') . '/api/v1/user/change-password',
                [
                    'headers' => $necktieRequestHeaders,
                    'body' => json_encode($necktieRequestBody),
                ]
            );
        } catch (RequestException $exception) {
            $statusCode = $exception->getResponse()->getStatusCode();

            if ($statusCode === 400) {
                $errorMessage = $this->notOkResponse('Invalid password');
            } elseif ($statusCode === 404) {
                $errorMessage = $this->notOkResponse('User not found');
            } else {
                $errorMessage = $this->notOkResponse(json_decode($exception->getResponse()->getBody()->getContents()));
            }

            return new JsonResponse($errorMessage);
        }

        if ($response->getStatusCode() === 200) {
            return new JsonResponse($this->okResponse());
        }

            return new JsonResponse($this->notOkResponse('Bad user password.'));
    }


    /**
     * Edit user's profile photo
     *
     * This input is validated by form
     *
     * Responses:
     * =========
     *
     * Everything ok:
     * --------------------
     * Return user profile
     *
     * Bad parameters:
     * ---------------
     *       {
     *           "status": "not ok",
     *           "message": [],
     *           "additional": "The form was posted, but it is not valid."
     *       }
     *
     * Invalid values:
     * ----------------
     *       {
     *           "status": "not ok",
     *           "message": "image_field is invalid",
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Change user's profile photo",
     *  parameters={
     *      {"name"="image_file","dataType"="file","required"=false,"description"="file encoded to base64 string"},
     *      {"name"="cropStartX","dataType"="int","required"=true,"description"="point x to start cropping"},
     *      {"name"="cropStartY","dataType"="int","required"=true,"description"="point y to start cropping"},
     *      {"name"="cropSize","dataType"="int","required"=true,"description"="size of crop"},
     *  }
     * )
     *
     * @Post("/profile/photo", name="api_user_change_profile_photo")
     *
     * @param Request $request
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeProfilePhotoAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $encodedFile = $request->get('image_file');

        if (is_null($encodedFile) || empty($encodedFile)) {
            $oldprofilePhoto = $user->getProfilePhoto();

            if (is_null($oldprofilePhoto)) {
                return $this->redirectToRoute('api_get_user_profile');
            }

            $user->setProfilePhoto(null);
            $em->remove($oldprofilePhoto);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('api_get_user_profile');
        }

        $profilePhoto = $user->getProfilePhoto();

        if (is_null($profilePhoto)) {
            $profilePhoto = new ProfilePhoto();

            $user->setProfilePhoto($profilePhoto);
        }

        $decodedFile = base64_decode($encodedFile);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $decodedFile, FILEINFO_MIME_TYPE);

        $fileName = uniqid() . '.' . (substr($mime_type, strpos($mime_type, '/') + 1));

        $fileSystem = $this->get('gaufrette.aws_profile_photos_original_filesystem');

        $gaufretteFile = new \Gaufrette\File($fileName, $fileSystem);
        $gaufretteFile->setContent($decodedFile, ['contentType' => $mime_type]);

        $profilePhoto->setImageName($gaufretteFile->getName());
        $profilePhoto->setImageFile($gaufretteFile);

        $profilePhoto->setCropSize($request->get('cropSize'));
        $profilePhoto->setCropStartX($request->get('cropStartX'));
        $profilePhoto->setCropStartY($request->get('cropStartY'));

        $urlGenerator = $this->get('flofit.services.profile_photo_url_generator');

        $profilePhoto->setOriginalPhotoUrl($urlGenerator->generateUrlToOriginalPhoto($profilePhoto));
        $profilePhoto->setCroopedPhotoUrl($urlGenerator->generateUrlToCroppedPhoto($profilePhoto));


        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('api_get_user_profile');
    }


    /**
     * @return PrivacySettings
     */
    private function getUserPrivateSettings()
    {
        $response = [];
        $response['id'] = 1; // random int

        $settings = [
            'publicProfile',
            'displayEmail',
            'displayLocation',
            'displayForumActivity',
            'displayFullName',
            'birthDateStyle',
            'displaySocialMedia'
        ];

        $entity = new PrivacySettings();

        foreach ($settings as $setting) {
            $response[$setting] = $this->get('trinity.settings')->get($setting, $this->getUser()->getId(), 'user_settings');
            $method = 'set' . ucfirst($setting);
            $entity->{$method}($response[$setting]);
        }

        return $entity;
    }
}