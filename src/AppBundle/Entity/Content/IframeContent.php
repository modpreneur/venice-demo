<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 12:36
 */

namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\IframeContentRepository")
 * Class IFrameContent
 *
 * @package AppBundle\Entity\Content
 */
class IframeContent extends \Venice\AppBundle\Entity\Content\IframeContent
{
    /**
     * @ORM\Column(type="string")
     */
    protected $IframeContentChild;
}