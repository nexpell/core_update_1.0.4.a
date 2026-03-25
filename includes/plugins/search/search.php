<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

global $languageService, $_database;

$tpl = new Template();

function getCurrentSearchLanguage(): string
{
    foreach (['language', 'lang'] as $key) {
        if (!empty($_GET[$key]) && is_string($_GET[$key])) {
            return normalizeSearchLanguageCode($_GET[$key]);
        }
        if (!empty($_POST[$key]) && is_string($_POST[$key])) {
            return normalizeSearchLanguageCode($_POST[$key]);
        }
        if (!empty($_SESSION[$key]) && is_string($_SESSION[$key])) {
            return normalizeSearchLanguageCode($_SESSION[$key]);
        }
    }

    return 'de';
}

function normalizeSearchLanguageCode(string $lang): string
{
    $lang = strtolower(trim($lang));

    return match ($lang) {
        'gb', 'uk' => 'en',
        default => $lang !== '' ? $lang : 'de',
    };
}

function normalizeSearchQuery(string $query): string
{
    $query = html_entity_decode($query, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $query = preg_replace('/\s+/u', ' ', trim($query));
    return (string) $query;
}

function tokenizeSearchQuery(string $query): array
{
    $parts = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($query), -1, PREG_SPLIT_NO_EMPTY);
    $parts = array_values(array_unique(array_filter($parts, static function ($term) {
        return mb_strlen($term) >= 2;
    })));

    return array_slice($parts, 0, 8);
}

function getLocalizedText(string $text, string $lang = 'de'): string
{
    if ($text === '') {
        return '';
    }

    if (preg_match("/\[\[lang:" . preg_quote($lang, '/') . "\]\](.*?)(\[\[lang:|$)/si", $text, $matches)) {
        return trim($matches[1]);
    }

    if (preg_match("/\[\[lang:.*?\]\](.*?)(\[\[lang:|$)/si", $text, $matches)) {
        return trim($matches[1]);
    }

    return trim($text);
}

function plainSearchText(string $text, string $lang): string
{
    $localized = getLocalizedText($text, $lang);
    $normalized = preg_replace('/\s+/u', ' ', trim(strip_tags(html_entity_decode($localized, ENT_QUOTES | ENT_HTML5, 'UTF-8'))));
    return (string) $normalized;
}

function highlightSearchTerms(string $text, array $terms): string
{
    $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    foreach ($terms as $term) {
        $encodedTerm = htmlspecialchars($term, ENT_QUOTES, 'UTF-8');
        if ($encodedTerm === '') {
            continue;
        }

        $safe = preg_replace('/(' . preg_quote($encodedTerm, '/') . ')/iu', '<mark>$1</mark>', $safe);
    }

    return $safe;
}

function makeSearchSnippet(string $text, array $terms, int $radius = 70): string
{
    if ($text === '') {
        return '';
    }

    $lower = mb_strtolower($text);
    $ranges = [];

    foreach ($terms as $term) {
        $term = trim($term);
        if ($term === '') {
            continue;
        }

        $needle = mb_strtolower($term);
        $offset = 0;

        while (($pos = mb_stripos($lower, $needle, $offset)) !== false) {
            $start = max(0, $pos - $radius);
            $end = min(mb_strlen($text), $pos + mb_strlen($term) + $radius);
            $ranges[] = [$start, $end];
            $offset = $pos + mb_strlen($needle);
        }
    }

    if (empty($ranges)) {
        $snippet = mb_substr($text, 0, $radius * 2);
        if (mb_strlen($text) > mb_strlen($snippet)) {
            $snippet .= '...';
        }
        return highlightSearchTerms($snippet, $terms);
    }

    usort($ranges, static function (array $a, array $b) {
        return $a[0] <=> $b[0];
    });

    $merged = [];
    foreach ($ranges as $range) {
        if (empty($merged) || $range[0] > $merged[count($merged) - 1][1]) {
            $merged[] = $range;
            continue;
        }

        $merged[count($merged) - 1][1] = max($merged[count($merged) - 1][1], $range[1]);
    }

    $parts = [];
    foreach (array_slice($merged, 0, 3) as [$start, $end]) {
        $part = mb_substr($text, $start, $end - $start);
        if ($start > 0) {
            $part = '...' . ltrim($part);
        }
        if ($end < mb_strlen($text)) {
            $part = rtrim($part) . '...';
        }
        $parts[] = $part;
    }

    return highlightSearchTerms(implode(' ', $parts), $terms);
}

function buildSearchConditions(array $columns, array $terms, string $query): array
{
    $conditions = [];
    $params = [];
    $types = '';

    if ($query !== '') {
        foreach ($columns as $column) {
            $conditions[] = "`$column` LIKE ?";
            $params[] = '%' . $query . '%';
            $types .= 's';
        }
    }

    foreach ($terms as $term) {
        foreach ($columns as $column) {
            $conditions[] = "`$column` LIKE ?";
            $params[] = '%' . $term . '%';
            $types .= 's';

            $conditions[] = "`$column` LIKE ?";
            $params[] = $term . '%';
            $types .= 's';
        }
    }

    return [implode(' OR ', $conditions), $types, $params];
}

function getTableColumns(string $table): array
{
    $columns = [];
    $resCols = safe_query("SHOW COLUMNS FROM `$table`");
    while ($column = mysqli_fetch_assoc($resCols)) {
        $columns[] = $column['Field'];
    }

    return $columns;
}

function getSearchFieldCandidates(): array
{
    return [
        'title', 'headline', 'name', 'subject', 'label',
        'content', 'body', 'description', 'intro', 'text', 'summary', 'excerpt', 'message',
        'caption', 'alt_text', 'tags', 'slug', 'content_key', 'feature_text',
        'item_name', 'boss_name', 'template_name', 'class_name', 'role_name',
        'section_title', 'footer_link_name', 'footer_link_url',
        'price_unit', 'button_text', 'source', 'tactics', 'question', 'answer',
        'username', 'server_name', 'channel_name', 'main_channel', 'extra_channels',
    ];
}

function getLivePluginRegistry(): array
{
    return [
        'plugins_about' => ['id' => 'id', 'title' => ['content_key'], 'content' => ['content'], 'label' => 'Über uns'],
        'plugins_achievements' => ['id' => 'id', 'title' => ['name'], 'content' => ['description'], 'label' => 'Achievements'],
        'plugins_achievements_categories' => ['id' => 'id', 'title' => ['name'], 'content' => ['description'], 'label' => 'Achievements'],
        'plugins_articles' => ['id' => 'id', 'title' => ['title'], 'content' => ['content', 'slug'], 'label' => 'Artikel'],
        'plugins_articles_categories' => ['id' => 'id', 'title' => ['name'], 'content' => ['description'], 'label' => 'Artikel'],
        'plugins_carousel' => ['id' => 'id', 'title' => ['type'], 'content' => ['link', 'media_file'], 'label' => 'Carousel'],
        'plugins_carousel_lang' => ['id' => 'id', 'title' => ['content_key'], 'content' => ['content'], 'label' => 'Carousel'],
        'plugins_discord' => ['id' => 'id', 'title' => ['name'], 'content' => ['value'], 'label' => 'Discord'],
        'plugins_downloads' => ['id' => 'id', 'title' => ['title'], 'content' => ['description', 'access_roles'], 'label' => 'Downloads'],
        'plugins_downloads_categories' => ['id' => 'categoryID', 'title' => ['title'], 'content' => ['description'], 'label' => 'Downloads'],
        'plugins_footer' => ['id' => 'id', 'title' => ['section_title', 'footer_link_name'], 'content' => ['footer_link_url', 'category_key'], 'label' => 'Footer'],
        'plugins_footer_lang' => ['id' => 'id', 'title' => ['content_key'], 'content' => ['content'], 'label' => 'Footer'],
        'plugins_forum_boards' => ['id' => 'boardID', 'title' => ['title'], 'content' => ['description'], 'label' => 'Forum'],
        'plugins_forum_categories' => ['id' => 'catID', 'title' => ['title'], 'content' => ['description'], 'label' => 'Forum'],
        'plugins_forum_posts' => ['id' => 'postID', 'title' => [], 'content' => ['content'], 'label' => 'Forum'],
        'plugins_forum_threads' => ['id' => 'threadID', 'title' => ['title'], 'content' => [], 'label' => 'Forum'],
        'plugins_gallery' => ['id' => 'id', 'title' => ['title'], 'content' => ['caption', 'alt_text', 'tags', 'photographer'], 'label' => 'Galerie'],
        'plugins_gallery_categories' => ['id' => 'id', 'title' => ['name'], 'content' => [], 'label' => 'Galerie'],
        'plugins_gametracker_servers' => ['id' => 'id', 'title' => ['game', 'ip'], 'content' => ['game_pic'], 'label' => 'Gametracker'],
        'plugins_joinus_applications' => ['id' => 'id', 'title' => ['name', 'role'], 'content' => ['message', 'email', 'role_custom', 'admin_note'], 'label' => 'JoinUs'],
        'plugins_joinus_squads' => ['id' => 'id', 'title' => ['name'], 'content' => [], 'label' => 'JoinUs'],
        'plugins_joinus_types' => ['id' => 'id', 'title' => ['label', 'type_key'], 'content' => [], 'label' => 'JoinUs'],
        'plugins_messages' => ['id' => 'id', 'title' => ['image_url'], 'content' => ['text'], 'label' => 'Messenger'],
        'plugins_news' => ['id' => 'id', 'title' => ['title', 'link_name'], 'content' => ['content', 'slug', 'link'], 'label' => 'News'],
        'plugins_news_categories' => ['id' => 'id', 'title' => ['name', 'slug'], 'content' => ['description'], 'label' => 'News'],
        'plugins_news_lang' => ['id' => 'id', 'title' => ['content_key'], 'content' => ['content'], 'label' => 'News'],
        'plugins_partners' => ['id' => 'id', 'title' => ['content_key', 'slug'], 'content' => ['content'], 'label' => 'Partners'],
        'plugins_pricing_features' => ['id' => 'id', 'title' => ['feature_text'], 'content' => ['feature_text', 'feature_text_de', 'feature_text_en', 'feature_text_it'], 'label' => 'Pricing'],
        'plugins_pricing_plans' => ['id' => 'id', 'title' => ['title', 'title_de', 'title_en', 'title_it'], 'content' => ['price_unit', 'price_unit_de', 'price_unit_en', 'price_unit_it', 'button_text_de', 'button_text_en', 'button_text_it', 'target_url'], 'label' => 'Pricing'],
        'plugins_raidplaner_bosses' => ['id' => 'id', 'title' => ['boss_name'], 'content' => ['tactics'], 'label' => 'Raidplaner'],
        'plugins_raidplaner_classes' => ['id' => 'id', 'title' => ['class_name'], 'content' => [], 'label' => 'Raidplaner'],
        'plugins_raidplaner_events' => ['id' => 'id', 'title' => ['title'], 'content' => ['description'], 'label' => 'Raidplaner'],
        'plugins_raidplaner_items' => ['id' => 'id', 'title' => ['item_name'], 'content' => ['source', 'boss_name', 'raid_name', 'slot', 'class_spec'], 'label' => 'Raidplaner'],
        'plugins_raidplaner_roles' => ['id' => 'id', 'title' => ['role_name'], 'content' => [], 'label' => 'Raidplaner'],
        'plugins_raidplaner_templates' => ['id' => 'id', 'title' => ['template_name', 'title'], 'content' => ['description'], 'label' => 'Raidplaner'],
        'plugins_rules' => ['id' => 'id', 'title' => ['title'], 'content' => ['description', 'content'], 'label' => 'Rules'],
        'plugins_shoutbox_messages' => ['id' => 'id', 'title' => ['username'], 'content' => ['message'], 'label' => 'Shoutbox'],
        'plugins_sponsors' => ['id' => 'id', 'title' => ['name', 'slug'], 'content' => ['description', 'level'], 'label' => 'Sponsoren'],
        'plugins_teamspeak' => ['id' => 'id', 'title' => ['title'], 'content' => ['host', 'server_country'], 'label' => 'Teamspeak'],
        'plugins_todo' => ['id' => 'id', 'title' => ['task'], 'content' => ['description', 'priority'], 'label' => 'ToDo'],
        'plugins_twitch_settings' => ['id' => 'id', 'title' => ['main_channel'], 'content' => ['extra_channels'], 'label' => 'Twitch'],
        'plugins_youtube' => ['id' => 'id', 'title' => ['setting_key'], 'content' => ['setting_value'], 'label' => 'YouTube'],
    ];
}

function getRegisteredTableConfig(string $table): ?array
{
    $registry = getLivePluginRegistry();
    return $registry[$table] ?? null;
}

function shouldSkipSearchTable(string $table): bool
{
    if (getRegisteredTableConfig($table) !== null) {
        return false;
    }

    foreach ([
        '_settings', '_settings_widgets', '_logs', '_log', '_read', '_permissions',
        '_uploaded_images', '_banner_cache', '_cache', '_likes', '_lang',
    ] as $needle) {
        if (str_contains($table, $needle)) {
            return true;
        }
    }

    return false;
}

function findLanguageColumns(array $columns, array $bases, string $lang): array
{
    $matches = [];
    $lang = strtolower($lang);

    foreach ($bases as $base) {
        if (in_array($base, $columns, true)) {
            $matches[] = $base;
        }

        $preferred = $base . '_' . $lang;
        if (in_array($preferred, $columns, true)) {
            $matches[] = $preferred;
        }
    }

    return array_values(array_unique($matches));
}

function firstExistingColumn(array $columns, array $candidates, string $lang): ?string
{
    $languageCandidates = findLanguageColumns($columns, $candidates, $lang);
    if (!empty($languageCandidates)) {
        return $languageCandidates[0];
    }

    foreach ($candidates as $candidate) {
        if (in_array($candidate, $columns, true)) {
            return $candidate;
        }
    }

    return null;
}

function guessIdColumn(array $columns, string $table, string $lang): ?string
{
    $preferred = [
        'id', 'staticID', 'pageID', 'categoryID', 'catID', 'boardID', 'threadID', 'postID',
        'squadID', 'typeID', 'plan_id', 'raid_id', 'event_id', 'character_id',
    ];

    $match = firstExistingColumn($columns, array_merge([$table . 'ID'], $preferred), $lang);
    if ($match !== null) {
        return $match;
    }

    $blacklist = [
        'userID', 'user_id', 'created_by_user_id', 'updated_by', 'assigned_to',
        'category_id', 'plan_id', 'fileID', 'boss_id', 'template_id', 'role_id',
        'class_id', 'event_id', 'threadID', 'postID', 'boardID', 'catID',
    ];

    foreach ($columns as $column) {
        if (in_array($column, $blacklist, true)) {
            continue;
        }

        if (preg_match('/(^id$|ID$|_id$)/', $column)) {
            return $column;
        }
    }

    return null;
}

function collectRowText(array $row, array $fields, string $lang): string
{
    $parts = [];

    foreach ($fields as $field) {
        if (!empty($row[$field])) {
            $parts[] = plainSearchText((string) $row[$field], $lang);
        }
    }

    return implode(' ', array_filter($parts));
}

function buildSelectColumns(array $fields): string
{
    $selects = [];

    foreach ($fields as $field) {
        $selects[] = "`$field`";
    }

    return implode(', ', $selects);
}

function getModuleNameFromTable(string $table): string
{
    $module = preg_replace('/^plugins_/', '', $table);
    $parts = explode('_', $module);
    return $parts[0] ?? $module;
}

function resolveGenericPluginUrl(string $type, int $id): string
{
    $module = getModuleNameFromTable($type);

    if (method_exists(SeoUrlHandler::class, 'buildPluginUrl')) {
        switch ($type) {
            case 'plugins_news':
            case 'plugins_news_categories':
                return SeoUrlHandler::buildPluginUrl($type, $id);
        }
    }

    switch ($type) {
        case 'plugins_articles':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=articles&action=watch&id=' . $id);
        case 'plugins_articles_categories':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=articles&action=show&id=' . $id);
        case 'plugins_downloads':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=downloads&action=detail&id=' . $id);
        case 'plugins_downloads_categories':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=downloads&action=cat_list&id=' . $id);
        case 'plugins_forum_boards':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=forum');
        case 'plugins_forum_categories':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=forum&action=category&id=' . $id);
        case 'plugins_forum_threads':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=forum&action=thread&id=' . $id);
        case 'plugins_gallery':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=gallery&action=detail&id=' . $id);
        case 'plugins_gallery_categories':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=gallery');
        case 'plugins_gametracker_servers':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=gametracker&action=serverdetails&id=' . $id);
        case 'plugins_joinus_applications':
        case 'plugins_joinus_roles':
        case 'plugins_joinus_squads':
        case 'plugins_joinus_types':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=joinus');
        case 'plugins_links':
        case 'plugins_links_categories':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=links');
        case 'plugins_messages':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=messenger');
        case 'plugins_news':
        case 'plugins_news_categories':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=news');
        case 'plugins_partners':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=partners');
        case 'plugins_pricing_plans':
        case 'plugins_pricing_features':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=pricing');
        case 'plugins_raidplaner_events':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=raidplaner&action=show&id=' . $id);
        case 'plugins_raidplaner_items':
        case 'plugins_raidplaner_templates':
        case 'plugins_raidplaner_bosses':
        case 'plugins_raidplaner_characters':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=raidplaner');
        case 'plugins_rules':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=rules');
        case 'plugins_shoutbox_messages':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=shoutbox');
        case 'plugins_sponsors':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=sponsors');
        case 'plugins_teamspeak':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=teamspeak');
        case 'plugins_todo':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=todo');
        case 'plugins_twitch_settings':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=twitch');
        case 'plugins_youtube':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=youtube');
        case 'plugins_carousel':
        case 'plugins_carousel_lang':
        case 'plugins_carousel_agency':
        case 'plugins_carousel_parallax':
        case 'plugins_carousel_sticky':
            return SeoUrlHandler::convertToSeoUrl('index.php');
        case 'plugins_footer':
        case 'plugins_footer_lang':
            return SeoUrlHandler::convertToSeoUrl('index.php');
        case 'plugins_news_lang':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=news');
        case 'plugins_about':
            return SeoUrlHandler::convertToSeoUrl('index.php?site=about');
    }

    return SeoUrlHandler::convertToSeoUrl('index.php?site=' . $module);
}

function humanizePluginType(string $type, LanguageService $languageService): string
{
    $config = getRegisteredTableConfig($type);
    if ($config !== null && !empty($config['label'])) {
        return (string) $config['label'];
    }

    if ($type === 'settings_static') {
        return $languageService->get('type_page');
    }

    $module = getModuleNameFromTable($type);
    $label = str_replace('_', ' ', $module);
    $label = ucwords($label);

    return $label !== '' ? $label : $languageService->get('type_page');
}

function buildVisibilityConditions(array $columns): string
{
    $checks = [];

    $positiveFlags = ['active', 'is_active', 'activate', 'published', 'is_published', 'visible'];
    foreach ($positiveFlags as $column) {
        if (in_array($column, $columns, true)) {
            $checks[] = "(`$column` = 1 OR `$column` = '1')";
        }
    }

    $negativeFlags = ['deleted', 'is_deleted', 'hidden', 'disabled'];
    foreach ($negativeFlags as $column) {
        if (in_array($column, $columns, true)) {
            $checks[] = "(`$column` = 0 OR `$column` = '0' OR `$column` IS NULL)";
        }
    }

    return implode(' AND ', $checks);
}

function countRegexMatches(string $pattern, string $subject): int
{
    if ($pattern === '') {
        return 0;
    }

    return preg_match_all($pattern, $subject, $matches) ?: 0;
}

function scoreSearchResult(array $row, string $query, array $terms, string $lang): int
{
    $title = plainSearchText((string) ($row['title'] ?? ''), $lang);
    $body = plainSearchText((string) ($row['body'] ?? ''), $lang);

    $titleLower = mb_strtolower($title);
    $bodyLower = mb_strtolower($body);
    $queryLower = mb_strtolower($query);
    $score = 0;

    if ($queryLower !== '') {
        if ($titleLower === $queryLower) {
            $score += 220;
        } elseif (mb_stripos($titleLower, $queryLower) !== false) {
            $score += 130;
        }

        if (mb_stripos($bodyLower, $queryLower) !== false) {
            $score += 70;
        }
    }

    $matchedTerms = 0;

    foreach ($terms as $term) {
        $termLower = mb_strtolower($term);
        $wordPattern = '/\b' . preg_quote($termLower, '/') . '\b/iu';
        $prefixPattern = '/\b' . preg_quote($termLower, '/') . '\p{L}*/iu';

        $titleWordMatches = countRegexMatches($wordPattern, $titleLower);
        $bodyWordMatches = countRegexMatches($wordPattern, $bodyLower);
        $titlePrefixMatches = countRegexMatches($prefixPattern, $titleLower);
        $bodyPrefixMatches = countRegexMatches($prefixPattern, $bodyLower);

        if ($titleWordMatches > 0 || $bodyWordMatches > 0 || $titlePrefixMatches > 0 || $bodyPrefixMatches > 0) {
            $matchedTerms++;
        }

        $score += min(4, $titleWordMatches) * 32;
        $score += min(6, $bodyWordMatches) * 10;
        $score += max(0, min(3, $titlePrefixMatches) - $titleWordMatches) * 14;
        $score += max(0, min(4, $bodyPrefixMatches) - $bodyWordMatches) * 4;

        if ($titleWordMatches === 0 && $bodyWordMatches === 0) {
            if (levenshtein($termLower, mb_substr($titleLower, 0, min(mb_strlen($titleLower), mb_strlen($termLower) + 2))) <= 1) {
                $score += 8;
            }
        }
    }

    if (!empty($terms) && $matchedTerms === count($terms)) {
        $score += 45;
    }

    if ($title !== '') {
        $score += max(0, 20 - (int) floor(mb_strlen($title) / 12));
    }

    return $score;
}

function resolveSearchResult(array $row, string $lang, LanguageService $languageService): array
{
    $type = (string) ($row['type'] ?? '');
    $id = (int) ($row['id'] ?? 0);
    $threadID = (int) ($row['threadID'] ?? 0);

    if ($type === 'plugin_catalog') {
        $module = (string) ($row['module'] ?? '');
        return [
            'type' => $type,
            'id' => $id,
            'url' => SeoUrlHandler::convertToSeoUrl('index.php?site=' . $module),
            'typeLabel' => 'Plugin',
            'titleText' => plainSearchText((string) ($row['title'] ?? ''), $lang),
            'contentText' => plainSearchText((string) ($row['body'] ?? ''), $lang),
        ];
    }

    $content = (string) ($row['body'] ?? '');
    $typeLabel = humanizePluginType($type, $languageService);
    $url = resolveGenericPluginUrl($type, $id);

    switch ($type) {
        case 'plugins_articles':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=articles&action=watch&id=' . $id);
            $typeLabel = 'Artikel';
            break;

        case 'plugins_articles_categories':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=articles&action=show&id=' . $id);
            $typeLabel = 'Artikelkategorie';
            break;

        case 'plugins_wiki':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=wiki&action=detail&id=' . $id);
            $typeLabel = 'Wiki';
            break;

        case 'plugins_wiki_categories':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=wiki&cat=' . $id);
            $typeLabel = 'Wikikategorie';
            break;

        case 'plugins_downloads':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=downloads&action=detail&id=' . $id);
            $typeLabel = 'Download';
            break;

        case 'plugins_downloads_categories':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=downloads&action=cat_list&id=' . $id);
            $typeLabel = 'Downloadkategorie';
            break;

        case 'plugins_forum_boards':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=forum');
            $typeLabel = 'Forum';
            break;

        case 'plugins_forum_threads':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=forum&action=thread&id=' . $id);
            $typeLabel = 'Forum';
            break;

        case 'plugins_forum_posts':
            $url = $threadID > 0
                ? SeoUrlHandler::convertToSeoUrl('index.php?site=forum&action=thread&id=' . $threadID . '#post' . $id)
                : SeoUrlHandler::convertToSeoUrl('index.php?site=forum&action=thread&id=' . $id);
            $typeLabel = 'Forum';
            break;

        case 'site_about':
        case 'plugins_about':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=about');
            $typeLabel = 'Über uns';
            $content = collectRowText($row, array_keys($row), $lang);
            break;

        case 'site_leistung':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=leistung');
            $typeLabel = 'Leistung';
            $content = collectRowText($row, array_keys($row), $lang);
            break;

        case 'site_info':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=info');
            $typeLabel = 'Info';
            $content = collectRowText($row, array_keys($row), $lang);
            break;

        case 'site_resume':
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=resume');
            $typeLabel = 'Resume';
            $content = collectRowText($row, array_keys($row), $lang);
            break;

        case 'settings_static':
            $staticID = (int) ($row['staticID'] ?? $id);
            $url = SeoUrlHandler::convertToSeoUrl('index.php?site=static&staticID=' . $staticID);
            $typeLabel = $languageService->get('type_page');
            $content = collectRowText($row, array_keys($row), $lang);
            break;
    }

    return [
        'type' => $type,
        'id' => $id,
        'url' => $url,
        'typeLabel' => $typeLabel,
        'titleText' => plainSearchText((string) ($row['title'] ?? ''), $lang),
        'contentText' => plainSearchText($content, $lang),
    ];
}

function appendSearchResult(array &$results, array &$seen, array $row, string $query, array $terms, string $lang): void
{
    $type = (string) ($row['type'] ?? '');
    $id = (int) ($row['id'] ?? 0);
    $keySuffix = !empty($row['result_key']) ? ':' . $row['result_key'] : '';

    if ($type === '' || $id <= 0) {
        return;
    }

    $key = $type . ':' . $id . $keySuffix;
    $score = scoreSearchResult($row, $query, $terms, $lang);

    if ($score <= 0) {
        return;
    }

    if (!isset($seen[$key]) || $score > $results[$seen[$key]]['score']) {
        $row['score'] = $score;

        if (isset($seen[$key])) {
            $results[$seen[$key]] = $row;
            return;
        }

        $seen[$key] = count($results);
        $results[] = $row;
    }
}

function loadPluginCatalogResults(string $query, array $terms, string $lang, array &$results, array &$seen): void
{
    $catalogPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'plugins_v2.json';
    if (!is_file($catalogPath)) {
        return;
    }

    $json = file_get_contents($catalogPath);
    $decoded = is_string($json) ? json_decode($json, true) : null;
    if (!is_array($decoded) || !isset($decoded['plugins']) || !is_array($decoded['plugins'])) {
        return;
    }

    foreach ($decoded['plugins'] as $plugin) {
        $module = (string) ($plugin['modulname'] ?? '');
        if ($module === '') {
            continue;
        }

        $row = [
            'type' => 'plugin_catalog',
            'id' => (int) sprintf('%u', crc32($module)),
            'module' => $module,
            'title' => (string) ($plugin['name'] ?? $module),
            'body' => (string) ($plugin['description'] ?? ''),
            'result_key' => $module,
        ];

        appendSearchResult($results, $seen, $row, $query, $terms, $lang);
    }
}

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars((string) ($config['selected_style'] ?? ''), ENT_QUOTES, 'UTF-8');
$currentLang = getCurrentSearchLanguage();

$data_array = [
    'class' => $class,
    'title' => $languageService->get('title'),
    'subtitle' => $languageService->get('subtitle'),
];
echo $tpl->loadTemplate('search', 'head', $data_array, 'plugin');

$q = normalizeSearchQuery((string) ($_GET['q'] ?? $_POST['q'] ?? ''));

$data_array = [
    'placeholder' => $languageService->get('placeholder'),
    'button' => $languageService->get('button'),
    'query' => htmlspecialchars($q, ENT_QUOTES, 'UTF-8'),
    'current_lang' => $currentLang,
];
echo $tpl->loadTemplate('search', 'form', $data_array, 'plugin');

if ($q === '') {
    echo $tpl->loadTemplate('search', 'foot', [], 'plugin');
    return;
}

$terms = tokenizeSearchQuery($q);
if (empty($terms) && $q !== '') {
    $terms = [mb_strtolower($q)];
}

$results = [];
$seen = [];

$staticTable = 'settings_static';
$staticColumns = getTableColumns($staticTable);
$staticIdCol = firstExistingColumn($staticColumns, ['staticID', 'id', 'pageID'], $currentLang);
$staticTitleCols = findLanguageColumns($staticColumns, ['title', 'headline', 'name'], $currentLang);
$staticBodyCols = findLanguageColumns($staticColumns, ['content', 'body', 'description', 'intro', 'text', 'summary'], $currentLang);
$staticSearchCols = array_values(array_unique(array_merge($staticTitleCols, $staticBodyCols)));

[$whereStatic, $typesStatic, $paramsStatic] = buildSearchConditions($staticSearchCols, $terms, $q);

if ($staticIdCol !== null && !empty($staticSearchCols) && $whereStatic !== '') {
    $staticSelectCols = array_values(array_unique(array_merge([$staticIdCol], $staticSearchCols)));
    $staticTitleCol = firstExistingColumn($staticColumns, ['title', 'headline', 'name'], $currentLang);
    $staticBodyCol = firstExistingColumn($staticColumns, ['content', 'body', 'description', 'intro', 'text', 'summary'], $currentLang);
    $sqlPages = "
        SELECT 'settings_static' AS type, `$staticIdCol` AS id, " . buildSelectColumns($staticSelectCols) . ",
        " . ($staticTitleCol !== null ? "`$staticTitleCol` AS title" : "'' AS title") . ",
        " . ($staticBodyCol !== null ? "`$staticBodyCol` AS body" : "'' AS body") . "
        FROM `$staticTable`
        WHERE ($whereStatic)
    ";

    $stmtPages = $_database->prepare($sqlPages);
    if ($stmtPages) {
        $stmtPages->bind_param($typesStatic, ...$paramsStatic);
        $stmtPages->execute();
        $resPages = $stmtPages->get_result();

        while ($row = $resPages->fetch_assoc()) {
            appendSearchResult($results, $seen, $row, $q, $terms, $currentLang);
        }

        $stmtPages->close();
    }
}

$pluginTables = [];
$resTables = safe_query("SHOW TABLES LIKE 'plugins_%'");
while ($row = mysqli_fetch_row($resTables)) {
    if (!shouldSkipSearchTable($row[0])) {
        $pluginTables[] = $row[0];
    }
}

foreach ($pluginTables as $table) {
    $columns = getTableColumns($table);
    $tableConfig = getRegisteredTableConfig($table);

    if ($tableConfig !== null) {
        $idCol = in_array($tableConfig['id'], $columns, true) ? $tableConfig['id'] : guessIdColumn($columns, $table, $currentLang);
        $titleCol = firstExistingColumn($columns, $tableConfig['title'], $currentLang);
        $contentCol = firstExistingColumn($columns, $tableConfig['content'], $currentLang);
        $searchColumns = array_values(array_unique(array_merge(
            findLanguageColumns($columns, $tableConfig['title'], $currentLang),
            findLanguageColumns($columns, $tableConfig['content'], $currentLang)
        )));
    } elseif ($table === 'plugins_forum_threads') {
        $idCol = 'threadID';
        $titleCol = firstExistingColumn($columns, ['title', 'headline', 'subject', 'name'], $currentLang);
        $contentCol = firstExistingColumn($columns, ['content', 'body', 'text', 'message'], $currentLang);
        $searchColumns = array_values(array_unique(array_merge(
            $titleCol !== null ? findLanguageColumns($columns, [$titleCol], $currentLang) : [],
            $contentCol !== null ? findLanguageColumns($columns, [$contentCol], $currentLang) : []
        )));
    } elseif ($table === 'plugins_forum_posts') {
        $idCol = 'postID';
        $titleCol = null;
        $contentCol = firstExistingColumn($columns, ['content', 'text', 'body', 'message'], $currentLang);
        $searchColumns = array_values(array_unique(
            $contentCol !== null ? findLanguageColumns($columns, [$contentCol], $currentLang) : []
        ));
    } else {
        $idCol = guessIdColumn($columns, $table, $currentLang);
        $titleCol = firstExistingColumn($columns, ['title', 'headline', 'name', 'subject', 'label', 'content_key'], $currentLang);
        $contentCol = firstExistingColumn($columns, [
            'content', 'description', 'body', 'intro', 'text', 'summary', 'excerpt', 'message',
            'caption', 'alt_text', 'tags', 'slug', 'feature_text', 'item_name', 'boss_name',
            'template_name', 'class_name', 'role_name', 'section_title', 'footer_link_name',
            'footer_link_url', 'button_text', 'price_unit', 'source', 'tactics', 'username',
            'server_name', 'channel_name', 'main_channel', 'extra_channels',
        ], $currentLang);
        $searchColumns = array_values(array_unique(array_merge(
            findLanguageColumns($columns, getSearchFieldCandidates(), $currentLang),
            $titleCol !== null ? findLanguageColumns($columns, [$titleCol], $currentLang) : [],
            $contentCol !== null ? findLanguageColumns($columns, [$contentCol], $currentLang) : []
        )));
    }

    if ($idCol === null || ($titleCol === null && $contentCol === null)) {
        continue;
    }

    [$wherePlugin, $typesPlugin, $paramsPlugin] = buildSearchConditions($searchColumns, $terms, $q);
    if ($wherePlugin === '') {
        continue;
    }

    $visibilityWhere = buildVisibilityConditions($columns);
    $selectFields = array_values(array_unique(array_merge([$idCol], $searchColumns)));
    $sql = "SELECT '$table' AS type, `$idCol` AS id, "
        . buildSelectColumns($selectFields) . ", "
        . ($titleCol !== null ? "`$titleCol` AS title, " : "'' AS title, ")
        . ($contentCol !== null ? "`$contentCol` AS body " : "'' AS body ")
        . ($table === 'plugins_forum_posts' && in_array('threadID', $columns, true) ? ", threadID " : "")
        . "FROM `$table` WHERE ($wherePlugin)";

    if ($visibilityWhere !== '') {
        $sql .= " AND $visibilityWhere";
    }

    $stmt = $_database->prepare($sql);
    if (!$stmt) {
        continue;
    }

    $stmt->bind_param($typesPlugin, ...$paramsPlugin);
    $stmt->execute();
    $resPlugin = $stmt->get_result();

    while ($row = $resPlugin->fetch_assoc()) {
        appendSearchResult($results, $seen, $row, $q, $terms, $currentLang);
    }

    $stmt->close();
}

loadPluginCatalogResults($q, $terms, $currentLang, $results, $seen);

usort($results, static function (array $a, array $b) {
    $scoreCompare = ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
    if ($scoreCompare !== 0) {
        return $scoreCompare;
    }

    return strcasecmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
});

$perPage = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$total = count($results);
$totalPages = max(1, (int) ceil($total / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;
$paginated = array_slice($results, $offset, $perPage);

if ($total === 0) {
    echo $tpl->loadTemplate('search', 'no_results', [
        'no_results' => $languageService->get('no_results'),
    ], 'plugin');
    echo $tpl->loadTemplate('search', 'foot', [], 'plugin');
    return;
}

$resultCountText = str_replace(
    ['{total}', '{from}', '{to}'],
    [(string) $total, (string) ($offset + 1), (string) min($offset + $perPage, $total)],
    $languageService->get('results_summary')
);

echo '<div class="card nx-search-meta-card mb-4"><div class="card-body py-3 px-4">'
    . '<div class="d-flex flex-wrap align-items-center justify-content-between gap-2">'
    . '<div class="fw-semibold">' . htmlspecialchars($resultCountText, ENT_QUOTES, 'UTF-8') . '</div>'
    . '</div></div></div>';

foreach ($paginated as $row) {
    $resolved = resolveSearchResult($row, $currentLang, $languageService);
    $titleText = $resolved['titleText'] !== '' ? $resolved['titleText'] : $languageService->get('untitled');
    $snippet = makeSearchSnippet($resolved['contentText'], $terms, 70);

    $tplData = [
        'type' => htmlspecialchars($resolved['typeLabel'], ENT_QUOTES, 'UTF-8'),
        'title' => highlightSearchTerms($titleText, $terms),
        'snippet' => $snippet,
        'url' => $resolved['url'],
        'open_label' => $languageService->get('open_result'),
    ];

    echo $tpl->loadTemplate('search', 'result_item', $tplData, 'plugin');
}

if ($totalPages > 1) {
    $baseUrl = 'index.php?' . http_build_query([
        'site' => 'search',
        'language' => $currentLang,
        'q' => $q,
    ]);
    echo $tpl->renderPagination($baseUrl, (int)$page, (int)$totalPages);
}

echo $tpl->loadTemplate('search', 'foot', [], 'plugin');
?>
