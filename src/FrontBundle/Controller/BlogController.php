<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\BlogArticle;
use FrontBundle\Helpers\Ajax;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlogController
 *
 * @Route("/blog")
 * @package FrontBundle/Controller
 */
class BlogController extends Controller
{
    use Ajax;

    /**
     * @Route("", name="blog_index")
     * @Route("/offset/{offset}", name="blog_index_with_offset")
     * @param Request $request
     * @param int $offset
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \LogicException
     */
    public function indexAction(Request $request, $offset = 0)
    {
        $numberOfPosts = $this->getParameter('blog_number_of_posts_displayed');
        $entityManager = $this->getDoctrine()->getManager();

//        $category = $entityManager->getRepository('AppBundle:Category')
//            ->findOneBy(['name' => 'blog']);
//
//        $postCount = $entityManager->getRepository('AppBundle:BlogArticle')
//            ->countPostsByCategory($category);
//
//        $posts = $entityManager->getRepository('AppBundle:BlogArticle')
//            ->findAllPagesByCategory($category, true, $offset, $numberOfPosts);
//
        $count = $entityManager->getRepository(BlogArticle::class)->count();
        $offset += $this->getParameter('blog_number_of_posts_displayed');
        $displayLoadMore = $offset < $count and $count !== 0 and $count < $numberOfPosts;

        $blogArticles = $entityManager->getRepository(BlogArticle::class)->findAll();


        return $this->render(
            'VeniceFrontBundle:Blog:index.html.twig',
            array(
                'blogArticles' =>$blogArticles,
                'displayLoadMore' => $displayLoadMore,
                'offset' =>$offset,
                'isAjax' =>$request->isXmlHttpRequest()
            )
        );
    }

    /**
     * @Route("/permanent/post/{id}", name="blog_permanent_post_detail")
     * @param BlogArticle $blogArticle
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function permanentPostLink(BlogArticle $blogArticle)
    {
        return $this->render('VeniceFrontBundle:Blog:postDetails.html.twig', [
            'blogPost' => $blogArticle,
            'authorPublicProfileLink' => $this->generateUrl(
                'core_front_user_public_profile',
                ['username'=>$blogArticle->getPublisher()->getUsername()]
            )
        ]);
    }

    /**
     * @Route("/post/{handle}", name="blog_post_details")
     * @param BlogArticle $blogArticle
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function blogPostAction(BlogArticle $blogArticle)
    {
        return $this->render('VeniceFrontBundle:Blog:postDetails.html.twig', [
            'blogPost' => $blogArticle,
            'authorPublicProfileLink' => $this->generateUrl(
                'core_front_user_public_profile',
                ['username'=>$blogArticle->getPublisher()->getUsername()]
            ),
        ]);
    }
}