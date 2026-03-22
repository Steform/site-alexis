<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @brief Serializes entity field state to/from JSON-safe arrays for snapshot history.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class EntitySnapshotStateSerializer
{
    /**
     * @brief Builds a snapshot array from a managed entity (current DB state).
     *
     * @param EntityManagerInterface $em The entity manager.
     * @param object $entity The entity.
     * @return array<string, mixed> Serializable field map including id and association Ids.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function normalizeEntity(EntityManagerInterface $em, object $entity): array
    {
        $meta = $em->getClassMetadata($entity::class);
        $data = [];

        $idField = $meta->getSingleIdentifierFieldName();
        $data[$idField] = $meta->getFieldValue($entity, $idField);

        foreach ($meta->getFieldNames() as $fieldName) {
            if ($fieldName === $idField) {
                continue;
            }
            $value = $meta->getFieldValue($entity, $fieldName);
            $data[$fieldName] = $this->normalizeScalarValue($value, $meta->getTypeOfField($fieldName));
        }

        foreach ($meta->getAssociationMappings() as $assocName => $mapping) {
            if (($mapping['type'] & ClassMetadata::MANY_TO_ONE) === 0 && ($mapping['type'] & ClassMetadata::ONE_TO_ONE) === 0) {
                continue;
            }
            if (!($mapping['isOwningSide'] ?? true)) {
                continue;
            }
            $related = $meta->getFieldValue($entity, $assocName);
            $data[$assocName . 'Id'] = $related !== null && \is_object($related) && method_exists($related, 'getId')
                ? $related->getId()
                : null;
        }

        return $data;
    }

    /**
     * @brief Converts Doctrine original entity data (pre-update) to JSON-safe values.
     *
     * @param array<string, mixed> $original Original entity data from UnitOfWork.
     * @param ClassMetadata<object> $meta Class metadata.
     * @return array<string, mixed> Serializable map.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function normalizeOriginalData(array $original, ClassMetadata $meta): array
    {
        $data = [];
        foreach ($original as $fieldName => $value) {
            if ($meta->hasField($fieldName)) {
                $data[$fieldName] = $this->normalizeScalarValue($value, $meta->getTypeOfField($fieldName));

                continue;
            }
            if ($meta->hasAssociation($fieldName)) {
                $mapping = $meta->getAssociationMapping($fieldName);
                if (($mapping['type'] & ClassMetadata::MANY_TO_ONE) > 0 || ($mapping['type'] & ClassMetadata::ONE_TO_ONE) > 0) {
                    if (\is_object($value) && method_exists($value, 'getId')) {
                        $data[$fieldName . 'Id'] = $value->getId();
                    } elseif ($value === null) {
                        $data[$fieldName . 'Id'] = null;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @brief Applies snapshot data onto an existing entity (update rollback).
     *
     * @param EntityManagerInterface $em The entity manager.
     * @param object $entity The target entity.
     * @param array<string, mixed> $data The snapshot data.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function applyDataToEntity(EntityManagerInterface $em, object $entity, array $data): void
    {
        $meta = $em->getClassMetadata($entity::class);
        $idField = $meta->getSingleIdentifierFieldName();

        foreach ($meta->getFieldNames() as $fieldName) {
            if ($fieldName === $idField) {
                continue;
            }
            if (!\array_key_exists($fieldName, $data)) {
                continue;
            }
            $raw = $data[$fieldName];
            $value = $this->denormalizeScalarValue($raw, $meta->getTypeOfField($fieldName));
            $meta->setFieldValue($entity, $fieldName, $value);
        }

        foreach ($meta->getAssociationMappings() as $assocName => $mapping) {
            if (($mapping['type'] & ClassMetadata::MANY_TO_ONE) === 0 && ($mapping['type'] & ClassMetadata::ONE_TO_ONE) === 0) {
                continue;
            }
            if (!($mapping['isOwningSide'] ?? true)) {
                continue;
            }
            $key = $assocName . 'Id';
            if (!\array_key_exists($key, $data)) {
                continue;
            }
            $targetId = $data[$key];
            $targetClass = $mapping['targetEntity'];
            if ($targetId === null) {
                $meta->setFieldValue($entity, $assocName, null);

                continue;
            }
            $meta->setFieldValue($entity, $assocName, $em->getReference($targetClass, (int) $targetId));
        }
    }

    /**
     * @brief Creates a new entity instance from snapshot data (delete rollback).
     *
     * @param EntityManagerInterface $em The entity manager.
     * @param class-string $className The entity class.
     * @param array<string, mixed> $data The snapshot data.
     * @return object The new entity (not yet persisted).
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function createEntityFromData(EntityManagerInterface $em, string $className, array $data): object
    {
        $meta = $em->getClassMetadata($className);
        $entity = $meta->newInstance();
        $idField = $meta->getSingleIdentifierFieldName();
        $meta->setFieldValue($entity, $idField, null);

        $copy = $data;
        unset($copy[$idField]);

        $this->applyDataToEntity($em, $entity, $copy);

        return $entity;
    }

    /**
     * @brief Normalizes a single value for JSON encoding.
     *
     * @param mixed $value The value.
     * @param string $fieldType The Doctrine field type.
     * @return mixed JSON-safe value.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function normalizeScalarValue(mixed $value, string $fieldType): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            if (\str_contains($fieldType, 'datetime') || (\str_contains($fieldType, 'date') && !\str_contains($fieldType, 'time'))) {
                return $value->format('c');
            }
            if (\str_contains($fieldType, 'time')) {
                return $value->format('H:i:s');
            }

            return $value->format('c');
        }
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return $value;
    }

    /**
     * @brief Converts JSON value back to PHP value for a field type.
     *
     * @param mixed $raw The raw value.
     * @param string $fieldType The Doctrine field type.
     * @return mixed The PHP value.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function denormalizeScalarValue(mixed $raw, string $fieldType): mixed
    {
        if ($raw === null) {
            return null;
        }
        if (!\is_string($raw)) {
            return $raw;
        }
        if (\str_contains($fieldType, 'datetime') || (\str_contains($fieldType, 'date') && !\str_contains($fieldType, 'time'))) {
            if (\str_contains($fieldType, 'mutable')) {
                return new \DateTime($raw);
            }

            return new \DateTimeImmutable($raw);
        }
        if (\str_contains($fieldType, 'time')) {
            $base = date('Y-m-d');
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $base . ' ' . $raw)
                ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i', $base . ' ' . $raw);

            return $dt ?? new \DateTimeImmutable($raw);
        }

        return $raw;
    }
}

