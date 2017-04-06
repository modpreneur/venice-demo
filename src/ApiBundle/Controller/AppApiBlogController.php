<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 20.07.15
 * Time: 13:03
 */

namespace ApiBundle\Controller;

use ApiBundle\Api;
use ApiBundle\Filters\BlogFilter;
use AppBundle\Entity\Category;
use AppBundle\Services\Arrayizer;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Tests\Controller;

/**
 *
 * Class AppApiBlogController
 *
 * @Route("/api/posts")
 */
class AppApiBlogController extends FOSRestController
{
    use Api;

    /**
     * Get posts.
     *
     * Example URI:
     * /articles?offset=3&limit=10
     *
     * Categories:
     * ===========
     * blog
     * ----
     *
     * article
     * -------
     *
     * content contains html tags
     *
     * Everything ok:
     * ==============
     *      {
     *         "status": "ok",
     *         "data": [
     *         {
     *             "author": "Test Testovic",
     *             "authorUsername": "test",
     *             "id": 1,
     *             "dateWritten": "20.07.2015 12:13",
     *             "dateToPublish": "21.07.2015 00:00",
     *             "published": true,
     *             "handle": "test-1",
     *             "title": "Test 1",
     *             "content": "lorem ipsum"
     *         },
     *         {
     *             "author": "Test Testovic",
     *             "authorUsername": "test",
     *             "id": 2,
     *             "dateWritten": "21.07.2015 12:13",
     *             "dateToPublish": "23.07.2015 00:00",
     *             "published": true,
     *             "handle": "test-2",
     *             "title": "Test 2",
     *             "content": "lorem ipsum
     *         },
     *         ]
     *      }
     *
     * No category found
     * ==================
     *      {
     *           "status": "not ok",
     *           "message": "No category found"
     *       }
     *
     * ApiDoc(
     *   resource=false,
     *   description="Get posts",
     *   requirements={
     *      {"name"="category","dataType"="string","description"="category of posts"},
     *      {"name"="offset","dataType"="int","description"="offset of the posts(number of posts from the beginning to be skipped). default=0"},
     *      {"name"="limit","dataType"="int","description"="number of posts to be returned. default=5"},
     *  }
     * )
     *
     * @Get("/{category}", name="api_get_blog_posts")
     *
     * @param Request $request
     *
     * @param $category
     *
     * @return JsonResponse
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function getAllBlogPostsAction(Request $request, $category)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $category = $entityManager->getRepository(Category::class)
            ->findOneBy(['handle' => $category]);

        if (!$category) {
            return new JsonResponse($this->notOkResponse('No category found'));
        }

        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');

        if (null !== $offset && !is_numeric($offset)) {
            $offset = 0;
        }

        if (null !== $limit && !is_numeric($limit)) {
            $limit = 5;
        }

        $blogArticles = $entityManager->getRepository('AppBundle:BlogArticle')
            ->findArticlesByCategory($category, true, $offset, $limit);

        $blogFilter = new BlogFilter();
        $output = $blogFilter->filter($blogArticles, new Arrayizer);

        return new JsonResponse($this->okResponse($output));
    }
}
