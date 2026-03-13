<?php

namespace App\Controller\Public;

use App\Repository\HorairesRepository;
use App\Service\OpeningHoursFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public opening hours page.
 *
 * @author Stephane H.
 * @created 2026-03-12
 *
 * @inputs  Request, repositories
 * @outputs Horaires detail page
 */
class HorairesPublicController extends AbstractController
{
    public function __construct(
        private readonly HorairesRepository $horairesRepository,
        private readonly OpeningHoursFormatter $openingHoursFormatter,
    ) {
    }

    /**
     * Displays the detailed opening hours.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $horaires = $this->horairesRepository->findAllOrdered();
        $locale = $request->getLocale();

        return $this->render('public/horaires.html.twig', [
            'horaires_full' => $this->openingHoursFormatter->formatFull($horaires, $locale),
        ]);
    }
}

