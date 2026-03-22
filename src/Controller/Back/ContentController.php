<?php

namespace App\Controller\Back;

use App\Entity\AboutPhoto;
use App\Entity\AboutSection;
use App\Entity\HomeHeroPhoto;
use App\Entity\Service;
use App\Entity\ServiceProcessStep;
use App\Entity\ServicesWhyCard;
use App\Form\AboutPhotoType;
use App\Form\HomeHeroPhotoType;
use App\Form\ServiceProcessStepType;
use App\Form\ServicesWhyCardType;
use App\Repository\AboutPhotoRepository;
use App\Repository\AboutSectionRepository;
use App\Repository\DevisTypeCarburantRepository;
use App\Repository\DevisTypePrestationRepository;
use App\Repository\GalleryItemRepository;
use App\Repository\HomeHeroPhotoRepository;
use App\Repository\ServiceProcessStepRepository;
use App\Repository\ServiceRepository;
use App\Repository\ServicesWhyCardRepository;
use App\Service\AboutPhotoImageProcessor;
use App\Service\ArchivedUploadService;
use App\Service\ContentBlockManager;
use App\Service\HistoryMergeService;
use App\Service\HomeHeroPhotoImageProcessor;
use App\Service\HomeServiceCardImageProcessor;
use App\Service\ServiceDetailHeroImageProcessor;
use App\Service\ServiceTeaserImageProcessor;
use App\Service\AdminAuditLogger;
use App\Service\AdminAuditActions;
use App\Service\EntitySnapshotDomain;
use App\Service\EntitySnapshotRecorder;
use App\Service\EntitySnapshotRollbackService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @param HistoryMergeService $historyMergeService The merged timeline builder.
     * @param AdminAuditLogger $adminAuditLogger The admin audit logger.
     * @param AboutPhotoRepository $aboutPhotoRepository The about photo repository.
     * @param AboutSectionRepository $aboutSectionRepository The about section repository.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param AboutPhotoImageProcessor $aboutPhotoImageProcessor The image processing service.
     * @param HomeHeroPhotoRepository $homeHeroPhotoRepository The home hero photo repository.
     * @param HomeHeroPhotoImageProcessor $homeHeroPhotoImageProcessor The home hero image processing service.
     * @param HomeServiceCardImageProcessor $homeServiceCardImageProcessor The home service card image processor.
     * @param ServiceTeaserImageProcessor $serviceTeaserImageProcessor The services list teaser image processor.
     * @param ServiceDetailHeroImageProcessor $serviceDetailHeroImageProcessor The service detail page hero image processor.
     * @param DevisTypePrestationRepository $devisTypePrestationRepository The quote service type repository.
     * @param DevisTypeCarburantRepository $devisTypeCarburantRepository The quote fuel type repository.
     * @param GalleryItemRepository $galleryItemRepository The gallery repository.
     * @param ServicesWhyCardRepository $servicesWhyCardRepository The services why-card repository.
     * @param ServiceProcessStepRepository $serviceProcessStepRepository The service process step repository.
     * @param ServiceRepository $serviceRepository The service repository.
     * @param ArchivedUploadService $archivedUploadService The deleted upload archiver.
     * @param TranslatorInterface $translator The translator.
     * @param EntitySnapshotRecorder $entitySnapshotRecorder The entity snapshot recorder.
     * @param EntitySnapshotRollbackService $entitySnapshotRollbackService The entity snapshot rollback service.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct(
        private readonly ContentBlockManager $contentBlockManager,
        private readonly HistoryMergeService $historyMergeService,
        private readonly AdminAuditLogger $adminAuditLogger,
        private readonly AboutPhotoRepository $aboutPhotoRepository,
        private readonly AboutSectionRepository $aboutSectionRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly AboutPhotoImageProcessor $aboutPhotoImageProcessor,
        private readonly HomeHeroPhotoRepository $homeHeroPhotoRepository,
        private readonly HomeHeroPhotoImageProcessor $homeHeroPhotoImageProcessor,
        private readonly HomeServiceCardImageProcessor $homeServiceCardImageProcessor,
        private readonly ServiceTeaserImageProcessor $serviceTeaserImageProcessor,
        private readonly ServiceDetailHeroImageProcessor $serviceDetailHeroImageProcessor,
        private readonly DevisTypePrestationRepository $devisTypePrestationRepository,
        private readonly DevisTypeCarburantRepository $devisTypeCarburantRepository,
        private readonly GalleryItemRepository $galleryItemRepository,
        private readonly ServicesWhyCardRepository $servicesWhyCardRepository,
        private readonly ServiceProcessStepRepository $serviceProcessStepRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly ArchivedUploadService $archivedUploadService,
        private readonly TranslatorInterface $translator,
        private readonly EntitySnapshotRecorder $entitySnapshotRecorder,
        private readonly EntitySnapshotRollbackService $entitySnapshotRollbackService,
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
     * @brief Creates a new process step for a service detail page.
     *
     * @param Request $request The HTTP request.
     * @param string $locale The editor locale (fr|de).
     * @param string $slug The service slug (FR slug).
     * @return Response The response.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function serviceProcessStepNew(Request $request, string $locale, string $slug): Response
    {
        $service = $this->serviceRepository->findBySlug($slug);
        if ($service === null) {
            throw $this->createNotFoundException('Service not found.');
        }

        $step = new ServiceProcessStep();
        $step->setService($service);
        $step->setPosition($this->serviceProcessStepRepository->countByService($service));

        $form = $this->createForm(ServiceProcessStepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (trim($step->getLabelFr()) === '' && trim($step->getLabelDe()) === '') {
                $this->addFlash('error', $this->translator->trans('back.service_process_step.validation_labels_empty', [], 'back'));
            } else {
                $this->entityManager->persist($step);
                $this->entityManager->flush();

                $this->entitySnapshotRecorder->recordAfterCreate($this->entityManager, $step, EntitySnapshotDomain::SERVICE_PROCESS_STEP, $this->getUser());
                $this->entityManager->flush();

                $this->adminAuditLogger->log(AdminAuditActions::SERVICE_PROCESS_STEP_CREATE, [
                    'id' => $step->getId(),
                    'serviceId' => $service->getId(),
                    'position' => $step->getPosition(),
                ], $this->getUser());

                $this->addFlash('success', $this->translator->trans('back.service_process_step.flash.created', [], 'back'));

                return $this->redirectToRoute(
                    $this->getContentRouteName($this->contentBlockManager->getServiceDetailPageName($service->getSlug()), $locale),
                    $this->getContentRouteParams($this->contentBlockManager->getServiceDetailPageName($service->getSlug()), $service)
                );
            }
        }

        return $this->render('back/content/service_process_step_form.html.twig', [
            'step' => $step,
            'form' => $form,
            'locale' => $locale,
            'service' => $service,
        ]);
    }

    /**
     * @brief Edits a process step for a service detail page.
     *
     * @param Request $request The HTTP request.
     * @param string $locale The editor locale (fr|de).
     * @param string $slug The service slug (FR slug).
     * @param ServiceProcessStep $step The step entity.
     * @return Response The response.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function serviceProcessStepEdit(Request $request, string $locale, string $slug, ServiceProcessStep $step): Response
    {
        $service = $this->serviceRepository->findBySlug($slug);
        if ($service === null || $step->getService() === null || $step->getService()->getId() !== $service->getId()) {
            throw $this->createNotFoundException('Service or step not found.');
        }

        $form = $this->createForm(ServiceProcessStepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (trim($step->getLabelFr()) === '' && trim($step->getLabelDe()) === '') {
                $this->addFlash('error', $this->translator->trans('back.service_process_step.validation_labels_empty', [], 'back'));
            } else {
                $this->entitySnapshotRecorder->recordBeforeUpdate($this->entityManager, $step, EntitySnapshotDomain::SERVICE_PROCESS_STEP, $this->getUser());
                $this->entityManager->flush();

                $this->adminAuditLogger->log(AdminAuditActions::SERVICE_PROCESS_STEP_UPDATE, [
                    'id' => $step->getId(),
                    'serviceId' => $service->getId(),
                    'position' => $step->getPosition(),
                ], $this->getUser());

                $this->addFlash('success', $this->translator->trans('back.service_process_step.flash.updated', [], 'back'));

                return $this->redirectToRoute(
                    $this->getContentRouteName($this->contentBlockManager->getServiceDetailPageName($service->getSlug()), $locale),
                    $this->getContentRouteParams($this->contentBlockManager->getServiceDetailPageName($service->getSlug()), $service)
                );
            }
        }

        return $this->render('back/content/service_process_step_form.html.twig', [
            'step' => $step,
            'form' => $form,
            'locale' => $locale,
            'service' => $service,
        ]);
    }

    /**
     * @brief Deletes a process step from a service detail page.
     *
     * @param Request $request The HTTP request.
     * @param string $locale The editor locale (fr|de).
     * @param string $slug The service slug (FR slug).
     * @param ServiceProcessStep $step The step entity.
     * @return Response The response.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function serviceProcessStepDelete(Request $request, string $locale, string $slug, ServiceProcessStep $step): Response
    {
        $service = $this->serviceRepository->findBySlug($slug);
        if ($service === null || $step->getService() === null || $step->getService()->getId() !== $service->getId()) {
            throw $this->createNotFoundException('Service or step not found.');
        }

        if (!$this->isCsrfTokenValid('delete_service_process_step' . $step->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');

            return $this->redirectToRoute(
                $this->getContentRouteName($this->contentBlockManager->getServiceDetailPageName($service->getSlug()), $locale),
                $this->getContentRouteParams($this->contentBlockManager->getServiceDetailPageName($service->getSlug()), $service)
            );
        }

        $this->entitySnapshotRecorder->recordBeforeDelete($this->entityManager, $step, EntitySnapshotDomain::SERVICE_PROCESS_STEP, $this->getUser());

        $this->adminAuditLogger->log(AdminAuditActions::SERVICE_PROCESS_STEP_DELETE, [
            'id' => $step->getId(),
            'serviceId' => $service->getId(),
        ], $this->getUser());

        $this->entityManager->remove($step);
        $this->entityManager->flush();

        $this->addFlash('success', $this->translator->trans('back.service_process_step.flash.deleted', [], 'back'));

        return $this->redirectToRoute(
            $this->getContentRouteName($this->contentBlockManager->getServiceDetailPageName($service->getSlug()), $locale),
            $this->getContentRouteParams($this->contentBlockManager->getServiceDetailPageName($service->getSlug()), $service)
        );
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

            $this->entitySnapshotRecorder->recordAfterCreate($this->entityManager, $card, EntitySnapshotDomain::SERVICES_WHY_CARD, $this->getUser());
            $this->entityManager->flush();

            $this->adminAuditLogger->log(AdminAuditActions::SERVICES_WHY_CARD_CREATE, [
                'id' => $card->getId(),
                'position' => $card->getPosition(),
                'titleFr' => (string) ($card->getTitleFr() ?? ''),
            ], $this->getUser());

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
            $this->entitySnapshotRecorder->recordBeforeUpdate($this->entityManager, $card, EntitySnapshotDomain::SERVICES_WHY_CARD, $this->getUser());
            $this->entityManager->flush();

            $this->adminAuditLogger->log(AdminAuditActions::SERVICES_WHY_CARD_UPDATE, [
                'id' => $card->getId(),
                'position' => $card->getPosition(),
                'titleFr' => (string) ($card->getTitleFr() ?? ''),
            ], $this->getUser());

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

        $this->entitySnapshotRecorder->recordBeforeDelete($this->entityManager, $card, EntitySnapshotDomain::SERVICES_WHY_CARD, $this->getUser());

        $this->adminAuditLogger->log(AdminAuditActions::SERVICES_WHY_CARD_DELETE, [
            'id' => $card->getId(),
            'titleFr' => (string) ($card->getTitleFr() ?? ''),
        ], $this->getUser());

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
        $photo->setPosition($this->aboutPhotoRepository->getNextPosition());
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

            $this->entitySnapshotRecorder->recordAfterCreate($this->entityManager, $photo, EntitySnapshotDomain::ABOUT_PHOTO, $this->getUser());
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
            $this->entitySnapshotRecorder->recordBeforeUpdate($this->entityManager, $photo, EntitySnapshotDomain::ABOUT_PHOTO, $this->getUser());
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

        $this->entitySnapshotRecorder->recordBeforeDelete($this->entityManager, $photo, EntitySnapshotDomain::ABOUT_PHOTO, $this->getUser());

        try {
            $meta = json_encode([
                'reason' => 'delete',
                'entityId' => $photo->getId(),
                'altFr' => $photo->getAltFr(),
                'altDe' => $photo->getAltDe(),
            ], \JSON_THROW_ON_ERROR);
            $this->archivedUploadService->archiveAndRecord(
                $photo->getImage(),
                ArchivedUploadService::CONTEXT_ABOUT,
                $this->getUser(),
                $meta
            );
        } catch (\JsonException|\RuntimeException) {
            $this->addFlash('error', $this->translator->trans('back.content.upload_delete_archive_error', [], 'back'));

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
        $photo->setPosition($this->homeHeroPhotoRepository->getNextPosition());
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

            $this->entitySnapshotRecorder->recordAfterCreate($this->entityManager, $photo, EntitySnapshotDomain::HOME_HERO_PHOTO, $this->getUser());
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
            $this->entitySnapshotRecorder->recordBeforeUpdate($this->entityManager, $photo, EntitySnapshotDomain::HOME_HERO_PHOTO, $this->getUser());
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

        $this->entitySnapshotRecorder->recordBeforeDelete($this->entityManager, $photo, EntitySnapshotDomain::HOME_HERO_PHOTO, $this->getUser());

        try {
            $meta = json_encode([
                'reason' => 'delete',
                'entityId' => $photo->getId(),
                'altFr' => $photo->getAltFr(),
                'altDe' => $photo->getAltDe(),
            ], \JSON_THROW_ON_ERROR);
            $this->archivedUploadService->archiveAndRecord(
                $photo->getImage(),
                ArchivedUploadService::CONTEXT_HOME_HERO,
                $this->getUser(),
                $meta
            );
        } catch (\JsonException|\RuntimeException) {
            $this->addFlash('error', $this->translator->trans('back.content.upload_delete_archive_error', [], 'back'));

            return $this->redirectToRoute($this->getContentRouteName('home', $locale));
        }

        $this->entityManager->remove($photo);
        $this->entityManager->flush();

        $this->addFlash('success', 'Photo du haut de page supprimée.');

        return $this->redirectToRoute($this->getContentRouteName('home', $locale));
    }

    /**
     * @brief Uploads a home service card image (WebP, no resize) and updates CMS blocks for FR and DE.
     *
     * @param Request $request The HTTP request.
     * @param string $locale The editor locale (redirect target).
     * @param int $slot The card slot (1–5).
     * @return Response Redirect to home content editor.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function uploadHomeServiceCardImage(Request $request, string $locale, int $slot): Response
    {
        $route = $this->getContentRouteName('home', $locale);
        if (!\in_array($locale, ['fr', 'de'], true)) {
            throw $this->createNotFoundException();
        }
        if ($slot < 1 || $slot > 5) {
            throw $this->createNotFoundException();
        }
        if (!$this->isCsrfTokenValid('upload_home_service_card_' . $slot, (string) $request->request->get('_token'))) {
            $this->addFlash('error', $this->translator->trans('back.content.upload_csrf_error', [], 'back'));

            return $this->redirectToRoute($route);
        }

        /** @var UploadedFile|null $file */
        $file = $request->files->get('image_file');
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            $this->addFlash('error', $this->translator->trans('back.content.home_service_card_image.upload_missing', [], 'back'));

            return $this->redirectToRoute($route);
        }

        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
        if (!\in_array((string) $file->getMimeType(), $allowedMimes, true)) {
            $this->addFlash('error', $this->translator->trans('back.content.home_service_card_image.upload_invalid_type', [], 'back'));

            return $this->redirectToRoute($route);
        }

        $blockKey = sprintf('services.card%d.image', $slot);
        $previous = $this->contentBlockManager->getBlockValueForLocale('home', $blockKey, 'fr');
        if ($previous === '') {
            $previous = $this->contentBlockManager->getBlockValueForLocale('home', $blockKey, 'de');
        }

        try {
            $path = $this->homeServiceCardImageProcessor->processToWebp(
                $file,
                (string) $this->getParameter('kernel.project_dir'),
                $slot
            );
            if ($previous !== '') {
                $normalizedPrevious = ltrim(str_replace('\\', '/', trim($previous)), '/');
                $shouldArchivePrevious = str_starts_with($normalizedPrevious, 'uploads/');
                if ($shouldArchivePrevious) {
                    try {
                        $this->archivedUploadService->archiveReplacement(
                            $previous,
                            ArchivedUploadService::CONTEXT_HOME_SERVICE_CARD,
                            $this->getUser(),
                            [
                                'blockKey' => $blockKey,
                                'slot' => $slot,
                            ]
                        );
                    } catch (\JsonException|\RuntimeException) {
                        $this->addFlash('error', $this->translator->trans('back.content.upload_delete_archive_error', [], 'back'));

                        return $this->redirectToRoute($route);
                    }
                }
            }
            $this->contentBlockManager->updateHomeServiceCardImage($slot, $path, $this->getUser());
        } catch (\RuntimeException|\InvalidArgumentException) {
            $this->addFlash('error', $this->translator->trans('back.content.home_service_card_image.upload_error', [], 'back'));

            return $this->redirectToRoute($route);
        }

        $this->addFlash('success', $this->translator->trans('back.content.home_service_card_image.upload_success', [], 'back'));

        return $this->redirectToRoute($route);
    }

    /**
     * @brief Uploads a services list teaser image for a Service row (WebP, updates service.image).
     *
     * @param Request $request The HTTP request.
     * @param string $locale The editor locale (redirect target).
     * @param int $id The service id.
     * @return Response Redirect to services content editor.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function uploadServiceTeaserImage(Request $request, string $locale, int $id): Response
    {
        $route = $this->getContentRouteName('services', $locale);
        if (!\in_array($locale, ['fr', 'de'], true)) {
            throw $this->createNotFoundException();
        }

        $service = $this->serviceRepository->find($id);
        if ($service === null) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('upload_service_teaser_image_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', $this->translator->trans('back.content.upload_csrf_error', [], 'back'));

            return $this->redirectToRoute($route);
        }

        /** @var UploadedFile|null $file */
        $file = $request->files->get('image_file');
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            $this->addFlash('error', $this->translator->trans('back.content.service_teaser_image.upload_missing', [], 'back'));

            return $this->redirectToRoute($route);
        }

        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
        if (!\in_array((string) $file->getMimeType(), $allowedMimes, true)) {
            $this->addFlash('error', $this->translator->trans('back.content.service_teaser_image.upload_invalid_type', [], 'back'));

            return $this->redirectToRoute($route);
        }

        $previous = (string) ($service->getImage() ?? '');

        try {
            $this->entitySnapshotRecorder->recordCurrentStateForPendingUpdate($this->entityManager, $service, EntitySnapshotDomain::SERVICE, $this->getUser());

            $path = $this->serviceTeaserImageProcessor->processToWebp(
                $file,
                (string) $this->getParameter('kernel.project_dir'),
                $id
            );
            if ($previous !== '') {
                $normalizedPrevious = ltrim(str_replace('\\', '/', trim($previous)), '/');
                $shouldArchivePrevious = str_starts_with($normalizedPrevious, 'uploads/');
                if ($shouldArchivePrevious) {
                    try {
                        $this->archivedUploadService->archiveReplacement(
                            $previous,
                            ArchivedUploadService::CONTEXT_SERVICE_TEASER,
                            $this->getUser(),
                            [
                                'serviceId' => $id,
                                'slug' => $service->getSlug(),
                            ]
                        );
                    } catch (\JsonException|\RuntimeException) {
                        $this->addFlash('error', $this->translator->trans('back.content.upload_delete_archive_error', [], 'back'));

                        return $this->redirectToRoute($route);
                    }
                }
            }

            $service->setImage($path);
            $this->entityManager->flush();

            $this->adminAuditLogger->log(AdminAuditActions::SERVICES_TEASER_IMAGE_UPDATE, [
                'serviceId' => $id,
                'slug' => $service->getSlug(),
                'path' => $path,
            ], $this->getUser());
        } catch (\RuntimeException) {
            $this->addFlash('error', $this->translator->trans('back.content.service_teaser_image.upload_error', [], 'back'));

            return $this->redirectToRoute($route);
        }

        $this->addFlash('success', $this->translator->trans('back.content.service_teaser_image.upload_success', [], 'back'));

        return $this->redirectToRoute($route);
    }

    /**
     * @brief Uploads a dedicated public detail-page hero image for a Service (WebP, does not change list teaser image).
     *
     * @param Request $request The HTTP request (optional POST redirect_target=services_list to return to the services list editor).
     * @param string $locale The editor locale (fr|de).
     * @param string $slug The service slug (FR slug).
     * @return Response Redirect to the service detail content editor or services list editor.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function uploadServiceDetailHeroImage(Request $request, string $locale, string $slug): Response
    {
        $service = $this->serviceRepository->findBySlug($slug);
        if ($service === null) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('upload_service_detail_hero_' . $service->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', $this->translator->trans('back.content.upload_csrf_error', [], 'back'));

            return $this->redirectAfterServiceDetailHeroUpload($request, $locale, $slug);
        }

        /** @var UploadedFile|null $file */
        $file = $request->files->get('image_file');
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            $this->addFlash('error', $this->translator->trans('back.content.service_detail_hero_image.upload_missing', [], 'back'));

            return $this->redirectAfterServiceDetailHeroUpload($request, $locale, $slug);
        }

        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
        if (!\in_array((string) $file->getMimeType(), $allowedMimes, true)) {
            $this->addFlash('error', $this->translator->trans('back.content.service_detail_hero_image.upload_invalid_type', [], 'back'));

            return $this->redirectAfterServiceDetailHeroUpload($request, $locale, $slug);
        }

        $id = (int) $service->getId();
        $previous = (string) ($service->getDetailHeroImage() ?? '');

        try {
            $this->entitySnapshotRecorder->recordCurrentStateForPendingUpdate($this->entityManager, $service, EntitySnapshotDomain::SERVICE, $this->getUser());

            $path = $this->serviceDetailHeroImageProcessor->processToWebp(
                $file,
                (string) $this->getParameter('kernel.project_dir'),
                $id
            );
            if ($previous !== '') {
                $normalizedPrevious = ltrim(str_replace('\\', '/', trim($previous)), '/');
                $shouldArchivePrevious = str_starts_with($normalizedPrevious, 'uploads/');
                if ($shouldArchivePrevious) {
                    try {
                        $this->archivedUploadService->archiveReplacement(
                            $previous,
                            ArchivedUploadService::CONTEXT_SERVICE_DETAIL_HERO,
                            $this->getUser(),
                            [
                                'serviceId' => $id,
                                'slug' => $service->getSlug(),
                            ]
                        );
                    } catch (\JsonException|\RuntimeException) {
                        $this->addFlash('error', $this->translator->trans('back.content.upload_delete_archive_error', [], 'back'));

                        return $this->redirectAfterServiceDetailHeroUpload($request, $locale, $slug);
                    }
                }
            }

            $service->setDetailHeroImage($path);
            $this->entityManager->flush();

            $this->adminAuditLogger->log(AdminAuditActions::SERVICE_DETAIL_HERO_IMAGE_UPDATE, [
                'serviceId' => $id,
                'slug' => $service->getSlug(),
                'path' => $path,
            ], $this->getUser());
        } catch (\RuntimeException) {
            $this->addFlash('error', $this->translator->trans('back.content.service_detail_hero_image.upload_error', [], 'back'));

            return $this->redirectAfterServiceDetailHeroUpload($request, $locale, $slug);
        }

        $this->addFlash('success', $this->translator->trans('back.content.service_detail_hero_image.upload_success', [], 'back'));

        return $this->redirectAfterServiceDetailHeroUpload($request, $locale, $slug);
    }

    /**
     * @brief Redirects after service detail hero upload (list CMS vs service detail editor).
     *
     * @param Request $request The HTTP request (redirect_target=services_list returns to the services list editor).
     * @param string $locale The editor locale (fr|de).
     * @param string $slug The service slug for the detail editor route.
     * @return Response The redirect response.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function redirectAfterServiceDetailHeroUpload(Request $request, string $locale, string $slug): Response
    {
        if ($request->request->get('redirect_target') === 'services_list') {
            $routeName = $locale === 'fr' ? 'app_back_content_services_fr' : 'app_back_content_services_de';

            return $this->redirectToRoute($routeName);
        }

        $routeName = $locale === 'fr' ? 'app_back_content_service_detail_fr' : 'app_back_content_service_detail_de';

        return $this->redirectToRoute($routeName, ['slug' => $slug]);
    }

    /**
     * @brief Persists display order for home "about" slider photos from an ordered ID list.
     *
     * @param Request $request The HTTP request (JSON body: _token, ids).
     * @param string $locale The locale (for future use; kept for route consistency).
     * @return JsonResponse JSON ok flag or error.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function reorderHomePhotos(Request $request, string $locale): JsonResponse
    {
        if (!\in_array($locale, ['fr', 'de'], true)) {
            return new JsonResponse(['ok' => false, 'error' => 'locale'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent() ?: '{}', true);
        if (!\is_array($data)) {
            return new JsonResponse(['ok' => false, 'error' => 'invalid_json'], Response::HTTP_BAD_REQUEST);
        }

        $token = (string) ($data['_token'] ?? '');
        if (!$this->isCsrfTokenValid('reorder_home_photos', $token)) {
            return new JsonResponse(['ok' => false, 'error' => 'csrf'], Response::HTTP_FORBIDDEN);
        }

        $ids = $data['ids'] ?? null;
        if (!\is_array($ids)) {
            return new JsonResponse(['ok' => false, 'error' => 'invalid_ids'], Response::HTTP_BAD_REQUEST);
        }

        $ids = array_map(static fn ($v) => (int) $v, $ids);
        $photos = $this->aboutPhotoRepository->findAllOrdered();
        $existingIds = array_map(static fn (AboutPhoto $p) => $p->getId(), $photos);
        $sortedExisting = $existingIds;
        $sortedSubmitted = $ids;
        sort($sortedExisting, SORT_NUMERIC);
        sort($sortedSubmitted, SORT_NUMERIC);

        if ($sortedExisting !== $sortedSubmitted || \count($ids) !== \count($existingIds)) {
            return new JsonResponse(['ok' => false, 'error' => 'mismatch'], Response::HTTP_BAD_REQUEST);
        }

        $byId = [];
        foreach ($photos as $photo) {
            $byId[$photo->getId()] = $photo;
        }

        $oldPositions = [];
        foreach ($photos as $photo) {
            $oldPositions[(string) $photo->getId()] = $photo->getPosition();
        }

        foreach ($photos as $photo) {
            $this->entitySnapshotRecorder->recordCurrentStateForPendingUpdate($this->entityManager, $photo, EntitySnapshotDomain::ABOUT_PHOTO, $this->getUser());
        }

        foreach ($ids as $index => $id) {
            if (!isset($byId[$id])) {
                return new JsonResponse(['ok' => false, 'error' => 'unknown_id'], Response::HTTP_BAD_REQUEST);
            }
            $byId[$id]->setPosition($index);
        }

        $this->entityManager->flush();

        $this->adminAuditLogger->log(AdminAuditActions::SLIDER_ABOUT_REORDER, [
            'oldPositions' => $oldPositions,
            'newOrderIds' => $ids,
        ], $this->getUser());

        return new JsonResponse(['ok' => true]);
    }

    /**
     * @brief Persists display order for home hero slider photos from an ordered ID list.
     *
     * @param Request $request The HTTP request (JSON body: _token, ids).
     * @param string $locale The locale (for future use; kept for route consistency).
     * @return JsonResponse JSON ok flag or error.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function reorderHomeHeroPhotos(Request $request, string $locale): JsonResponse
    {
        if (!\in_array($locale, ['fr', 'de'], true)) {
            return new JsonResponse(['ok' => false, 'error' => 'locale'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent() ?: '{}', true);
        if (!\is_array($data)) {
            return new JsonResponse(['ok' => false, 'error' => 'invalid_json'], Response::HTTP_BAD_REQUEST);
        }

        $token = (string) ($data['_token'] ?? '');
        if (!$this->isCsrfTokenValid('reorder_home_hero_photos', $token)) {
            return new JsonResponse(['ok' => false, 'error' => 'csrf'], Response::HTTP_FORBIDDEN);
        }

        $ids = $data['ids'] ?? null;
        if (!\is_array($ids)) {
            return new JsonResponse(['ok' => false, 'error' => 'invalid_ids'], Response::HTTP_BAD_REQUEST);
        }

        $ids = array_map(static fn ($v) => (int) $v, $ids);
        $photos = $this->homeHeroPhotoRepository->findAllOrdered();
        $existingIds = array_map(static fn (HomeHeroPhoto $p) => $p->getId(), $photos);
        $sortedExisting = $existingIds;
        $sortedSubmitted = $ids;
        sort($sortedExisting, SORT_NUMERIC);
        sort($sortedSubmitted, SORT_NUMERIC);

        if ($sortedExisting !== $sortedSubmitted || \count($ids) !== \count($existingIds)) {
            return new JsonResponse(['ok' => false, 'error' => 'mismatch'], Response::HTTP_BAD_REQUEST);
        }

        $byId = [];
        foreach ($photos as $photo) {
            $byId[$photo->getId()] = $photo;
        }

        $oldPositions = [];
        foreach ($photos as $photo) {
            $oldPositions[(string) $photo->getId()] = $photo->getPosition();
        }

        foreach ($photos as $photo) {
            $this->entitySnapshotRecorder->recordCurrentStateForPendingUpdate($this->entityManager, $photo, EntitySnapshotDomain::HOME_HERO_PHOTO, $this->getUser());
        }

        foreach ($ids as $index => $id) {
            if (!isset($byId[$id])) {
                return new JsonResponse(['ok' => false, 'error' => 'unknown_id'], Response::HTTP_BAD_REQUEST);
            }
            $byId[$id]->setPosition($index);
        }

        $this->entityManager->flush();

        $this->adminAuditLogger->log(AdminAuditActions::SLIDER_HERO_REORDER, [
            'oldPositions' => $oldPositions,
            'newOrderIds' => $ids,
        ], $this->getUser());

        return new JsonResponse(['ok' => true]);
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
            'service_process_steps' => (str_starts_with($pageName, 'service_') && $currentService !== null)
                ? $this->serviceProcessStepRepository->findByServiceOrdered($currentService)
                : [],
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
            $previous = $photo->getImage();
            $path = $this->aboutPhotoImageProcessor->processToWebp(
                $file,
                (string) $this->getParameter('kernel.project_dir')
            );
            if ($previous !== null && $previous !== '') {
                try {
                    $this->archivedUploadService->archiveReplacement(
                        $previous,
                        ArchivedUploadService::CONTEXT_ABOUT,
                        $this->getUser(),
                        [
                            'entityId' => $photo->getId(),
                            'altFr' => $photo->getAltFr(),
                            'altDe' => $photo->getAltDe(),
                        ]
                    );
                } catch (\JsonException|\RuntimeException) {
                    $this->addFlash('error', $this->translator->trans('back.content.upload_delete_archive_error', [], 'back'));

                    return false;
                }
            }
            $photo->setImage($path);

            return true;
        } catch (\RuntimeException|FileException) {
            $this->addFlash('error', 'Erreur lors du traitement de la photo (conversion WebP).');
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
            $previous = $photo->getImage();
            $path = $this->homeHeroPhotoImageProcessor->processToWebp(
                $file,
                (string) $this->getParameter('kernel.project_dir')
            );
            if ($previous !== null && $previous !== '') {
                try {
                    $this->archivedUploadService->archiveReplacement(
                        $previous,
                        ArchivedUploadService::CONTEXT_HOME_HERO,
                        $this->getUser(),
                        [
                            'entityId' => $photo->getId(),
                            'altFr' => $photo->getAltFr(),
                            'altDe' => $photo->getAltDe(),
                        ]
                    );
                } catch (\JsonException|\RuntimeException) {
                    $this->addFlash('error', $this->translator->trans('back.content.upload_delete_archive_error', [], 'back'));

                    return false;
                }
            }
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
        $allQuery = $request->query->all();
        $kinds = $allQuery['kinds'] ?? [];
        $domains = $allQuery['domains'] ?? [];
        if (!\is_array($kinds)) {
            $kinds = $kinds !== null && $kinds !== '' ? [(string) $kinds] : [];
        }
        if (!\is_array($domains)) {
            $domains = $domains !== null && $domains !== '' ? [(string) $domains] : [];
        }

        $query = [
            'page' => $pageFilter,
            'locale' => $localeFilter,
            'kinds' => $kinds,
            'domains' => $domains,
        ];

        $timelineItems = $this->historyMergeService->buildMergedTimeline($query);

        return $this->render('back/content/history.html.twig', [
            'timeline_items' => $timelineItems,
            'page_filter' => $pageFilter,
            'locale_filter' => $localeFilter,
            'kinds_filter' => $kinds,
            'domains_filter' => $domains,
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
     * @brief Restores an entity from an entity snapshot history row.
     *
     * @param Request $request The HTTP request.
     * @param int $id The entity_snapshot_history id.
     * @return Response Redirect to history.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function rollbackEntitySnapshot(Request $request, int $id): Response
    {
        if (!$this->isCsrfTokenValid('rollback_entity_snapshot_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');

            return $this->redirectToRoute('app_back_content_history');
        }

        try {
            $this->entitySnapshotRollbackService->rollback($id, $this->getUser());
            $this->addFlash('success', $this->translator->trans('back.content.history.entity_snapshot_restored', [], 'back'));
        } catch (\Throwable $e) {
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

