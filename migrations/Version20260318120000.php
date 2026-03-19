<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Create AboutSection/AboutPhoto tables and seed initial content + photos.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
final class Version20260318120000 extends AbstractMigration
{
    /**
     * @brief Returns the migration description.
     *
     * @return string The migration description.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getDescription(): string
    {
        return 'Add AboutSection and AboutPhoto with initial seed data.';
    }

    /**
     * @brief Creates tables and inserts the initial about content.
     *
     * @param Schema $schema The database schema.
     * @return void
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function up(Schema $schema): void
    {
        $createAboutSectionSql = 'CREATE TABLE about_section (id INT AUTO_INCREMENT NOT NULL, lead_fr VARCHAR(255) NOT NULL, lead_de VARCHAR(255) NOT NULL, content_fr LONGTEXT NOT NULL, content_de LONGTEXT NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $createAboutPhotoSql = 'CREATE TABLE about_photo (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) NOT NULL, alt_fr VARCHAR(255) DEFAULT NULL, alt_de VARCHAR(255) DEFAULT NULL, position SMALLINT NOT NULL, is_active TINYINT(1) NOT NULL, about_section_id INT DEFAULT NULL, INDEX IDX_9F0B5B5E9B6B4B9F (about_section_id), PRIMARY KEY(id), CONSTRAINT FK_9F0B5B5E9B6B4B9F FOREIGN KEY (about_section_id) REFERENCES about_section (id) ON DELETE SET NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';

        // Execute CREATE TABLE statements immediately so inserts can run safely.
        $this->connection->executeStatement($createAboutSectionSql);
        $this->connection->executeStatement($createAboutPhotoSql);

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $leadFr = 'Alexis Haffner, carrossier à Baldersheim.';
        $leadDe = 'Alexis Haffner, Karosseriespengler in Baldersheim.';

        $contentFr = '<p>Alexis travaille avec son père dans une entreprise familiale. Expertise en réparation, peinture et entretien pour tous véhicules. Travail soigné, équipe à l\'écoute et solutions rapides depuis 2007.</p>';
        $contentDe = '<p>Alexis arbeitet gemeinsam mit seinem Vater in einem Familienbetrieb. Kompetenz in Reparatur, Lackierung und Wartung für alle Fahrzeuge. Sorgfältige Arbeit, ein Team mit offenen Ohren und schnelle Lösungen seit 2007.</p>';

        // Insert initial data.

        $this->connection->executeStatement(
            'INSERT INTO about_section (lead_fr, lead_de, content_fr, content_de, updated_at) VALUES (?, ?, ?, ?, ?)',
            [$leadFr, $leadDe, $contentFr, $contentDe, $now],
            [
                \Doctrine\DBAL\ParameterType::STRING,
                \Doctrine\DBAL\ParameterType::STRING,
                \Doctrine\DBAL\ParameterType::STRING,
                \Doctrine\DBAL\ParameterType::STRING,
                \Doctrine\DBAL\ParameterType::STRING,
            ]
        );

        $aboutSectionId = (int) $this->connection->lastInsertId();

        $photos = [
            [
                'images/qui-sommes-nous/qui-sommes-nous.webp',
                'Carrosserie Lino - Qui sommes-nous',
                'Carrosserie Lino - Über uns',
                0,
            ],
            [
                'images/qui-sommes-nous/qui-sommes-nous-2.webp',
                'Carrosserie Lino - Qui sommes-nous',
                'Carrosserie Lino - Über uns',
                1,
            ],
            [
                'images/qui-sommes-nous/qui-sommes-nous-3.jpg',
                'Carrosserie Lino - Qui sommes-nous',
                'Carrosserie Lino - Über uns',
                2,
            ],
        ];

        foreach ($photos as $p) {
            $this->connection->executeStatement(
                'INSERT INTO about_photo (image, alt_fr, alt_de, position, is_active, about_section_id) VALUES (?, ?, ?, ?, ?, ?)',
                [$p[0], $p[1], $p[2], $p[3], 1, $aboutSectionId],
                [
                    \Doctrine\DBAL\ParameterType::STRING,
                    \Doctrine\DBAL\ParameterType::STRING,
                    \Doctrine\DBAL\ParameterType::STRING,
                    \Doctrine\DBAL\ParameterType::INTEGER,
                    \Doctrine\DBAL\ParameterType::INTEGER,
                    \Doctrine\DBAL\ParameterType::INTEGER,
                ]
            );
        }
    }

    /**
     * @brief Drops tables.
     *
     * @param Schema $schema The database schema.
     * @return void
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE about_photo');
        $this->addSql('DROP TABLE about_section');
    }
}

