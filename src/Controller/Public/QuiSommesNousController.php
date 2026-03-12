<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Qui sommes-nous page - Alexis Haffner identity.
 *
 * @author Stephane H.
 * @created 2026-03-12
 *
 * @inputs  None
 * @outputs Qui sommes-nous page with Alexis identity
 */
class QuiSommesNousController extends AbstractController
{
    /**
     * Renders the "Qui sommes-nous" page with Alexis Haffner identity.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('public/qui_sommes_nous.html.twig');
    }
}
