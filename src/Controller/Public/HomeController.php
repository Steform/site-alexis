<?php

namespace App\Controller\Public;

use App\Repository\AvisRepository;
use App\Repository\HorairesRepository;
use App\Repository\MessageRepository;
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
    public function __construct(
        private readonly HorairesRepository $horairesRepository,
        private readonly AvisRepository $avisRepository,
        private readonly MessageRepository $messageRepository,
    ) {
    }

    /**
     * Renders the public home page with dynamic content.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('public/index.html.twig', [
            'horaires' => $this->horairesRepository->findAllOrdered(),
            'avis' => $this->avisRepository->findAllOrderedByDate(),
            'messages' => $this->messageRepository->findActive(),
        ]);
    }
}
