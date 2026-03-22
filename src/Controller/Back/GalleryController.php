<?php

namespace App\Controller\Back;

use App\Entity\GalleryItem;
use App\Form\GalleryItemType;
use App\Repository\GalleryItemRepository;
use App\Service\AdminAuditActions;
use App\Service\AdminAuditLogger;
use App\Service\ArchivedUploadService;
use App\Service\EntitySnapshotDomain;
use App\Service\EntitySnapshotRecorder;
use App\Service\GalleryImageProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @brief Back-office CRUD for gallery items.
 *
 * @date 2026-03-11
 * @author Stephane H.
 */
#[IsGranted('ROLE_USER')]
class GalleryController extends AbstractController
{
    /**
     * @brief GalleryController constructor.
     *
     * @param GalleryItemRepository $repository The gallery repository.
     * @param EntityManagerInterface $em The entity manager.
     * @param GalleryImageProcessor $galleryImageProcessor The gallery image processor.
     * @param ArchivedUploadService $archivedUploadService The deleted upload archiver.
     * @param AdminAuditLogger $adminAuditLogger The admin audit logger.
     * @param TranslatorInterface $translator The translator.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function __construct(
        private readonly GalleryItemRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly GalleryImageProcessor $galleryImageProcessor,
        private readonly ArchivedUploadService $archivedUploadService,
        private readonly AdminAuditLogger $adminAuditLogger,
        private readonly TranslatorInterface $translator,
        private readonly EntitySnapshotRecorder $entitySnapshotRecorder,
    ) {
    }

    public function index(): Response
    {
        return $this->render('back/gallery/index.html.twig', [
            'items' => $this->repository->findAllOrdered(),
        ]);
    }

    public function new(Request $request): Response
    {
        $item = new GalleryItem();
        $item->setOrdre($this->repository->count([]));

        $form = $this->createForm(GalleryItemType::class, $item, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->handleImageUpload($form, $item)) {
                return $this->render('back/gallery/form.html.twig', [
                    'item' => $item,
                    'form' => $form,
                ]);
            }
            $this->em->persist($item);
            $this->em->flush();

            $this->entitySnapshotRecorder->recordAfterCreate($this->em, $item, EntitySnapshotDomain::GALLERY_ITEM, $this->getUser());
            $this->em->flush();

            $this->adminAuditLogger->log(AdminAuditActions::GALLERY_ITEM_CREATE, [
                'id' => $item->getId(),
                'ordre' => $item->getOrdre(),
                'titre' => (string) ($item->getTitre() ?? ''),
            ], $this->getUser());

            $this->addFlash('success', 'Élément ajouté à la galerie.');

            return $this->redirectToRoute(
                str_starts_with($request->getLocale(), 'de') ? 'app_back_content_gallery_de' : 'app_back_content_gallery_fr'
            );
        }

        return $this->render('back/gallery/form.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    public function edit(Request $request, GalleryItem $item): Response
    {
        $form = $this->createForm(GalleryItemType::class, $item, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entitySnapshotRecorder->recordCurrentStateForPendingUpdate($this->em, $item, EntitySnapshotDomain::GALLERY_ITEM, $this->getUser());
            if (!$this->handleImageUpload($form, $item)) {
                return $this->render('back/gallery/form.html.twig', [
                    'item' => $item,
                    'form' => $form,
                ]);
            }
            $this->em->flush();

            $this->adminAuditLogger->log(AdminAuditActions::GALLERY_ITEM_UPDATE, [
                'id' => $item->getId(),
                'ordre' => $item->getOrdre(),
                'titre' => (string) ($item->getTitre() ?? ''),
            ], $this->getUser());

            $this->addFlash('success', 'Élément modifié.');

            return $this->redirectToRoute(
                str_starts_with($request->getLocale(), 'de') ? 'app_back_content_gallery_de' : 'app_back_content_gallery_fr'
            );
        }

        return $this->render('back/gallery/form.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    /**
     * @brief Deletes a gallery item after archiving its image to uploads/historique/gallery/.
     *
     * @param Request $request The HTTP request.
     * @param GalleryItem $item The item to remove.
     * @return Response The response.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function delete(Request $request, GalleryItem $item): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_back_gallery_index');
        }

        $this->entitySnapshotRecorder->recordBeforeDelete($this->em, $item, EntitySnapshotDomain::GALLERY_ITEM, $this->getUser());

        try {
            $meta = json_encode([
                'reason' => 'delete',
                'entityId' => $item->getId(),
                'titre' => $item->getTitre(),
                'titreDe' => $item->getTitreDe(),
            ], \JSON_THROW_ON_ERROR);
            $this->archivedUploadService->archiveAndRecord(
                $item->getImage(),
                ArchivedUploadService::CONTEXT_GALLERY,
                $this->getUser(),
                $meta
            );
        } catch (\JsonException|\RuntimeException) {
            $this->addFlash('error', $this->translator->trans('back.content.upload_delete_archive_error', [], 'back'));

            return $this->redirectToRoute(
                str_starts_with($request->getLocale(), 'de') ? 'app_back_content_gallery_de' : 'app_back_content_gallery_fr'
            );
        }

        $this->em->remove($item);
        $this->em->flush();
        $this->addFlash('success', 'Élément supprimé.');

        return $this->redirectToRoute(
            str_starts_with($request->getLocale(), 'de') ? 'app_back_content_gallery_de' : 'app_back_content_gallery_fr'
        );
    }

    /**
     * @brief Handles gallery image upload with WebP conversion and optional archive of the previous file.
     *
     * @param \Symfony\Component\Form\FormInterface $form The submitted form.
     * @param GalleryItem $item The gallery item entity.
     * @return bool True when processing succeeds.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function handleImageUpload($form, GalleryItem $item): bool
    {
        $file = $form->get('imageFile')->getData();
        if (!$file) {
            return true;
        }

        try {
            $previous = $item->getImage();
            $path = $this->galleryImageProcessor->processToWebp(
                $file,
                (string) $this->getParameter('kernel.project_dir')
            );
            if ($previous !== null && $previous !== '') {
                try {
                    $this->archivedUploadService->archiveReplacement(
                        $previous,
                        ArchivedUploadService::CONTEXT_GALLERY,
                        $this->getUser(),
                        [
                            'entityId' => $item->getId(),
                            'titre' => $item->getTitre(),
                            'titreDe' => $item->getTitreDe(),
                        ]
                    );
                } catch (\JsonException|\RuntimeException) {
                    $this->addFlash('error', $this->translator->trans('back.content.upload_delete_archive_error', [], 'back'));

                    return false;
                }
            }
            $item->setImage($path);

            return true;
        } catch (\RuntimeException|FileException) {
            $this->addFlash('error', 'Erreur lors de l\'upload de l\'image (conversion WebP).');
        }

        return false;
    }
}
