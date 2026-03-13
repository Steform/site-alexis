<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create gallery_item table.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
final class Version20260311160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create gallery_item table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE gallery_item (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) NOT NULL, ordre SMALLINT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE gallery_item');
    }
}
