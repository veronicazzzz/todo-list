<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request): Response
    {
        $_username = "batman";
        $_password = "batmobil";

        $factory = $this->get('security.encoder_factory');

        $user_manager = $this->get('fos_user.user_manager');
        $user = $user_manager->findUserByUsername($_username);
        $user = $this->getDoctrine()->getManager()->getRepository("userBundle:User")
            ->findOneBy(array('username' => $_username));

        if(!$user){
            return new Response(
                'Username doesnt exists',
                Response::HTTP_UNAUTHORIZED,
                array('Content-type' => 'application/json')
            );
        }

        $encoder = $factory->getEncoder($user);
        $salt = $user->getSalt();

        if(!$encoder->isPasswordValid($user->getPassword(), $_password, $salt)) {
            return new Response(
                'Username or Password not valid.',
                Response::HTTP_UNAUTHORIZED,
                array('Content-type' => 'application/json')
            );
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);

        $this->get('session')->set('_security_main', serialize($token));

        return new Response(
            $token,
            Response::HTTP_OK,
            array('Content-type' => 'application/json')
        );
    }

    /**
     * @throws \Exception
     */
    public function logoutAction()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}