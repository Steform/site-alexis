<?php

namespace App\Controller\Public;

use App\Repository\GalleryItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    ) {
    }

    /**
     * Renders the public gallery page.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('public/gallery/index.html.twig', [
            'items' => $this->repository->findAllOrdered(),
        ]);
    }
}
