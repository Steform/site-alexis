<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @brief Back-office controller for editable site texts.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
#[IsGranted('ROLE_USER')]
class SiteTextsController extends AbstractController
{
    /**
     * @brief Displays the editable site texts index page.
     *
     * @return Response The response rendering the site texts index.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function index(): Response
    {
        return $this->render('back/texts/index.html.twig');
    }
}

