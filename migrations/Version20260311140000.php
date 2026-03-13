<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add nom column to user table.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
final class Version20260311140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add nom column to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` ADD nom VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP nom');
    }
}
