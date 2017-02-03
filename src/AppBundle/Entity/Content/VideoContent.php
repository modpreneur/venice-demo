<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 15:57.
 */
namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\VideoContentRepository")
 * Class VideoContent
 */
class VideoContent extends \Venice\AppBundle\Entity\Content\VideoContent
{
    /**
     * @ORM\Column(type="string")
     */
    protected $VideoContentChild;

    /**
     * @var string
     *
     * @ORM\Column(name="vimeo_thumbnail_id", type="string", length=255, nullable=true, options={"default"=null})
     */
    protected $vimeoThumbnailId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="need_gear", type="boolean", options={"default"=false})
     */
    protected $needGear;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $downloadType;


    /**
     * VideoContent constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->downloadType = '';
        $this->needGear = false;
    }


    /**
     * @return mixed
     */
    public function getVideoContentChild()
    {
        return $this->VideoContentChild;
    }

    /**
     * @param mixed $VideoContentChild
     */
    public function setVideoContentChild($VideoContentChild)
    {
        $this->VideoContentChild = $VideoContentChild;
    }


    /**
     * @param $size
     *
     * @return string
     */
    public function generateVimeoThumbnail($size)
    {
        if (is_null($this->vimeoThumbnailId)) {
            return $this->previewImage;
        }

        return "https://i.vimeocdn.com/video/{$this->vimeoThumbnailId}_{$size}.jpg";
    }


    /**
     * @return bool
     */
    public function isNeedGear(): bool
    {
        return $this->needGear;
    }


    /**
     * @param bool $needGear
     */
    public function setNeedGear(bool $needGear)
    {
        $this->needGear = $needGear;
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
}
