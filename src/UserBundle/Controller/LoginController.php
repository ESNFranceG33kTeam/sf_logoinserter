<?php

namespace UserBundle\Controller;

use Esn\EsnBundle\Model\Section;
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
        $em = $this->getDoctrine()->getEntityManager();
        $user_cas = null;

        $cas_host = $this->container->getParameter('cas_host');
        $cas_port = $this->container->getParameter('cas_port');
        $cas_context = $this->container->getParameter('cas_context');

        /** @var UserProvider $up */
        $up = new UserProvider($cas_host, $cas_context, $cas_port);

        $user_cas = $up->loadGalaxyUser();

        if ($user_cas != null){
            $user_db = $em->getRepository("UserBundle:User")->findOneBy(array("email" => $user_cas->getEmail()));
            $user = (!$user_db) ? new User() : $user_db;

            /** @var Section $section */
            $section = $em->getRepository("MainBundle:Section")->findOneBy(array("code" => $user_cas->getSc()));

            if (!$section){
                throw new Exception('Section not found');
            }

            $user->setUsername($user_cas->getEmail());
            $user->setUsernameCanonical($user_cas->getEmail());
            $user->setEmail($user_cas->getEmail());
            $user->setGalaxyRoles(implode(",", $user_cas->getRoles()));
            $user->setFirstname($user_cas->getFirstname());
            $user->setLastname($user_cas->getLastname());
            $user->setBirthdate(\DateTime::createFromFormat("d/m/Y", $user_cas->getBirthdate()));
            $user->setGender($user_cas->getGender());
            $user->setGalaxyPicture($user_cas->getPicture());
            $user->setMobile($user_cas->getTelephone());

            if (!$user_db) {
                $user->setEnabled(true);
                $user->setRoles(array('ROLE_USER'));
                $user->setRandomPassword();
                //$section->addUser($user);
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

            return $this->redirect($this->generateUrl('faucondor_homepage'));
        }

        $translator = new Translator('fr_FR');
        $this->addFlash('error ', $translator->trans('label.error.login'));

        return $this->redirect($this->generateUrl('faucondor_login'));
    }

    public function logoutAction(){
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();

        return $this->redirect($this->generateUrl('faucondor_login'));
    }

    /**
     * @param User $user
     *
     * @return GalaxyUser
     */
    private function userTransformer(User $user){
        $username = $user->getUsername();
        $attributes = array();

        $attributes['mail'] = $user->getEmail();
        $attributes['first'] = $user->getFirstname();
        $attributes['last'] = $user->getLastname();
        $attributes['nationality'] = $user->getSection()->getCountry();
        $attributes['picture'] = $user->getGalaxyPicture();
        $attributes['birthdate'] = $user->getBirthdate()->format('d/m/Y');
        $attributes['gender'] = $user->getGender();
        $attributes['telephone'] = $user->getMobile();
        $attributes['address'] = $user->getAddress();
        $attributes['section'] = $user->getSection()->getCode();
        $attributes['country'] = $user->getSection()->getCountry();
        $attributes['sc'] = $user->getSection()->getCode();
        $attributes['roles'] = explode(',', $user->getGalaxyRoles());

        return new GalaxyUser($username, $attributes);
    }
}
