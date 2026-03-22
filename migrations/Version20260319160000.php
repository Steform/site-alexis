<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates content_block_history table for modification history.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
final class Version20260319160000 extends AbstractMigration
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
        return 'Create content_block_history table for content modification history.';
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
        $this->addSql('CREATE TABLE content_block_history (
            id INT AUTO_INCREMENT NOT NULL,
            page_name VARCHAR(100) NOT NULL,
            block_key VARCHAR(150) NOT NULL,
            locale VARCHAR(5) NOT NULL,
            value LONGTEXT NOT NULL,
            color VARCHAR(7) DEFAULT NULL,
            color_dark VARCHAR(7) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            created_by_id INT DEFAULT NULL,
            INDEX IDX_content_block_history_lookup (page_name, block_key, locale, created_at),
            INDEX IDX_content_block_history_created_by (created_by_id),
            PRIMARY KEY(id),
            CONSTRAINT FK_content_block_history_created_by FOREIGN KEY (created_by_id) REFERENCES `user` (id) ON DELETE SET NULL
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
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
        $this->addSql('DROP TABLE content_block_history');
    }
}
