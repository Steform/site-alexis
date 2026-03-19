<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates content_block table and seeds phase 1 content.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
final class Version20260318133000 extends AbstractMigration
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
        return 'Create content_block table and seed home/qui_sommes_nous texts.';
    }

    /**
     * @brief Creates the table and seeds initial content blocks.
     *
     * @param Schema $schema The schema.
     * @return void
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function up(Schema $schema): void
    {
        $createSql = 'CREATE TABLE content_block (id INT AUTO_INCREMENT NOT NULL, page_name VARCHAR(100) NOT NULL, block_key VARCHAR(150) NOT NULL, locale VARCHAR(5) NOT NULL, type VARCHAR(20) NOT NULL, value LONGTEXT NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7E4975589E33C1EA (page_name), UNIQUE INDEX uniq_content_block_page_key_locale (page_name, block_key, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->connection->executeStatement($createSql);

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $seed = [
            ['home', 'hero.ribbon', 'fr', 'plain', 'Expérimentés & professionnels'],
            ['home', 'hero.title', 'fr', 'plain', 'Carrosserie & réparation auto'],
            ['home', 'hero.subtitle', 'fr', 'plain', 'À votre service depuis 2007'],
            ['home', 'hero.cta', 'fr', 'plain', 'Obtenez un devis'],
            ['home', 'about.title', 'fr', 'plain', 'Qui sommes-nous ?'],
            ['home', 'about.lead', 'fr', 'plain', 'Alexis Haffner, carrossier à Baldersheim.'],
            ['home', 'about.body', 'fr', 'rich', '<p>Alexis travaille avec son père dans une entreprise familiale. Expertise en réparation, peinture et entretien pour tous véhicules. Travail soigné, équipe à l\'écoute et solutions rapides depuis 2007.</p>'],
            ['home', 'about.cta', 'fr', 'plain', 'Découvrir notre équipe'],
            ['qui_sommes_nous', 'title', 'fr', 'plain', 'Qui sommes-nous ?'],
            ['qui_sommes_nous', 'alexis.role', 'fr', 'plain', 'Carrossier'],
            ['qui_sommes_nous', 'alexis.lead', 'fr', 'rich', '<p>À la tête de la Carrosserie Lino depuis 2022, Alexis Haffner perpétue l\'excellence du garage avec une vision familiale et professionnelle.</p>'],
            ['qui_sommes_nous', 'alexis.text1', 'fr', 'rich', '<p>Alexis travaille aux côtés de son père pour vous offrir un service de qualité. Une équipe familiale, des valeurs de confiance et de proximité : la Carrosserie Lino, c\'est bien plus qu\'un garage — c\'est une histoire de famille au service de votre véhicule.</p>'],
            ['qui_sommes_nous', 'alexis.text2', 'fr', 'rich', '<p>Réparation, peinture, débosselage : expertise et savoir-faire transmis de génération en génération pour vous garantir un travail soigné et des conseils honnêtes.</p>'],
            ['qui_sommes_nous', 'cta.quote', 'fr', 'plain', 'Demander un devis'],
            ['qui_sommes_nous', 'card.family.title', 'fr', 'plain', 'Entreprise familiale'],
            ['qui_sommes_nous', 'card.family.text', 'fr', 'plain', 'Alexis et son père travaillent ensemble pour vous offrir un service personnalisé et de confiance.'],
            ['qui_sommes_nous', 'card.expertise.title', 'fr', 'plain', 'Expertise carrosserie'],
            ['qui_sommes_nous', 'card.expertise.text', 'fr', 'plain', 'Réparation, peinture, débosselage : un savoir-faire reconnu pour tous types de véhicules.'],
            ['qui_sommes_nous', 'card.location.title', 'fr', 'plain', 'Baldersheim'],
            ['qui_sommes_nous', 'card.location.text', 'fr', 'plain', 'Votre garage de proximité à Baldersheim, à votre service depuis 2007.'],

            ['home', 'hero.ribbon', 'de', 'plain', 'Erfahren & professionell'],
            ['home', 'hero.title', 'de', 'plain', 'Karosserie- und Autoreparatur'],
            ['home', 'hero.subtitle', 'de', 'plain', 'Seit 2007 für Sie da'],
            ['home', 'hero.cta', 'de', 'plain', 'Offerte anfordern'],
            ['home', 'about.title', 'de', 'plain', 'Über uns'],
            ['home', 'about.lead', 'de', 'plain', 'Alexis Haffner, Karosseriespengler in Baldersheim.'],
            ['home', 'about.body', 'de', 'rich', '<p>Alexis arbeitet gemeinsam mit seinem Vater in einem Familienbetrieb. Kompetenz in Reparatur, Lackierung und Wartung für alle Fahrzeuge. Sorgfältige Arbeit, ein Team mit offenen Ohren und schnelle Lösungen seit 2007.</p>'],
            ['home', 'about.cta', 'de', 'plain', 'Unser Team entdecken'],
            ['qui_sommes_nous', 'title', 'de', 'plain', 'Über uns'],
            ['qui_sommes_nous', 'alexis.role', 'de', 'plain', 'Karosseriespengler'],
            ['qui_sommes_nous', 'alexis.lead', 'de', 'rich', '<p>Seit 2022 leitet Alexis Haffner die Carrosserie Lino und führt die Qualität des Betriebs mit einer familiären und professionellen Vision weiter.</p>'],
            ['qui_sommes_nous', 'alexis.text1', 'de', 'rich', '<p>Alexis arbeitet zusammen mit seinem Vater, um Ihnen einen hochwertigen Service zu bieten. Ein Familienbetrieb mit Werten wie Vertrauen und Nähe: Die Carrosserie Lino ist mehr als nur eine Werkstatt – sie ist eine Familiengeschichte im Dienst Ihres Fahrzeugs.</p>'],
            ['qui_sommes_nous', 'alexis.text2', 'de', 'rich', '<p>Reparatur, Lackierung, Ausbeulen: Fachwissen und Know-how, die von Generation zu Generation weitergegeben werden, um eine sorgfältige Arbeit und ehrliche Beratung zu garantieren.</p>'],
            ['qui_sommes_nous', 'cta.quote', 'de', 'plain', 'Offerte anfordern'],
            ['qui_sommes_nous', 'card.family.title', 'de', 'plain', 'Familienbetrieb'],
            ['qui_sommes_nous', 'card.family.text', 'de', 'plain', 'Alexis und sein Vater arbeiten gemeinsam, um Ihnen einen persönlichen und vertrauenswürdigen Service zu bieten.'],
            ['qui_sommes_nous', 'card.expertise.title', 'de', 'plain', 'Karosserie-Expertise'],
            ['qui_sommes_nous', 'card.expertise.text', 'de', 'plain', 'Reparatur, Lackierung, Ausbeulen: anerkanntes Know-how für alle Fahrzeugtypen.'],
            ['qui_sommes_nous', 'card.location.title', 'de', 'plain', 'Baldersheim'],
            ['qui_sommes_nous', 'card.location.text', 'de', 'plain', 'Ihre Werkstatt vor Ort in Baldersheim, seit 2007 für Sie da.'],
        ];

        foreach ($seed as $row) {
            $this->connection->executeStatement(
                'INSERT INTO content_block (page_name, block_key, locale, type, value, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
                [$row[0], $row[1], $row[2], $row[3], $row[4], $now],
                [
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                ]
            );
        }
    }

    /**
     * @brief Drops content_block table.
     *
     * @param Schema $schema The schema.
     * @return void
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE content_block');
    }
}

