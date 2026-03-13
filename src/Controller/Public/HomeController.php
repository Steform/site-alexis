<?php

namespace App\Controller\Public;

use App\Service\OpeningHoursFormatter;
use App\Repository\AvisRepository;
use App\Repository\HorairesRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        private readonly OpeningHoursFormatter $openingHoursFormatter,
    ) {
    }

    /**
     * Renders the public home page with dynamic content.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $horaires = $this->horairesRepository->findAllOrdered();
        $locale = $request->getLocale();

        return $this->render('public/index.html.twig', [
            'horaires' => $horaires,
            'horaires_compact' => $this->openingHoursFormatter->formatCompact($horaires, $locale),
            'avis' => $this->avisRepository->findAllOrderedByDate(),
            'messages' => $this->messageRepository->findActive(),
        ]);
    }
}
