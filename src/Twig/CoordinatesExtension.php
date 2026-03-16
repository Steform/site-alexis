<?php

namespace App\Twig;

use App\Repository\CoordinatesRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @brief Provides global business coordinates to Twig templates.
 *
 * @date 2026-03-16
 * @author Stephane H.
 */
class CoordinatesExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @brief CoordinatesExtension constructor.
     *
     * @param CoordinatesRepository $coordinatesRepository The coordinates repository.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function __construct(
        private readonly CoordinatesRepository $coordinatesRepository,
    ) {
    }

    /**
     * @brief Exposes global Twig variables.
     *
     * @return array<string, mixed> The global variables array.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getGlobals(): array
    {
        return [
            'coordinates' => $this->coordinatesRepository->findSingle(),
        ];
    }
}

