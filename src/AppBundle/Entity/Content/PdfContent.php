<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 13:07.
 */
namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\PdfContentRepository")
 * Class PdfContent
 */
class PdfContent extends \Venice\AppBundle\Entity\Content\PdfContent
{
    /**
     * @ORM\Column(type="string")
     */
    protected $PdfContentChild;

    /**
     * @return mixed
     */
    public function getPdfContentChild()
    {
        return $this->PdfContentChild;
    }

    /**
     * @param mixed $PdfContentChild
     */
    public function setPdfContentChild($PdfContentChild)
    {
        $this->PdfContentChild = $PdfContentChild;
    }
}
