<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Sets mechanical service card image to local mecanique.webp asset.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class Version20260322150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update service image path for entretien-mecanique to images/services/mecanique.webp';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE service SET image = 'images/services/mecanique.webp' WHERE slug = 'entretien-mecanique'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE service SET image = 'images/services/entretien-et-mecanique.svg' WHERE slug = 'entretien-mecanique'");
    }
}
