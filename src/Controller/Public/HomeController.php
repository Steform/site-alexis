<?php

namespace App\Controller\Public;

use App\Service\OpeningHoursFormatter;
use App\Repository\AvisRepository;
use App\Repository\AboutPhotoRepository;
use App\Repository\HomeHeroPhotoRepository;
use App\Repository\ServiceRepository;
use App\Service\ContentBlockManager;
use App\Repository\HorairesRepository;
use App\Repository\MessageRepository;
use App\Service\HtmlContentSanitizer;
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
        private readonly AboutPhotoRepository $aboutPhotoRepository,
        private readonly HomeHeroPhotoRepository $homeHeroPhotoRepository,
        private readonly ContentBlockManager $contentBlockManager,
        private readonly MessageRepository $messageRepository,
        private readonly OpeningHoursFormatter $openingHoursFormatter,
        private readonly HtmlContentSanitizer $htmlContentSanitizer,
        private readonly ServiceRepository $serviceRepository,
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
        $contentLocale = str_starts_with($locale, 'de') ? 'de' : 'fr';

        $homeContent = $this->contentBlockManager->getPageContent('home', $contentLocale);
        $homeColors = $this->contentBlockManager->getPageColors('home', $contentLocale);
        $homeContent['about.body'] = $this->htmlContentSanitizer->sanitize($homeContent['about.body'] ?? '');
        $heroPhotos = $this->homeHeroPhotoRepository->findActiveOrdered();
        $aboutPhotos = $this->aboutPhotoRepository->findActiveOrdered();

        return $this->render('public/index.html.twig', [
            'horaires' => $horaires,
            'horaires_compact' => $this->openingHoursFormatter->formatCompact($horaires, $locale),
            'avis' => $this->avisRepository->findAllOrderedByDate(),
            'messages' => $this->messageRepository->findActive(),
            'home_content' => $homeContent,
            'home_colors' => $homeColors,
            'hero_photos' => $heroPhotos,
            'about_photos' => $aboutPhotos,
            'services' => $this->serviceRepository->findAllOrdered(),
        ]);
    }
}
