<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Adds color support for CMS content blocks.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
final class Version20260319100000 extends AbstractMigration
{
    /**
     * @brief Returns the migration description.
     *
     * @return string The migration description.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getDescription(): string
    {
        return 'Add content_block color column and initialize default colors for home and qui_sommes_nous.';
    }

    /**
     * @brief Adds color column and initializes default values.
     *
     * @param Schema $schema The schema.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function up(Schema $schema): void
    {
        $this->connection->executeStatement("ALTER TABLE content_block ADD color VARCHAR(7) DEFAULT NULL AFTER value");

        $colorMap = [
            'home' => [
                'hero.ribbon' => '#FFFFFF',
                'hero.title' => '#FFFFFF',
                'hero.subtitle' => '#FFFFFF',
                'hero.cta' => '#FFFFFF',
                'about.title' => '#212529',
                'about.lead' => '#212529',
                'about.body' => '#6C757D',
                'about.cta' => '#FFFFFF',
            ],
            'qui_sommes_nous' => [
                'title' => '#212529',
                'alexis.role' => '#6C757D',
                'alexis.lead' => '#212529',
                'alexis.text1' => '#212529',
                'alexis.text2' => '#212529',
                'cta.quote' => '#FFFFFF',
                'card.family.title' => '#212529',
                'card.family.text' => '#6C757D',
                'card.expertise.title' => '#212529',
                'card.expertise.text' => '#6C757D',
                'card.location.title' => '#212529',
                'card.location.text' => '#6C757D',
            ],
        ];

        foreach ($colorMap as $pageName => $blocks) {
            foreach ($blocks as $blockKey => $color) {
                $this->connection->executeStatement(
                    'UPDATE content_block SET color = ? WHERE page_name = ? AND block_key = ?',
                    [$color, $pageName, $blockKey],
                    [ParameterType::STRING, ParameterType::STRING, ParameterType::STRING]
                );
            }
        }
    }

    /**
     * @brief Removes color support from CMS content blocks.
     *
     * @param Schema $schema The schema.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE content_block DROP color');
    }
}

