<?php
/**
 * Created by PhpStorm.
 * User: polki
 * Date: 26.8.2015
 * Time: 10:39
 */
namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\SocialStream\SocialPost;
use ApiBundle\Api;
//use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/api/social-stream")
 *
 * Class AppApiSocialStreamController
 * @package ApiBundle\Controller
 */
class AppApiSocialStreamController extends FOSRestController
{
    use Api;
    /**
     * Get social stream posts
     *
     * Everything ok
     * =============
     *       {
     *           "status": "ok",
     *           "data": [
     *           {
     *               "type": "facebook",
     *               "author": "Patricia Alajcjccbadac Huiman",
     *               "dateTime": "2015-08-07 12:51:04",
     *               "message": "\nPatricia Alajcjccbadac Huiman updated her profile picture.",
     *               "profilePic": "https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpf1/v/t1.0-1/p50x50/11056554_102605283426249_7316289718182365310_n.jpg?oh=01656d10123d85fc668d622df43aef51&oe=56790FC8&__gda__=1449850979_297b43a53e1f897bb179f0996e427b88",
     *               "url": "https://www.facebook.com/102276470125797"
     *           },
     *           {
     *               "type": "facebook",
     *               "author": "Patricia Alajcjccbadac Huiman",
     *               "dateTime": "2015-08-07 12:25:01",
     *               "message": "Today i go new avesome album of MASSIVE ABS!!!",
     *               "profilePic": "https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpf1/v/t1.0-1/p50x50/11056554_102605283426249_7316289718182365310_n.jpg?oh=01656d10123d85fc668d622df43aef51&oe=56790FC8&__gda__=1449850979_297b43a53e1f897bb179f0996e427b88",
     *               "url": "https://www.facebook.com/102276470125797"
     *           },
     *           {
     *               "type": "twitter",
     *               "author": "Mod Developer",
     *               "dateTime": "2015-08-07 09:43:35",
     *               "message": "That's it, that feel awesome #beer",
     *               "profilePic": "http://pbs.twimg.com/profile_images/629636553820798976/52Dti3fC_normal.jpg",
     *               "url": "https://twitter.com/modepreneur"
     *           }
     *           ]
     *       }
     *
     * Invalid count parameter
     * =======================
     *       {
     *           "status": "not ok",
     *           "message": "Parameter count is missing"
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Get social stream posts",
     *  requirements={
     *      {"name"="count","dataType"="integer","description"="count of social posts, default = 10"},
     *  }
     * )
     *
     * @Get("/{count}", name="api_get_social_stream")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getMessagesAction(Request $request, $count)
    {
        if (is_null($count) or !is_numeric($count)) {
            return new JsonResponse($this->notOkResponse("Parameter count is missing or invalid"));
        }
        $socialService = $this->get('flofit.services.social_feed');
        $socialStream = $socialService->getLatestPosts($this->getParameter('social_stream_number_of_posts_downloaded'));
        $socialStream = array_slice($socialStream, 0, $count);
        $arrayizer = $this->get('flofit.services.arrayizer');
        $messagesArray = [];
        foreach ($socialStream as $post) {
            $messagesArray[] = $arrayizer->arrayize($post);
        }
        return new JsonResponse($this->okResponse($messagesArray));
    }
}