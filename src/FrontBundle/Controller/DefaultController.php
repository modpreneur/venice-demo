<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\Product\StandardProduct;
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
 * @Route("/")
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
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \LogicException
     */
    public function indexAction(Request $request)
    {
        $this->get('flofit.trial.listener')->giveUserTrialAccess($this->getUser());

        $productService = $this->get('flofit.product_posts_service');
        $products = $productService
            ->getLatestProductPosts($this->getParameter('number_of_product_posts'));

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
        $messagesService = $this->get('flofit.prod_env_forum_connector');
        $messages = $messagesService->getConversations($this->getUser());

        $latestForumPosts = $messagesService->getLatestForumPosts($this->getUser());
        //$latestForumPosts = [];

        $workoutGuide = $entityManager->getRepository(StandardProduct::class)
            ->findOneBy(['handle' => 'workout-guide-book']);

        $nutritionGuide = $entityManager->getRepository(StandardProduct::class)
            ->findOneBy(['handle' => 'nutrition-guide']);

        // @todo

        $render = $this->render(
            'VeniceFrontBundle:Front:index.html.twig',
            [
                'socialPosts' => $socialStream,
                'messages'    => $messages,
                'forumPosts'  => $latestForumPosts,
                'blogArticles' => $blogArticles,
                'productPosts' => $products,
                'communityInboxUrl' => $this->container->getParameter('forum_read_conversation_url'),
                'communityForumUrl' => $this->container->getParameter('forum_url'),
                'workoutGuide'      => $workoutGuide,
                'nutritionGuide'    => $nutritionGuide,
                'displayMobileAdv'  => false,
                'displayQuickStartGuide' => $this
                    ->get('trinity.settings')
                    ->get('displayQuickStartGuide', $this->getUser()->getId(), 'user'),
                'firstLogin' => $this
                    ->get('trinity.settings')
                    ->get('firstLogin', $this->getUser()->getId(), 'user'),
            ]
        );

        $this
            ->get('trinity.settings')
            ->set('displayQuickStartGuide', false, $this->getUser()->getId(), 'user');

        $this->get('trinity.settings')
            ->set('firstLogin', false, $this->getUser()->getId(), 'user');

        return $render;
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
