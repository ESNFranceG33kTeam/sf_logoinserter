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

        $downloadsession = null;
        $code = "error";

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

        if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
            $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

            if(in_array(strtolower($extension), Picture::$AUTHORIZED_EXTENSION)){
                $code = "success";
            }

            //Image is ready to upload
            if ($code != "error")
            {
                $picture = new Picture();
                $picture->setCreatedAt(new \DateTime("now"));
                $picture->setName($_FILES['upl']['name']);

                /** @var UploadedFile $file */
                $file = new UploadedFile($_FILES['upl']['tmp_name'], $_FILES['upl']['name']);
                $picture->setFile($file);

                if($picture->upload($downloadsession)){
                    //Resize, crop and merge with logo
                    //$functions->resizeAndCrop();
                    //$functions->mergeWithLogo();
                    //$Sections->addNewPicture($_SESSION['logoname']);

                    $code = "success";
                }

                $em->persist($picture);

                $downloadsession->addPicture($picture);

                $em->flush();
            }
        }

        return new JsonResponse(array(
            'code' => $code
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
        $downloadsession = null;

        /** @var PicturesManager $pm */
        $pm = $this->get('pictures.manager');

        $code = "error";

        if ($request->getSession()->get('downloadSession_id')) {
            $downloadsession = $this->getDoctrine()->getManager()->getRepository('MainBundle:DownloadSession')->find($request->getSession()->get('downloadSession_id'));
        }

        if (!$downloadsession){
            throw $this->createNotFoundException();
        }

        /** @var Picture $picture */
        foreach($downloadsession->getPictures() as $picture){
            $this->getSection()->increaseDownloaded();
            $downloadsession->getLogo()->increaseDownloaded();

            //Resize, crop and merge with logo
            $pm->resizeAndCrop($picture);
            $pm->mergeWithLogo($picture, $downloadsession);
        }

        return new JsonResponse(array(
            'code' => $code
        ));
    }
}
