<?php

namespace MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Logo
 *
 * @ORM\Table(name="logo")
 * @ORM\Entity(repositoryClass="MainBundle\Repository\LogoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Logo
{
    public static $AUTHORIZED_MIME_TYPES = array('image/jpeg', 'image/png', 'image/jpg');

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, unique=true)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    public $name;

    /**
     * How many times this logo has been downloaded
     *
     * @var int
     *
     * @ORM\Column(name="downloaded", type="integer")
     */
    private $downloaded;

    /**
     * @var bool
     *
     * @ORM\Column(name="public", type="boolean")
     */
    private $public;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @var Section
     */
    private $section;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="DownloadSession", mappedBy="logo")
     */
    protected $downloadedSessions;

    /**
     * Constructor
     */
    public function __construct(){
        $this->downloaded= 0;
        $this->downloadSessions = new ArrayCollection();
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
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
     * Set path
     *
     * @param string $path
     *
     * @return Logo
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set downloaded
     *
     * @param integer $downloaded
     *
     * @return Logo
     */
    public function setDownloaded($downloaded)
    {
        $this->downloaded = $downloaded;

        return $this;
    }

    /**
     * Get downloaded
     *
     * @return int
     */
    public function getDownloaded()
    {
        return $this->downloaded;
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Logo
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
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return boolean
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param boolean $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        $path = ($this->isPublic()) ? 'public' : 'sections' . '/' . strtolower($this->getSection()->getCode());

        if (!is_dir($this->getUploadRootDir(). '/' .$path)){
            if (!mkdir($this->getUploadRootDir(). '/' .$path)){
                throw new \Exception('Create dir exception');
            }
        }

        $filename = sha1(uniqid(mt_rand(), true)).'.'.$this->getFile()->guessExtension();

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(). '/' .$path,
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->path = $path . '/' . $filename;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads';
    }

    /**
     * @return bool
     */
    public function isValid(){
        return in_array($this->getFile()->getClientMimeType(), $this::$AUTHORIZED_MIME_TYPES);
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
     * @return array
     */
    public function getSize(){
        return getimagesize($this->getAbsolutePath());
    }

    public function getWidth(){
        return $this->getSize()[0];
    }

    public function getHeight(){
        return $this->getSize()[1];
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

        $downloadSession->setLogo($this);

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

    public function __toString(){
        return $this->getName();
    }
}

