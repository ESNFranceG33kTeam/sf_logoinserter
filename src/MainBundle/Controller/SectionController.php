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
use UserBundle\Repository\UserRepository;

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

        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $users = $userRepo->getActiveUser();


        return $this->render('MainBundle:Layout:statistiques.html.twig', array(
            "sections" => $sectionRepo->getStatistiques(),
            "pictures" => $totalDownloaded,
            "activeUsers" => $users,
            "users" => $userRepo->getStatistiques()
        ));
    }
}
