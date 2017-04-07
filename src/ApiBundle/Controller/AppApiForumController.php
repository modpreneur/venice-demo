<?php
/**
 * Created by PhpStorm.
 * User: mmate
 * Date: 17.08.2015
 * Time: 14:34
 */

namespace AppBundle\Controller\AppApi;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use ApiBundle\Filters\ForumPostDetailFilter;
use ApiBundle\Filters\ForumPostsFilter;
use AppBundle\Entity\User;
use ApiBundle\Api;
//use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/forum")
 *
 * Class AppApiForumController
 * @package ApiBundle\Controller
 */
class AppApiForumController extends FOSRestController
{
    use Api;

    /**
     * Forum API flow
     * ===============
     *
     * ## 1) Get all forum categories:
     *
     *  GET /api/forum/categories
     *
     *
     * ## 2) Get all posts from category
     *
     *  GET /api/forum/posts/{category}
     *
     *
     * ## 3) Get post comments
     *
     *  GET /api/forum/post-comments/{postId}
     *
     * ApiDoc(
     *    resource=true
     * )
     * @Get("")
     */
    public function docAction()
    {
        return new JsonResponse($this->notOkResponse("Welcome to the documentation!"));
    }

    /**
     * Get forum categories
     *
     * Everything ok
     * ==============
     *
     *       {
     *           "status": "ok",
     *           "data": [
     *           {
     *               "id": "1",
     *               "countOfDiscussions": "4",
     *               "countOfComments": "8",
     *               "name": "General",
     *               "description": "General discussions",
     *               "lastEdit": "2015-07-10 09:21:48"
     *           }
     *           ]
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Get all categories from forum",
     * )
     *
     * @Get("/categories", name="api_get_forum_categories")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getCategories(Request $request)
    {
        $arrayizer = $this->get('flofit.services.arrayizer');
        $forumService = $this->get('flofit.prod_env_forum_connector');
        $categories = $forumService->getCategories($this->getUser());
        $output = [];

        foreach ($categories as $category) {
            $output[] = $arrayizer->arrayize($category);
        }

        return new JsonResponse($this->okResponse($output));
    }

    /**
     * DEPRECATED
     * ==========
     *
     * Get all posts from forum(unsorted by categories)
     *
     * Everything ok
     * ==============
     *       {
     *           "status": "ok",
     *           "data": [
     *           {
     *               "views": "2",
     *               "comments": "3",
     *               "id": "4",
     *               "name": "this is testing discussion",
     *               "body": "The body!"
     *               "categoryId": "1",
     *               "lastDate": "2015-08-21 09:43:12",
     *               "author": "admin"
     *           },
     *           {
     *               "views": "4",
     *               "comments": "0",
     *               "id": "3",
     *               "name": "how to?",
     *               "body": "The body!"
     *               "categoryId": "1",
     *               "lastDate": "2015-08-19 07:33:24",
     *               "author": "admin"
     *           },
     *           {
     *               "views": "10",
     *               "comments": "3",
     *               "id": "2",
     *               "name": "some title",
     *               "body": "The body!"
     *               "categoryId": "1",
     *               "lastDate": "2015-08-18 10:00:53",
     *               "author": "admin"
     *           },
     *           {
     *               "views": "39",
     *               "comments": "2",
     *               "id": "1",
     *               "name": "BAM! You’ve got a sweet forum",
     *               "body": "The body!"
     *               "categoryId": "1",
     *               "lastDate": "2015-07-31 16:26:54",
     *               "author": "System"
     *           }
     *           ]
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Get forum latest posts",
     * )
     *
     * @Get("/latest-posts", name="api_get_forum_latest_posts")
     * @param Request $request
     * @return JsonResponse
     */
    public function getLatestPostsAction(Request $request)
    {
        $forumService = $this->get('flofit.prod_env_forum_connector');
        $arrayizer = $this->get('flofit.services.arrayizer');
        $posts = $forumService->getLatestForumPosts($this->getUser());

        $filter = new ForumPostsFilter();

        $data = $filter->filter($posts, $arrayizer);
        return new JsonResponse($this->okResponse($data));
    }

    /**
     * Get all forum posts in given category
     *
     * Category: either category ID or name
     *
     * Everything ok
     * =============
     *
     *      {
     *       "status": "ok",
     *       "data": [
     *           {
     *               "views": "2",
     *               "comments": "0",
     *               "id": "5",
     *               "name": "Discussion without comments",
     *               "body": "The body!"
     *               "categoryId": "1",
     *               "lastDate": "2015-08-25 07:30:08",
     *               "author": "admin"
     *           },
     *           {
     *               "views": "6",
     *               "comments": "3",
     *               "id": "4",
     *               "name": "this is testing discussion",
     *               "body": "The body!"
     *               "categoryId": "1",
     *               "lastDate": "2015-08-21 09:43:12",
     *               "author": "admin"
     *           },
     *           {
     *               "views": "5",
     *               "comments": "0",
     *               "id": "3",
     *               "name": "how to?",
     *               "body": "The body!"
     *               "categoryId": "1",
     *               "lastDate": "2015-08-19 07:33:24",
     *               "author": "admin"
     *           },
     *           {
     *               "views": "10",
     *               "comments": "3",
     *               "id": "2",
     *               "name": "some title",
     *               "body": "The body!"
     *               "categoryId": "1",
     *               "lastDate": "2015-08-18 10:00:53",
     *               "author": "admin"
     *           },
     *           {
     *               "views": "39",
     *               "comments": "2",
     *               "id": "1",
     *               "name": "BAM! You’ve got a sweet forum",
     *               "body": "The body!"
     *               "categoryId": "1",
     *               "lastDate": "2015-07-31 16:26:54",
     *               "author": "System"
     *           }
     *       ]
     *       }
     *
     * No category found
     * =================
     *      {
     *          "status": "not ok",
     *          "message": "No category or posts found"
     *      }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Get forum latest posts",
     *  requirements={
     *      {"name"="category","dataType"="integer|string","description"="Id or name of the category"}
     *  },
     * )
     *
     * @Get("/posts/{category}", name="api_get_forum_category_posts")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getPostsByCategoryAction(Request $request, $category)
    {
        $forumService = $this->get('flofit.prod_env_forum_connector');
        $arrayizer = $this->get('flofit.services.arrayizer');
        $posts = $forumService->getForumPostsByCategory($this->getUser(), $category);

        $filter = new ForumPostsFilter();

        $data = $filter->filter($posts, $arrayizer);
        if (count($data) === 0) {
            return new JsonResponse($this->notOkResponse("No category or posts found"));
        }
        return new JsonResponse($this->okResponse($data));
    }

    /**
     * Get comments of given forum post
     *
     * Everything ok
     * =============
     *       {
     *           "status": "ok",
     *           "data": [
     *               {
     *                   "id": "6",
     *                   "author": "admin",
     *                   "body": "i think, yes!\r\n",
     *                   "lastEdit": "2015-08-21 09:43:00",
     *                   "postId": "4"
     *               },
     *               {
     *                   "id": "7",
     *                   "author": "admin",
     *                   "body": "maybe not",
     *                   "lastEdit": "2015-08-21 09:43:06",
     *                   "postId": "4"
     *               },
     *               {
     *                   "id": "8",
     *                   "author": "admin",
     *                   "body": "do not know\r\n",
     *                   "lastEdit": "2015-08-21 09:43:12",
     *                   "postId": "4"
     *               }
     *           ]
     *       }
     *
     * No post found
     * ==============
     *       {
     *           "status": "not ok",
     *           "message": "No post with id 422 found"
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Get comments of forum post",
     *  requirements={
     *      {"name"="postId", "dataType"="integer", "description"="Id of the post"}
     * }
     * )
     *
     * @Get("/post-comments/{postId}", name="api_get_forum_post_comments")
     * @param Request $request
     *
     *  @return JsonResponse
     */
    public function getPostComments(Request $request, $postId)
    {
        $arrayizer = $this->get('flofit.services.arrayizer');
        $filter = new ForumPostDetailFilter();

        $forumService = $this->get('flofit.prod_env_forum_connector');
        $comments = $forumService->getForumPostDetail($this->getUser(), $postId);

        if (is_null($comments)) {
            return new JsonResponse($this->notOkResponse('No post with id ' . $postId . ' found'));
        }
        $data = $filter->filter([$comments], $arrayizer);

        return new JsonResponse($this->okResponse($data));
    }

    /**
     * Everything ok
     * ==============
     *
     *       {
     *           "status": "ok",
     *           "data": [
     *           {
     *               "username": "test",
     *               "name": "Test LOL Testovic",
     *               "avatar": "http://localhost/FlofitVenice/web/media/cache/profile_picture/rc/U30y7E4R/images/profile-photo/1/55e2c9adf2d4c.jpeg"
     *           },
     *           {
     *               "username": "admin",
     *               "name": "Admin Admin",
     *               "avatar": "http://localhost/FlofitVenice/web/media/cache/profile_picture/rc/U30y7E4R/images/profile-photo/1/55e2c9adf2d4c.jpeg"
     *           },
     *           {
     *               "username": "iosuser",
     *               "name": "test testovic",
     *               "avatar": "http://localhost/FlofitVenice/web/media/cache/profile_picture/rc/U30y7E4R/images/profile-photo/1/55e2c9adf2d4c.jpeg"
     *           },
     *           {
     *               "username": "martin",
     *               "name": "Martin Matejka",
     *               "avatar": "http://localhost/FlofitVenice/web/media/cache/profile_picture/rc/U30y7E4R/images/profile-photo/1/55e2c9adf2d4c.jpeg"
     *           },
     *           {
     *               "username": "test2",
     *               "name": "Test LOL Testovic",
     *               "avatar": "http://localhost/FlofitVenice/web/images/site/default-profile-photo.png"
     *           }
     *           ]
     *       }
     *
     * ApiDoc(
     *      resource=false,
     *      description="Get all users from the forum"
     * )
     *
     * @Get("/users", name="api_get_forum_all_users")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAllUsersAction(Request $request)
    {
        $forumService = $this->get('flofit.services.arrayizer');

        $users = $forumService->getAllUsers($this->getUser());

        $output = $this->getUsersOutput($users, $request);

        return new JsonResponse($this->okResponse($output));
    }

    /**
     * Search users.
     *
     * Everything ok
     * ==============
     *       {
     *           "status": "ok",
     *           "data": [
     *           {
     *               "username": "james",
     *               "name": "James Smiths",
     *               "avatar": "http://localhost/FlofitVenice/web/media/cache/resolve/profile_picture/rc/0Vh4oXDy/images/profile-photo/3/0/55de53c39a1e5.jpeg?filters%5Bcrop%5D%5Bstart%5D%5B0%5D=161&filters%5Bcrop%5D%5Bstart%5D%5B1%5D=235&filters%5Bcrop%5D%5Bsize%5D%5B0%5D=264&filters%5Bcrop%5D%5Bsize%5D%5B1%5D=264"
     *           },
     *           {
     *               "username": "martin",
     *               "name": "Martin Matejka",
     *               "avatar": "http://localhost/FlofitVenice/web/media/cache/resolve/profile_picture/rc/gWFhxRWm/images/profile-photo/1/4/8f3712d95c00a.jpeg?filters%5Bcrop%5D%5Bstart%5D%5B0%5D=712&filters%5Bcrop%5D%5Bstart%5D%5B1%5D=524&filters%5Bcrop%5D%5Bsize%5D%5B0%5D=2008&filters%5Bcrop%5D%5Bsize%5D%5B1%5D=2008"
     *               },
     *           {
     *               "username": "test2",
     *               "name": "Test LOL Testovicova",
     *               "avatar": "http://localhost/FlofitVenice/web/media/cache/resolve/profile_picture/rc/q1WTCDIs/images/profile-photo/2/0/55c89604ecad6.jpeg?filters%5Bcrop%5D%5Bstart%5D%5B0%5D=202&filters%5Bcrop%5D%5Bstart%5D%5B1%5D=22&filters%5Bcrop%5D%5Bsize%5D%5B0%5D=576&filters%5Bcrop%5D%5Bsize%5D%5B1%5D=576"
     *               },
     *           {
     *               "username": "test",
     *               "name": "Yuri Sergeyevitch Tiestov",
     *               "avatar": "http://localhost/FlofitVenice/web/media/cache/resolve/profile_picture/rc/VJVWjM2i/images/profile-photo/1/55e412e8d0964.png?filters%5Bcrop%5D%5Bstart%5D%5B0%5D=15&filters%5Bcrop%5D%5Bstart%5D%5B1%5D=15&filters%5Bcrop%5D%5Bsize%5D%5B0%5D=122&filters%5Bcrop%5D%5Bsize%5D%5B1%5D=122"
     *           },
     *           {
     *               "username": "iosuser",
     *               "name": "test testovic",
     *               "avatar": "http://localhost/FlofitVenice/web/media/cache/resolve/profile_picture/rc/J01REtAO/images/profile-photo/1/3/ab691678f5187.jpeg?filters%5Bcrop%5D%5Bstart%5D%5B0%5D=10&filters%5Bcrop%5D%5Bstart%5D%5B1%5D=20&filters%5Bcrop%5D%5Bsize%5D%5B0%5D=1000&filters%5Bcrop%5D%5Bsize%5D%5B1%5D=1000"
     *               }
     *           ]
     *       }
     *
     * No user found
     * =============
     *       {
     *           "status": "ok",
     *           "data": []
     *       }
     *
     * No search parameter found
     * ===========================
     *       {
     *           "status": "not ok",
     *           "message": "No search parameter found"
     *       }
     *
     * ApiDoc(
     *     resource=false,
     *     description="Search users in the forum. Get 10 most relevant users.",
     *     requirements={
     *      {"name"="search", "dataType"="string", "description"="part of name to find"}
     * },
     * )
     *
     * @Get("/users/search/{search}", name="api_search_forum_users")
     */
    public function searchAction(Request $request, $search)
    {
        if (is_null($search)) {
            return new JsonResponse($this->notOkResponse('No search parameter found'));
        }
        /** @var User[] $users */
        $users = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:User')
            ->getUsersByPartOfName($search);
        ;

        foreach ($users as $key => $user) {
            if ($user->getUsername() == $this->getUser()->getUsername()) {
                unset($users[$key]);
            }
        }

        return new JsonResponse($this->okResponse($this->getUsersOutput($users, $request)));
    }

    /**
     * @param User[] $users
     * @param Request $request
     *
     * @return array
     */
    protected function getUsersOutput($users, $request)
    {
        $imageUrlGenerator = $this->get('flofit.services.profile_photo_url_generator');
        $output = [];

        /** @var User $user */
        foreach ($users as $user) {
            $userArray['username'] = $user->getUsername();
            $userArray['name'] = $user->getFullName();
            $profilePhoto = $user->getProfilePhoto();

            if (is_null($profilePhoto)) {
                $userArray['avatar'] = $request->getUriForPath('/images/site/default-profile-photo.png');
            } else {
                $userArray['avatar'] = $imageUrlGenerator->generateUrlToCroppedPhoto($user->getProfilePhoto()); // null?
            }

            $output[] = $userArray;
        }

        //sort the output alphabetically by name
        $keys = [];
        foreach ($output as $key => $row) {
            $keys[$key] = $row['name'];
        }

        array_multisort($keys, SORT_ASC, $output);

        return $output;
    }
}