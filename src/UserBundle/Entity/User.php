<?php
namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Esn\EsnBundle\Entity\GalaxyUser;
use MainBundle\Entity\DownloadSession;
use MainBundle\Entity\Section;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MainBundle\Entity\DownloadSession", mappedBy="owner")
     */
    protected $downloadedSessions;

    /**
     * How many times the user upload a logo
     *
     * @var int
     *
     * @ORM\Column(name="downloaded", type="integer")
     */
    private $downloaded;

    /**
     * @param $galaxyUsername
     * @param $attributes
     */
    public function __construct($galaxyUsername, $attributes){
        parent::__construct($galaxyUsername, $attributes);

        $this->downloaded = 0;
        $this->downloadSessions = new ArrayCollection();
    }

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

    /**
     * @return ArrayCollection
     */
    public function getDownloadedSessions(){
        return $this->downloadSessions;
    }

    /**
     * @param DownloadSession $downloadSession
     *
     * @return $this
     */
    public function addDownloadedSession(DownloadSession $downloadSession){
        $this->downloadSessions->add($downloadSession);

        $downloadSession->setOwner($this);

        return $this;
    }

    /**
     * @param DownloadSession $downloadSession
     *
     * @return $this
     */
    public function removeDownloadedSession(DownloadSession $downloadSession){
        $this->downloadSessions->removeElement($downloadSession);

        return $this;
    }

    /**
     * @return int
     */
    public function getDownloaded()
    {
        return $this->downloaded;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->firstname . " " . strtoupper($this->lastname);
    }

    /**
     * @return string
     */
    public function getFullnameWithSection()
    {
        return $this->getFullname() . " (" . $this->getSection()->getName() . ")";
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
     * @return User
     */
    public function increaseDownloaded()
    {
        $this->downloaded = $this->getDownloaded() + 1;

        return $this;
    }
}