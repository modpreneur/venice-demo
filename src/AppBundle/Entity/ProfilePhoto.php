<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile_photo")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 */
class ProfilePhoto
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="user_profile", fileNameProperty="imageName")
     *
     * @var File $imageFile
     */
    protected $imageFile;

    /**
     * @ORM\Column(type="string", length=255, name="image_name")
     *
     * @var string $imageName
     */
    protected $imageName;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="profilePhoto", cascade={"persist"})
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(name="crop_start_x", type="integer", options={"default":0})
     * @var integer
     */
    protected $cropStartX;

    /**
     * @ORM\Column(name="crop_start_y", type="integer", options={"default":0})
     * @var integer
     */
    protected $cropStartY;

    /**
     * @ORM\Column(name="crop_size", type="integer", options={"default":100})
     * @var integer
     */
    protected $cropSize;

    /**
     * @var string
     * @ORM\Column(name="original_photo_url", type="text", nullable=true)
     */
    protected $originalPhotoUrl;

    /**
     * @var string
     * @ORM\Column(name="crooped_photo_url", type="text", nullable=true)
     */
    protected $croopedPhotoUrl;




    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile($image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }


    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }


    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * @return int
     */
    public function getCropStartX()
    {
        return $this->cropStartX;
    }


    /**
     * @param int $cropStartX
     *
     * @return $this
     */
    public function setCropStartX($cropStartX)
    {
        $this->cropStartX = $cropStartX;

        return $this;
    }


    /**
     * @return int
     */
    public function getCropStartY()
    {
        return $this->cropStartY;
    }


    /**
     * @param int $cropStartY
     *
     * @return $this
     */
    public function setCropStartY($cropStartY)
    {
        $this->cropStartY = $cropStartY;

        return $this;
    }


    /**
     * @return int
     */
    public function getCropSize()
    {
        return $this->cropSize;
    }


    /**
     * @param $cropSize
     *
     * @return $this
     */
    public function setCropSize($cropSize)
    {
        $this->cropSize = $cropSize;

        return $this;
    }


    /**
     * @return string
     */
    public function getOriginalPhotoUrl()
    {
        return $this->originalPhotoUrl;
    }


    /**
     * @param string $originalPhotoUrl
     *
     * @return $this
     */
    public function setOriginalPhotoUrl($originalPhotoUrl)
    {
        $this->originalPhotoUrl = $originalPhotoUrl;

        return $this;
    }


    /**
     * @return string
     */
    public function getCroopedPhotoUrl()
    {
        return $this->croopedPhotoUrl;
    }


    /**
     * @param string $croopedPhotoUrl
     *
     * @return $this
     */
    public function setCroopedPhotoUrl($croopedPhotoUrl)
    {
        $this->croopedPhotoUrl = $croopedPhotoUrl;

        return $this;
    }
}
