<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 13:07
 */

namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\PdfContentRepository")
 * Class PdfContent
 *
 * @package AppBundle\Entity\Content
 */
class PdfContent extends \Venice\AppBundle\Entity\Content\PdfContent
{
    /**
     * @ORM\Column(type="string")
     */
    protected $PdfContentChild;

}