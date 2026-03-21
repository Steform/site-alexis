<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\BackupService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @brief Twig extension providing backup-related helper functions.
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
class BackupExtension extends AbstractExtension
{
    /**
     * @brief Registers Twig functions.
     *
     * @return array<TwigFunction>
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('format_size', [BackupService::class, 'formatSize']),
        ];
    }
}
