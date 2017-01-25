<?php

namespace AppBundle\Entity\Newsletter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Question
 *
 * @ORM\Table(name="newsletter_optimalization_question")
 * @ORM\Entity()
 */
class Question
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
     * @var ArrayCollection<Product>
     * @ORM\OneToMany(targetEntity="UserAnswer", mappedBy="question")
     *
     */
    private $userAnswers;

    /**
     * @var ArrayCollection<Product>
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Newsletter\Answer", mappedBy="question")
     */
    private $answers;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=500)
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="successful_redirect", type="string", length=500)
     */
    private $successfulRedirect;

    /**
     * @var boolean
     *
     * @ORM\Column(name="multiple", type="boolean")
     */
    private $multiple;

    /**
     * @var integer
     *
     * @ORM\Column(name="page", type="integer")
     */
    private $page;

    /**
     * @var integer
     *
     * @ORM\Column(name="Order", type="integer")
     */
    private $order;

    public function __construct()
    {
        $this->userAnswers = new ArrayCollection();
        $this->answers =  new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param ArrayCollection $answers
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }



    /**
     * Set question
     *
     * @param string $question
     * @return Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }



    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set multiple
     *
     * @param boolean $multiple
     * @return Question
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuccessfulRedirect()
    {
        return $this->successfulRedirect;
    }

    /**
     * @param string $successfulRedirect
     */
    public function setSuccessfulRedirect($successfulRedirect)
    {
        $this->successfulRedirect = $successfulRedirect;
    }

    /**
     * Get multiple
     *
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Set page
     *
     * @param integer $page
     * @return Question
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }
}
