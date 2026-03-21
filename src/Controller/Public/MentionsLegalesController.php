<?php

namespace App\Controller\Public;

use App\Repository\CoordinatesRepository;
use App\Service\ContentBlockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @brief Renders the legal mentions page (editable via Content).
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
class MentionsLegalesController extends AbstractController
{
    public function __construct(
        private readonly ContentBlockManager $contentBlockManager,
        private readonly CoordinatesRepository $coordinatesRepository,
    ) {
    }

    /**
     * @brief Renders the legal mentions page.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();
        $contentLocale = str_starts_with($locale, 'de') ? 'de' : 'fr';

        return $this->render('public/mentions_legales.html.twig', [
            'mentions_content' => $this->contentBlockManager->getPageContent('mentions_legales', $contentLocale),
            'coordinates' => $this->coordinatesRepository->findSingle(),
        ]);
    }
}
