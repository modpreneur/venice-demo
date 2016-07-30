<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:47
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\BlogArticleRepository")
 * Class BlogArticle
 */
class BlogArticle extends \Venice\AppBundle\Entity\BlogArticle
{
    /**
     * @ORM\Column(type="string")
     */
    protected $BlogArticleChild;
}