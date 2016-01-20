<?php

namespace MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use \Esn\EsnBundle\Entity\Section as BaseSection;
use Esn\EsnBundle\Model\GalaxyUser;
use UserBundle\Entity\User;

/**
 * Section
 *
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="MainBundle\Repository\SectionRepository")
 */
class Section extends BaseSection
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\User", mappedBy="section")
     */
    private $users;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Logo")
     */
    protected $logos;

    /**
     * How many times the section has download logos
     *
     * @var int
     *
     * @ORM\Column(name="downloaded", type="integer")
     */
    private $downloaded;

    /**
     * Constructor
     */
    public function __construct(){
        $this->downloaded= 0;
        $this->users = new ArrayCollection();
        $this->logos = new ArrayCollection();
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
    public function addLogo(Logo $logo)
    {
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
     * @param User $user
     * @return $this
     */
    public function addUser($user)
    {
        $this->users->add($user);

        $user->setSection($this);

        return $this;
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Check if section has is own logo
     *
     * @return bool
     */
    public function hasSectionLogo(){
        /** @var Logo $logo */
        foreach($this->getLogos() as $logo){
            if (!$logo->isPublic()) return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getDownloaded()
    {
        return $this->downloaded;
    }

    /**
     * @param int $downloaded
     */
    public function setDownloaded($downloaded)
    {
        $this->downloaded = $downloaded;
    }

    /**
     * Increase downloaded
     *
     * @return Logo
     */
    public function increaseDownloaded()
    {
        $this->downloaded = $this->getDownloaded() + 1;

        return $this;
    }
}

