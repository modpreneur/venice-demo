<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Content\VideoContent;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserPlayedVideos
 *
 * @ORM\Table(name="user_played_videos")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\UserPlayedVideosRepository")
 */
class UserPlayedVideos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="replayHistory")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var VideoContent
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Content\VideoContent")
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id")
     */
    protected $video;

    /**
     * @var \DateTime playedDate
     *
     * @ORM\Column(name="played", type="datetime")
     */
    protected $playedDate;

    /**
     * UserPlayedVideos constructor.
     */
    public function __construct()
    {
        $this->playedDate = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return VideoContent
     */
    public function getVideo(): Content\VideoContent
    {
        return $this->video;
    }

    /**
     * @param VideoContent $video
     *
     * @return $this
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPlayedDate(): \DateTime
    {
        return $this->playedDate;
    }

    /**
     * @param \DateTime $playedDate
     *
     * @return $this
     */
    public function setPlayedDate(\DateTime $playedDate = null)
    {
        if (null !== $playedDate) {
            $this->playedDate = $playedDate;
        }

        return $this;
    }
}