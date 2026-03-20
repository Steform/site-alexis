<?php

namespace App\Controller\Back;

use App\Entity\GalleryItem;
use App\Form\GalleryItemType;
use App\Repository\GalleryItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Back-office CRUD for gallery items.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
#[IsGranted('ROLE_USER')]
class GalleryController extends AbstractController
{
    private const UPLOAD_DIR = 'uploads/gallery';

    public function __construct(
        private readonly GalleryItemRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly SluggerInterface $slugger,
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
            $this->handleImageUpload($form, $item);
            $this->em->persist($item);
            $this->em->flush();
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
            $this->handleImageUpload($form, $item);
            $this->em->flush();
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

    public function delete(Request $request, GalleryItem $item): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_back_gallery_index');
        }
        $this->em->remove($item);
        $this->em->flush();
        $this->addFlash('success', 'Élément supprimé.');

        return $this->redirectToRoute(
            str_starts_with($request->getLocale(), 'de') ? 'app_back_content_gallery_de' : 'app_back_content_gallery_fr'
        );
    }

    private function handleImageUpload($form, GalleryItem $item): void
    {
        $file = $form->get('imageFile')->getData();
        if (!$file) {
            return;
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/' . self::UPLOAD_DIR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        try {
            $file->move($uploadDir, $newFilename);
            $item->setImage(self::UPLOAD_DIR . '/' . $newFilename);
        } catch (FileException $e) {
            $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
        }
    }
}
