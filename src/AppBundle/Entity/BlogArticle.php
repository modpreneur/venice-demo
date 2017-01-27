<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:47.
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\BlogArticleRepository")
 * Class BlogArticle;
 */
class BlogArticle extends \Venice\AppBundle\Entity\BlogArticle
{
    /**
     * @ORM\Column(type="string")
     */
    protected $BlogArticleChild;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $published;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $commentsOn;

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

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return mixed
     */
    public function getCommentsOn()
    {
        return $this->commentsOn;
    }

    /**
     * @param mixed $commentsOn
     */
    public function setCommentsOn($commentsOn)
    {
        $this->commentsOn = $commentsOn;
    }
}
