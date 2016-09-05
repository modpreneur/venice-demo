<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 16:09.
 */
namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\ContentInGroupRepository")
 * Class ContentInGroup
 */
class ContentInGroup extends \Venice\AppBundle\Entity\Content\ContentInGroup
{
    /**
     * @ORM\Column(type="string")
     */
    protected $ContentInGroupChild;

    /**
     * @return mixed
     */
    public function getContentInGroupChild()
    {
        return $this->ContentInGroupChild;
    }

    /**
     * @param mixed $ContentInGroupChild
     */
    public function setContentInGroupChild($ContentInGroupChild)
    {
        $this->ContentInGroupChild = $ContentInGroupChild;
    }
}
