<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SocialStream\SocialStreamReloadLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Venice\FrontBundle\Controller\FrontController;

/**
 * Class SocialStreamController
 * @Route("/web-api/core")
 * @package AppBundle\Controller
 */
class SocialStreamController extends FrontController
{
    /**
     * @Route("/reload-social-stream")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function reloadStreamAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $entityManager->getConnection()->beginTransaction();

            /** @var SocialStreamReloadLog $lastLogEntry */
            $lastLogEntry = $entityManager
                ->getRepository(SocialStreamReloadLog::class)
                ->findLatestUpdate();

            $newLogEntry = new SocialStreamReloadLog();

            $minimalTimeToRun = (int)$this->getParameter('social_stream_reload_min_time');
            $now = new \DateTime();

            if (is_null($lastLogEntry) ||
                $minimalTimeToRun <= ($now->getTimestamp() - $lastLogEntry->getTimestamp()->getTimestamp())
            ) {
                $start = microtime(true);

                $socialService = $this->get('flofit.services.social_feed');
                $postsCount = $this->getParameter('social_stream_number_of_posts_downloaded');

                $socialService->removeAllCachedPosts();
                $socialService->getLatestPosts($postsCount);

                $newLogEntry->setReloadRan(true);
                $newLogEntry->setLoadedPosts($socialService->countPosts());
                $newLogEntry->setLoadingDuration(microtime(true) - $start);
            }

            $requesterIp = $request->headers->get('X-Forwarded-For');
            if ($requesterIp === null) {
                $requesterIp = $request->getClientIp();
            }

            $newLogEntry->setFromIP($requesterIp);

            $entityManager->persist($newLogEntry);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return new JsonResponse([
                'status'  => 'ok',
                'message' => $newLogEntry->isReloadRan() ? 'loaded' : 'not loaded'
            ]);
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollback();
            return new JsonResponse(['status' => 'not ok', 'message' => $e->getMessage()]);
        }
    }
}
