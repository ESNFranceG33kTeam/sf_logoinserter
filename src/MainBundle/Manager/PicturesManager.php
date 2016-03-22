<?php

namespace MainBundle\Manager;
use Doctrine\ORM\EntityManager;
use MainBundle\Entity\DownloadSession;
use MainBundle\Entity\Logo;
use MainBundle\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use UserBundle\Entity\User;

/**
 * Created by PhpStorm.
 * User: jerem
 * Date: 20/01/16
 * Time: 13:46
 */
class PicturesManager
{
    private $marge = 10;
    private $pourcent_jpg = 100;
    const FB_WIDTH = 1080;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var User
     */
    private $user;

    /**
     * @var EntityManager
     */
    private $em;

    public function setTranslator($translator){$this->translator = $translator;}
    public function setUser(TokenStorage $tokenStorage){$this->user = $tokenStorage->getToken()->getUser(); }
    public function setManager(EntityManager $em){$this->em = $em;}

    /**
     * Merge a logo with an image
     *
     * @param Picture         $picture
     * @param DownloadSession $downloadSession
     */
    public function mergeWithLogo(Picture $picture, $downloadSession){
        $image = $this->createImage($picture);

        //$logo  = imagecreatefrompng($this->imagesPath . "/" . $this->logoname);
        if ($downloadSession->getWidth()){
            $this->resizeLogo($downloadSession);
        }

        $filepath = $downloadSession->getLogo()->getWebPath();

        $logo = imagecreatefrompng($filepath);

        $logo_x = imagesx($logo);
        $logo_y = imagesy($logo);

        $image_x= imagesx($image);
        $image_y= imagesy($image);

        imagesavealpha($logo, true);
        imagealphablending($logo, true);

        switch($downloadSession->getPosition()){
            case "bl" :	//Bottom Left
                $dest_x = $this->marge;
                $dest_y = ($image_y - $logo_y) - $this->marge;
                break;
            case "tl" :	//Top Left
                $dest_x = $this->marge;
                $dest_y = $this->marge;
                break;
            case "tr" : //Top Right
                $dest_x = ($image_x - $logo_x) - $this->marge;
                $dest_y = $this->marge;
                break;
            default : 	//Bottom Right
                $dest_x = ($image_x - $logo_x) - $this->marge;
                $dest_y = ($image_y - $logo_y) - $this->marge;
                break;
        }

        //Width : 300 pour 1080
        //Height: 97 pour 729
        //debug("dest_x:$dest_x, dest_y:$dest_y, logo_x:$logo_x, logo_y=$logo_y");

        imagecopy($image, $logo, $dest_x, $dest_y, 0, 0, $logo_x, $logo_y);

        $this->saveImage($image, $picture);
    }

    /**
     * Resize the picture
     *
     * @param Logo    $logo
     */
    public function resizeAndCrop(Picture $picture){
        // Get new sizes
        $image = $this->createImage($picture);

        $width = imagesx($image);
        $height= imagesy($image);

        //$percent = ($width <= $height) ? ($this->width / $width) : ($this->height / $height);
        if ($width > $height){
            $percent = $width / self::FB_WIDTH;
        }
        else{
            $percent = $height / self::FB_WIDTH;
        }

        $percent = round($percent, 4);
        $percent = ($percent < 1) ? 1 : $percent;

        $newwidth = round($width / $percent);
        $newheight = round($height / $percent);

        // Resize
        $resize = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($resize, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height); //Far better quality than the other one

        // Output and free memory
        $this->saveImage($resize, $picture);
    }

    /**
     * @param DownloadSession $downloadSession
     *
     * @return resource
     */
    public function resizeLogo(DownloadSession $downloadSession){
        // Get new sizes
        $image = $this->createImage($downloadSession->getLogo());

        $width = imagesx($image);
        $height= imagesy($image);

        $percent = $downloadSession->getWidth() / $width;
        $percent = round($percent, 4);

        $newwidth = round($width * $percent);
        $newheight = round($height * $percent);

        // Resize
        $resize = imagecreatetruecolor($newwidth, $newheight);
        imagealphablending($resize, false);
        imagesavealpha($resize,true);
        $transparent = imagecolorallocatealpha($resize, 255, 255, 255, 127);
        imagefilledrectangle($resize, 0, 0, $newwidth, $newheight, $transparent);
        imagecopyresampled($resize, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height); //Far better quality than the other one

        // Output and free memory
        imagepng($resize);

        return $image;
    }

    /**
     * Create an image based on its extension
     *
     * @param Picture|Logo $picture
     *
     * @return resource
     */
    public function createImage($picture){
        //JPG is the only extension from facebook, gif, jpeg and png are only from uploaded file
        $filepath = $picture->getWebPath();

        switch(pathinfo($filepath, PATHINFO_EXTENSION)){
            case 'jpg':
                $image = imagecreatefromjpeg($filepath);
                break;
            case 'gif':
                $image = imagecreatefromgif($filepath);
                break;
            case 'jpeg':
                $image = imagecreatefromjpeg($filepath);
                break;
            case 'png':
                $image = imagecreatefrompng($filepath);
                break;
            default :
                $image = imagecreatefrompng($filepath);
                break;
        }

        return $image;
    }

    /**
     * @param $src
     * @param Picture|Logo $picture
     */
    public function saveImage($src, $picture){
        //JPG is the only extension from facebook, gif, jpeg and png are only from uploaded file
        $filepath = $picture->getWebPath();

        switch(pathinfo($filepath, PATHINFO_EXTENSION)){
            case 'jpg':
                imagejpeg($src, $filepath, $this->pourcent_jpg);
                break;
            case 'gif':
                imagegif($src, $filepath);
                break;
            case 'jpeg':
                imagejpeg($src, $filepath, $this->pourcent_jpg);
                break;
            case 'png':
                imagepng($src, $filepath);
        }

        imagedestroy($src);
    }
}