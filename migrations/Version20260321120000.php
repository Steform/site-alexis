<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Adds slug_de column to service table for SEO-friendly German URLs.
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
final class Version20260321120000 extends AbstractMigration
{
    /**
     * @brief Returns migration description.
     *
     * @return string The migration description.
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function getDescription(): string
    {
        return 'Add slug_de column to service table for SEO-friendly German URLs.';
    }

    /**
     * @brief Applies migration.
     *
     * @param Schema $schema The schema.
     * @return void
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function up(Schema $schema): void
    {
        $this->connection->executeStatement('ALTER TABLE service ADD slug_de VARCHAR(255) DEFAULT NULL');
        $this->connection->executeStatement('CREATE UNIQUE INDEX UNIQ_SERVICE_SLUG_DE ON service (slug_de)');

        $mapping = [
            'reparation-carrosserie-peinture' => 'karosserie-reparatur-lackierung',
            'debosselage' => 'beulenfrei',
            'pare-brise-optique' => 'windscheibe-scheiben',
            'entretien-mecanique' => 'mechanische-wartung',
            'vehicule-pret-courtoisie' => 'mietwagen-service',
        ];

        foreach ($mapping as $slug => $slugDe) {
            $this->connection->executeStatement(
                'UPDATE service SET slug_de = ? WHERE slug = ?',
                [$slugDe, $slug],
                [ParameterType::STRING, ParameterType::STRING]
            );
        }
    }

    /**
     * @brief Reverts migration.
     *
     * @param Schema $schema The schema.
     * @return void
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function down(Schema $schema): void
    {
        $this->connection->executeStatement('DROP INDEX UNIQ_SERVICE_SLUG_DE ON service');
        $this->connection->executeStatement('ALTER TABLE service DROP slug_de');
    }
}
