<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.08.15
 * Time: 15:12
 */

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use ApiBundle\Api;
//use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("api/social")
 *
 * Class AppApiSocialController
 * @package ApiBundle\Controller
 */
class AppApiSocialController extends FOSRestController
{
    use Api;

    /**
     * Get social parameters - links to profile on social networks, social network application ids
     *
     * Everything ok
     * =============
     *       {
     *           "status": "ok",
     *           "data": {
     *              "url_mike_saffaie_facebook": "https://www.facebook.com/pages/Mike-Saffaie/359627120721911",
     *              "url_mike_saffaie_twitter": "https://twitter.com/califitmike",
     *              "url_mike_saffaie_instagram": "https://instagram.com/califitmike",
     *              "url_mike_saffaie_youtube": "https://www.youtube.com/channel/UC8ZTr6n7uWVVGWUomJHSaKA",
     *              "url_flo_fit_facebook": "https://www.facebook.com/GetFLOFIT",
     *              "url_flo_fit_twitter": "https://twitter.com/getflofit",
     *              "url_flo_fit_instagram": "https://instagram.com/getflofit",
     *              "url_flo_fit_youtube": "https://www.youtube.com/channel/UCqjt-Tod1JMGgsjVevl4Lwg",
     *              "url_flo_rida_facebook": "https://www.facebook.com/officialflo",
     *              "url_flo_rida_twitter": "https://twitter.com/official_flo",
     *              "url_flo_rida_instagram": "https://instagram.com/official_flo",
     *              "url_flo_rida_youtube": "https://www.youtube.com/channel/UCBRFlde39a2U4nAkmGqJwAQ",
     *              "url_natalie_larose_facebook": "https://www.facebook.com/NatalieLaRoseMusic",
     *              "url_natalie_larose_twitter": "https://twitter.com/natalielarose",
     *              "url_natalie_larose_instagram": "https://instagram.com/natalielarose",
     *              "url_natalie_larose_youtube": "https://www.youtube.com/channel/UCL1TElUgyJX9PRacUphrSsA",
     *           }
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Get social sites parameters"
     * )
     *
     * @Get("/parameters", name="api_get_social_parameters")
     * @param Request $request
     *
     * @return mixed
     */
    public function getParametersAction(Request $request)
    {
        $data['url_mike_saffaie_facebook'] = $this->getParameter('url_mike_saffaie_facebook');
        $data['url_mike_saffaie_twitter'] = $this->getParameter('url_mike_saffaie_twitter');
        $data['url_mike_saffaie_instagram'] = $this->getParameter('url_mike_saffaie_instagram');
        $data['url_mike_saffaie_youtube'] = $this->getParameter('url_mike_saffaie_youtube');

        $data['url_flo_fit_facebook'] = $this->getParameter('url_flo_fit_facebook');
        $data['url_flo_fit_twitter'] = $this->getParameter('url_flo_fit_twitter');
        $data['url_flo_fit_instagram'] = $this->getParameter('url_flo_fit_instagram');
        $data['url_flo_fit_youtube'] = $this->getParameter('url_flo_fit_youtube');

        $data['url_flo_rida_facebook'] = $this->getParameter('url_flo_rida_facebook');
        $data['url_flo_rida_twitter'] = $this->getParameter('url_flo_rida_twitter');
        $data['url_flo_rida_instagram'] = $this->getParameter('url_flo_rida_instagram');
        $data['url_flo_rida_youtube'] = $this->getParameter('url_flo_rida_youtube');

        $data['url_natalie_larose_facebook'] = $this->getParameter('url_natalie_larose_facebook');
        $data['url_natalie_larose_twitter'] = $this->getParameter('url_natalie_larose_twitter');
        $data['url_natalie_larose_instagram'] = $this->getParameter('url_natalie_larose_instagram');
        $data['url_natalie_larose_youtube'] = $this->getParameter('url_natalie_larose_youtube');

        return new JsonResponse($this->okResponse($data));
    }
}