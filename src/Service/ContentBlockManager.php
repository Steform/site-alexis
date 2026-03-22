<?php

namespace App\Service;

use App\Entity\ContentBlock;
use App\Entity\ContentBlockHistory;
use App\Repository\ContentBlockHistoryRepository;
use App\Repository\ContentBlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @brief Manages editable CMS blocks with translation fallback.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
class ContentBlockManager
{
    /**
     * @var array<string, array<string, array{light: string, dark: string}>>
     */
    private const DEFAULT_COLORS = [
        'home' => [
            'hero.top_content' => ['light' => '#FFFFFF', 'dark' => '#FFFFFF'],
            'hero.text_shadow_color' => ['light' => '#000000', 'dark' => '#000000'],
            'hero.custom_css' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'hero.cta' => ['light' => '#FFFFFF', 'dark' => '#FFFFFF'],
            'about.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'about.lead' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'about.body' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
            'about.cta' => ['light' => '#FFFFFF', 'dark' => '#FFFFFF'],
            'services.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card1.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card1.image' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card2.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card2.image' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card3.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card3.image' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card4.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card4.image' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card5.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.card5.image' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'services.cta' => ['light' => '#FFFFFF', 'dark' => '#FFFFFF'],
            'reviews.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'reviews.default.text' => ['light' => '#2c3e50', 'dark' => '#d0d5dc'],
            'reviews.default.author' => ['light' => '#34495e', 'dark' => '#a0a5b0'],
        ],
        'qui_sommes_nous' => [
            'title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'alexis.role' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
            'alexis.lead' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'alexis.text1' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'alexis.text2' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'cta.quote' => ['light' => '#FFFFFF', 'dark' => '#FFFFFF'],
            'card.family.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'card.family.text' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
            'card.expertise.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'card.expertise.text' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
            'card.location.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'card.location.text' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
        ],
        'devis' => [
            'devis.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'devis.lead' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
            'devis.cta' => ['light' => '#FFFFFF', 'dark' => '#FFFFFF'],
        ],
        'gallery' => [
            'gallery.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'gallery.lead' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
        ],
        'services' => [
            'why.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'list.title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'cta' => ['light' => '#FFFFFF', 'dark' => '#FFFFFF'],
            'cta_discover' => ['light' => '#212529', 'dark' => '#e4e6eb'],
        ],
        'service_detail' => [
            'teaser' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'description' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'advantage1' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
            'advantage2' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
            'advantage3' => ['light' => '#6C757D', 'dark' => '#b7bdc8'],
            'description_title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'advantages_title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'process_title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'cta_lead' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'other_services_title' => ['light' => '#212529', 'dark' => '#e4e6eb'],
        ],
        'mentions_legales' => [
            'editor' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'hoster' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'intellectual_property' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'hyperlinks' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'personal_data' => ['light' => '#212529', 'dark' => '#e4e6eb'],
            'cookies' => ['light' => '#212529', 'dark' => '#e4e6eb'],
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
            'services.title' => ['type' => 'plain', 'translation_key' => 'home.services.title'],
            'services.card1.title' => ['type' => 'plain', 'translation_key' => 'home.services.card1.title'],
            'services.card1.image' => ['type' => 'plain', 'translation_key' => 'home.services.card1.image'],
            'services.card2.title' => ['type' => 'plain', 'translation_key' => 'home.services.card2.title'],
            'services.card2.image' => ['type' => 'plain', 'translation_key' => 'home.services.card2.image'],
            'services.card3.title' => ['type' => 'plain', 'translation_key' => 'home.services.card3.title'],
            'services.card3.image' => ['type' => 'plain', 'translation_key' => 'home.services.card3.image'],
            'services.card4.title' => ['type' => 'plain', 'translation_key' => 'home.services.card4.title'],
            'services.card4.image' => ['type' => 'plain', 'translation_key' => 'home.services.card4.image'],
            'services.card5.title' => ['type' => 'plain', 'translation_key' => 'home.services.card5.title'],
            'services.card5.image' => ['type' => 'plain', 'translation_key' => 'home.services.card5.image'],
            'services.cta' => ['type' => 'plain', 'translation_key' => 'home.services.cta'],
            'reviews.title' => ['type' => 'plain', 'translation_key' => 'home.reviews.title'],
            'reviews.default.text' => ['type' => 'plain', 'translation_key' => 'home.reviews.default.text'],
            'reviews.default.author' => ['type' => 'plain', 'translation_key' => 'home.reviews.default.author'],
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
        'devis' => [
            'devis.title' => ['type' => 'plain', 'translation_key' => 'devis.title'],
            'devis.lead' => ['type' => 'plain', 'translation_key' => 'devis.lead'],
            'devis.cta' => ['type' => 'plain', 'translation_key' => 'home.quote.cta'],
        ],
        'gallery' => [
            'gallery.title' => ['type' => 'plain', 'translation_key' => 'gallery.title'],
            'gallery.lead' => ['type' => 'plain', 'translation_key' => 'gallery.lead'],
        ],
        'services' => [
            'why.title' => ['type' => 'plain', 'translation_key' => 'services.why.title'],
            'list.title' => ['type' => 'plain', 'translation_key' => 'services.list.title'],
            'cta' => ['type' => 'plain', 'translation_key' => 'services.cta'],
            'cta_discover' => ['type' => 'plain', 'translation_key' => 'services.cta_discover'],
        ],
        'mentions_legales' => [
            'editor' => ['type' => 'rich', 'translation_key' => 'mentions.editor'],
            'hoster' => ['type' => 'rich', 'translation_key' => 'mentions.hoster'],
            'intellectual_property' => ['type' => 'rich', 'translation_key' => 'mentions.intellectual_property'],
            'hyperlinks' => ['type' => 'rich', 'translation_key' => 'mentions.hyperlinks'],
            'personal_data' => ['type' => 'rich', 'translation_key' => 'mentions.personal_data'],
            'cookies' => ['type' => 'rich', 'translation_key' => 'mentions.cookies'],
        ],
    ];

    /**
     * @brief ContentBlockManager constructor.
     *
     * @param ContentBlockRepository $repository The content block repository.
     * @param ContentBlockHistoryRepository $historyRepository The history repository.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param TranslatorInterface $translator The translator service.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct(
        private readonly ContentBlockRepository $repository,
        private readonly ContentBlockHistoryRepository $historyRepository,
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
     * @brief Returns the page name for a service detail page.
     *
     * @param string $slug The service slug.
     * @return string The page name (e.g. service_reparation_carrosserie_peinture).
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function getServiceDetailPageName(string $slug): string
    {
        return 'service_' . $slug;
    }

    /**
     * @brief Returns editable block definitions for a service detail page.
     *
     * @param string $slug The service slug.
     * @return array<string, array{type: string, translation_key: string}> Page definitions.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function getPageDefinitionsForService(string $slug): array
    {
        return [
            'teaser' => ['type' => 'plain', 'translation_key' => 'services.teaser.' . $slug],
            'description' => ['type' => 'rich', 'translation_key' => 'services.detail.' . $slug . '.placeholder'],
            'advantage1' => ['type' => 'plain', 'translation_key' => 'services.detail.' . $slug . '.advantage1'],
            'advantage2' => ['type' => 'plain', 'translation_key' => 'services.detail.' . $slug . '.advantage2'],
            'advantage3' => ['type' => 'plain', 'translation_key' => 'services.detail.' . $slug . '.advantage3'],
            'description_title' => ['type' => 'plain', 'translation_key' => 'services.detail.description_title'],
            'advantages_title' => ['type' => 'plain', 'translation_key' => 'services.detail.advantages_title'],
            'process_title' => ['type' => 'plain', 'translation_key' => 'services.detail.process_title'],
            'cta_lead' => ['type' => 'plain', 'translation_key' => 'services.detail.cta_lead'],
            'other_services_title' => ['type' => 'plain', 'translation_key' => 'services.detail.other_services_title'],
        ];
    }

    /**
     * @brief Checks if a page exists in the CMS configuration.
     *
     * @param string $pageName The page name.
     * @return bool True when the page is configured.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function hasPage(string $pageName): bool
    {
        if (array_key_exists($pageName, self::PAGE_DEFINITIONS)) {
            return true;
        }

        return str_starts_with($pageName, 'service_');
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
        if (array_key_exists($pageName, self::PAGE_DEFINITIONS)) {
            return self::PAGE_DEFINITIONS[$pageName];
        }

        if (str_starts_with($pageName, 'service_')) {
            $slug = substr($pageName, 8);

            return $this->getPageDefinitionsForService($slug);
        }

        return [];
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
     * @brief Returns editor colors for all blocks of one page (light and dark).
     *
     * @param string $pageName The page name.
     * @return array<string, array{light: string, dark: string}> Colors indexed by block key.
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
            $block = $frBlock ?? $deBlock;
            $storedLight = $block?->getColor();
            $storedDark = $block?->getColorDark();
            $defaults = $this->getDefaultColors($pageName, $key);
            $colors[$key] = [
                'light' => $this->normalizeColor($storedLight, $defaults['light']),
                'dark' => $this->normalizeColor($storedDark, $defaults['dark']),
            ];
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
    public function savePageContent(string $pageName, array $values, array $colors = [], array $colorsDark = [], ?UserInterface $user = null): void
    {
        $definitions = $this->getPageDefinitions($pageName);
        foreach (['fr', 'de'] as $locale) {
            $localeValues = [];
            foreach ($definitions as $key => $_definition) {
                $localeValues[$key] = $values[$key][$locale] ?? '';
            }
            $this->savePageContentForLocale($pageName, $locale, $localeValues, $colors, $colorsDark, false, $user);
        }
        $this->entityManager->flush();
    }

    /**
     * @brief Saves page content values for one locale and shared colors.
     *
     * @param string $pageName The page name.
     * @param string $locale The edited locale.
     * @param array<string, string> $values Submitted values indexed by block key.
     * @param array<string, string> $colors Submitted light colors indexed by block key.
     * @param array<string, string> $colorsDark Submitted dark colors indexed by block key.
     * @param bool $flush Indicates if flush must be executed.
     * @param UserInterface|null $user The user who triggered the save.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function savePageContentForLocale(
        string $pageName,
        string $locale,
        array $values,
        array $colors = [],
        array $colorsDark = [],
        bool $flush = true,
        ?UserInterface $user = null
    ): void {
        $definitions = $this->getPageDefinitions($pageName);
        $now = new \DateTimeImmutable();
        $otherLocale = $locale === 'fr' ? 'de' : 'fr';

        foreach ($definitions as $key => $definition) {
            $value = trim((string) ($values[$key] ?? ''));
            if ($pageName === 'home' && $key === 'hero.text_shadow_color') {
                $value = preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1 ? strtoupper($value) : '#000000';
            }
            if ($pageName === 'home' && preg_match('/^services\.card[1-5]\.image$/', $key) === 1) {
                $value = $this->normalizePublicAssetPath($value);
            }
            $defaults = $this->getDefaultColors($pageName, $key);
            $requestedColor = $colors[$key] ?? null;
            $requestedColorDark = $colorsDark[$key] ?? null;
            $color = $this->normalizeColor(is_string($requestedColor) ? $requestedColor : null, $defaults['light']);
            $colorDark = $this->normalizeColor(is_string($requestedColorDark) ? $requestedColorDark : null, $defaults['dark']);

            $editedBlock = $this->repository->findOneByComposite($pageName, $key, $locale);
            $blockExisted = $editedBlock !== null;
            if ($editedBlock === null) {
                $editedBlock = $this->findOrCreateContentBlock($pageName, $key, $locale, $definition['type']);
            }

            if ($blockExisted) {
                $oldValue = $editedBlock->getValue() ?? '';
                $oldColor = $editedBlock->getColor();
                $oldColorDark = $editedBlock->getColorDark();
                $valueChanged = $oldValue !== $value;
                $colorChanged = $this->normalizeColor($oldColor, $defaults['light']) !== $color;
                $colorDarkChanged = $this->normalizeColor($oldColorDark, $defaults['dark']) !== $colorDark;
                if ($valueChanged || $colorChanged || $colorDarkChanged) {
                    $this->pushToHistory($editedBlock, $now, $user);
                }
            }

            $editedBlock
                ->setValue($value)
                ->setColor($color)
                ->setColorDark($colorDark)
                ->setUpdatedAt($now);

            $otherBlock = $this->repository->findOneByComposite($pageName, $key, $otherLocale);
            if ($otherBlock !== null) {
                if ($pageName === 'home' && in_array($key, ['hero.custom_css', 'hero.text_shadow_color'], true)) {
                    $otherBlock->setValue($value);
                }
                $otherBlock
                    ->setColor($color)
                    ->setColorDark($colorDark)
                    ->setUpdatedAt($now);
            }

            if ($pageName === 'home' && in_array($key, ['hero.custom_css', 'hero.text_shadow_color'], true) && $otherBlock === null) {
                $otherBlock = $this->findOrCreateContentBlock($pageName, $key, $otherLocale, $definition['type']);
                $otherBlock
                    ->setValue($value)
                    ->setColor($color)
                    ->setColorDark($colorDark)
                    ->setUpdatedAt($now);
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @brief Returns the raw stored value for one block and locale (empty string if missing).
     *
     * @param string $pageName The page name.
     * @param string $blockKey The block key.
     * @param string $locale The locale.
     * @return string The stored value or empty string.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getBlockValueForLocale(string $pageName, string $blockKey, string $locale): string
    {
        $block = $this->repository->findOneByComposite($pageName, $blockKey, $locale);

        return $block !== null ? (string) ($block->getValue() ?? '') : '';
    }

    /**
     * @brief Updates home service card image path for both FR and DE with CMS history.
     *
     * @param int $slot Card index from 1 to 5.
     * @param string $relativePath Public-relative path (e.g. uploads/home-service-cards/x.webp).
     * @param UserInterface|null $user The acting user.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function updateHomeServiceCardImage(int $slot, string $relativePath, ?UserInterface $user = null): void
    {
        if ($slot < 1 || $slot > 5) {
            throw new \InvalidArgumentException('Invalid service card slot.');
        }

        $blockKey = sprintf('services.card%d.image', $slot);
        $definitions = $this->getPageDefinitions('home');
        if (!isset($definitions[$blockKey])) {
            throw new \InvalidArgumentException('Unknown block key.');
        }

        $definition = $definitions[$blockKey];
        $value = $this->normalizePublicAssetPath($relativePath);
        if ($value === '') {
            throw new \InvalidArgumentException('Invalid asset path.');
        }

        $now = new \DateTimeImmutable();

        foreach (['fr', 'de'] as $locale) {
            $editedBlock = $this->repository->findOneByComposite('home', $blockKey, $locale);
            $blockExisted = $editedBlock !== null;
            if ($editedBlock === null) {
                $editedBlock = $this->findOrCreateContentBlock('home', $blockKey, $locale, $definition['type']);
            }

            $defaults = $this->getDefaultColors('home', $blockKey);
            $color = $defaults['light'];
            $colorDark = $defaults['dark'];

            if ($blockExisted) {
                $oldValue = $editedBlock->getValue() ?? '';
                if ($oldValue !== $value) {
                    $this->pushToHistory($editedBlock, $now, $user);
                }
            }

            $editedBlock
                ->setValue($value)
                ->setColor($color)
                ->setColorDark($colorDark)
                ->setUpdatedAt($now);
        }

        $this->entityManager->flush();
    }

    /**
     * @brief Pushes the current block state to history before it is overwritten.
     *
     * @param ContentBlock $block The block whose state to archive.
     * @param \DateTimeImmutable $now The current timestamp.
     * @param UserInterface|null $user The user who triggered the save.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function pushToHistory(ContentBlock $block, \DateTimeImmutable $now, ?UserInterface $user): void
    {
        $history = new ContentBlockHistory();
        $history->setPageName($block->getPageName());
        $history->setBlockKey($block->getKey());
        $history->setLocale($block->getLocale());
        $history->setValue($block->getValue() ?? '');
        $history->setColor($block->getColor());
        $history->setColorDark($block->getColorDark());
        $history->setCreatedAt($now);
        $history->setCreatedBy($user);
        $this->entityManager->persist($history);
    }

    /**
     * @brief Restores a content block to a previous state from history.
     *
     * Pushes the current prod state to history, then restores the block from the selected history entry.
     *
     * @param int $historyId The history entry ID to restore.
     * @param UserInterface|null $user The user who triggered the rollback.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function rollbackToHistory(int $historyId, ?UserInterface $user = null): void
    {
        $history = $this->historyRepository->find($historyId);
        if ($history === null) {
            throw new \InvalidArgumentException('History entry not found.');
        }

        $block = $this->repository->findOneByComposite(
            $history->getPageName(),
            $history->getBlockKey(),
            $history->getLocale()
        );

        $now = new \DateTimeImmutable();
        if ($block !== null) {
            $this->pushToHistory($block, $now, $user);
        } else {
            $definitions = $this->getPageDefinitions($history->getPageName());
            $block = $this->findOrCreateContentBlock(
                $history->getPageName(),
                $history->getBlockKey(),
                $history->getLocale(),
                $definitions[$history->getBlockKey()]['type'] ?? 'plain'
            );
        }

        $block
            ->setValue($history->getValue() ?? '')
            ->setColor($history->getColor())
            ->setColorDark($history->getColorDark())
            ->setUpdatedAt($now);

        $this->entityManager->flush();
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

        $colors = [];
        foreach ($definitions as $key => $_definition) {
            $block = $localeIndexed[$key] ?? $fallbackIndexed[$key] ?? null;
            $storedLight = $block?->getColor();
            $storedDark = $block?->getColorDark();
            $defaults = $this->getDefaultColors($pageName, $key);
            $colors[$key] = [
                'light' => $this->normalizeColor($storedLight, $defaults['light']),
                'dark' => $this->normalizeColor($storedDark, $defaults['dark']),
            ];
        }

        return $colors;
    }

    /**
     * @brief Returns default colors for a block (light and dark).
     *
     * @param string $pageName The page name.
     * @param string $blockKey The block key.
     * @return array{light: string, dark: string} Default HEX colors.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getDefaultColors(string $pageName, string $blockKey): array
    {
        $defaults = self::DEFAULT_COLORS[$pageName][$blockKey]
            ?? (str_starts_with($pageName, 'service_') ? (self::DEFAULT_COLORS['service_detail'][$blockKey] ?? null) : null)
            ?? ['light' => '#212529', 'dark' => '#e4e6eb'];

        return is_array($defaults) ? $defaults : ['light' => (string) $defaults, 'dark' => '#e4e6eb'];
    }

    /**
     * @brief Normalizes a color value to a valid HEX format.
     *
     * @param string|null $color The input color.
     * @param string $fallback The fallback HEX color when invalid.
     * @return string A valid HEX color.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function normalizeColor(?string $color, string $fallback): string
    {
        if ($color !== null && preg_match('/^#[0-9a-fA-F]{6}$/', $color) === 1) {
            return strtoupper($color);
        }

        return $fallback;
    }

    /**
     * @brief Normalizes a public asset path for CMS image blocks (no leading slash, allowed roots only).
     *
     * @param string $value Raw submitted path.
     * @return string Normalized path or empty string when invalid.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function normalizePublicAssetPath(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        $path = ltrim($trimmed, '/');
        if (str_contains($path, '..')) {
            return '';
        }

        if (preg_match('#^(images|uploads)/#', $path) !== 1) {
            return '';
        }

        return $path;
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

