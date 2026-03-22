<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Maintenance mode service.
 * Uses a flag file in var/ to store maintenance state.
 *
 * @author Stephane H.
 * @date 2026-03-21
 */
class MaintenanceService
{
    private const FLAG_FILENAME = 'maintenance.flag';

    public function __construct(
        private readonly string $projectDir
    ) {
    }

    /**
     * @brief Checks if maintenance mode is active.
     *
     * @return bool True if maintenance is enabled
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function isActive(): bool
    {
        return file_exists($this->getFlagPath());
    }

    /**
     * @brief Enables maintenance mode.
     *
     * @return void
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function enable(): void
    {
        $path = $this->getFlagPath();
        $dir = \dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        touch($path);
    }

    /**
     * @brief Disables maintenance mode.
     *
     * @return void
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function disable(): void
    {
        $path = $this->getFlagPath();
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function getFlagPath(): string
    {
        return $this->projectDir . '/var/' . self::FLAG_FILENAME;
    }
}
