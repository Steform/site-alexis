<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\MaintenanceService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Twig extension exposing maintenance state to templates.
 *
 * @author Stephane H.
 * @date 2026-03-21
 */
class MaintenanceExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly MaintenanceService $maintenanceService
    ) {
    }

    /**
     * @brief Returns global Twig variables.
     *
     * @return array<string, bool>
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function getGlobals(): array
    {
        return [
            'maintenance_active' => $this->maintenanceService->isActive(),
        ];
    }
}
