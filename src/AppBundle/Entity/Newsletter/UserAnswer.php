<?php

namespace AppBundle\Entity\Newsletter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;


/**
 * UserAnswer
 *
 * @ORM\Table(name="newsletter_optimalization_user_answer")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Newsletter\UserAnswerRepository")
 */
class UserAnswer
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
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Newsletter\Question", inversedBy="userAnswers")
     * @JoinColumn(name="question_id", referencedColumnName="id")
    */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Newsletter\Answer", inversedBy="userAnswers")
     * @JoinColumn(name="answer_id", referencedColumnName="id")
     *
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="userAnswers")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var boolean
     * @ORM\Column(name="clicked", type="boolean")
     */
    private $clicked;


    /**
     * UserAnswer constructor.
     *
     * @param null $user
     * @param null $answer
     * @param null $question
     */
    public function __construct($user = null, $answer = null, $question = null)
    {
        $this->timestamp = new \DateTime();
        $this->clicked = true;
        $this->user = $user;
        $this->answer = $answer;
        $this->question = $question;
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;



    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }



    /**
     * @return mixed
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
     * @return Answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
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
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return UserAnswer
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return boolean
     */
    public function isClicked()
    {
        return $this->clicked;
    }

    /**
     * @param boolean $clicked
     */
    public function setClicked($clicked)
    {
        $this->clicked = $clicked;
    }
}
