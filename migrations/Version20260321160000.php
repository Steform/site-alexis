<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates services_why_card table and seeds initial "Why choose us" cards.
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
final class Version20260321160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create services_why_card table and seed initial why choose us cards.';
    }

    public function up(Schema $schema): void
    {
        $this->connection->executeStatement('CREATE TABLE `services_why_card` (
            id INT AUTO_INCREMENT NOT NULL,
            position SMALLINT NOT NULL DEFAULT 0,
            title_fr VARCHAR(255) NOT NULL,
            title_de VARCHAR(255) NOT NULL,
            text_fr LONGTEXT NOT NULL,
            text_de LONGTEXT NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $cards = [
            [
                0,
                'Comment ça se passe',
                'So funktioniert es',
                'Contactez-nous pour un devis ou un rendez-vous. Nous examinons votre véhicule, établissons un devis détaillé et planifions l\'intervention. Transparence et conseils honnêtes à chaque étape.',
                'Kontaktieren Sie uns für eine Offerte oder einen Termin. Wir prüfen Ihr Fahrzeug, erstellen ein detailliertes Angebot und planen die Arbeit. Transparenz und ehrliche Beratung bei jedem Schritt.',
            ],
            [
                1,
                'Les avantages Carrosserie Lino',
                'Vorteile der Carrosserie Lino',
                'Entreprise familiale, véhicules de courtoisie disponibles. Un garage de proximité à Baldersheim, à votre écoute depuis 2007.',
                'Familienbetrieb, Ersatzfahrzeuge verfügbar. Eine Werkstatt vor Ort in Baldersheim, seit 2007 für Sie da.',
            ],
            [
                2,
                'Pourquoi nous faire confiance ?',
                'Warum uns vertrauen?',
                'Travail soigné, équipe à l\'écoute et solutions adaptées à votre budget. Nos clients nous font confiance — consultez leurs avis ci-dessous.',
                'Sorgfältige Arbeit, ein Team mit offenen Ohren und Lösungen, die zu Ihrem Budget passen. Unsere Kunden vertrauen uns — lesen Sie ihre Bewertungen unten.',
            ],
        ];

        foreach ($cards as $card) {
            $this->connection->executeStatement(
                'INSERT INTO services_why_card (position, title_fr, title_de, text_fr, text_de) VALUES (?, ?, ?, ?, ?)',
                $card,
                [ParameterType::INTEGER, ParameterType::STRING, ParameterType::STRING, ParameterType::STRING, ParameterType::STRING]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->connection->executeStatement('DROP TABLE `services_why_card`');
    }
}
