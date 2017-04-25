<?php
/**
 * Created by PhpThunderStorm.
 * User: ondrejbohac
 * Date: 27.10.15
 * Time: 14:03
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * AfterPurchaseSurvey
 *
 * @property string name
 */
class AfterPurchaseSurvey
{
    const MALE = [
      'Male' => 1,
      'Female' => 0
    ];

    const AGES_LIST = [
         '<20' => 1,
         '21-30' => 2,
         '31-40' => 3,
         '41-50' => 4,
         '51-60' => 5,
         '>60' => 6,
    ];

    const BIGGEST_OBSTACLE_LIST = [
         'Lack of consistency with your workouts' => 1,
         'Poor diet habits' => 2,
         'Confused as to what you should do' => 3,
         'Overall lack of commitment' => 4,
         'Lack of time' => 5,
         'Lack of results' => 6,
    ];

    const MAIN_GOAL_LIST = [
         'Lose weight' => 1,
         'Lose weight AND tone (or build) muscle' => 2,
         'Tone (or build) muscle ONLY (I don\'t need to lose weight)' => 3
    ];

    const HEALTH_HISTORY_LIST = [
         'Do you have/are you concerned about Diabetes?' => 1,
         "Do you have/are you concerned about Alzheimer's?" => 2,
         'Do you have/are you concerned about Heart Disease?' => 3,
         'Do you have/are you concerned about Joint Pain?' => 4,
         'Do you have/are you concerned about Joint Injuries?' => 5,
         'Do you have/are you concerned about Hair Loss?' => 6,
         'Do you have/are you concerned about Vision Problems?' => 7,
    ];

    const WHY_BETTER_SHAPE = [
         'To look good' => 1,
         'To be healthy' => 2
    ];

    const FIELDS = [
        'male',
        'old',
        'biggestObstacle',
        'mainGoal',
        'healthHistory',
        'betterShape',
    ];

    /**
     * @var integer
     *
     */
    protected $id;

    /**
     *
     */
    protected $user;

    /**
     * @var boolean
     */
    protected $male;

    /**
     * @var
     */
    protected $old;

    /**
     * @var string
     */
    protected $biggestObstacle;

    /**
     * @var string
     */
    protected $mainGoal;

    /**
     * @var string
     */
    protected $timestamp;

    /**
     * @var string
     *
     */
    protected $betterShape;

    /**
     * @var string
     */
    protected $name;

    /**
     * AfterPurchaseSurvey constructor.
     */
    public function __construct()
    {
        $this->timestamp = new \DateTime();
        $this->otherHealthIssues = '';
        $this->promptedSomething = '';
        $this->healthHistory = [];
        $this->betterShape = '';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMale()
    {
        return $this->male;
    }

    /**
     * @param $male
     * @return $this
     */
    public function setMale($male)
    {
        $this->male = $male;

        return $this;
    }

    /**
     * @return int
     */
    public function getOld()
    {
        return $this->old;
    }

    /**
     * @param mixed $old
     * @return $this
     */
    public function setOld($old)
    {
        $this->old = $old;

        return $this;
    }

    /**
     * @return string
     */
    public function getBiggestObstacle()
    {
        return $this->biggestObstacle;
    }

    /**
     * @param $biggestObstacle
     * @return $this
     */
    public function setBiggestObstacle($biggestObstacle)
    {
        $this->biggestObstacle = $biggestObstacle;

        return $this;
    }

    /**
     * @return string
     */
    public function getMainGoal()
    {
        return $this->mainGoal;
    }

    /**
     * @param $mainGoal
     * @return $this
     */
    public function setMainGoal($mainGoal)
    {
        $this->mainGoal = $mainGoal;

        return $this;
    }

    /**
     * @return string
     */
    public function getHealthHistory()
    {
        return $this->healthHistory;
    }

    /**
     * @param $healthHistory
     * @return $this
     */
    public function setHealthHistory($healthHistory)
    {
        $this->healthHistory = $healthHistory;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherHealthIssues()
    {
        return $this->otherHealthIssues;
    }

    /**
     * @param string $otherHealthIssues
     * @return $this
     */
    public function setOtherHealthIssues($otherHealthIssues)
    {
        $this->otherHealthIssues = $otherHealthIssues;

        return $this;
    }

    /**
     * @return string
     */
    public function getPromptedSomething()
    {
        return $this->promptedSomething;
    }

    /**
     * @param string $promptedSomething
     * @return $this
     */
    public function setPromptedSomething($promptedSomething)
    {
        $this->promptedSomething = $promptedSomething;

        return $this;
    }

    /**
     * @return ArrayCollection<HealthHistory>
     */
    public function getSubProducts()
    {
        return $this->healthHistory;
    }

    /**
     * @return mixed
     */
    public function getBetterShape()
    {
        return $this->betterShape;
    }

    /**
     * @param string $betterShape
     * @return $this
     */
    public function setBetterShape($betterShape)
    {
        $this->betterShape = $betterShape;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
}
