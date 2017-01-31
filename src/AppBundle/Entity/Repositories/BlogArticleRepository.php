<?php

namespace AppBundle\Entity\Repositories;

use AppBundle\Entity\Category;

/**
 * BlogArticleRepository.
 */
class BlogArticleRepository extends \Venice\AppBundle\Entity\Repositories\BlogArticleRepository
{
    /**
     * @param Category $category
     * @param bool $publishedOnly
     * @param int $offset
     * @param int $limit
     *
     * @return mixed
     */
    public function findArticlesByCategory(Category $category, $publishedOnly = false, $offset = 0, $limit = 5)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT article
            FROM AppBundle:BlogArticle AS article
            WHERE :category MEMBER OF article.categories
            AND article.dateToPublish <= :now
            AND article.published = :published
            ORDER BY article.dateToPublish DESC
         ')
            ->setParameters([
                'category'=>$category,
                'published'=>$publishedOnly,
                'now'=>new \DateTime()
            ])
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $query->getResult();
    }
}
