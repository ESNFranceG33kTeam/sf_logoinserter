<?php
/**
 * Created by PhpStorm.
 * User: jerem
 * Date: 18/01/16
 * Time: 18:56
 */

namespace MainBundle\Controller;


use MainBundle\Entity\Section;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Translation\Translator;
use UserBundle\Entity\User;

class BaseController extends Controller
{
    /**
     * @return Section
     */
    public function getSection(){
        return $this->getUser()->getSection();
    }

    /**
     * @return Translator
     */
    public function getTranslator(){
        return $this->get('translator');
    }
}