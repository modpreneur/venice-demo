<?php

namespace AppBundle\Entity\SocialStream;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SocialStreamReloadLogRepository
 * @package AppBundle\Entity\SocialStream
 */
class SocialStreamReloadLogRepository extends EntityRepository
{
    public function findLatestUpdate()
    {
        /**
         * @return SocialStreamReloadLog
         */
        return $this->createQueryBuilder('s')
            ->orderBy("s.timestamp", 'DESC')
            ->where('s.reloadRan = 1')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}