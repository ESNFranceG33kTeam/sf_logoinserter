<?php

namespace MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use phpCAS;

class LogoController extends Controller
{
    public function indexAction()
    {
        return $this->render('MainBundle:Logo:index.html.twig');
    }

    public function uploadAction()
    {
        return $this->render('MainBundle:Logo:upload.html.twig');
    }
}
