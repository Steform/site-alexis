<?php

declare(strict_types=1);

namespace App\Controller\Public;

use App\Repository\CoordinatesRepository;
use App\Service\MaintenanceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public maintenance page controller.
 *
 * @author Stephane H.
 * @date 2026-03-21
 */
class MaintenanceController extends AbstractController
{
    public function __construct(
        private readonly MaintenanceService $maintenanceService,
        private readonly CoordinatesRepository $coordinatesRepository
    ) {
    }

    /**
     * @brief Displays the maintenance page, or redirects to home when maintenance is disabled.
     *
     * @param Request $request The current request
     * @return Response The maintenance page or redirect to home
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function index(Request $request): Response
    {
        if (!$this->maintenanceService->isActive()) {
            $path = $request->getPathInfo();
            $route = str_starts_with($path, '/de') ? 'app_home_de' : 'app_home';

            return $this->redirectToRoute($route);
        }

        $coordinates = $this->coordinatesRepository->findSingle();

        return $this->render('public/maintenance.html.twig', [
            'coordinates' => $coordinates,
        ]);
    }
}
