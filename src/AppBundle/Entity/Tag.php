<?php
/**
 * Created by PhpStorm.
 * User: marek
 * Date: 26/01/17
 * Time: 18:17.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tag.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\TagRepository")
 */
class Tag extends \Venice\AppBundle\Entity\Tag
{
}
