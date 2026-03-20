<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Adds color_dark column to content_block for dark mode text colors.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
final class Version20260319150000 extends AbstractMigration
{
    /**
     * @brief Returns migration description.
     *
     * @return string The migration description.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getDescription(): string
    {
        return 'Add color_dark column to content_block table.';
    }

    /**
     * @brief Applies migration.
     *
     * @param Schema $schema The schema.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE content_block ADD color_dark VARCHAR(7) DEFAULT NULL');
    }

    /**
     * @brief Reverts migration.
     *
     * @param Schema $schema The schema.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE content_block DROP color_dark');
    }
}
