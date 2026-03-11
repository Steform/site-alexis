<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public home page.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
class HomeController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('public/index.html.twig');
    }
}
