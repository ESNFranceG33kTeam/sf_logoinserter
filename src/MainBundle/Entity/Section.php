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

    public function __construct(){
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
     * @param ArrayCollection $logos
     */
    public function setLogos($logos)
    {
        $this->logos = $logos;
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
}

