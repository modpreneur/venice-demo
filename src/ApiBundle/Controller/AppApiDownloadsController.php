<?php

namespace ApiBundle\Controller;

use ApiBundle\Api;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProductGroup;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppApiDownloadsController
 *
 * @Route("/api/downloads")
 * @package GeneralBackend\DownloadsBundle\Controller\AppApi
 */
class AppApiDownloadsController extends FOSRestController
{
    use Api;

    const COUNT_PARAMETER = 'count';
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Get all products from given group
     *
     * @Get("/{group}/products", name="api_get_products")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \InvalidArgumentException
     */
    public function getFlofitProductsAction(Request $request, $group)
    {
        if ($group === 'flofit') {
            $handle = ProductGroup::HANDLE_FLOFIT;
        } elseif ($group === 'platinumclub') {
            $handle = ProductGroup::HANDLE_FLOMERSION;
        } else {
            return new JsonResponse($this->notOkResponse('Group not found'));
        }

        $product = $this->getDoctrine()
            ->getManager()
            ->getRepository(StandardProduct::class)
            ->findOneBy(['handle' => $handle]);

        if (!$product) {
            return new JsonResponse($this->notOkResponse('Group not found'));
        }

        return new JsonResponse($this->okResponse($data));
    }

    /**
     * Deprecated
     * ===========
     * Last played videos of user
     *
     * Responses
     * =========
     *
     * Everything ok:
     * -------------
     *
     *       {
     *           "status": "ok",
     *           "data": [
     *               10,
     *               11,
     *               9,
     *               8
     *           ]
     *       }
     *
     *
     * @Get("/last-played-video-products", name="api_get_last_played_videos")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLastPlayedVideoProductsAction(Request $request)
    {
        $user = $this->getUser();
        $count = ($this->getRequestParameter($request, self::COUNT_PARAMETER)) ?: 10;

        $replayHistoryIds = [];
        //get last played videos of user
        $replayHistory = $user->getReplayHistory()->slice(0, $count);

        foreach ($replayHistory as $item) {
            $replayHistoryIds[] = $item->getVideo()->getId();
        }

        return new JsonResponse($this->okResponse($replayHistoryIds));
    }

    /**
     * Log video as watched
     *
     * Responses
     * ==========
     *
     * Everything ok:
     * -------------
     *       {
     *           "status": "not ok",
     *           "message": "Videos updated."
     *       }
     *
     * Bad videos parameter
     * --------------------
     * videos parameter is not specified, an array or is empty
     *
     *      {
     *          "status": "not ok",
     *          "message": "Missing videos data."
     *      }
     *
     * Bad videos fields
     * ------------------
     *      {
     *          "status": "not ok",
     *          "message": "Videos data is not valid."
     *      }
     *
     * Video product does not exist
     * ----------------------------
     *      {
     *          "status": "not ok",
     *          "message": "No video product with id 3 found."
     *      }
     *
     * Bad date format
     * ---------------
     *      {
     *          "status": "not ok",
     *          "message": "Bad date format in video id 12."
     *      }
     *
     * @Post("/last-played-video-products", name="api_post_last_played_video")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postLastPlayedVideoProductsAction(Request $request)
    {
        $user = $this->getUser();
        $videos = json_decode($request->get('videos'), true);

        if (!$videos || !is_array($videos) || empty($videos)) {
            return new JsonResponse($this->notOkResponse('Missing videos data.'));
        }

        $manager = $this->getDoctrine()->getManager();

        foreach ($videos as $video) {
            if (!isset($video['id']) || !isset($video['date'])) {
                return new JsonResponse($this->notOkResponse('Videos data is not valid.'));
            }

            //get id and date
            $id = $video['id'];
            $date = $video['date'];
            try {
                $dateObject = new \DateTime($date);
            } catch (\Exception $e) {
                return new JsonResponse($this->notOkResponse('Bad date format in video with id ' . $id . '.'));
            }

            $videoObject = $manager->getRepository('ModernEntrepreneurGeneralBackendDownloadsBundle:VideoProduct')->findOneBy(['id' => $id]);

            if (!$videoObject)
                return new JsonResponse($this->notOkResponse('No video product with id ' . $id . ' found.'));

            $user->watchVideo($videoObject, $dateObject);
        }

        $manager->persist($user);
        $manager->flush();

        return new JsonResponse($this->okResponse('Videos updated.'));
    }
}