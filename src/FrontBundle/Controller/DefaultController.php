<?php

namespace FrontBundle\Controller;
use AppBundle\Services\VanillaForumConnector;
use FrontBundle\Helpers\Ajax;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\FrontBundle\Controller\FrontController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController
 * @package FrontBundle\Controller
 *
 * @Route("/front")
 */
class DefaultController extends FrontController
{
    use Ajax;

    /**
     * @param Request $request
     *
     * @Route("/", name="front_index")
     * @Route("/", name="landing_page")
     *
     * @return Response
     * @throws \LogicException
     */
    public function indexAction(Request $request)
    {
        $logger = $this->get('logger');

        $socialService = $this->get('flofit.services.social_feed');

        $postsCount = $this->getParameter('social_stream_number_of_posts_downloaded');
        $socialStream = $socialService->getLatestPostsFromCache($postsCount);
        
        if (count($socialStream) === 0) {
            $socialStream = $socialService->getLatestPosts($postsCount);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $category = $entityManager->getRepository('AppBundle:Category')
            ->findOneBy(['name' => 'blog']);

        $blogArticles = $entityManager->getRepository('AppBundle:BlogArticle')
            ->findArticlesByCategory($category, true, 0, 2);


        /** @var VanillaForumConnector $messagesService */
        $messagesService = $this->get($this->getParameter('forum_service_name'));
        $messages = $messagesService->getConversations($this->getUser());

        $latestForumPosts = $messagesService->getLatestForumPosts($this->getUser());

        return $this->render(
            'VeniceFrontBundle:Front:index.html.twig',
            [
                'socialPosts' => $socialStream,
                'messages'    => $messages,
                'forumPosts'  => $latestForumPosts,
                'blogArticles' => $blogArticles,
                'productPosts' => [],
                'communityInboxUrl' => $this->container->getParameter('forum_read_conversation_url'),
                'communityForumUrl' => $this->container->getParameter('forum_url'),
                'workoutGuide'      => null,
                'nutritionGuide'    => null,
                'displayMobileAdv'  => false,
                'displayQuickStartGuide' => null,
                'firstLogin' => new \DateTime(),
            ]
        );
    }


    /**
     * override FOSUSERBUNDLE - user show action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        return $this->redirectToRoute('core_front_user_profile_edit');
    }


    /**
     * @Route("/profile/login", name="core_front_login_profile")
     */
    public function loginAction()
    {
        return $this->render('VeniceFrontBundle:Core:login.html.twig');
    }
}
