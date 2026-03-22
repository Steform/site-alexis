<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Adds optional detail page hero image path on service (distinct from list teaser image).
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class Version20260322180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add service.detail_hero_image nullable column for public service detail hero.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE service ADD detail_hero_image VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE service DROP detail_hero_image');
    }
}
