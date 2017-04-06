<?php

namespace AppBundle\Entity\Repositories;

/**
 * UserRepository.
 */
class UserRepository extends \Venice\AppBundle\Entity\Repositories\UserRepository
{
    /**
     * Return users whose name(firstName + " " + lastName) contains given string
     *
     * @param string $partOfName
     *
     * @return array of GlobalUser
     */
    public function getUsersByPartOfName($partOfName)
    {
        //create query which is like this one: select * from users where cat(firstName," " ,lastName) like %partOfName%
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder
            ->select('u')
            ->where($queryBuilder->expr()->like(
                $queryBuilder->expr()->concat(
                    'u.firstName',
                    $queryBuilder->expr()->concat($queryBuilder->expr()->literal(' '), 'u.lastName')
                ),
                $queryBuilder->expr()->literal('%' . $partOfName. '%')
            ))
            ->setMaxResults(10);
        ;
        $query = $queryBuilder->getQuery();
        $result = $query->execute();
        return $result;
    }
}
