<?php

namespace App\Controller\Back;

use App\Entity\AboutPhoto;
use App\Entity\AboutSection;
use App\Entity\HomeHeroPhoto;
use App\Entity\Service;
use App\Entity\ServicesWhyCard;
use App\Form\AboutPhotoType;
use App\Form\HomeHeroPhotoType;
use App\Form\ServicesWhyCardType;
use App\Repository\AboutPhotoRepository;
use App\Repository\AboutSectionRepository;
use App\Repository\ContentBlockHistoryRepository;
use App\Repository\DevisTypeCarburantRepository;
use App\Repository\DevisTypePrestationRepository;
use App\Repository\GalleryItemRepository;
use App\Repository\HomeHeroPhotoRepository;
use App\Repository\ServiceRepository;
use App\Repository\ServicesWhyCardRepository;
use App\Service\AboutPhotoImageProcessor;
use App\Service\ContentBlockManager;
use App\Service\HomeHeroPhotoImageProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @brief Back-office controller for generic CMS content pages.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
#[IsGranted('ROLE_USER')]
class ContentController extends AbstractController
{
    /**
     * @brief ContentController constructor.
     *
     * @param ContentBlockManager $contentBlockManager The content block manager.
     * @param ContentBlockHistoryRepository $historyRepository The content block history repository.
     * @param AboutPhotoRepository $aboutPhotoRepository The about photo repository.
     * @param AboutSectionRepository $aboutSectionRepository The about section repository.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param AboutPhotoImageProcessor $aboutPhotoImageProcessor The image processing service.
     * @param HomeHeroPhotoRepository $homeHeroPhotoRepository The home hero photo repository.
     * @param HomeHeroPhotoImageProcessor $homeHeroPhotoImageProcessor The home hero image processing service.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct(
        private readonly ContentBlockManager $contentBlockManager,
        private readonly ContentBlockHistoryRepository $historyRepository,
        private readonly AboutPhotoRepository $aboutPhotoRepository,
        private readonly AboutSectionRepository $aboutSectionRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly AboutPhotoImageProcessor $aboutPhotoImageProcessor,
        private readonly HomeHeroPhotoRepository $homeHeroPhotoRepository,
        private readonly HomeHeroPhotoImageProcessor $homeHeroPhotoImageProcessor,
        private readonly DevisTypePrestationRepository $devisTypePrestationRepository,
        private readonly DevisTypeCarburantRepository $devisTypeCarburantRepository,
        private readonly GalleryItemRepository $galleryItemRepository,
        private readonly ServicesWhyCardRepository $servicesWhyCardRepository,
        private readonly ServiceRepository $serviceRepository,
    ) {
    }

    /**
     * @brief Edits CMS blocks for the home page.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function homeFr(Request $request): Response
    {
        return $this->editPage($request, 'home', 'fr');
    }

    /**
     * @brief Edits CMS blocks for the home page in German.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function homeDe(Request $request): Response
    {
        return $this->editPage($request, 'home', 'de');
    }

    /**
     * @brief Edits CMS blocks for the who-we-are page in French.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function quiSommesNousFr(Request $request): Response
    {
        return $this->editPage($request, 'qui_sommes_nous', 'fr');
    }

    /**
     * @brief Edits CMS blocks for the who-we-are page in German.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function quiSommesNousDe(Request $request): Response
    {
        return $this->editPage($request, 'qui_sommes_nous', 'de');
    }

    /**
     * @brief Edits CMS blocks for the devis page (types de prestation) in French.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function devisFr(Request $request): Response
    {
        return $this->editPage($request, 'devis', 'fr');
    }

    /**
     * @brief Edits CMS blocks for the devis page (types de prestation) in German.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function devisDe(Request $request): Response
    {
        return $this->editPage($request, 'devis', 'de');
    }

    /**
     * @brief Edits CMS blocks for the gallery page in French.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function galleryFr(Request $request): Response
    {
        return $this->editPage($request, 'gallery', 'fr');
    }

    /**
     * @brief Edits CMS blocks for the gallery page in German.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function galleryDe(Request $request): Response
    {
        return $this->editPage($request, 'gallery', 'de');
    }

    /**
     * @brief Edits CMS blocks for the legal mentions page in French.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function mentionsLegalesFr(Request $request): Response
    {
        return $this->editPage($request, 'mentions_legales', 'fr');
    }

    /**
     * @brief Edits CMS blocks for the legal mentions page in German.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function mentionsLegalesDe(Request $request): Response
    {
        return $this->editPage($request, 'mentions_legales', 'de');
    }

    /**
     * @brief Edits CMS blocks for the services page in French.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function servicesFr(Request $request): Response
    {
        return $this->editPage($request, 'services', 'fr');
    }

    /**
     * @brief Edits CMS blocks for the services page in German.
     *
     * @param Request $request The HTTP request.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function servicesDe(Request $request): Response
    {
        return $this->editPage($request, 'services', 'de');
    }

    /**
     * @brief Edits CMS blocks for a service detail page in French.
     *
     * @param Request $request The HTTP request.
     * @param string $slug The service slug.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function serviceDetailFr(Request $request, string $slug): Response
    {
        $service = $this->serviceRepository->findBySlug($slug);
        if ($service === null) {
            throw $this->createNotFoundException('Service not found.');
        }

        return $this->editPage($request, $this->contentBlockManager->getServiceDetailPageName($service->getSlug()), 'fr', $service);
    }

    /**
     * @brief Edits CMS blocks for a service detail page in German.
     *
     * @param Request $request The HTTP request.
     * @param string $slug The service slug.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function serviceDetailDe(Request $request, string $slug): Response
    {
        $service = $this->serviceRepository->findBySlug($slug);
        if ($service === null) {
            throw $this->createNotFoundException('Service not found.');
        }

        return $this->editPage($request, $this->contentBlockManager->getServiceDetailPageName($service->getSlug()), 'de', $service);
    }

    /**
     * @brief Creates a new "Why choose us" card from the services content editor.
     *
     * @param Request $request The HTTP request.
     * @param string $locale The current locale.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function servicesWhyCardNew(Request $request, string $locale): Response
    {
        $card = new ServicesWhyCard();
        $card->setPosition($this->servicesWhyCardRepository->count([]));

        $form = $this->createForm(ServicesWhyCardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($card);
            $this->entityManager->flush();

            $this->addFlash('success', 'Carte ajoutée.');

            return $this->redirectToRoute($this->getContentRouteName('services', $locale));
        }

        return $this->render('back/content/services_why_card_form.html.twig', [
            'card' => $card,
            'form' => $form,
            'locale' => $locale,
        ]);
    }

    /**
     * @brief Edits a "Why choose us" card from the services content editor.
     *
     * @param Request $request The HTTP request.
     * @param ServicesWhyCard $card The card to edit.
     * @param string $locale The current locale.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function servicesWhyCardEdit(Request $request, ServicesWhyCard $card, string $locale): Response
    {
        $form = $this->createForm(ServicesWhyCardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Carte mise à jour.');

            return $this->redirectToRoute($this->getContentRouteName('services', $locale));
        }

        return $this->render('back/content/services_why_card_form.html.twig', [
            'card' => $card,
            'form' => $form,
            'locale' => $locale,
        ]);
    }

    /**
     * @brief Deletes a "Why choose us" card from the services content editor.
     *
     * @param Request $request The HTTP request.
     * @param ServicesWhyCard $card The card to delete.
     * @param string $locale The current locale.
     * @return Response The response.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function servicesWhyCardDelete(Request $request, ServicesWhyCard $card, string $locale): Response
    {
        if (!$this->isCsrfTokenValid('delete_services_why_card' . $card->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute($this->getContentRouteName('services', $locale));
        }

        $this->entityManager->remove($card);
        $this->entityManager->flush();

        $this->addFlash('success', 'Carte supprimée.');

        return $this->redirectToRoute($this->getContentRouteName('services', $locale));
    }

    /**
     * @brief Creates a new slider photo from the home content editor.
     *
     * @param Request $request The HTTP request.
     * @param string $locale The current locale.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function homePhotoNew(Request $request, string $locale): Response
    {
        $aboutSection = $this->aboutSectionRepository->findSingleton();
        if ($aboutSection === null) {
            $aboutSection = new AboutSection();
            $this->entityManager->persist($aboutSection);
        }

        $photo = new AboutPhoto();
        $photo->setPosition($this->aboutPhotoRepository->count([]));
        $photo->setIsActive(true);
        $photo->setAboutSection($aboutSection);

        $form = $this->createForm(AboutPhotoType::class, $photo, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->handleImageUploadFromContentEditor($form, $photo)) {
                return $this->render('back/content/photo_form.html.twig', [
                    'photo' => $photo,
                    'form' => $form,
                    'locale' => $locale,
                ]);
            }

            $this->entityManager->persist($photo);
            $this->entityManager->flush();

            $this->addFlash('success', 'Photo ajoutée.');

            return $this->redirectToRoute($this->getContentRouteName('home', $locale));
        }

        return $this->render('back/content/photo_form.html.twig', [
            'photo' => $photo,
            'form' => $form,
            'locale' => $locale,
        ]);
    }

    /**
     * @brief Edits a slider photo from the home content editor.
     *
     * @param Request $request The HTTP request.
     * @param AboutPhoto $photo The photo to edit.
     * @param string $locale The current locale.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function homePhotoEdit(Request $request, AboutPhoto $photo, string $locale): Response
    {
        $form = $this->createForm(AboutPhotoType::class, $photo, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->handleImageUploadFromContentEditor($form, $photo)) {
                return $this->render('back/content/photo_form.html.twig', [
                    'photo' => $photo,
                    'form' => $form,
                    'locale' => $locale,
                ]);
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Photo mise à jour.');

            return $this->redirectToRoute($this->getContentRouteName('home', $locale));
        }

        return $this->render('back/content/photo_form.html.twig', [
            'photo' => $photo,
            'form' => $form,
            'locale' => $locale,
        ]);
    }

    /**
     * @brief Deletes a slider photo from the home content editor.
     *
     * @param Request $request The HTTP request.
     * @param AboutPhoto $photo The photo to delete.
     * @param string $locale The current locale.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function homePhotoDelete(Request $request, AboutPhoto $photo, string $locale): Response
    {
        if (!$this->isCsrfTokenValid('delete_home_photo' . $photo->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute($this->getContentRouteName('home', $locale));
        }

        $this->entityManager->remove($photo);
        $this->entityManager->flush();

        $this->addFlash('success', 'Photo supprimée.');

        return $this->redirectToRoute($this->getContentRouteName('home', $locale));
    }

    /**
     * @brief Creates a new home hero slider photo.
     *
     * @param Request $request The HTTP request.
     * @param string $locale The current locale.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function homeHeroPhotoNew(Request $request, string $locale): Response
    {
        $photo = new HomeHeroPhoto();
        $photo->setPosition($this->homeHeroPhotoRepository->count([]));
        $photo->setIsActive(true);

        $form = $this->createForm(HomeHeroPhotoType::class, $photo, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->handleHeroImageUpload($form, $photo)) {
                return $this->render('back/content/hero_photo_form.html.twig', [
                    'photo' => $photo,
                    'form' => $form,
                    'locale' => $locale,
                ]);
            }

            $this->entityManager->persist($photo);
            $this->entityManager->flush();

            $this->addFlash('success', 'Photo du haut de page ajoutée.');

            return $this->redirectToRoute($this->getContentRouteName('home', $locale));
        }

        return $this->render('back/content/hero_photo_form.html.twig', [
            'photo' => $photo,
            'form' => $form,
            'locale' => $locale,
        ]);
    }

    /**
     * @brief Edits a home hero slider photo.
     *
     * @param Request $request The HTTP request.
     * @param HomeHeroPhoto $photo The photo to edit.
     * @param string $locale The current locale.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function homeHeroPhotoEdit(Request $request, HomeHeroPhoto $photo, string $locale): Response
    {
        $form = $this->createForm(HomeHeroPhotoType::class, $photo, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->handleHeroImageUpload($form, $photo)) {
                return $this->render('back/content/hero_photo_form.html.twig', [
                    'photo' => $photo,
                    'form' => $form,
                    'locale' => $locale,
                ]);
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Photo du haut de page mise à jour.');

            return $this->redirectToRoute($this->getContentRouteName('home', $locale));
        }

        return $this->render('back/content/hero_photo_form.html.twig', [
            'photo' => $photo,
            'form' => $form,
            'locale' => $locale,
        ]);
    }

    /**
     * @brief Deletes a home hero slider photo.
     *
     * @param Request $request The HTTP request.
     * @param HomeHeroPhoto $photo The photo to delete.
     * @param string $locale The current locale.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function homeHeroPhotoDelete(Request $request, HomeHeroPhoto $photo, string $locale): Response
    {
        if (!$this->isCsrfTokenValid('delete_home_hero_photo' . $photo->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute($this->getContentRouteName('home', $locale));
        }

        $this->entityManager->remove($photo);
        $this->entityManager->flush();

        $this->addFlash('success', 'Photo du haut de page supprimée.');

        return $this->redirectToRoute($this->getContentRouteName('home', $locale));
    }

    /**
     * @brief Handles generic CMS page edition.
     *
     * @param Request $request The HTTP request.
     * @param string $pageName The page name.
     * @param string $locale The selected locale.
     * @param Service|null $currentService The current service when editing a service detail page.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function editPage(Request $request, string $pageName, string $locale, ?Service $currentService = null): Response
    {
        $definitions = $this->contentBlockManager->getPageDefinitions($pageName);
        if (!$this->contentBlockManager->hasPage($pageName)) {
            throw $this->createNotFoundException('Unknown CMS page.');
        }

        if ($request->isMethod('POST')) {
            $submitted = (array) $request->request->all('content');
            $colors = (array) $request->request->all('colors');
            $colorsDark = (array) $request->request->all('colors_dark');

            $this->contentBlockManager->savePageContentForLocale(
                $pageName,
                $locale,
                $submitted,
                $colors,
                $colorsDark,
                true,
                $this->getUser()
            );
            $this->addFlash('success', 'Contenus enregistrés.');

            $routeName = $this->getContentRouteName($pageName, $locale);
            $routeParams = $this->getContentRouteParams($pageName, $currentService);

            return $this->redirectToRoute($routeName, $routeParams);
        }

        $isServicesContext = $pageName === 'services' || str_starts_with($pageName, 'service_');

        return $this->render('back/content/edit.html.twig', [
            'page_name' => $pageName,
            'locale' => $locale,
            'definitions' => $definitions,
            'content_values' => $this->contentBlockManager->getEditorDataForLocale($pageName, $locale),
            'content_colors' => $this->contentBlockManager->getEditorColors($pageName),
            'about_photos' => $pageName === 'home' ? $this->aboutPhotoRepository->findAllOrdered() : [],
            'home_hero_photos' => $pageName === 'home' ? $this->homeHeroPhotoRepository->findAllOrdered() : [],
            'devis_type_prestations' => $pageName === 'devis' ? $this->devisTypePrestationRepository->findAllOrdered() : [],
            'devis_type_carburants' => $pageName === 'devis' ? $this->devisTypeCarburantRepository->findAllOrdered() : [],
            'gallery_items' => $pageName === 'gallery' ? $this->galleryItemRepository->findAllOrdered() : [],
            'services_why_cards' => $pageName === 'services' ? $this->servicesWhyCardRepository->findAllOrdered() : [],
            'services' => $isServicesContext ? $this->serviceRepository->findAllOrdered() : [],
            'current_service' => $currentService,
        ]);
    }

    /**
     * @brief Handles image upload for content editor photo actions.
     *
     * @param \Symfony\Component\Form\FormInterface $form The submitted form.
     * @param AboutPhoto $photo The photo entity.
     * @return bool True when upload processing is successful.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function handleImageUploadFromContentEditor($form, AboutPhoto $photo): bool
    {
        $file = $form->get('imageFile')->getData();
        if ($file === null) {
            return true;
        }

        try {
            $path = $this->aboutPhotoImageProcessor->processToWebp(
                $file,
                (string) $this->getParameter('kernel.project_dir')
            );
            $photo->setImage($path);
            return true;
        } catch (\RuntimeException|FileException) {
            $this->addFlash('error', 'Erreur lors du traitement de la photo (conversion WebP 960x550).');
            return false;
        }
    }

    /**
     * @brief Handles image upload for home hero photo actions.
     *
     * @param \Symfony\Component\Form\FormInterface $form The submitted form.
     * @param HomeHeroPhoto $photo The photo entity.
     * @return bool True when upload processing is successful.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function handleHeroImageUpload($form, HomeHeroPhoto $photo): bool
    {
        $file = $form->get('imageFile')->getData();
        if ($file === null) {
            return true;
        }

        try {
            $path = $this->homeHeroPhotoImageProcessor->processToWebp(
                $file,
                (string) $this->getParameter('kernel.project_dir')
            );
            $photo->setImage($path);
            return true;
        } catch (\RuntimeException|FileException) {
            $this->addFlash('error', 'Erreur lors du traitement de la photo du haut de page (conversion WebP).');
            return false;
        }
    }

    /**
     * @brief Displays content block modification history.
     *
     * @param Request $request The HTTP request (for filters).
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function history(Request $request): Response
    {
        $pageFilter = $request->query->get('page');
        $localeFilter = $request->query->get('locale');
        $entries = $this->historyRepository->findAllOrdered(200, $pageFilter ?: null, $localeFilter ?: null);

        return $this->render('back/content/history.html.twig', [
            'history_entries' => $entries,
            'page_filter' => $pageFilter,
            'locale_filter' => $localeFilter,
        ]);
    }

    /**
     * @brief Restores a content block to a previous state from history.
     *
     * @param Request $request The HTTP request.
     * @param int $id The history entry ID.
     * @return Response The response.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function rollback(Request $request, int $id): Response
    {
        if (!$this->isCsrfTokenValid('rollback_content_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_back_content_history');
        }

        try {
            $this->contentBlockManager->rollbackToHistory($id, $this->getUser());
            $this->addFlash('success', 'Version restaurée.');
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_back_content_history');
    }

    /**
     * @brief Returns the route name for one page and locale.
     *
     * @param string $pageName The page name.
     * @param string $locale The locale.
     * @return string The route name.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function getContentRouteName(string $pageName, string $locale): string
    {
        if (str_starts_with($pageName, 'service_')) {
            return $locale === 'fr' ? 'app_back_content_service_detail_fr' : 'app_back_content_service_detail_de';
        }

        $routes = [
            'home' => [
                'fr' => 'app_back_content_home_fr',
                'de' => 'app_back_content_home_de',
            ],
            'qui_sommes_nous' => [
                'fr' => 'app_back_content_qui_sommes_nous_fr',
                'de' => 'app_back_content_qui_sommes_nous_de',
            ],
            'devis' => [
                'fr' => 'app_back_content_devis_fr',
                'de' => 'app_back_content_devis_de',
            ],
            'gallery' => [
                'fr' => 'app_back_content_gallery_fr',
                'de' => 'app_back_content_gallery_de',
            ],
            'services' => [
                'fr' => 'app_back_content_services_fr',
                'de' => 'app_back_content_services_de',
            ],
            'mentions_legales' => [
                'fr' => 'app_back_content_mentions_legales_fr',
                'de' => 'app_back_content_mentions_legales_de',
            ],
        ];

        return $routes[$pageName][$locale] ?? 'app_back_dashboard';
    }

    /**
     * @brief Returns route parameters for redirect (e.g. slug for service detail).
     *
     * @param string $pageName The page name.
     * @param Service|null $currentService The current service when editing a service detail page.
     * @return array<string, mixed> Route parameters.
     * @date 2026-03-21
     * @author Stephane H.
     */
    private function getContentRouteParams(string $pageName, ?Service $currentService): array
    {
        if (str_starts_with($pageName, 'service_') && $currentService !== null) {
            return ['slug' => $currentService->getSlug()];
        }

        return [];
    }
}

