<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 15:57
 */

namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\VideoContentRepository")
 * Class VideoContent
 *
 * @package AppBundle\Entity\Content
 */
class VideoContent extends \Venice\AppBundle\Entity\Content\VideoContent
{
    /**
     * @ORM\Column(type="string")
     */
    protected $VideoContentChild;
}