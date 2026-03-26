<?php
// ==========================================================
// Session starten (für Sprache, Login, Theme-Toggle etc.)
// ==========================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================================
// Sprache per ?setlang=xx wechseln → in Session speichern
// ==========================================================
if (isset($_GET['setlang'])) {
    $lang = preg_replace('/[^a-z]/', '', $_GET['setlang']); // Sicherheitsfilter
    $_SESSION['language'] = $lang;

    if (isset($languageService)) {
        $languageService->setLanguage($lang);
    }

    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

use webspell\LanguageService;
use nexpell\SeoUrlHandler;
use nexpell\PluginManager;
require_once BASE_PATH . '/system/core/builder_widgets_core.php';

// ==========================================================
// SEO-URL Routing (schöne URLs wie /news/123 auflösen)
// ==========================================================
SeoUrlHandler::route();

// ==========================================================
// PluginManager initialisieren (Seiten + Widgets laden)
// ==========================================================
$pluginManager = new PluginManager($_database);
$currentSite = $_GET['site'] ?? 'start';

// ==========================================================
// Sprache erneut für Redirect-Variante ?setlang setzen
// (Erhalt der URL-Parameter ohne setlang)
// ==========================================================
if (isset($_GET['setlang'])) {

    $lang = strtolower(preg_replace('/[^a-z]/', '', $_GET['setlang']));
    $_SESSION['language'] = $lang;

    if (isset($languageService) && method_exists($languageService, 'setLanguage')) {
        $languageService->setLanguage($lang);
    }

    $params = $_GET;
    unset($params['setlang']);

    $target = $_SERVER['PHP_SELF'];
    if (!empty($params)) {
        $target .= '?' . http_build_query($params);
    }

    header("Location: $target", true, 302);
    exit;
}

// ==========================================================
// LanguageService initialisieren + aktive Sprache ermitteln
// ==========================================================
if (!isset($languageService)) {
    $languageService = new LanguageService($_database);
}

$currentLang = $_SESSION['language'] ?? $languageService->detectLanguage();
$languageService->setLanguage($currentLang);

// Alte Variable für Kompatibilität
$_language = $languageService;

// Aktuelle Seite für Widgets
$page = $_GET['site'] ?? 'index';
$page_escaped = mysqli_real_escape_string($GLOBALS['_database'], $page);

// ==========================================================
// HTML-THEME aus DB übernehmen
// auto = startet immer als light (wichtig – JS switcht später)
// ==========================================================
$settings = [];

$res = $_database->query("
    SELECT setting_key, setting_value
    FROM navigation_website_settings
");

while ($row = $res->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$dbTheme = $settings['navbar_modus'] ?? 'auto';
$htmlTheme = ($dbTheme === 'auto') ? 'light' : $dbTheme;


// =========================================
// NX SETTINGS – GLOBAL INIT (SAFE)
// =========================================
if (!isset($GLOBALS['nx_settings'])) {

    global $_database;
    $GLOBALS['nx_settings'] = [];

    if (isset($_database)) {
        $res = $_database->query("
            SELECT setting_key, setting_value
            FROM navigation_website_settings
        ");

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $GLOBALS['nx_settings'][$row['setting_key']] = $row['setting_value'];
            }
        }
    }
}

// ==========================================================
// Widgets für die aktuelle Seite aus DB laden
// ==========================================================
$positionsRaw = [];
$res = safe_query("SELECT * FROM settings_widgets_positions WHERE page='$page_escaped' ORDER BY position, sort_order ASC");
while ($row = mysqli_fetch_assoc($res)) {
    $pos = (string)$row['position'];
    if (!isset($positionsRaw[$pos])) {
        $positionsRaw[$pos] = [];
    }
    $positionsRaw[$pos][] = $row;
}

// Normalisierte Struktur für alle Positionen (inkl. neuer Layout-Zonen) vorbereiten,
// damit Core-Layout-Widgets (Sektionen/Spalten) ihre Kinder rendern können.
$__NX_ALL_WIDGET_ROWS = [];
foreach ($positionsRaw as $pos => $rows) {
    foreach ($rows as $row) {
        $settingsArr = [];
        $json = trim((string)($row['settings'] ?? ''));
        if ($json !== '') {
            $tmp = json_decode($json, true);
            if (is_array($tmp)) {
                $settingsArr = $tmp;
            }
        }
        $__NX_ALL_WIDGET_ROWS[$pos][] = [
            'position'    => $pos,
            'widget_key'  => (string)$row['widget_key'],
            'instance_id' => (string)($row['instance_id'] ?? ''),
            'settings'    => $settingsArr,
            'title'       => (string)($row['title'] ?? $row['widget_key']),
            'modulname'   => (string)($row['modulname'] ?? ''),
        ];
    }
}

// Global bereitstellen, damit der Core-Renderer für Sektionen darauf zugreifen kann
$GLOBALS['__NX_ALL_WIDGET_ROWS'] = $__NX_ALL_WIDGET_ROWS;

// Widgets rendern – eine Zone: alles in 'content' (Merge aller Legacy-Positionen)
$allPositions = ['content'];
$legacyMerge = ['top', 'undertop', 'left', 'maintop', 'mainbottom', 'right', 'bottom', 'content'];
$widgetsByPosition = [];
foreach ($allPositions as $position) {
    $widgetsByPosition[$position] = [];
    $rows = [];
    foreach ($legacyMerge as $leg) {
        foreach ($__NX_ALL_WIDGET_ROWS[$leg] ?? [] as $w) {
            $rows[] = $w;
        }
    }
    foreach ($rows as $w) {
        $key   = $w['widget_key'];
        $title = $w['title'] ?: $key;
        $output = function_exists('nxb_render_frontend_widget_html')
            ? nxb_render_frontend_widget_html($key, (string)($w['instance_id'] ?? ''), (array)($w['settings'] ?? []), $title)
            : ((strpos($key, 'core_') === 0 && function_exists('nxb_render_core_widget_html'))
                ? nxb_render_core_widget_html($key, $w['settings'], $title)
                : $pluginManager->renderWidget($key, [
                    'instanceId' => (string)($w['instance_id'] ?? ''),
                    'settings' => (array)($w['settings'] ?? []),
                    'title' => $title,
                    'ctx' => ['builder' => false, 'widget_key' => $key, 'instance_id' => (string)($w['instance_id'] ?? ''), 'title' => $title],
                ]));
        if (!empty(trim((string)$output))) {
            $widgetsByPosition[$position][] = $output;
        }
    }
}

// ==========================================================
// Aktives Website-Theme ermitteln (builder-driven)
// ==========================================================
$currentTheme = 'default';
$theme_name = 'default';

// ==========================================================
// SEO-Metadaten der aktuellen Seite laden
// ==========================================================
require_once BASE_PATH.'/system/seo_meta_helper.php';
$meta = getSeoMeta($page);

// ==========================================================
// Plugin-CSS/JS für Startpage / Plugineinbindung
// ==========================================================

$site = detectSite();

$plugin = detectPluginForSite($site);
if ($plugin) {
    $pluginManager->loadPluginAssets($site);
} else {
    registerModuleAssets($site);
}

// ==========================================================
// Plugin-CSS/JS für späteren <head> Ausgaben vorbereiten
// ==========================================================
$pluginFile = $pluginManager->loadPluginPage($currentSite);
if ($pluginFile) {
    $pluginName = basename($pluginFile, '.php');
    $pluginManager->loadPluginAssets($pluginName);
}

$plugin_css = $pluginManager->cssOutput;
$plugin_js  = $pluginManager->jsOutput;

// ==========================================================
// Live-Visitor Statistik aktualisieren
// zählt Seitenbesuche, Online-Zeit etc.
// ==========================================================
live_visitor_track($currentSite);
