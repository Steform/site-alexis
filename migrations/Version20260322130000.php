<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates upload_deletion_history for archived deleted upload files.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class Version20260322130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create upload_deletion_history table for deleted image archive audit trail.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE upload_deletion_history (id INT AUTO_INCREMENT NOT NULL, context VARCHAR(32) NOT NULL, original_relative_path VARCHAR(512) NOT NULL, archived_relative_path VARCHAR(512) DEFAULT NULL, file_missing TINYINT(1) NOT NULL DEFAULT 0, metadata LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by_id INT DEFAULT NULL, INDEX idx_upload_deletion_history_created_at (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE upload_deletion_history ADD CONSTRAINT FK_UPLOAD_DEL_HIST_USER FOREIGN KEY (created_by_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE upload_deletion_history DROP FOREIGN KEY FK_UPLOAD_DEL_HIST_USER');
        $this->addSql('DROP TABLE upload_deletion_history');
    }
}
