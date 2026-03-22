<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

/**
 * @brief Handles full site backup (database + uploaded files) and restore operations.
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
class BackupService
{
    /**
     * @brief Semantic version of the backup archive layout (ZIP contents and naming convention).
     */
    public const BACKUP_ARCHIVE_FORMAT_VERSION = '0.2';

    private const BACKUP_DIR = 'var/backups';
    private const UPLOADS_DIR = 'public/uploads';

    private string $backupDir;
    private string $uploadsDir;

    public function __construct(
        private readonly Connection $connection,
        private readonly string $projectDir,
    ) {
        $this->backupDir = $this->projectDir . '/' . self::BACKUP_DIR;
        $this->uploadsDir = $this->projectDir . '/' . self::UPLOADS_DIR;
    }

    /**
     * @brief Creates a full backup (SQL dump + upload files) as a ZIP archive.
     *
     * @return string The backup filename.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function createBackup(): string
    {
        $this->ensureBackupDir();

        $filename = 'backup_' . $this->formatVersionFileSegment() . '_' . date('Ymd_His') . '.zip';
        $zipPath = $this->backupDir . '/' . $filename;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Cannot create ZIP archive: ' . $zipPath);
        }

        $sqlDump = $this->generateSqlDump();
        $zip->addFromString('database.sql', $sqlDump);

        $this->addDirectoryToZip($zip, $this->uploadsDir, 'uploads');

        $zip->close();

        return $filename;
    }

    /**
     * @brief Restores the site from a backup ZIP archive.
     *
     * @param string $filename The backup filename to restore from.
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function restoreBackup(string $filename): void
    {
        $zipPath = $this->getBackupPath($filename);
        if (!file_exists($zipPath)) {
            throw new \RuntimeException('Backup file not found: ' . $filename);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('Cannot open ZIP archive: ' . $filename);
        }

        $tempDir = sys_get_temp_dir() . '/site_restore_' . uniqid();
        $zip->extractTo($tempDir);
        $zip->close();

        $sqlFile = $tempDir . '/database.sql';
        if (file_exists($sqlFile)) {
            $this->restoreSqlDump(file_get_contents($sqlFile));
        }

        $uploadsSource = $tempDir . '/uploads';
        if (is_dir($uploadsSource)) {
            $this->restoreUploads($uploadsSource);
        }

        $this->removeDirectory($tempDir);
    }

    /**
     * @brief Lists all available backups sorted by date (newest first).
     *
     * @return array<int, array{filename: string, date: \DateTimeImmutable, size: int}>
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function listBackups(): array
    {
        $this->ensureBackupDir();
        $backups = [];

        foreach (new \DirectoryIterator($this->backupDir) as $file) {
            if ($file->isDot() || $file->getExtension() !== 'zip') {
                continue;
            }
            $backups[] = [
                'filename' => $file->getFilename(),
                'date' => \DateTimeImmutable::createFromFormat('U', (string) $file->getMTime()),
                'size' => $file->getSize(),
            ];
        }

        usort($backups, fn(array $a, array $b) => $b['date'] <=> $a['date']);

        return $backups;
    }

    /**
     * @brief Deletes a backup archive.
     *
     * @param string $filename The backup filename to delete.
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function deleteBackup(string $filename): void
    {
        $path = $this->getBackupPath($filename);
        if (!file_exists($path)) {
            throw new \RuntimeException('Backup file not found: ' . $filename);
        }
        unlink($path);
    }

    /**
     * @brief Imports an uploaded ZIP backup into var/backups/ and restores it.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile The uploaded ZIP file.
     * @return string The stored backup filename.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function importAndRestore(\Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile): string
    {
        $this->ensureBackupDir();

        $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $originalName . '_' . $this->formatVersionFileSegment() . '_' . date('Ymd_His') . '.zip';
        $uploadedFile->move($this->backupDir, $filename);

        $this->restoreBackup($filename);

        return $filename;
    }

    /**
     * @brief Returns the absolute path to a backup file.
     *
     * @param string $filename The backup filename.
     * @return string Absolute path.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function getBackupPath(string $filename): string
    {
        $safe = basename($filename);
        return $this->backupDir . '/' . $safe;
    }

    /**
     * @brief Formats a file size in bytes to a human-readable string.
     *
     * @param int $bytes The file size in bytes.
     * @return string Formatted size string.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public static function formatSize(int $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $factor = 0;
        $size = (float) $bytes;
        while ($size >= 1024 && $factor < count($units) - 1) {
            $size /= 1024;
            $factor++;
        }
        return round($size, 1) . ' ' . $units[$factor];
    }

    /**
     * @brief Builds the version segment used in backup archive filenames (e.g. v0.2).
     *
     * @return string Version segment including the leading "v".
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function formatVersionFileSegment(): string
    {
        return 'v' . self::BACKUP_ARCHIVE_FORMAT_VERSION;
    }

    /**
     * @brief Generates a complete SQL dump of all tables (structure + data).
     *
     * @return string The SQL dump content.
     * @date 2026-03-21
     * @author Stephane H.
     */
    private function generateSqlDump(): string
    {
        $sql = '-- Site backup: ' . date('Y-m-d H:i:s') . ', archive format v' . self::BACKUP_ARCHIVE_FORMAT_VERSION
            . '; uploads: full tree under public/uploads (restore mirrors entire directory)' . "\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        $tables = $this->connection->fetchFirstColumn('SHOW TABLES');

        foreach ($tables as $table) {
            $createResult = $this->connection->fetchAssociative('SHOW CREATE TABLE `' . $table . '`');
            $createSql = $createResult['Create Table'] ?? '';

            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createSql . ";\n\n";

            $rows = $this->connection->fetchAllAssociative('SELECT * FROM `' . $table . '`');
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return $this->connection->quote((string) $value);
                    }, $row);

                    $columns = array_map(fn($col) => '`' . $col . '`', array_keys($row));
                    $sql .= 'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        return $sql;
    }

    /**
     * @brief Restores the database from an SQL dump string.
     *
     * @param string $sqlContent The SQL dump content.
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    private function restoreSqlDump(string $sqlContent): void
    {
        $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

        $tables = $this->connection->fetchFirstColumn('SHOW TABLES');
        foreach ($tables as $table) {
            $this->connection->executeStatement('DROP TABLE IF EXISTS `' . $table . '`');
        }

        $statements = $this->splitSqlStatements($sqlContent);
        foreach ($statements as $statement) {
            $trimmed = trim($statement);
            if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                continue;
            }
            $this->connection->executeStatement($trimmed);
        }

        $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * @brief Splits an SQL dump into individual executable statements.
     *
     * @param string $sql The full SQL dump.
     * @return array<int, string> Array of SQL statements.
     * @date 2026-03-21
     * @author Stephane H.
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];

            if ($inString) {
                $current .= $char;
                if ($char === '\\' && $i + 1 < $length) {
                    $current .= $sql[++$i];
                    continue;
                }
                if ($char === $stringChar) {
                    $inString = false;
                }
                continue;
            }

            if ($char === '\'' || $char === '"') {
                $inString = true;
                $stringChar = $char;
                $current .= $char;
                continue;
            }

            if ($char === '-' && $i + 1 < $length && $sql[$i + 1] === '-') {
                while ($i < $length && $sql[$i] !== "\n") {
                    $i++;
                }
                continue;
            }

            if ($char === ';') {
                $trimmed = trim($current);
                if ($trimmed !== '') {
                    $statements[] = $trimmed;
                }
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $trimmed = trim($current);
        if ($trimmed !== '') {
            $statements[] = $trimmed;
        }

        return $statements;
    }

    /**
     * @brief Replaces the entire public/uploads tree with the extracted backup (full mirror).
     *
     * Clears the current uploads root then copies every file and subdirectory from the archive,
     * matching the scope of addDirectoryToZip() used when creating backups.
     *
     * @param string $sourceDir Path to the extracted uploads directory (e.g. temp/uploads).
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function restoreUploads(string $sourceDir): void
    {
        if (!is_dir($sourceDir)) {
            return;
        }

        if (!is_dir($this->uploadsDir)) {
            mkdir($this->uploadsDir, 0775, true);
        } else {
            $this->clearDirectory($this->uploadsDir);
        }

        $this->copyDirectory($sourceDir, $this->uploadsDir);
    }

    /**
     * @brief Recursively adds a directory to a ZipArchive.
     *
     * @param \ZipArchive $zip The ZIP archive to add files to.
     * @param string $dirPath Absolute path to the directory.
     * @param string $zipPrefix Prefix path inside the ZIP.
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    private function addDirectoryToZip(\ZipArchive $zip, string $dirPath, string $zipPrefix): void
    {
        if (!is_dir($dirPath)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $relativePath = $zipPrefix . '/' . ltrim(
                str_replace('\\', '/', substr($file->getPathname(), strlen($dirPath))),
                '/'
            );

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($file->getPathname(), $relativePath);
            }
        }
    }

    /**
     * @brief Recursively copies a directory.
     *
     * @param string $source Source directory path.
     * @param string $destination Destination directory path.
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    private function copyDirectory(string $source, string $destination): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $targetPath = $destination . '/' . ltrim(
                str_replace('\\', '/', substr($item->getPathname(), strlen($source))),
                '/'
            );

            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0775, true);
                }
            } else {
                copy($item->getPathname(), $targetPath);
            }
        }
    }

    /**
     * @brief Removes all files in a directory without deleting the directory itself.
     *
     * @param string $dir Directory path to clear.
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    private function clearDirectory(string $dir): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
    }

    /**
     * @brief Recursively removes a directory and all its contents.
     *
     * @param string $dir Directory path to remove.
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $this->clearDirectory($dir);
        rmdir($dir);
    }

    /**
     * @brief Ensures the backup directory exists.
     *
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    private function ensureBackupDir(): void
    {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0775, true);
        }
    }
}
