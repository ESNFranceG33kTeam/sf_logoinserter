<?php

namespace MainBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use MainBundle\Entity\DownloadSession;
use MainBundle\Entity\Logo;
use MainBundle\Entity\Picture;
use MainBundle\Manager\PicturesManager;
use MainBundle\Repository\LogoRepository;
use MainBundle\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use phpCAS;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class DownloadSessionController extends BaseController
{
    /**
     * @param Request $request
     * @param $logo_id
     *
     * @return JsonResponse
     */
    public function sliderAction(Request $request, $logo_id)
    {
        if (!$request->isXmlHttpRequest()){
            throw $this->createAccessDeniedException();
        }

        /** @var Logo $logo */
        $logo = $this->getDoctrine()->getManager()->getRepository('MainBundle:Logo')->find($logo_id);

        if (!$logo){
            throw $this->createNotFoundException();
        }

        return new JsonResponse(array(
            'code' => 'success',
            'html' => $this->renderView('MainBundle:DownloadSession:slider.html.twig', array(
                'logo' => $logo
            ))
        ));
    }

    /**
     * @param Request $request
     * @param $logo_id
     *
     * @return JsonResponse
     */
    public function positionAction(Request $request, $logo_id)
    {
        if (!$request->isXmlHttpRequest()){
            throw $this->createAccessDeniedException();
        }

        /** @var Logo $logo */
        $logo = $this->getDoctrine()->getManager()->getRepository('MainBundle:Logo')->find($logo_id);

        if (!$logo){
            throw $this->createNotFoundException();
        }

        return new JsonResponse(array(
            'code' => 'success',
            'html' => $this->renderView('MainBundle:DownloadSession:position.html.twig', array(
                'logo' => $logo
            ))
        ));
    }

    public function uploadboxAction(Request $request, $width, $position, $logo_id)
    {
        if (!$request->isXmlHttpRequest()){
            throw $this->createAccessDeniedException();
        }

        /** @var Logo $logo */
        $logo = $this->getDoctrine()->getManager()->getRepository('MainBundle:Logo')->find($logo_id);

        if (!$logo){
            throw $this->createNotFoundException();
        }

        return new JsonResponse(array(
            'code' => 'success',
            'html' => $this->renderView('MainBundle:DownloadSession:uploadbox.html.twig', array(
                'logo' => $logo,
                'width'=> $width,
                'position' => $position
            ))
        ));
    }

    public function uploadFilesAction(Request $request, $width, $position, $logo_id)
    {
        if (!$request->isXmlHttpRequest()){
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();

        $pm = $this->getPicturesManager();

        $downloadsession = null;
        $code = "error";
        $message = "";

        /** @var Logo $logo */
        $logo = $this->getDoctrine()->getManager()->getRepository('MainBundle:Logo')->find($logo_id);

        if (!$logo){
            throw $this->createNotFoundException();
        }

        if ($request->getSession()->get('downloadSession_id')){
            /** @var DownloadSession $downloadsession */
            $downloadsession = $this->getDoctrine()->getManager()->getRepository('MainBundle:DownloadSession')->find($request->getSession()->get('downloadSession_id'));
        }

        if (!$downloadsession){
            $downloadsession = new DownloadSession();
            $downloadsession->setOwner($this->getUser());
            $downloadsession->setLogo($logo);
            $downloadsession->setCreatedAt(new \DateTime("now"));
            $downloadsession->setWidth($width);
            $downloadsession->setPosition($position);

            $em->persist($downloadsession);
            $em->flush();

            $request->getSession()->set('downloadSession_id', $downloadsession->getId());
        }

        if(isset($_FILES['upl'])){
            if ($_FILES['upl']['error'] == 0) {
                $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

                if (in_array(strtolower($extension), Picture::$AUTHORIZED_EXTENSION)) {
                    $code = "success";
                }

                //Image is ready to upload
                if ($code != "error") {
                    $picture = new Picture();
                    $picture->setCreatedAt(new \DateTime("now"));
                    $picture->setName($_FILES['upl']['name']);

                    /** @var UploadedFile $file */
                    $file = new UploadedFile($_FILES['upl']['tmp_name'], $_FILES['upl']['name']);
                    $picture->setFile($file);

                    if ($picture->upload($downloadsession)) {
                        //Resize, crop and merge with logo
                        $pm->resizeAndCrop($picture, $downloadsession->getWidth());
                        $pm->mergeWithLogo($picture, $downloadsession);

                        $code = "success";
                    }

                    $em->persist($picture);

                    $request->getSession()->set('path', $picture->getUploadDir());

                    $downloadsession->addPicture($picture);

                    $em->flush();
                }
            }else{
                switch($_FILES['upl']['error']){
                    case UPLOAD_ERR_INI_SIZE :
                        $message = "La taille du fichier téléchargé excède la valeur de upload_max_filesize (" .ini_get('upload_max_filesize'). "), configurée dans le php.ini";
                        break;
                    case UPLOAD_ERR_FORM_SIZE :
                        $message = "La taille du fichier téléchargé excède la valeur de MAX_FILE_SIZE (" .ini_get('MAX_FILE_SIZE'). "), qui a été spécifiée dans le formulaire HTML";
                        break;
                    case UPLOAD_ERR_PARTIAL :
                        $message = "Le fichier n'a été que partiellement téléchargé";
                        break;
                    case UPLOAD_ERR_NO_FILE :
                        $message = "Aucun fichier n'a été téléchargé";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR :
                        $message = "Un dossier temporaire est manquant. Introduit en PHP 5.0.3.";
                        break;
                    case UPLOAD_ERR_CANT_WRITE :
                        $message = "Échec de l'écriture du fichier sur le disque. Introduit en PHP 5.1.0.";
                        break;
                    case UPLOAD_ERR_EXTENSION :
                        $message = "Une extension PHP a arrêté l'envoi de fichier. PHP ne propose aucun moyen de déterminer quelle extension est en cause. L'examen du phpinfo() peut aider. Introduit en PHP 5.2.0.";
                        break;
                    default :
                        $message = "Autre probleme";
                        break;
                }

            }
        }else{
            $message = "No uploaded Files";
        }

        return new JsonResponse(array(
            'code' => $code,
            'message' => $message
        ));
    }

    public function zipAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getEntityManager();

        /** @var DownloadSession $downloadsession */
        $downloadsession = $path = $opened = null;

        /** @var PicturesManager $pm */
        $pm = $this->get('pictures.manager');

        $options = array();
        $options['code'] = "error";
        $options['message'] = "";

        if ($request->getSession()->get('downloadSession_id')) {
            $downloadsession = $this->getDoctrine()->getManager()->getRepository('MainBundle:DownloadSession')->find($request->getSession()->get('downloadSession_id'));
        }else{
            $options['message'] = "no dowloadsession id";
        }

        if ($request->getSession()->get('path')) {
            $path = $request->getSession()->get('path');
        }else{
            $options['message'] = "no path in session";
        }

        if (!$downloadsession){
            $options['code'] = "error";
            $options['message'] = "createNotFoundException";
        }else{
            $request->getSession()->set('downloadSession_id', null);
        }

        if ($downloadsession && $path){
            $archivepath =  $path . '/' . $downloadsession->getId() . '/archive.zip';
            $zip = new \ZipArchive();

            $options['message'] = "archivepath : $archivepath\n";
            $downloadsession->getLogo()->increaseDownloaded();

            if ($downloadsession->getPictures()->count() > 0){
                /** @var Picture $picture */
                foreach($downloadsession->getPictures() as $picture){

                    $this->getSection()->increaseDownloaded();
                    $this->getUser()->increaseDownloaded();

                    $opened = $zip->open($archivepath, \ZipArchive::CREATE);
                    if($opened === true)
                    {
                        $zip->addFile($picture->getWebPath());
                        $options['message'] = "file added\n";
                        $zip->close();
                    }
                }
            }else{
                $options['message'] = "no pictures in downoadsession no : " . $downloadsession->getId();
            }

            if (is_file($archivepath)){
                $options['code'] = "success";
                $options['zippath'] = '/' . $archivepath;
            }

            $em->flush();
        }else{
            $options['message'] = "dowloadsession or path null";
        }

        return new JsonResponse($options);
    }
}
