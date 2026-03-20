<?php

namespace App\Controller\Public;

use App\Repository\GalleryItemRepository;
use App\Service\ContentBlockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public gallery page controller.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
class GalleryController extends AbstractController
{
    public function __construct(
        private readonly GalleryItemRepository $repository,
        private readonly ContentBlockManager $contentBlockManager,
    ) {
    }

    /**
     * Renders the public gallery page.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $contentLocale = str_starts_with($request->getLocale(), 'de') ? 'de' : 'fr';
        $galleryContent = $this->contentBlockManager->getPageContent('gallery', $contentLocale);
        $galleryColors = $this->contentBlockManager->getPageColors('gallery', $contentLocale);

        return $this->render('public/gallery/index.html.twig', [
            'items' => $this->repository->findAllOrdered(),
            'gallery_content' => $galleryContent,
            'gallery_colors' => $galleryColors,
        ]);
    }
}
