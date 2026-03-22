<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Removes obsolete home quick-service CMS blocks and seeds default service card image paths.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class Version20260322160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Delete home quick.* content blocks and history; seed services.cardN.image for FR/DE when missing.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("DELETE FROM content_block WHERE page_name = 'home' AND block_key LIKE 'quick.%'");
        $this->addSql("DELETE FROM content_block_history WHERE page_name = 'home' AND block_key LIKE 'quick.%'");

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $defaults = [
            'services.card1.image' => 'images/services/reparation-carrosserie-peinture.webp',
            'services.card2.image' => 'images/services/debosselage.webp',
            'services.card3.image' => 'images/services/pare-brise.webp',
            'services.card4.image' => 'images/services/vehicule-pret-courtoisie.webp',
            'services.card5.image' => 'images/services/mecanique.webp',
        ];

        foreach (['fr', 'de'] as $locale) {
            foreach ($defaults as $blockKey => $value) {
                $count = (int) $this->connection->fetchOne(
                    'SELECT COUNT(*) FROM content_block WHERE page_name = ? AND block_key = ? AND locale = ?',
                    ['home', $blockKey, $locale]
                );
                if ($count > 0) {
                    continue;
                }

                $this->connection->executeStatement(
                    'INSERT INTO content_block (page_name, block_key, locale, type, value, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
                    ['home', $blockKey, $locale, 'plain', $value, $now],
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
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM content_block WHERE page_name = 'home' AND block_key IN (
            'services.card1.image',
            'services.card2.image',
            'services.card3.image',
            'services.card4.image',
            'services.card5.image'
        )");
    }
}
