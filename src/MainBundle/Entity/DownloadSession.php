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
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Logo")
     */
    private $logos;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="downloadedSessions")
     */
    private $owner;

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
    public function getLogos()
    {
        return $this->logos;
    }

    /**
     * @param Logo $logo
     *
     * @return $this
     */
    public function addLogo(Logo $logo){
        $this->logos->add($logo);

        return $this;
    }

    /**
     * @param Logo $logo
     *
     * @return $this
     */
    public function removeLogo(Logo $logo){
        $this->logos->removeElement($logo);

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
}

