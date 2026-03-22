<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates service table and seeds initial services.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
final class Version20260319170000 extends AbstractMigration
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
        return 'Create service table and seed initial car body shop services.';
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
        $this->connection->executeStatement('CREATE TABLE `service` (
            id INT AUTO_INCREMENT NOT NULL,
            slug VARCHAR(255) NOT NULL,
            titre VARCHAR(255) NOT NULL,
            titre_de VARCHAR(255) DEFAULT NULL,
            description LONGTEXT DEFAULT NULL,
            description_de LONGTEXT DEFAULT NULL,
            image VARCHAR(255) NOT NULL,
            ordre SMALLINT NOT NULL DEFAULT 0,
            UNIQUE INDEX UNIQ_E19D9AD2989D9B62 (slug),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $services = [
            ['reparation-carrosserie-peinture', 'Réparation carrosserie & peinture', 'Reparatur & Lackierung', 'images/services/reparation-carrosserie-peinture.webp', 1],
            ['debosselage', 'Débosselage', 'Ausbeulen', 'images/services/debosselage.webp', 2],
            ['pare-brise-optique', 'Pare-brise & optique', 'Scheiben & Verglasung', 'images/services/pare-brise.webp', 3],
            ['entretien-mecanique', 'Entretien & mécanique', 'Wartung & Mechanik', 'images/services/entretien-et-mecanique.svg', 4],
            ['vehicule-pret-courtoisie', 'Véhicule de prêt courtoisie', 'Ersatzfahrzeuge', 'images/services/vehicule-pret-courtoisie.webp', 5],
        ];

        foreach ($services as $s) {
            $this->connection->executeStatement(
                'INSERT INTO `service` (slug, titre, titre_de, description, description_de, image, ordre) VALUES (?, ?, ?, NULL, NULL, ?, ?)',
                [$s[0], $s[1], $s[2], $s[3], $s[4]],
                [ParameterType::STRING, ParameterType::STRING, ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
            );
        }
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
        $this->connection->executeStatement('DROP TABLE `service`');
    }
}
