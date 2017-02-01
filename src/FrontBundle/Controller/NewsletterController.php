<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\Newsletter\Answer;
use AppBundle\Entity\Newsletter\Question;
use AppBundle\Entity\Newsletter\UserAnswer;
use AppBundle\Helpers\FlashMessages;
use AppBundle\Newsletter\NewsletterOptimalization;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class NewsletterController
 * @package FrontBundle\Controller
 */
class NewsletterController extends Controller
{
    /**
     * @Route("/form-solver/{page}", name="question_form_solver")
     * @param Request $request
     * @param         $page
     * @return Response
     * @throws \LogicException
     */
    public function editActionWithQuestionsAction(Request $request, $page)
    {
        $optimalizationService = new NewsletterOptimalization($this->container);

        $form = $optimalizationService->createForm($page, $this->getUser());
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

        $tags  = ['add_tags'=> [],'remove_tags'=> []];
        $lists = ['subscribe'=> [],'unsubscribe'=> []];

        if ($form->isValid()) {
            $i = 0;
            foreach ($optimalizationService->getQuestions() as $question) {
                /** @var Question $question */

                $userAnsweredInForm = $form->get('question' . (string)$i)->getNormData();

                foreach ($question->getAnswers() as $answer) {
                    /** @var Answer $answer */
                    /** @var UserAnswer $lastAnswer */
                    $lastAnswer = $entityManager->getRepository(UserAnswer::class)
                        ->getLastAnswer($answer, $this->getUser());

                    if ((
                            is_array($userAnsweredInForm)
                            && in_array($answer->getId(), $userAnsweredInForm, false)
                        )
                        || $userAnsweredInForm === $answer->getId()
                    ) {
                        if (null === $lastAnswer || ($lastAnswer && $lastAnswer->isClicked() === false)) {
                            $userAnswer = new UserAnswer();
                            $userAnswer->setQuestion($question);
                            $userAnswer->setAnswer($answer);
                            $userAnswer->setUser($this->getUser());

                            $entityManager->persist($userAnswer);

                            if ($answer->getListId() !== 0) {
                                $lists['subscribe'][] = $answer->getListId();
                            }

                            if ($answer->getTag() !== '') {
                                $tags['add_tags'][] = $answer->getTag();
                            }
                        }
                    } elseif (null !== $lastAnswer && $lastAnswer->isClicked()) {
                        $userAnswer = new UserAnswer();
                        $userAnswer->setQuestion($question);
                        $userAnswer->setAnswer($answer);
                        $userAnswer->setUser($this->getUser());
                        $userAnswer->setClicked(false);

                        $entityManager->persist($userAnswer);

                        if ($answer->getListId() !== 0) {
                            $lists['unsubscribe'][] = $answer->getListId();
                        }

                        if ($answer->getTag() !== '') {
                            $tags['remove_tags'][] = $answer->getTag();
                        }
                    }
                }

                $i++;
            }

            $entityManager->flush();

            $maropostConnector = $this->get('flofit.services.maropost_connector');

            if (count($tags['add_tags']) !== 0) {
                $maropostConnector->addTags($this->getUser(), $tags['add_tags']);
            }

            if (count($tags['remove_tags']) !== 0) {
                $maropostConnector->removeTags($this->getUser(), $tags['remove_tags']);
            }

            foreach ($lists['subscribe'] as $listId) {
                $maropostConnector->subscribeLists($this->getUser(), $listId);
            }

            foreach ($lists['unsubscribe'] as $listId) {
                $maropostConnector->unsubscribeLists($this->getUser(), $listId);
            }

            $this->addFlash('success', 'Successfully changed.');
//            return new JsonResponse(array('ok' => true));
        }

        return $this->redirectToRoute($optimalizationService->getQuestions()[0]->getSuccessfulRedirect());
    }
}
