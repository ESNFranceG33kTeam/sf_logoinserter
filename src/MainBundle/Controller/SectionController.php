<?php

namespace MainBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use MainBundle\Entity\Logo;
use MainBundle\Repository\LogoRepository;
use MainBundle\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use phpCAS;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class SectionController extends BaseController
{
    public function statistiqueAction()
    {
        /** @var SectionRepository $sectionRepo */
        $sectionRepo = $this->getDoctrine()->getManager()->getRepository('MainBundle:Section');

        /** @var LogoRepository $logoRepo */
        $logoRepo = $this->getDoctrine()->getManager()->getRepository('MainBundle:Logo');
        $logos = $logoRepo->findAll();
        $totalDownloaded = 0;

        /** @var Logo $logo */
        foreach($logos as $logo){
            $totalDownloaded += $logo->getDownloaded();
        }

        return $this->render('MainBundle:Layout:statistiques.html.twig', array(
            "sections" => $sectionRepo->getStatistiques(),
            "pictures" => $totalDownloaded
        ));
    }
}
