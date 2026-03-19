<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates home hero slider table and seeds top image.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
final class Version20260319123000 extends AbstractMigration
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
        return 'Create home_hero_photo table and seed with existing top.webp image.';
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
        $this->addSql('CREATE TABLE home_hero_photo (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) NOT NULL, alt_fr VARCHAR(255) DEFAULT NULL, alt_de VARCHAR(255) DEFAULT NULL, position SMALLINT NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql("INSERT INTO home_hero_photo (image, alt_fr, alt_de, position, is_active) VALUES ('images/top.webp', 'Image de couverture', 'Titelbild', 0, 1)");
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
        $this->addSql('DROP TABLE home_hero_photo');
    }
}

