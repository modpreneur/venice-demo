<?php

namespace AppBundle\Entity\Repositories;

use AppBundle\Entity\Content\VideoContent;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * UserPlayedVideosRepository
 */
class UserPlayedVideosRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param VideoContent $video
     *
     * @return \DateTime|null
     */
    public function getDateOfLastUserPlayOfVideo(User $user, VideoContent $video): ?\DateTime
    {
        $history = $this->findOneBy(
            [
                'user' => $user,
                'video' => $video,
            ]
        );

        if ($history) {
            return $history->getPlayedDate();
        }

        return null;
    }
}


