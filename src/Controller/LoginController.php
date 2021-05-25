<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class LoginController
 *
 * Kontroler obsługujący logowanie użytkownika;
 *
 * @package App\Controller
 */
class LoginController extends BasicController
{
    protected $page = [
        'title' => 'Logowanie'
    ];

    /**
     * Wyświetlanie strony/panelu logowania
     *
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $userPasswordEncoder) : Response
    {/*
        $user = new User();

        $password = $userPasswordEncoder->encodePassword($user, 'TR@s-2^~[J3m}"K!L8y,c^]y95wQFq;R');

        $user->setUsername('administrator');
        $user->setPassword($password);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setFirstName('Dawid');
        $user->setLastName('Jankowski');

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();*/

        // błędy zwracane przez bibliotekę sprawdzającą poprawność danych logowania;
        $error = $authenticationUtils->getLastAuthenticationError();

        // ostatnio użyta nazwa użytkownika;
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Operacja wylogowania użytkownika
     *
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return new RedirectResponse($this->generateUrl('app_login'));
    }
}