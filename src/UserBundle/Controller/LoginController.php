<?php

namespace UserBundle\Controller;

use Esn\EsnBundle\Entity\GalaxyUser;
use MainBundle\Entity\Section;
use Esn\EsnBundle\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Translation\Translator;
use UserBundle\Entity\User;

class LoginController extends Controller
{

    public function loginAction(){
        return $this->render('UserBundle:Login:login.html.twig');
    }
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user_cas = null;

        /** @var UserProvider $up */
        $up = new UserProvider($this->container);

        //$connected = @fsockopen("www.galaxy.esn.org", 80);
        $connected = false;

        if ($connected){
            $user_cas = $up->loadGalaxyUser();
            fclose($connected);
        }else{
            $user_cas = $em->getRepository("UserBundle:User")->findOneBy(array("email" => "claupcsilva@gmail.com"));
        }

        if ($user_cas != null){
            $user_db = $em->getRepository("UserBundle:User")->findOneBy(array("email" => $user_cas->getEmail()));

            /** @var User $user */
            $user = (!$user_db) ? new User($user_cas->getGalaxyUsername(), $up->getAttributes()) : $user_db;

            /** @var Section $section */
            $section = $em->getRepository("MainBundle:Section")->findOneBy(array("code" => $user_cas->getSectionCode()));

            if (!$section){
                throw new Exception('Section not found');
            }

            if (!$user_db) {
                $user->setEnabled(true);
                $user->setRoles(array('ROLE_USER'));
                $user->setPassword("nopassword");
                $section->addUser($user);
                $em->persist($user);
            }

            $em->flush();

            $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
            $this->get("security.context")->setToken($token);

            /** @var Request $request */
            $request = $this->get("request");
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            $translator = new Translator('fr_FR');
            $this->addFlash('success ', $translator->trans('label.success.login', array(), 'messages'));

            $route = ($user->getSection()->hasSectionLogo()) ? "homepage" : "upload_logo";

            return $this->redirect($this->generateUrl($route));
        }

        $translator = new Translator('fr_FR');
        $this->addFlash('error ', $translator->trans('label.error.login'));

        return $this->redirect($this->generateUrl('login'));
    }

    public function logoutAction(){
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();

        return $this->redirect($this->generateUrl('faucondor_login'));
    }
}
