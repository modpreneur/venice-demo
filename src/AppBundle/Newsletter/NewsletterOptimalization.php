<?php

namespace AppBundle\Newsletter;

use AppBundle\Entity\Newsletter\Answer;
use AppBundle\Entity\Newsletter\Question;
use AppBundle\Entity\Newsletter\UserAnswer;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Class NewsletterOptimalization
 * @package AppBundle\Newsletter
 */
class NewsletterOptimalization
{
    /** @var ContainerInterface  */
    private $serviceContainer;
    private $questions;

    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
        $this->questions = null;
    }


    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    private function getEntityManager()
    {
        return $this->serviceContainer->get('doctrine')->getManager();
    }


    /**
     * @param $page
     *
     * @return array|\GeneralBackend\NewsletterOptimalizationBundle\Entity\Question[]|null
     * @throws \UnexpectedValueException
     */
    private function loadQuestions($page)
    {
        if (null === $this->questions) {
            $this->questions = $this
                ->getEntityManager()
                ->getRepository(
                    Question::class
                )
                ->findBy(['page' => $page]);//,array('order' => 'ASC'));
        }

        return $this->questions;
    }


    /**
     * @return null
     */
    public function getQuestions()
    {
        return $this->questions;
    }


    /**
     * @param User $user
     *
     * @return array|\GeneralBackend\NewsletterOptimalizationBundle\Entity\UserAnswer[]
     */
    private function getUserAnswers(User $user)
    {
        return $this
            ->getEntityManager()
            ->getRepository(UserAnswer::class)
            ->findBy(
                ['user'      => $user],
                ['timestamp' => 'DESC']
            );
    }


    /**
     * @param User $user
     * @param Answer $answer
     *
     * @return array|UserAnswer[]
     */
    public function getOldAnswers(User $user, Answer $answer)
    {

        return $this->getEntityManager()->getRepository(UserAnswer::class)
            ->findBy(array('user' => $user, 'answer' => $answer), array('timestamp' => 'DESC'));
    }


    /**
     * @param $page
     * @param User $user
     *
     * @return null|\Symfony\Component\Form\FormInterface
     */
    public function createForm($page, User $user)
    {
        $questions   = $this->loadQuestions($page);
        $userAnswers = $this->getUserAnswers($user);

        if (count($questions) === 0) {
            return null;
        }

        $form = $this->serviceContainer->get('form.factory')
            ->createNamedBuilder('newsletter-optimization'.$page,FormType::class, null, array('attr' =>array('class' => 'newsletter-optimization')));

        $url = $this->serviceContainer->get('router')->generate('question_form_solver', array('page' => $questions[0]->getPage()));
        $form->setAction($url);

        $data = array();
        foreach ($userAnswers as $answer1) {
            $data[] = $answer1->getAnswer()->getId();
        }

        /**
         * @var Question $quest
         */
        $sumQuestion = count($questions);
        for ($i = 0;$i < $sumQuestion;$i++) {
            $choices = array();
            $data = array();
            $quest = $questions[$i];

            foreach ($quest->getAnswers() as $answer) {
                /** @var Answer $answer */
                $choices[$answer->getId()] = $answer->getAnswer();

                $lastAnswer = $this->getEntityManager()->getRepository(
                    UserAnswer::class
                )
                    ->getLastAnswer($answer, $user);

                if ($lastAnswer && $lastAnswer->isClicked()) {
                    $data[] = $lastAnswer->getAnswer()->getId();
                }
            }

            $multiple = $quest->getMultiple();
            if ($multiple === false && count($data) === 0) {
                $data = null;
            } elseif ($multiple === false) {
                $data = $data[0];
            }

            $form->add('question' . (string)$i,
                ChoiceType::class, array(
                    'multiple'   => $multiple,
                    'expanded'   => true,
                    'required'   => false,
                    'empty_data' => null,
                    'label'      => $quest->getQuestion(),
                    'choices'    => $choices,
                    'data'       => $data
                )
            );
        }

        $form = $form->getForm();

        return $form;
    }

    public function maropostSync($user, $userMaropost)
    {
        $entityManager = $this->getEntityManager();
        if ($userMaropost && isset($userMaropost['tags'])) {
            $questionSolved = array();
            foreach ($userMaropost['tags'] as $tag) {
                /** @var Answer $answer */
                $answer = $entityManager->getRepository('ModernEntrepreneurNewsletterOptimalizationBundle:Answer')
                    ->findOneBy(array('tag' => $tag['name']));
                if (null === $answer) {
                    continue;
                } else {
                    $question = $answer->getQuestion();

                    if ((!$question->getMultiple() && !in_array($question->getId(), $questionSolved))
                        || $question->getMultiple()
                    ) {
                        $userAnswer = new UserAnswer($user, $answer, $question);

                        $entityManager->persist($userAnswer);
                        $entityManager->flush();
                    }
                }
            }
        }
    }
}
