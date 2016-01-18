<?php
namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Esn\EsnBundle\Entity\GalaxyUser;
use MainBundle\Entity\Section;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends GalaxyUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Section
     *
     * @ORM\ManyToOne(targetEntity="MainBundle\Entity\Section", inversedBy="users")
     */
    protected $section;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param Section $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }
}