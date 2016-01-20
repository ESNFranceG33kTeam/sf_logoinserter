<?php

namespace MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * DownloadSession
 *
 * @ORM\Table(name="download_session")
 * @ORM\Entity(repositoryClass="MainBundle\Repository\DownloadSessionRepository")
 */
class DownloadSession
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Picture", mappedBy="downloadSession")
     */
    private $pictures;

    /**
     * @var Logo
     *
     * @ORM\ManyToOne(targetEntity="Logo", inversedBy="downloadedSessions")
     */
    private $logo;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="downloadedSessions")
     */
    private $owner;

    public function __construct(){
        $this->pictures = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return DownloadSession
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get logos
     *
     * @return ArrayCollection
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * @param Picture $picture
     *
     * @return $this
     */
    public function addPicture(Picture $picture){
        $this->pictures->add($picture);

        $picture->setDownloadSession($this);

        return $this;
    }

    /**
     * @param Picture $picture
     *
     * @return $this
     */
    public function removePicture(Picture $picture){
        $this->pictures->removeElement($picture);

        return $this;
    }

    /**
     * @return Section
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Section $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return Logo
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param Logo $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}

