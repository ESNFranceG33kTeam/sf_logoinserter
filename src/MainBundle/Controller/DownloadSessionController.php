<?php

namespace MainBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use MainBundle\Entity\Logo;
use MainBundle\Repository\LogoRepository;
use MainBundle\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use phpCAS;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
                'logo' => $logo
            ))
        ));
    }
}
