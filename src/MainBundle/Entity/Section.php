<?php

namespace MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use \Esn\EsnBundle\Entity\Section as BaseSection;

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
     * @ORM\ManyToMany(targetEntity="Logo")
     */
    protected $logos;

    public function __construct(){
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
}

