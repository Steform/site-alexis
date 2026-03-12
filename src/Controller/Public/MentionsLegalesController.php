<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mentions légales page (placeholder - content to be added later).
 *
 * @author Stephane H.
 * @created 2026-03-11
 *
 * @inputs  None
 * @outputs Mentions légales page
 */
class MentionsLegalesController extends AbstractController
{
    /**
     * Renders the legal mentions page.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('public/mentions_legales.html.twig');
    }
}
