<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Back-office dashboard.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('back/dashboard.html.twig');
    }
}
