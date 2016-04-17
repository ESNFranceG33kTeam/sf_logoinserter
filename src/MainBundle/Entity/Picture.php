<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Picture
 *
 * @ORM\Table(name="picture")
 * @ORM\Entity(repositoryClass="MainBundle\Repository\PictureRepository")
 */
class Picture
{
    public static $AUTHORIZED_EXTENSION = array('jpeg', 'png', 'jpg');

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @var Logo
     *
     * @ORM\ManyToOne(targetEntity="DownloadSession", inversedBy="pictures")
     */
    private $downloadSession;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, unique=true)
     */
    private $path;

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
     * Set name
     *
     * @param string $name
     *
     * @return Picture
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Picture
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

    public function upload(DownloadSession $downloadSession)
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return false;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        $path = $downloadSession->getId() ;

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

        return true;
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

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/downloadsessions';
    }

    /**
     * @return DownloadSession
     */
    public function getDownloadSession()
    {
        return $this->downloadSession;
    }

    /**
     * @param DownloadSession $downloadSession
     */
    public function setDownloadSession($downloadSession)
    {
        $this->downloadSession = $downloadSession;
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

    function getExtension() {
        return substr(strrchr($this->getWebPath(),'.'),1);
    }
}

