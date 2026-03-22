<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;

/**
 * @brief Wipes application data and uploads to return the site to the initial setup state (no users).
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class SiteResetService
{
    private const MIGRATIONS_TABLE = 'doctrine_migration_versions';

    public function __construct(
        private readonly Connection $connection,
        private readonly BackupService $backupService,
    ) {
    }

    /**
     * @brief Truncates all base tables except doctrine_migration_versions, clears uploads, and removes backup ZIPs.
     *
     * @param bool $createEmergencyBackupFirst When true, creates a ZIP first and keeps that file when purging archives.
     * @return array{preserved_backup: string|null, deleted_zip_count: int}
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function resetToSetupState(bool $createEmergencyBackupFirst): array
    {
        $preservedBackup = null;
        if ($createEmergencyBackupFirst) {
            $preservedBackup = $this->backupService->createBackup();
        }

        $deletedZipCount = $this->backupService->deleteAllBackupArchives($preservedBackup);

        $this->truncateApplicationTables();

        $this->backupService->clearPublicUploads();

        return [
            'preserved_backup' => $preservedBackup,
            'deleted_zip_count' => $deletedZipCount,
        ];
    }

    /**
     * @brief Truncates all BASE TABLE rows except the Doctrine migrations history table.
     *
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function truncateApplicationTables(): void
    {
        $dbName = $this->connection->fetchOne('SELECT DATABASE()');
        if (!\is_string($dbName) || $dbName === '') {
            throw new \RuntimeException('No database selected for site reset.');
        }

        $tables = $this->connection->fetchFirstColumn(
            'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = ?',
            [$dbName, 'BASE TABLE']
        );

        $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tables as $table) {
            $tableName = (string) $table;
            if ($tableName === self::MIGRATIONS_TABLE) {
                continue;
            }
            $quoted = '`' . str_replace('`', '``', $tableName) . '`';
            $this->connection->executeStatement('TRUNCATE TABLE ' . $quoted);
        }

        $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
