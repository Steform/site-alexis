<?php

namespace App\Controller\Public;

use App\Repository\AvisRepository;
use App\Repository\HorairesRepository;
use App\Repository\ServiceProcessStepRepository;
use App\Repository\ServiceRepository;
use App\Repository\ServicesWhyCardRepository;
use App\Service\ContentBlockManager;
use App\Service\OpeningHoursFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Public services pages (list and detail).
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
class ServicesController extends AbstractController
{
    public function __construct(
        private readonly ServiceRepository $serviceRepository,
        private readonly AvisRepository $avisRepository,
        private readonly HorairesRepository $horairesRepository,
        private readonly OpeningHoursFormatter $openingHoursFormatter,
        private readonly ContentBlockManager $contentBlockManager,
        private readonly ServicesWhyCardRepository $servicesWhyCardRepository,
        private readonly ServiceProcessStepRepository $serviceProcessStepRepository,
    ) {
    }

    /**
     * Renders the services list page.
     *
     * @param Request $request The HTTP request.
     * @return Response
     */
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();
        $contentLocale = str_starts_with($locale, 'de') ? 'de' : 'fr';
        $horaires = $this->horairesRepository->findAllOrdered();

        $services = $this->serviceRepository->findAllOrdered();

        return $this->render('public/services/index.html.twig', [
            'services' => $services,
            'avis' => $this->avisRepository->findAllOrderedByDate(),
            'horaires' => $horaires,
            'horaires_compact' => $this->openingHoursFormatter->formatCompact($horaires, $locale),
            'services_content' => $this->contentBlockManager->getPageContent('services', $contentLocale),
            'home_page_content' => $this->contentBlockManager->getPageContent('home', $contentLocale),
            'services_why_cards' => $this->servicesWhyCardRepository->findAllOrdered(),
        ]);
    }

    /**
     * Renders a single service detail page by slug.
     *
     * @param Request $request The HTTP request.
     * @param string $slug The service slug.
     * @return Response
     */
    public function show(Request $request, string $slug): Response
    {
        $service = $this->serviceRepository->findBySlug($slug);

        if (!$service) {
            throw new NotFoundHttpException();
        }

        $locale = $request->getLocale();
        $contentLocale = str_starts_with($locale, 'de') ? 'de' : 'fr';
        $serviceContent = $this->contentBlockManager->getPageContent(
            $this->contentBlockManager->getServiceDetailPageName($service->getSlug()),
            $contentLocale
        );

        if (empty(trim((string) ($serviceContent['description'] ?? '')))) {
            $serviceContent['description'] = $contentLocale === 'de'
                ? ($service->getDescriptionDe() ?? $service->getDescription())
                : ($service->getDescription() ?? $service->getDescriptionDe());
        }

        $allServices = $this->serviceRepository->findAllOrdered();
        $otherServices = array_values(array_filter($allServices, fn ($s) => $s->getId() !== $service->getId()));

        $vehiculePret = null;
        $others = [];
        foreach ($otherServices as $s) {
            if ($s->getSlug() === 'vehicule-pret-courtoisie') {
                $vehiculePret = $s;
            } else {
                $others[] = $s;
            }
        }
        if ($vehiculePret !== null) {
            $others[] = $vehiculePret;
        }
        $otherServices = $others;

        return $this->render('public/services/show.html.twig', [
            'service' => $service,
            'other_services' => $otherServices,
            'service_content' => $serviceContent,
            'home_page_content' => $this->contentBlockManager->getPageContent('home', $contentLocale),
            'service_process_steps' => $this->serviceProcessStepRepository->findByServiceOrdered($service),
        ]);
    }
}
