<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 12:36.
 */
namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\IframeContentRepository")
 * Class IFrameContent
 */
class IframeContent extends \Venice\AppBundle\Entity\Content\IframeContent
{
    /**
     * @ORM\Column(type="string")
     */
    protected $IframeContentChild;

    /**
     * @return mixed
     */
    public function getIframeContentChild()
    {
        return $this->IframeContentChild;
    }

    /**
     * @param mixed $IframeContentChild
     */
    public function setIframeContentChild($IframeContentChild)
    {
        $this->IframeContentChild = $IframeContentChild;
    }
}
