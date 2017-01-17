<?php

namespace FrontBundle\Controller;

use FrontBundle\Helpers\Ajax;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlogController
 *
 * @Route("/blog")
 * @package GeneralBackend\BlogBundle\Controller\Front
 */
class BlogController extends Controller
{
    use Ajax;

    /**
     * @Route("", name="blog_index")
     * @Route("/offset/{offset}", name="blog_index_with_offset")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $offset = 0)
    {
        $numberOfPosts = $this->getParameter("blog_number_of_posts_displayed");
        $entityManager = $this->getDoctrine()->getManager();

        $category = $entityManager->getRepository("GeneralBackendBlogBundle:Category")
            ->findOneBy(array("name"=>"blog"));

        $postCount = $entityManager->getRepository("GeneralBackendBlogBundle:Post")
            ->countPostsByCategory($category);

        $posts = $entityManager->getRepository("GeneralBackendBlogBundle:Post")
            ->findAllPagesByCategory($category,true,$offset,$numberOfPosts);

        $offset += $this->getParameter("blog_number_of_posts_displayed");

        $displayLoadMore = $offset < $postCount and $postCount != 0 and $postCount < $numberOfPosts;

        return $this->renderTrinity(
            "GeneralBackendBlogBundle:Front:index.html.twig",
            array(
                "blogPosts"=>$posts,
                "displayLoadMore" => $displayLoadMore,
                "offset"=>$offset,
                "isAjax"=>$request->isXmlHttpRequest()
            ),
            array(
                "blogPostsBlock"
            )
        );
    }

    /**
     * @Route("/pernament/post/{id}", name="blog_permanent_post_detail")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pernamentPostLink(Request $request, Post $post)
    {
        return $this->render("@GeneralBackendBlog/Front/postDetails.html.twig",
            array(
                "blogPost" => $post,
                "authorPublicProfileLink" => $this->generateUrl('core_front_user_public_profile', array('username'=>$post->getPublisher()->getUserName()))
            ));
    }

    /**
     * @Route("/post/{handle}", name="blog_post_details")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function blogPostAction(Request $request, Post $post)
    {
        return $this->render("@GeneralBackendBlog/Front/postDetails.html.twig",
            array(
                "blogPost" => $post,
                "authorPublicProfileLink" => $this->generateUrl('core_front_user_public_profile', array('username'=>$post->getPublisher()->getUserName()))
            ));
    }
}