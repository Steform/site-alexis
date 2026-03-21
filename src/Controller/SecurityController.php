<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Security controller (login, logout).
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
class SecurityController extends AbstractController
{
    /**
     * @brief Displays the login form, or redirects to setup if no users exist.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param UserRepository $userRepository
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        if ($userRepository->count([]) === 0) {
            return $this->redirectToRoute('app_setup');
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('app_back_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
