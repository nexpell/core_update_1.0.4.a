<?php
declare(strict_types=1);

use nexpell\AccessControl;
use nexpell\SeoUrlHandler;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $_database, $languageService;

function easyfolio_nav_lang(?string $txt): string
{
    global $languageService;

    if ($txt === null) {
        return '';
    }

    $txt = trim((string)$txt);
    if ($txt === '' || strpos($txt, '[[lang:') === false) {
        return $txt;
    }

    try {
        $parsed = (string)$languageService->parseMultilang($txt);
        return $parsed !== '' ? $parsed : $txt;
    } catch (\Throwable $e) {
        return $txt;
    }
}

function easyfolio_nav_table_has_column(string $table, string $column): bool
{
    global $_database;
    static $cache = [];

    $key = $table . '.' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    $tableEsc = $_database->real_escape_string($table);
    $colEsc = $_database->real_escape_string($column);
    $res = $_database->query("SHOW COLUMNS FROM `{$tableEsc}` LIKE '{$colEsc}'");
    $cache[$key] = ($res instanceof mysqli_result) && ($res->num_rows > 0);
    if ($res instanceof mysqli_result) {
        $res->free();
    }

    return $cache[$key];
}

function easyfolio_nav_resolve_url(string $url, string $currentLang): string
{
    $url = trim($url);
    if ($url === '') {
        return '/';
    }

    $url = str_replace(
        ['{current_lang}', '%7Bcurrent_lang%7D', '%7bcurrent_lang%7d'],
        rawurlencode($currentLang),
        $url
    );

    if (str_starts_with($url, 'index.php')) {
        return SeoUrlHandler::convertToSeoUrl($url);
    }

    return $url;
}

function easyfolio_nav_is_active(string $url): bool
{
    $requestUri = (string)($_SERVER['REQUEST_URI'] ?? '');
    if ($requestUri === '' || $url === '' || $url === '#') {
        return false;
    }

    $requestPath = strtok($requestUri, '?') ?: $requestUri;
    $urlPath = strtok($url, '?') ?: $url;

    return rtrim($requestPath, '/') === rtrim($urlPath, '/');
}

function easyfolio_nav_render_items(array $items, int $depth = 0): string
{
    $html = '';

    foreach ($items as $item) {
        $title = htmlspecialchars((string)($item['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars((string)($item['url'] ?? '#'), ENT_QUOTES, 'UTF-8');
        $children = $item['children'] ?? [];
        $isActive = !empty($item['active']);
        $hasChildren = !empty($children);

        if ($hasChildren) {
            $html .= '<li class="dropdown' . ($isActive ? ' active' : '') . '">';
            $html .= '<a href="' . $url . '"' . ($isActive ? ' class="active"' : '') . '><span>' . $title . '</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>';
            $html .= '<ul>';
            $html .= easyfolio_nav_render_items($children, $depth + 1);
            $html .= '</ul>';
            $html .= '</li>';
            continue;
        }

        $html .= '<li><a href="' . $url . '"' . ($isActive ? ' class="active"' : '') . '>' . $title . '</a></li>';
    }

    return $html;
}

$settings = [];
$settingsRes = $_database->query("SELECT setting_key, setting_value FROM navigation_website_settings");
while ($settingsRes && ($row = $settingsRes->fetch_assoc())) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$currentLang = strtolower((string)($languageService->currentLanguage ?? 'de'));
$currentLang = preg_replace('/[^a-z]/', '', $currentLang) ?: 'de';
$mainNameExpr = easyfolio_nav_table_has_column('navigation_website_main', 'name') ? "m.name" : "''";
$subNameExpr = easyfolio_nav_table_has_column('navigation_website_sub', 'name') ? "s.name" : "''";

$items = [];
$mainRes = $_database->query("
    SELECT
        m.*,
        COALESCE(NULLIF(l.content, ''), NULLIF({$mainNameExpr}, ''), m.modulname) AS display_name
    FROM navigation_website_main m
    LEFT JOIN navigation_website_lang l
        ON l.content_key = CONCAT('nav_main_', m.mnavID)
       AND l.language = '{$currentLang}'
    ORDER BY m.sort ASC
");

while ($mainRes && ($mainRow = $mainRes->fetch_assoc())) {
    $mainUrl = easyfolio_nav_resolve_url((string)($mainRow['url'] ?? ''), $currentLang);
    $mainTitle = easyfolio_nav_lang($mainRow['display_name'] ?? $mainRow['name'] ?? $mainRow['modulname'] ?? '');
    $entry = [
        'title' => $mainTitle,
        'url' => $mainUrl,
        'active' => easyfolio_nav_is_active($mainUrl),
        'children' => [],
    ];

    if ((int)($mainRow['isdropdown'] ?? 0) === 1) {
        $mnavID = (int)$mainRow['mnavID'];
        $subRes = $_database->query("
            SELECT
                s.*,
                COALESCE(NULLIF(l.content, ''), NULLIF({$subNameExpr}, ''), s.modulname) AS display_name
            FROM navigation_website_sub s
            LEFT JOIN navigation_website_lang l
                ON l.content_key = CONCAT('nav_sub_', s.snavID)
               AND l.language = '{$currentLang}'
            WHERE s.mnavID = {$mnavID}
            ORDER BY s.sort ASC
        ");

        while ($subRes && ($subRow = $subRes->fetch_assoc())) {
            $subUrl = easyfolio_nav_resolve_url((string)($subRow['url'] ?? ''), $currentLang);
            $subTitle = easyfolio_nav_lang($subRow['display_name'] ?? $subRow['name'] ?? $subRow['modulname'] ?? '');
            $entry['children'][] = [
                'title' => $subTitle,
                'url' => $subUrl,
                'active' => easyfolio_nav_is_active($subUrl),
                'children' => [],
            ];
            $entry['active'] = $entry['active'] || easyfolio_nav_is_active($subUrl);
        }
    }

    $items[] = $entry;
}

$logoFile = !empty($settings['logo_light']) ? '/includes/plugins/navigation/images/' . ltrim((string)$settings['logo_light'], '/') : '';
$brandName = htmlspecialchars((string)($settings['brand_title'] ?? 'EasyFolio'), ENT_QUOTES, 'UTF-8');
$homeUrl = SeoUrlHandler::convertToSeoUrl('index.php?site=index&lang=' . rawurlencode($currentLang));
if (!is_string($homeUrl) || $homeUrl === '') {
    $homeUrl = '/';
}

$socialLinks = [];
if (!empty($_SESSION['userID'])) {
    $uid = (int)$_SESSION['userID'];
    $socialLinks[] = [
        'url' => SeoUrlHandler::convertToSeoUrl("index.php?site=profile&userID={$uid}"),
        'icon' => 'bi-person',
        'label' => 'Profil',
    ];

    if (AccessControl::canAccessAdmin($_database, $uid)) {
        $socialLinks[] = [
            'url' => '/admin/admincenter.php',
            'icon' => 'bi-speedometer2',
            'label' => 'Admin',
        ];
    }

    $socialLinks[] = [
        'url' => SeoUrlHandler::convertToSeoUrl('index.php?site=logout'),
        'icon' => 'bi-box-arrow-right',
        'label' => 'Logout',
    ];
} else {
    $socialLinks[] = [
        'url' => SeoUrlHandler::convertToSeoUrl('index.php?site=login'),
        'icon' => 'bi-box-arrow-in-right',
        'label' => 'Login',
    ];
}

$languages = $languageService->getActiveLanguages();
foreach ($languages as $language) {
    $iso = (string)($language['iso_639_1'] ?? '');
    if ($iso === $currentLang) {
        continue;
    }

    $targetUrl = SeoUrlHandler::convertToSeoUrl('index.php?site=index&lang=' . rawurlencode($iso));
    $socialLinks[] = [
        'url' => $targetUrl ?: '/',
        'icon' => '',
        'label' => strtoupper($iso),
        'is_lang' => true,
    ];
}
?>
<header id="header" class="header d-flex align-items-center sticky-top">
  <div class="container-fluid position-relative d-flex align-items-center justify-content-between header-container">
    <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>" class="logo d-flex align-items-center me-auto me-xl-0">
      <?php if ($logoFile !== ''): ?>
        <img src="<?= htmlspecialchars($logoFile, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $brandName ?>">
      <?php endif; ?>
      <h1 class="sitename"><?= $brandName ?></h1>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <?= easyfolio_nav_render_items($items) ?>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    <div class="header-social-links">
      <?php foreach ($socialLinks as $link): ?>
        <a href="<?= htmlspecialchars((string)$link['url'], ENT_QUOTES, 'UTF-8') ?>"<?= !empty($link['is_lang']) ? ' class="lang-chip"' : '' ?><?= (($link['url'] ?? '') === '/admin/admincenter.php') ? ' target="_blank"' : '' ?> aria-label="<?= htmlspecialchars((string)$link['label'], ENT_QUOTES, 'UTF-8') ?>">
          <?php if (!empty($link['icon'])): ?>
            <i class="bi <?= htmlspecialchars((string)$link['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
          <?php else: ?>
            <span><?= htmlspecialchars((string)$link['label'], ENT_QUOTES, 'UTF-8') ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</header>
