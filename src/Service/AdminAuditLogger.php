<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AdminAuditLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @brief Persists sanitized audit entries to AdminAuditLog.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class AdminAuditLogger
{
    private const MAX_STRING_LENGTH = 500;

    private const MAX_DEPTH = 8;

    /**
     * @brief Keys (substring match, case-insensitive) to strip from payloads.
     */
    private const SENSITIVE_KEY_FRAGMENTS = [
        'password',
        'plainpassword',
        'csrf',
        'token',
        'secret',
        'authorization',
        'cookie',
    ];

    /**
     * @brief Creates the logger.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @brief Records an audit row with JSON payload (secrets removed, strings truncated).
     *
     * @param string $action The action code (see AdminAuditActions).
     * @param array<string, mixed> $payload The raw payload.
     * @param UserInterface|null $user The acting user.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function log(string $action, array $payload, ?UserInterface $user = null): void
    {
        $safe = $this->sanitizePayload($payload, 0);
        $json = json_encode($safe, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE);

        $row = new AdminAuditLog();
        $row->setAction($action);
        $row->setPayload($json);
        $row->setCreatedAt(new \DateTimeImmutable());
        if ($user instanceof User) {
            $row->setCreatedBy($user);
        }

        $this->entityManager->persist($row);
        $this->entityManager->flush();
    }

    /**
     * @brief Sanitizes a payload array for JSON storage.
     *
     * @param array<string, mixed> $payload The raw payload.
     * @param int $depth Current recursion depth.
     * @return array<string, mixed> The sanitized array.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function sanitizePayload(array $payload, int $depth): array
    {
        if ($depth > self::MAX_DEPTH) {
            return ['_truncated' => 'max_depth'];
        }

        $out = [];
        foreach ($payload as $key => $value) {
            if (!\is_string($key)) {
                continue;
            }
            if ($this->isSensitiveKey($key)) {
                continue;
            }
            $out[$key] = $this->sanitizeValue($value, $depth + 1);
        }

        return $out;
    }

    /**
     * @brief Checks whether a key name should be excluded.
     *
     * @param string $key The key.
     * @return bool True if excluded.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function isSensitiveKey(string $key): bool
    {
        $lower = strtolower($key);
        foreach (self::SENSITIVE_KEY_FRAGMENTS as $frag) {
            if (str_contains($lower, $frag)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @brief Sanitizes a scalar or nested structure.
     *
     * @param mixed $value The value.
     * @param int $depth Current depth.
     * @return mixed The sanitized value.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function sanitizeValue(mixed $value, int $depth): mixed
    {
        if ($depth > self::MAX_DEPTH) {
            return '_truncated';
        }
        if (\is_array($value)) {
            /** @var array<string, mixed> $value */
            return $this->sanitizePayload($value, $depth);
        }
        if (\is_string($value)) {
            if (strlen($value) > self::MAX_STRING_LENGTH) {
                return substr($value, 0, self::MAX_STRING_LENGTH) . '…';
            }

            return $value;
        }
        if (\is_bool($value) || \is_int($value) || $value === null) {
            return $value;
        }
        if (\is_float($value)) {
            return $value;
        }

        return (string) $value;
    }
}
