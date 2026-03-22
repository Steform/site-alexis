<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates admin_audit_log for generic back-office audit trail.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class Version20260322140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create admin_audit_log table for generic back-office audit entries.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE admin_audit_log (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(96) NOT NULL, payload LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by_id INT DEFAULT NULL, INDEX idx_admin_audit_log_created_at (created_at), INDEX idx_admin_audit_log_action (action), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_audit_log ADD CONSTRAINT FK_ADMIN_AUDIT_LOG_USER FOREIGN KEY (created_by_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admin_audit_log DROP FOREIGN KEY FK_ADMIN_AUDIT_LOG_USER');
        $this->addSql('DROP TABLE admin_audit_log');
    }
}
