<?php
/**
 * Created by PhpStorm.
 * User: mmate
 * Date: 18.08.2015
 * Time: 9:22
 */

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
//use ApiBundle\Filters\ForumPostsFilter;
use AppBundle\Entity\User;
use AppBundle\Entity\Vanilla\Conversation;
use AppBundle\Entity\Vanilla\Message;
use ApiBundle\Api;
//use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppApiMessagesController
 * @package ApiBundle\Controller\AppApi
 */
class AppApiMessagesController extends FOSRestController
{
    use Api;

    /**
     * Get all conversations
     *
     * Everything ok
     * ==============
     *
     *      {
     *          "status": "ok",
     *          "data": [
     *          {
     *              "date": "2015-08-19 08:42:10",
     *              "id": "2",
     *              "participants": [
     *              {
     *                  "name": "Admin Admin",
     *                  "avatar": ""
     *              }
     *              ],
     *              "newMessagesCount": 0,
     *              "lastMessageBody": "test"
     *          },
     *          {
     *              "date": "2015-07-22 11:03:32",
     *              "id": "4",
     *              "participants": [
     *              {
     *                  "name": "System",
     *                  "avatar": ""
     *              }
     *              ],
     *              "newMessagesCount": 0,
     *              "lastMessageBody": "meeeh"
     *          },
     *          {
     *              "date": "2015-07-22 10:57:59",
     *              "id": "1",
     *              "participants": [
     *              {
     *                  "name": "Admin Admin",
     *                  "avatar": ""
     *              }
     *              ],
     *              "newMessagesCount": 0,
     *              "lastMessageBody": "yo!"
     *              }
     *          ]
     *      }
     *
     * ApiDoc(
     *  resource=true,
     *  description="Get conversations",
     * )
     *
     * @Get("/api/conversations", name="api_get_conversations")
     * @param Request $request
     * @return JsonResponse
     */
    public function getConversationsAction(Request $request)
    {
        $forumService = $this->get('flofit.prod_env_forum_connector');
        $imageUrlGenerator = $this->get('flofit.services.profile_photo_url_generator');

        /** @var Conversation[] $conversations */
        $conversations = $forumService->getConversations($this->getUser());

        $arrayizer = $this->get("flofit.services.arrayizer");
        $arrayizer->setWithout([
            'participants',
            'countNewMessages',
            'countReadedMessages',
            'lastBody',
            'subject',
            'conversationId'
        ]);

        $conversationsArray = [];

        foreach ($conversations as $conversation) {
            $conversationObject = $arrayizer->arrayize($conversation);
            $conversationObject['id'] = $conversation->getConversationId();
            $conversationObject['participants'] = [];
            $conversationObject['newMessagesCount'] = $conversation->getCountNewMessages();
            $conversationObject['lastMessageBody'] = $conversation->getLastBody();

            foreach ($conversation->getParticipants() as $obj) {
                if ($obj instanceof User) {
                    if ($obj->getProfilePhoto()) {
                        $avatar = $imageUrlGenerator->generateUrlToCroppedPhoto($obj->getProfilePhoto());
                    } else {
                        $avatar = $request->getUriForPath('/images/site/default-profile-photo.png');
                    }
                    $conversationObject['participants'][] = [
                        'name' => $obj->getFullName(),
                        'username' => $obj->getUsername(),
                        'avatar' => $avatar
                    ];
                } else {
                    $conversationObject['participants'][] = ['name' => $obj, 'username' => $obj,  'avatar' => ''];
                }
            }

            $conversationsArray[] = $conversationObject;
        }

        return new JsonResponse($this->okResponse($conversationsArray));
    }


    /**
     * Get messages from conversation
     *
     * Everything ok
     * ==============
     *
     *      {
     *          "status": "ok",
     *          "data": [
     *          {
     *              "conversationId": "2",
     *              "body": "sup?",
     *              "id": "2",
     *              "author": "Admin Admin",
     *              "authorUsername": "admin",
     *              "date": "2015-07-22 10:58:44"
     *          },
     *          {
     *              "conversationId": "2",
     *              "body": "tak jo",
     *              "id": "6",
     *              "author": "Admin Admin",
     *              "authorUsername": "admin",
     *              "date": "2015-07-22 12:39:56"
     *          },
     *          {
     *              "conversationId": "2",
     *              "body": "tak jo",
     *              "id": "7",
     *              "author": "pepaaak novak",
     *              "authorUsername": "pepaak",
     *              "date": "2015-07-22 12:41:35"
     *          },
     *          {
     *              "conversationId": "2",
     *              "body": "asfasf",
     *              "id": "8",
     *              "author": "Admin Admin",
     *              "authorUsername": "admin",
     *              "date": "2015-07-22 12:41:59"
     *          }
     *          ]
     *      }
     *
     * No conversation found
     * ======================
     *       {
     *           "status": "not ok",
     *           "message": "No conversation found"
     *       }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Get messages by conversation id",
     *  requirements={
     *      {"name"="id","dataType"="int","required"=true,"description"="conversation id"},
     *      {"name"="limit","dataType"="int","required"=false,"description"="count of last messages requested, all is default"},
     *  }
     * )
     *
     * @Get("api/conversations/{id}/messages", name="api_get_messages")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getMessagesAction(Request $request, $id)
    {
        if (is_null($id) || !is_numeric($id)) {
            return new JsonResponse($this->notOkResponse('Conversation ID is missing'));
        }

        $limit = $request->get("limit");

        if (is_null($limit) || !is_numeric($limit) || $limit < 1) {
            $limit = false;
        }

        $forumService = $this->get('flofit.prod_env_forum_connector');

        $conversation = new Conversation($id, null, null, null, null, null, null);
        /** @var Message[] $messages */
        $messages = $forumService->getMessages($this->getUser(), $conversation);

        if (is_null($messages)) {
            return new JsonResponse($this->notOkResponse('No conversation found'));
        }

        if ($limit) {
            $messages = array_reverse($messages);
            $messages = array_slice($messages, 0, $limit);
        }

        $arrayizer = $this->get('flofit.services.arrayizer');
        $arrayizer->setWithout([
            'author',
            'dateInserted',
            'messageId'
        ]);


        $messagesArray = [];

        foreach ($messages as $message) {
            $messageObj = $arrayizer->arrayize($message);
            $messageObj['id'] = $message->getMessageId();
            $messageObj['author'] = $message->getAuthor()->getFullName();
            $messageObj['authorUsername'] = $message->getAuthor()->getUsername();
            $messageObj['date'] = $message->getDateInserted();
            $messagesArray[] = $messageObj;
        }

        return new JsonResponse($this->okResponse($messagesArray));
    }

    /**
     * Create new message
     *
     * Everything ok
     * ==============
     *
     * Redirect to /api/conversations/{id}
     *
     * Missing message body
     * =====================
     *       {
     *           "status": "not ok",
     *           "message": "Message body is missing"
     *       }
     *
     * Invalid participant
     * =====================
     *       {
     *           "status": "not ok",
     *           "message": "Invalid participants"
     *       }
     *
     * Wrong conversation id - it has to be an integer
     * ===============================================
     *       {
     *           "status": "not ok",
     *           "message": "Wrong conversation id"
     *       }
     *
     * Unknown error(check "data" for more information) - send response to developers
     * =============
     *      {
     *          "status": "not ok",
     *          "message": "Cannot send message",
     *          "data": info
     *      }
     *
     * ApiDoc(
     *  resource=false,
     *  description="Create new message",
     *  parameters={
     *      {"name"="participants", "dataType"="string", "required"=false, "description"="users involved in this conversation. required if you are creating a new conversation, non-required otherwise", "format" = "user1 OR user1,user2,user3,..."},
     *      {"name"="body", "dataType"="string", "required"=true, "description"="message body", "format"="this is the body of my post"}
     *  },
     *  requirements={
     *      {"name"="id", "dataType"="int|string", "description"="Create a new message to in existing conversation with id(int) or create a new conversation (id=new)"}
     *  }
     * )
     *
     * @Post("api/conversations/{id}/messages", name="api_new_message")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \LogicException
     */
    public function sendMessageAction(Request $request, $id)
    {
        $user = $this->getUser();
        $forumService = $this->get('flofit.prod_env_forum_connector');
        $messageBody = $request->get('body');
        $conversation = null;

        if (null === $messageBody || empty($messageBody)) {
            return new JsonResponse($this->notOkResponse('Message body is missing'));
        }

        if ($id === 'new') {
            //no conversation id
            //we want to create a new conversation
            $participants = $request->get('participants');

            if (!is_string($participants) || empty($participants)) {
                return new JsonResponse($this->notOkResponse('Invalid participants'));
            }

            //check, if the conversation already exists
            $conversation = $forumService->getExistingConversation($user, $participants);

            if (null === $conversation) {
                $response = $forumService->createConversation($this->getUser(), $participants, $messageBody);
                if ($response !== true) {
                    return new JsonResponse($this->notOkResponse('Can not create a new conversation', $response));
                }
                //because there is no know way to get last inserted conversation id from vanilla,
                // we have to find that conversation by ourselves
                $conversation = $forumService->getExistingConversation($user, $participants);

                /** @var Conversation $conversation */
                if ($conversation === null) {
                    return new JsonResponse($this->notOkResponse('Can not create a new conversation2'));
                }

                return $this->getMessagesAction($request, $conversation->getConversationId());
            }
        } elseif (is_numeric($id)) {
            //we have conversation id
            //so create a new message
            $conversation = new Conversation($id, null, null, null, null, null, null);
        } else {
            return new JsonResponse($this->notOkResponse('Wrong conversation id'));
        }

        $message = new Message(null, $conversation->getConversationId(), null, null, $messageBody);
        $response = $forumService->sendMessage($this->getUser(), $message);

        if ($response !== true) {
            return new JsonResponse($this->notOkResponse('Cannot send message', $response));
        }

        return $this->getMessagesAction($request, $conversation->getConversationId());
    }


    /**
     * Delete conversation
     *
     * @Post("api/conversations/delete", name="api_delete_conversation")
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteConversationAction(Request $request)
    {
        $user = $this->getUser();
        $forumService = $this->get('flofit.prod_env_forum_connector');

        $participants = $request->get('participants');

        if (!is_string($participants) || empty($participants)) {
            return new JsonResponse($this->notOkResponse('Invalid participants'));
        }

        $conversation = $forumService->getExistingConversation($user, $participants);

        if (is_null($conversation)) {
            return new JsonResponse($this->notOkResponse('Conversation was not found'));
        }

        $response = $forumService->deleteConversation($user, $conversation);

        $response = json_decode($response);

        if (!is_null($response) && !strlen($response->ErrorMessages)) {
            return new JsonResponse($this->okResponse($response->InformMessages[0]->Message));
        } else {
            return new JsonResponse($this->notOkResponse('Cannot delete conversation'));
        }
    }
}