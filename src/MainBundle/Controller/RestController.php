<?php

namespace MainBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use MainBundle\Entity\Logo;
use MainBundle\Entity\Section;
use MainBundle\Repository\LogoRepository;
use MainBundle\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use phpCAS;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Repository\UserRepository;

class RestController extends BaseController
{
    public function logoAction($code_section)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Section $section */
        $section = $em->getRepository('MainBundle:Section')->findOneBy(array("code" => $code_section));

        if (!$section){
            throw $this->createNotFoundException('No section found with this code');
        }

        /** @var Logo $logo */
        $logo = $section->getLogos()->first();

        $baseurl = "http://logoinserter.esnlille.fr";

        return new JsonResponse(
            $baseurl . "/" . $logo->getWebPath()
        );
    }
}
