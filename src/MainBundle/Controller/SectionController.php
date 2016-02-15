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
use UserBundle\Entity\User;
use UserBundle\Repository\UserRepository;

class SectionController extends BaseController
{
    public function statistiqueAction()
    {
        /** @var SectionRepository $sectionRepo */
        $sectionRepo = $this->getDoctrine()->getManager()->getRepository('MainBundle:Section');

        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $activeUsers = $userRepo->getActiveUser();

        $totalDownloaded = 0;

        /** @var User $user */
        foreach($activeUsers as $user){
            $totalDownloaded += $user->getDownloaded();
        }

        return $this->render('MainBundle:Layout:statistiques.html.twig', array(
            "activeSections" => $sectionRepo->getActiveSections(),
            "sections" => $sectionRepo->getStatistiques(),
            "pictures" => $totalDownloaded,
            "activeUsers" => $activeUsers,
            "users" => $userRepo->getStatistiques()
        ));
    }
}
