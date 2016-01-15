<?php

namespace MainBundle\Entity;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

