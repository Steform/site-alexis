<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Migrates home hero legacy fields into hero.top_content.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
final class Version20260319113000 extends AbstractMigration
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
        return 'Create home hero.top_content values from hero.ribbon, hero.title and hero.subtitle for FR/DE.';
    }

    /**
     * @brief Migrates legacy home hero values to a single rich content block.
     *
     * @param Schema $schema The schema.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function up(Schema $schema): void
    {
        foreach (['fr', 'de'] as $locale) {
            $ribbon = $this->getValue('hero.ribbon', $locale);
            $title = $this->getValue('hero.title', $locale);
            $subtitle = $this->getValue('hero.subtitle', $locale);

            $richValue = sprintf(
                "<p class='text-uppercase mb-2 opacity-90' style='font-size: 0.9rem; letter-spacing: 0.15em;'>%s</p><h1 class='mb-3'>%s</h1><p class='lead mb-0'>%s</p>",
                $this->escapeHtml($ribbon),
                $this->escapeHtml($title),
                $this->escapeHtml($subtitle)
            );

            $color = $this->getColor($locale);
            $this->connection->executeStatement(
                'INSERT INTO content_block (page_name, block_key, locale, type, value, color, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE value = VALUES(value), color = VALUES(color), updated_at = VALUES(updated_at)',
                ['home', 'hero.top_content', $locale, 'rich', $richValue, $color],
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
     * @brief Removes migrated hero.top_content blocks.
     *
     * @param Schema $schema The schema.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM content_block WHERE page_name = 'home' AND block_key = 'hero.top_content'");
    }

    /**
     * @brief Returns a legacy hero value for one locale.
     *
     * @param string $blockKey The legacy block key.
     * @param string $locale The locale.
     * @return string The block value or empty string.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function getValue(string $blockKey, string $locale): string
    {
        $result = $this->connection->fetchOne(
            'SELECT value FROM content_block WHERE page_name = ? AND block_key = ? AND locale = ? LIMIT 1',
            ['home', $blockKey, $locale],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::STRING]
        );

        return is_string($result) ? trim($result) : '';
    }

    /**
     * @brief Returns a legacy hero color for one locale.
     *
     * @param string $locale The locale.
     * @return string The color.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function getColor(string $locale): string
    {
        $result = $this->connection->fetchOne(
            "SELECT color FROM content_block WHERE page_name = 'home' AND block_key IN ('hero.title', 'hero.ribbon', 'hero.subtitle') AND locale = ? AND color IS NOT NULL LIMIT 1",
            [$locale],
            [ParameterType::STRING]
        );

        if (is_string($result) && preg_match('/^#[0-9a-fA-F]{6}$/', $result) === 1) {
            return strtoupper($result);
        }

        return '#FFFFFF';
    }

    /**
     * @brief Escapes text for safe HTML interpolation.
     *
     * @param string $value The input value.
     * @return string The escaped value.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

