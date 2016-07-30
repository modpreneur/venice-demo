<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.10.15
 * Time: 9:27
 */

namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\HtmlContentRepository")
 * Class HtmlContent
 * @package AppBundle\Entity\Content
 */
class HtmlContent extends \Venice\AppBundle\Entity\Content\HtmlContent
{
    /**
     * @ORM\Column(type="string")
     */
    protected $HtmlContentChild;
}
