<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates entity_snapshot_history for rollback of mutable entities.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class Version20260322200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create entity_snapshot_history for full entity snapshots and rollback.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE entity_snapshot_history (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(64) NOT NULL, entity_class VARCHAR(255) NOT NULL, entity_id INT DEFAULT NULL, change_kind VARCHAR(16) NOT NULL, snapshot_json LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by_id INT DEFAULT NULL, INDEX idx_entity_snapshot_domain_created (domain, created_at), INDEX idx_entity_snapshot_entity (domain, entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entity_snapshot_history ADD CONSTRAINT FK_ENTITY_SNAPSHOT_USER FOREIGN KEY (created_by_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE entity_snapshot_history DROP FOREIGN KEY FK_ENTITY_SNAPSHOT_USER');
        $this->addSql('DROP TABLE entity_snapshot_history');
    }
}
