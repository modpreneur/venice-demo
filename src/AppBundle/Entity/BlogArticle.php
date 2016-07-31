<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:47
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * @return mixed
     */
    public function getBlogArticleChild()
    {
        return $this->BlogArticleChild;
    }

    /**
     * @param mixed $BlogArticleChild
     */
    public function setBlogArticleChild($BlogArticleChild)
    {
        $this->BlogArticleChild = $BlogArticleChild;
    }


}