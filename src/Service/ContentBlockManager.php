<?php

namespace App\Service;

use App\Entity\ContentBlock;
use App\Repository\ContentBlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @brief Manages editable CMS blocks with translation fallback.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
class ContentBlockManager
{
    /**
     * @var array<string, array<string, string>>
     */
    private const DEFAULT_COLORS = [
        'home' => [
            'hero.top_content' => '#FFFFFF',
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

    /**
     * @var array<string, array<string, array{type: string, translation_key: string}>>
     */
    private const PAGE_DEFINITIONS = [
        'home' => [
            'hero.top_content' => ['type' => 'rich', 'translation_key' => 'home.hero.top_content'],
            'hero.text_shadow_color' => ['type' => 'plain', 'translation_key' => 'home.hero.text_shadow_color'],
            'hero.custom_css' => ['type' => 'plain', 'translation_key' => 'home.hero.custom_css'],
            'hero.cta' => ['type' => 'plain', 'translation_key' => 'home.hero.cta'],
            'about.title' => ['type' => 'plain', 'translation_key' => 'home.about.title'],
            'about.lead' => ['type' => 'plain', 'translation_key' => 'home.about.lead'],
            'about.body' => ['type' => 'rich', 'translation_key' => 'home.about.text'],
            'about.cta' => ['type' => 'plain', 'translation_key' => 'home.about.cta'],
        ],
        'qui_sommes_nous' => [
            'title' => ['type' => 'plain', 'translation_key' => 'qui_sommes_nous.title'],
            'alexis.role' => ['type' => 'plain', 'translation_key' => 'qui_sommes_nous.alexis.role'],
            'alexis.lead' => ['type' => 'rich', 'translation_key' => 'qui_sommes_nous.alexis.lead'],
            'alexis.text1' => ['type' => 'rich', 'translation_key' => 'qui_sommes_nous.alexis.text1'],
            'alexis.text2' => ['type' => 'rich', 'translation_key' => 'qui_sommes_nous.alexis.text2'],
            'cta.quote' => ['type' => 'plain', 'translation_key' => 'qui_sommes_nous.cta.quote'],
            'card.family.title' => ['type' => 'plain', 'translation_key' => 'qui_sommes_nous.card.family.title'],
            'card.family.text' => ['type' => 'plain', 'translation_key' => 'qui_sommes_nous.card.family.text'],
            'card.expertise.title' => ['type' => 'plain', 'translation_key' => 'qui_sommes_nous.card.expertise.title'],
            'card.expertise.text' => ['type' => 'plain', 'translation_key' => 'qui_sommes_nous.card.expertise.text'],
            'card.location.title' => ['type' => 'plain', 'translation_key' => 'qui_sommes_nous.card.location.title'],
            'card.location.text' => ['type' => 'plain', 'translation_key' => 'qui_sommes_nous.card.location.text'],
        ],
    ];

    /**
     * @brief ContentBlockManager constructor.
     *
     * @param ContentBlockRepository $repository The content block repository.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param TranslatorInterface $translator The translator service.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct(
        private readonly ContentBlockRepository $repository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @brief Returns editable block definitions for all configured pages.
     *
     * @return array<string, array<string, array{type: string, translation_key: string}>> Block definitions.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getDefinitions(): array
    {
        return self::PAGE_DEFINITIONS;
    }

    /**
     * @brief Returns editable block definitions for a single page.
     *
     * @param string $pageName The page name.
     * @return array<string, array{type: string, translation_key: string}> Page definitions.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getPageDefinitions(string $pageName): array
    {
        return self::PAGE_DEFINITIONS[$pageName] ?? [];
    }

    /**
     * @brief Returns page content for both locales with translation fallback.
     *
     * @param string $pageName The page name.
     * @return array<string, array<string, string>> Content indexed by block key and locale.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getEditorData(string $pageName): array
    {
        $definitions = $this->getPageDefinitions($pageName);
        $result = [];

        foreach ($definitions as $key => $definition) {
            foreach (['fr', 'de'] as $locale) {
                $block = $this->repository->findOneByComposite($pageName, $key, $locale);
                if ($block !== null && $block->getValue() !== null && $block->getValue() !== '') {
                    $result[$key][$locale] = $block->getValue();
                    continue;
                }

                $fallback = $this->translator->trans($definition['translation_key'], locale: $locale);
                if ($definition['type'] === 'rich') {
                    $fallback = $this->normalizeRichFallback($fallback);
                }
                $result[$key][$locale] = $fallback;
            }
        }

        return $result;
    }

    /**
     * @brief Returns page content for one editor locale with translation fallback.
     *
     * @param string $pageName The page name.
     * @param string $locale The editor locale.
     * @return array<string, string> Content indexed by block key.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getEditorDataForLocale(string $pageName, string $locale): array
    {
        $definitions = $this->getPageDefinitions($pageName);
        $result = [];

        foreach ($definitions as $key => $definition) {
            $block = $this->repository->findOneByComposite($pageName, $key, $locale);
            if ($block !== null && $block->getValue() !== null && $block->getValue() !== '') {
                $result[$key] = $block->getValue();
                continue;
            }

            $fallback = $this->translator->trans($definition['translation_key'], locale: $locale);
            if ($definition['type'] === 'rich') {
                $fallback = $this->normalizeRichFallback($fallback);
            }

            $result[$key] = $fallback;
        }

        return $result;
    }

    /**
     * @brief Returns editor colors for all blocks of one page.
     *
     * @param string $pageName The page name.
     * @return array<string, string> Colors indexed by block key.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getEditorColors(string $pageName): array
    {
        $definitions = $this->getPageDefinitions($pageName);
        $colors = [];

        foreach ($definitions as $key => $_definition) {
            $frBlock = $this->repository->findOneByComposite($pageName, $key, 'fr');
            $deBlock = $this->repository->findOneByComposite($pageName, $key, 'de');
            $storedColor = $frBlock?->getColor() ?? $deBlock?->getColor();
            $colors[$key] = $this->normalizeColor($storedColor, $pageName, $key);
        }

        return $colors;
    }

    /**
     * @brief Saves page content values for both locales.
     *
     * @param string $pageName The page name.
     * @param array<string, array<string, string>> $values Submitted values indexed by block key and locale.
     * @param array<string, string> $colors Submitted colors indexed by block key.
     * @return void
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function savePageContent(string $pageName, array $values, array $colors = []): void
    {
        $definitions = $this->getPageDefinitions($pageName);
        foreach (['fr', 'de'] as $locale) {
            $localeValues = [];
            foreach ($definitions as $key => $_definition) {
                $localeValues[$key] = $values[$key][$locale] ?? '';
            }
            $this->savePageContentForLocale($pageName, $locale, $localeValues, $colors, false);
        }
        $this->entityManager->flush();
    }

    /**
     * @brief Saves page content values for one locale and shared colors.
     *
     * @param string $pageName The page name.
     * @param string $locale The edited locale.
     * @param array<string, string> $values Submitted values indexed by block key.
     * @param array<string, string> $colors Submitted colors indexed by block key.
     * @param bool $flush Indicates if flush must be executed.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function savePageContentForLocale(
        string $pageName,
        string $locale,
        array $values,
        array $colors = [],
        bool $flush = true
    ): void {
        $definitions = $this->getPageDefinitions($pageName);
        $now = new \DateTimeImmutable();
        $otherLocale = $locale === 'fr' ? 'de' : 'fr';

        foreach ($definitions as $key => $definition) {
            $value = trim((string) ($values[$key] ?? ''));
            if ($pageName === 'home' && $key === 'hero.text_shadow_color') {
                $value = preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1 ? strtoupper($value) : '#000000';
            }
            $requestedColor = $colors[$key] ?? null;
            $color = $this->normalizeColor(
                is_string($requestedColor) ? $requestedColor : null,
                $pageName,
                $key
            );

            $editedBlock = $this->findOrCreateContentBlock($pageName, $key, $locale, $definition['type']);
            $editedBlock
                ->setValue($value)
                ->setColor($color)
                ->setUpdatedAt($now);

            $otherBlock = $this->repository->findOneByComposite($pageName, $key, $otherLocale);
            if ($otherBlock !== null) {
                if ($pageName === 'home' && in_array($key, ['hero.custom_css', 'hero.text_shadow_color'], true)) {
                    $otherBlock->setValue($value);
                }
                $otherBlock
                    ->setColor($color)
                    ->setUpdatedAt($now);
            }

            if ($pageName === 'home' && in_array($key, ['hero.custom_css', 'hero.text_shadow_color'], true) && $otherBlock === null) {
                $otherBlock = $this->findOrCreateContentBlock($pageName, $key, $otherLocale, $definition['type']);
                $otherBlock
                    ->setValue($value)
                    ->setColor($color)
                    ->setUpdatedAt($now);
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @brief Finds an existing content block or creates a new one.
     *
     * @param string $pageName The page name.
     * @param string $key The block key.
     * @param string $locale The block locale.
     * @param string $type The block type.
     * @return ContentBlock The managed content block.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function findOrCreateContentBlock(string $pageName, string $key, string $locale, string $type): ContentBlock
    {
        $block = $this->repository->findOneByComposite($pageName, $key, $locale);
        if ($block !== null) {
            return $block;
        }

        $block = new ContentBlock();
        $block
            ->setPageName($pageName)
            ->setKey($key)
            ->setLocale($locale)
            ->setType($type);
        $this->entityManager->persist($block);

        return $block;
    }

    /**
     * @brief Returns rendered page content for one locale with fallback.
     *
     * @param string $pageName The page name.
     * @param string $locale The locale.
     * @return array<string, string> Renderable content values indexed by block key.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getPageContent(string $pageName, string $locale): array
    {
        $definitions = $this->getPageDefinitions($pageName);
        $blocks = $this->repository->findByPageAndLocale($pageName, $locale);
        $indexed = [];

        foreach ($blocks as $block) {
            $indexed[$block->getKey()] = $block->getValue() ?? '';
        }

        $content = [];
        foreach ($definitions as $key => $definition) {
            $value = $indexed[$key] ?? '';
            if ($value === '') {
                $value = $this->translator->trans($definition['translation_key'], locale: $locale);
            }
            $content[$key] = $value;
        }

        return $content;
    }

    /**
     * @brief Returns display colors for one page and locale.
     *
     * @param string $pageName The page name.
     * @param string $locale The locale.
     * @return array<string, string> Colors indexed by block key.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getPageColors(string $pageName, string $locale): array
    {
        $definitions = $this->getPageDefinitions($pageName);
        $localeBlocks = $this->repository->findByPageAndLocale($pageName, $locale);
        $fallbackLocale = $locale === 'fr' ? 'de' : 'fr';
        $fallbackBlocks = $this->repository->findByPageAndLocale($pageName, $fallbackLocale);

        $localeIndexed = [];
        foreach ($localeBlocks as $block) {
            $localeIndexed[$block->getKey()] = $block;
        }

        $fallbackIndexed = [];
        foreach ($fallbackBlocks as $block) {
            $fallbackIndexed[$block->getKey()] = $block;
        }

        // #region agent log
        @file_put_contents(
            'debug-8a5f96.log',
            json_encode([
                'sessionId' => '8a5f96',
                'runId' => 'pre-fix',
                'hypothesisId' => 'H1',
                'location' => 'ContentBlockManager.php:getPageColors:before-loop',
                'message' => 'Indexed blocks snapshot',
                'data' => [
                    'pageName' => $pageName,
                    'locale' => $locale,
                    'definitions' => array_keys($definitions),
                    'localeIndexed' => array_keys($localeIndexed),
                    'fallbackIndexed' => array_keys($fallbackIndexed),
                ],
                'timestamp' => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion

        $colors = [];
        foreach ($definitions as $key => $_definition) {
            // #region agent log
            @file_put_contents(
                'debug-8a5f96.log',
                json_encode([
                    'sessionId' => '8a5f96',
                    'runId' => 'pre-fix',
                    'hypothesisId' => 'H2',
                    'location' => 'ContentBlockManager.php:getPageColors:per-key',
                    'message' => 'Key presence check',
                    'data' => [
                        'pageName' => $pageName,
                        'locale' => $locale,
                        'key' => $key,
                        'inLocaleIndexed' => array_key_exists($key, $localeIndexed),
                        'inFallbackIndexed' => array_key_exists($key, $fallbackIndexed),
                    ],
                    'timestamp' => (int) round(microtime(true) * 1000),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
            // #endregion

            $storedColor = ($localeIndexed[$key] ?? null)?->getColor() ?? ($fallbackIndexed[$key] ?? null)?->getColor();

            // #region agent log
            @file_put_contents(
                'debug-8a5f96.log',
                json_encode([
                    'sessionId' => '8a5f96',
                    'runId' => 'pre-fix',
                    'hypothesisId' => 'H3',
                    'location' => 'ContentBlockManager.php:getPageColors:resolved-color',
                    'message' => 'Resolved stored color before normalize',
                    'data' => [
                        'pageName' => $pageName,
                        'locale' => $locale,
                        'key' => $key,
                        'storedColor' => $storedColor,
                    ],
                    'timestamp' => (int) round(microtime(true) * 1000),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
            // #endregion

            $colors[$key] = $this->normalizeColor($storedColor, $pageName, $key);
        }

        return $colors;
    }

    /**
     * @brief Returns default color for a block.
     *
     * @param string $pageName The page name.
     * @param string $blockKey The block key.
     * @return string The default HEX color.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getDefaultColor(string $pageName, string $blockKey): string
    {
        return self::DEFAULT_COLORS[$pageName][$blockKey] ?? '#212529';
    }

    /**
     * @brief Normalizes a color value to a valid HEX format.
     *
     * @param string|null $color The input color.
     * @param string $pageName The page name.
     * @param string $blockKey The block key.
     * @return string A valid HEX color.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function normalizeColor(?string $color, string $pageName, string $blockKey): string
    {
        if ($color !== null && preg_match('/^#[0-9a-fA-F]{6}$/', $color) === 1) {
            return strtoupper($color);
        }

        return $this->getDefaultColor($pageName, $blockKey);
    }

    /**
     * @brief Normalizes rich fallback content from translations.
     *
     * @param string $fallback The translated fallback value.
     * @return string A safe and displayable rich HTML value.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function normalizeRichFallback(string $fallback): string
    {
        if (str_contains($fallback, '<')) {
            return $fallback;
        }

        return '<p>' . htmlspecialchars($fallback, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>';
    }
}

