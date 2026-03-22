<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UploadDeletionHistory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @brief Moves deleted upload files under public/uploads/historique/{context}/ and persists audit rows.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class ArchivedUploadService
{
    public const CONTEXT_ABOUT = 'about';

    public const CONTEXT_HOME_HERO = 'home-hero';

    public const CONTEXT_GALLERY = 'gallery';

    public const CONTEXT_HOME_SERVICE_CARD = 'home-service-card';

    public const CONTEXT_SERVICE_TEASER = 'service-teaser';

    public const CONTEXT_SERVICE_DETAIL_HERO = 'service-detail-hero';

    private const HISTORIQUE_BASE = 'uploads/historique';

    /**
     * @brief Creates the service with project root and entity manager.
     *
     * @param string $projectDir The Symfony project directory (kernel.project_dir).
     * @param EntityManagerInterface $entityManager The entity manager.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function __construct(
        private readonly string $projectDir,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @brief Archives a file replaced by a new upload (same as delete archive, metadata includes reason=replace).
     *
     * @param string|null $previousRelativePath The former public-relative path.
     * @param string $context One of CONTEXT_* constants.
     * @param UserInterface|null $actor The acting user.
     * @param array<string, mixed> $metadata Extra metadata merged with reason replace.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function archiveReplacement(
        ?string $previousRelativePath,
        string $context,
        ?UserInterface $actor,
        array $metadata = [],
    ): void {
        $merged = array_merge($metadata, ['reason' => 'replace']);
        $json = json_encode($merged, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE);
        $this->archiveAndRecord($previousRelativePath, $context, $actor, $json);
    }

    /**
     * @brief Archives a file to uploads/historique/{context}/ and records UploadDeletionHistory.
     *
     * If the source file is missing, still records history with fileMissing=true and null archived path.
     * Does nothing when relativePath is null or empty after trim.
     *
     * @param string|null $relativePath The path relative to public/ (e.g. uploads/about/x.webp).
     * @param string $context One of CONTEXT_* constants.
     * @param UserInterface|null $deletedBy The acting user.
     * @param string|null $metadataJson Optional JSON string for alt/title/entity id.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function archiveAndRecord(
        ?string $relativePath,
        string $context,
        ?UserInterface $deletedBy,
        ?string $metadataJson = null,
    ): void {
        $this->assertValidContext($context);

        if ($relativePath === null) {
            return;
        }

        $normalized = $this->normalizeRelativePath($relativePath);
        if ($normalized === '') {
            return;
        }

        $publicDir = rtrim($this->projectDir, '/\\') . \DIRECTORY_SEPARATOR . 'public' . \DIRECTORY_SEPARATOR;
        $sourceAbsolute = $publicDir . str_replace('/', \DIRECTORY_SEPARATOR, $normalized);

        $row = new UploadDeletionHistory();
        $row->setContext($context);
        $row->setOriginalRelativePath($normalized);
        $row->setCreatedAt(new \DateTimeImmutable());
        if ($deletedBy instanceof User) {
            $row->setCreatedBy($deletedBy);
        }
        if ($metadataJson !== null && $metadataJson !== '') {
            $row->setMetadata($metadataJson);
        }

        if (!is_file($sourceAbsolute)) {
            $row->setFileMissing(true);
            $row->setArchivedRelativePath(null);
            $this->entityManager->persist($row);
            $this->entityManager->flush();

            return;
        }

        $destDir = $publicDir . str_replace('/', \DIRECTORY_SEPARATOR, self::HISTORIQUE_BASE . '/' . $context);
        if (!is_dir($destDir) && !mkdir($destDir, 0755, true) && !is_dir($destDir)) {
            throw new \RuntimeException('Unable to create archive directory for deleted uploads.');
        }

        $basename = basename($normalized);
        $extension = pathinfo($basename, \PATHINFO_EXTENSION);
        $prefix = (new \DateTimeImmutable())->format('Ymd-His') . '-' . bin2hex(random_bytes(4));
        $archiveFilename = $extension !== '' ? $prefix . '.' . $extension : $prefix;
        $destAbsolute = $destDir . \DIRECTORY_SEPARATOR . $archiveFilename;

        if (!@rename($sourceAbsolute, $destAbsolute)) {
            if (!@copy($sourceAbsolute, $destAbsolute)) {
                throw new \RuntimeException('Unable to move deleted upload to history folder.');
            }
            if (!@unlink($sourceAbsolute)) {
                throw new \RuntimeException('Unable to remove source file after copy to history.');
            }
        }

        $archivedRelative = self::HISTORIQUE_BASE . '/' . $context . '/' . $archiveFilename;
        $row->setArchivedRelativePath($archivedRelative);
        $row->setFileMissing(false);

        $this->entityManager->persist($row);
        $this->entityManager->flush();
    }

    /**
     * @brief Validates context is a known archive subdirectory name.
     *
     * @param string $context The context string.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function assertValidContext(string $context): void
    {
        $allowed = [self::CONTEXT_ABOUT, self::CONTEXT_HOME_HERO, self::CONTEXT_GALLERY, self::CONTEXT_HOME_SERVICE_CARD, self::CONTEXT_SERVICE_TEASER, self::CONTEXT_SERVICE_DETAIL_HERO];
        if (!\in_array($context, $allowed, true)) {
            throw new \InvalidArgumentException('Invalid upload deletion context.');
        }
    }

    /**
     * @brief Normalizes a public-relative path (slashes, trim, no leading slash).
     *
     * @param string $relativePath The raw path.
     * @return string The normalized path or empty string.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function normalizeRelativePath(string $relativePath): string
    {
        $s = str_replace('\\', '/', trim($relativePath));
        $s = ltrim($s, '/');

        return $s;
    }
}
