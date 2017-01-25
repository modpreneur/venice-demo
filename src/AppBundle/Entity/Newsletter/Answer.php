<?php

namespace AppBundle\Entity\Newsletter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="newsletter_optimization_answer")
 * @ORM\Entity()
 */
class Answer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", nullable=true)
     */
    private $answer;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255, nullable=true)
     */
    private $tag;

    /**
     * @var integer
     *
     * @ORM\Column(name="list_id", type="integer")
     */
    private $listId;

    /**
     * @var ArrayCollection<Product>
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Newsletter\UserAnswer", mappedBy="answer")
     */
    private $userAnswers;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Newsletter\Question", inversedBy="answers")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    public function __construct()
    {
        $this->userAnswers = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }


    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }


    /**
     * Set answer
     *
     * @param boolean $answer
     * @return Answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }


    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }


    /**
     * Set tag
     *
     * @param string $tag
     * @return Answer
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }


    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }


    /**
     * Set listId
     *
     * @param integer $listId
     * @return Answer
     */
    public function setListId($listId)
    {
        $this->listId = $listId;

        return $this;
    }


    /**
     * Get listId
     *
     * @return integer
     */
    public function getListId()
    {
        return $this->listId;
    }
}
