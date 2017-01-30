<?php
/**
 * Created by PhpStorm.
 * User: marek
 * Date: 25/01/17
 * Time: 14:36.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Category.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\CategoryRepository")
 */
class Category extends \Venice\AppBundle\Entity\Category
{
}
