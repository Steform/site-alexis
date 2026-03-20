<?php

namespace App\Controller\Public;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    ) {
    }

    /**
     * Renders the services list page.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('public/services/index.html.twig', [
            'services' => $this->serviceRepository->findAllOrdered(),
        ]);
    }

    /**
     * Renders a single service detail page by slug.
     *
     * @param string $slug The service slug.
     * @return Response
     */
    public function show(string $slug): Response
    {
        $service = $this->serviceRepository->findBySlug($slug);

        if (!$service) {
            throw new NotFoundHttpException();
        }

        return $this->render('public/services/show.html.twig', [
            'service' => $service,
        ]);
    }
}
