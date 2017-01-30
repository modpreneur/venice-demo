<?php
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
     * @var string
     *
     * @ORM\Column(name="file_protected", type="string", length=255)
     *
     */
    protected $fileProtected;


    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $downloadType;


    /**
     * @return string
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


    /**
     * @return string
     */
    public function getFileProtected()
    {
        return $this->fileProtected;
    }


    /**
     * @param string $fileProtected
     * @return $this
     */
    public function setFileProtected($fileProtected)
    {
        $this->fileProtected = $fileProtected;

        return $this;
    }


    /**
     * @return string
     */
    public function getDownloadType(): string
    {
        return $this->downloadType;
    }


    /**
     * @param string $downloadType
     */
    public function setDownloadType(string $downloadType)
    {
        $this->downloadType = $downloadType;
    }

    public function haveDownloadName()
    {
        return false;
    }
}
