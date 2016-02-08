<?php

namespace MainBundle\Controller;

use Doctrine\ORM\EntityManager;
use MainBundle\Entity\Logo;
use MainBundle\Entity\Section;
use MainBundle\Repository\LogoRepository;
use MainBundle\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use phpCAS;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class LogoController extends BaseController
{
    public function indexAction()
    {
        /** @var LogoRepository $logoRepo */
        $logoRepo = $this->getDoctrine()->getManager()->getRepository('MainBundle:Logo');

        $logos = array();

        /** @var Logo $logo */
        foreach($this->getSection()->getLogos() as $logo){
            if (!in_array($logo->getId(), $logos)) {
                $logos[$logo->getId()] = $logo->getName();
            }
        }

        $form = $this->createFormBuilder()
            ->add('logos', 'choice', array(
                'empty_value' => '',
                'choices' => $logos
            ))
            ->getForm();

        return $this->render('MainBundle:Logo:index.html.twig', array(
            'logos' => $this->getSection()->getLogos(),
            'form'  => $form->createView()
        ));
    }

    public function uploadAction(Request $request)
    {
        $logo = new Logo();
        $form = $this->createFormBuilder($logo)
            ->add('name')
            ->add('public')
            ->add('file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->get('file') && $form->get('file')->getData()){
            /** @var Logo $tmpLogo */
            $tmpLogo = $form->getData();

            if (!$tmpLogo->isValid()){
                $form->get('file')->addError(new FormError($this->getTranslator()->trans('label.error.mimetype')));
            }
        }

        if ($form->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $logo->setSection($this->getSection());
            $logo->upload();

            $em->persist($logo);

            if (!$logo->isPublic()){
                $section = $this->getSection();
                $section->addLogo($logo);
            }else{
                /** @var Section $section */
                foreach($em->getRepository('MainBundle:Section')->findAll() as $section){
                    $section->addLogo($logo);
                }
            }

            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return  $this->render('MainBundle:Logo:upload.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
