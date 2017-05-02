<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Content\VideoContent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Trinity\NotificationBundle\Annotations as N;
use Venice\AppBundle\Entity\Product\Product;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\UserRepository")
 * Class User
 *
 * @N\Source(columns="necktieId, username, email, firstName, lastName, avatar, locked, phoneNumber, website")
 * Users cannot be created on client so there is no need to use POST
 * @N\Methods(types={"put", "delete"})
 */
class User extends \Venice\AppBundle\Entity\User implements \Trinity\Component\Core\Interfaces\UserInterface
{
    const PREFERRED_IMPERIAL = 'imperial';
    const PREFERRED_METRIC = 'metric';

    const DEFAULT_PREFERRED_METRICS = self::PREFERRED_IMPERIAL;

    const DIR_PICTURES = 'images/profile/';
    const PROFILE_PICTURE = 'empty_profile.png';
    const PROFILE_AVATAR = 'empty_profile.png';


    /**
     * @var ProfilePhoto
     *
     * @ORM\OneToOne(targetEntity="ProfilePhoto", inversedBy="user", cascade={"persist","merge","remove"},
     *     orphanRemoval=true)
     * @ORM\JoinColumn(name="profile_photo_id", referencedColumnName="id", nullable=true)
     */
    protected $profilePhoto;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $UserChild;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    protected $maropostSynced;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_of_birth_v", type="date", nullable=true, options={"default" = null})
     */
    public $dateOfBirth;


    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $location;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $youtubeLink;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    public $snapchatNickname;

    /**
     * @var \DateTime
     */
    private $lastPasswordChange;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true, name="flomersion_start")
     */
    protected $flomersionStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true, name="flomersion_end")
     */
    protected $flomersionEnd;

    /**
     * @var ArrayCollection<UserPlayedVideos>
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserPlayedVideos", mappedBy="user", cascade={"persist"})
     * @ORM\OrderBy({"playedDate" = "DESC"})
     */
    protected $replayHistory;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->lastPasswordChange = new \DateTime();
        $this->replayHistory = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getUserChild()
    {
        return $this->UserChild;
    }


    /**
     * @param mixed $UserChild
     */
    public function setUserChild($UserChild): void
    {
        $this->UserChild = $UserChild;
    }


    /**
     * @param Product $product
     *
     * @return bool
     */
    public function haveAccess(Product $product): bool
    {
        return $this->hasAccessToProduct($product);
    }


    /**
     * Set profilePhoto
     *
     * @param ProfilePhoto $profilePhoto
     * @return User
     */
    public function setProfilePhoto($profilePhoto = null): User
    {
        if (is_array($profilePhoto)) {
            $profilePhoto = new ProfilePhoto($profilePhoto);
        }

        $this->profilePhoto = $profilePhoto;

        if ($profilePhoto) {
            $this->profilePhoto->setUser($this);
        }

        return $this;
    }


    /**
     * Get profilePhoto
     *
     * @return ProfilePhoto
     */
    public function getProfilePhoto(): ProfilePhoto
    {
        return $this->profilePhoto;
    }


    /**
     * @param Product $product
     *
     * @return int
     */
    public function daysRemainingToUnlock(Product $product): int
    {
        return $product->daysRemainingToUnlock($this);
    }


    /**
     * @return \DateTime
     */
    public function getDateOfBirth(): \DateTime
    {
        return $this->dateOfBirth;
    }


    /**
     * @param \DateTime $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }


    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }


    /**
     * @return \DateTime
     */
    public function getLastPasswordChange(): \DateTime
    {
        return $this->createdAt ?? new \DateTime(); // @todo

        return $this->lastPasswordChange;
    }


    /**
     * @param \DateTime $lastPasswordChange
     */
    public function setLastPasswordChange(\DateTime $lastPasswordChange): void
    {
        $this->lastPasswordChange = $lastPasswordChange;
    }


    /**
     * @return bool
     */
    public function getPublic(): bool
    {
        return $this->isPublic();
    }


    /**
     * @return bool
     */
    public function isMaropostSynced(): bool
    {
        return $this->maropostSynced;
    }


    /**
     * @param bool $maropostSynced
     */
    public function setMaropostSynced(bool $maropostSynced): void
    {
        $this->maropostSynced = $maropostSynced;
    }


    /**
     * @return int
     */
    public function getAge(): int
    {
        $date = $this->getDateOfBirth();

        if (!$date) {
            return null;
        }

        $now = new \DateTime();

        return $now->diff($date)->y;
    }


    /**
     * @return int
     */
    public function getCommunityId(): int
    {
        // TODO @TomasJancar I think there should be some code ?
        return 0;
    }


    /**
     * @return \DateTime
     */
    public function getFlomersionStart(): \DateTime
    {
        return $this->flomersionStart;
    }


    /**
     * @param \DateTime $flomersionStart
     */
    public function setFlomersionStart($flomersionStart): void
    {
        $this->flomersionStart = $flomersionStart;
    }


    /**
     * @return \DateTime
     */
    public function getFlomersionEnd(): \DateTime
    {
        return $this->flomersionEnd;
    }


    /**
     * @param \DateTime $flomersionEnd
     */
    public function setFlomersionEnd($flomersionEnd): void
    {
        $this->flomersionEnd = $flomersionEnd;
    }


    /**
     * @todo
     * @return string
     */
    public function getfacebookId()
    {
        return null;
    }

    /**
     *
     * @param VideoContent $video
     * @param \DateTime $watchedDate
     */
    public function watchVideo(VideoContent $video, \DateTime $watchedDate = null): void
    {
        $history = $this->replayHistory;
        $changed = false;

        /** @var UserPlayedVideos $historyItem */
        foreach ($history as $historyItem) {
            if ($historyItem->getVideo()->getId() === $video->getId()) {
                if ($watchedDate != null) {
                    if ($watchedDate > $historyItem->getPlayedDate()) {
                        $historyItem->setPlayedDate($watchedDate);
                        $changed = true;
                    }
                } else {
                    $historyItem->setPlayedDate(new \DateTime('now'));
                    $changed = true;
                }
            }
        }
        //no entity was changed, so it is the first time that this user watches this video
        if (!$changed) {
            $historyItem = new UserPlayedVideos();
            $historyItem
                ->setUser($this)
                ->setVideo($video)
                ->setPlayedDate(($watchedDate) ?: new \DateTime('now'));

            $this->replayHistory->add($historyItem);
        }
    }


    /**
     * @return ArrayCollection
     */
    public function getReplayHistory()
    {
        return $this->replayHistory;
    }
}
