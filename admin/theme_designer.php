<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\AccessControl;
use nexpell\ThemeManager;

AccessControl::checkAdminAccess('ac_theme');

if (!class_exists('nexpell\\ThemeManager')) {
    require_once __DIR__ . '/../system/classes/ThemeManager.php';
}
require_once __DIR__ . '/../system/core/theme_builder_helper.php';
require_once __DIR__ . '/../system/core/builder_core.php';

$db = $GLOBALS['_database'] ?? null;
if (!$db instanceof mysqli) {
    echo '<div class="alert alert-danger">Keine Datenbankverbindung verfuegbar.</div>';
    return;
}

$themeManager = $GLOBALS['nx_theme_manager'] ?? new ThemeManager($db, dirname(__DIR__) . '/includes/themes', '/includes/themes');
$themeManager->ensureSchema();

$activeTheme = $themeManager->getActiveManifest();
$activeThemeSlug = $themeManager->getActiveThemeSlug();
$currentLang = strtolower((string)($_SESSION['language'] ?? 'de'));
$selectedPage = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)($_GET['target_page'] ?? ($_GET['page'] ?? 'index')));
if ($selectedPage === '') {
    $selectedPage = 'index';
}
$pages = ['index' => 'Startseite'];
$res = safe_query("SELECT modulname FROM settings_plugins ORDER BY modulname ASC");
$exclude = ['navigation', 'carousel', 'error_404', 'footer', 'login', 'register', 'lostpassword', 'profile', 'edit_profile', 'lastlogin'];
while ($row = mysqli_fetch_assoc($res)) {
    $module = (string)($row['modulname'] ?? '');
    if ($module === '' || in_array($module, $exclude, true)) {
        continue;
    }
    $pages[$module] = $module;
}

$livePath = $selectedPage === 'index'
    ? '/' . rawurlencode($currentLang) . '/'
    : '/' . rawurlencode($currentLang) . '/' . rawurlencode($selectedPage) . '/';
$liveBuilderUrlBase = $livePath . '?builder=1&designer=1';
$widgetBuilderUrl = '/admin/plugin_widgets_preview.php?page=' . rawurlencode($selectedPage);
$csrf = (string)($_SESSION['csrf_token'] ?? '');
$designerThemeStatus = '';
$themeColorPresets = [
    'nexpell' => [
        'label' => 'Nexpell',
        'description' => 'Eigenes Farbsystem von nexpell.de.',
        'colors' => [
            'accent' => '#E67E22',
            'text' => '#F3F6FB',
            'page_top' => '#121826',
            'page_bg' => '#172033',
            'surface' => '#1F2A3D',
        ],
        'card_background' => '#1F2A3D',
        'card_border_color' => '#314158',
        'card_style' => 'elevated',
        'card_radius' => '1rem',
        'card_border_width' => '1',
        'button_radius' => '.5rem',
        'input_radius' => '.5rem',
        'heading_weight' => '700',
        'hero_radius' => '1.5rem',
        'nav_style' => 'solid',
        'nav_variant' => 'dark',
        'nav_link_weight' => '600',
        'nav_font_size' => '1rem',
        'nav_text_transform' => 'none',
        'nav_border_width' => '1',
        'nav_radius' => '20',
        'nav_background' => '#1A1D20',
        'nav_link' => '#F3F6FB',
        'nav_hover' => '#E67E22',
        'nav_active' => '#E67E22',
        'nav_dropdown_background' => '#1F2A3D',
        'pagination_radius' => '999px',
        'pagination_border_width' => '1',
        'pagination_gap' => '.25rem',
        'pagination_font_weight' => '600',
        'pagination_color' => '#E67E22',
        'pagination_background' => '#1F2A3D',
        'pagination_border_color' => '#314158',
        'pagination_hover_color' => '#FFFFFF',
        'pagination_hover_background' => '#E67E22',
        'pagination_hover_border_color' => '#E67E22',
        'pagination_active_color' => '#FFFFFF',
        'pagination_active_background' => '#E67E22',
        'pagination_active_border_color' => '#E67E22',
    ],
    'cyborg' => [
        'label' => 'Cyborg',
        'description' => 'Bootswatch: Jet black and electric blue.',
        'colors' => [
            'accent' => '#2A9FD6',
            'text' => '#adafae',
            'page_top' => '#050505',
            'page_bg' => '#060606',
            'surface' => '#222222',
        ],
        'card_background' => '#222222',
        'card_border_color' => '#495057',
        'card_style' => 'outlined',
        'card_radius' => '0',
        'card_border_width' => '1',
        'button_radius' => '0',
        'input_radius' => '0',
        'heading_weight' => '500',
        'hero_radius' => '0',
        'nav_style' => 'solid',
        'nav_variant' => 'dark',
        'nav_link_weight' => '300',
        'nav_font_size' => '1rem',
        'nav_text_transform' => 'none',
        'nav_border_width' => '1',
        'nav_radius' => '0',
        'nav_background' => '#060606',
        'nav_link' => '#adafae',
        'nav_hover' => '#2A9FD6',
        'nav_active' => '#2A9FD6',
        'nav_dropdown_background' => '#222222',
        'pagination_radius' => '.375rem',
        'pagination_border_width' => '1',
        'pagination_gap' => '.15rem',
        'pagination_font_weight' => '500',
        'pagination_color' => '#2A9FD6',
        'pagination_background' => '#222222',
        'pagination_border_color' => '#282828',
        'pagination_hover_color' => '#FFFFFF',
        'pagination_hover_background' => '#2A9FD6',
        'pagination_hover_border_color' => '#2A9FD6',
        'pagination_active_color' => '#FFFFFF',
        'pagination_active_background' => '#2A9FD6',
        'pagination_active_border_color' => '#2A9FD6',
    ],
    'lux' => [
        'label' => 'Lux',
        'description' => 'Bootswatch: A touch of class.',
        'colors' => [
            'accent' => '#1a1a1a',
            'text' => '#55595c',
            'page_top' => '#f7f7f9',
            'page_bg' => '#ffffff',
            'surface' => '#ffffff',
        ],
        'card_background' => '#ffffff',
        'card_border_color' => '#eceeef',
        'card_style' => 'outlined',
        'card_radius' => '0',
        'card_border_width' => '1',
        'button_radius' => '0',
        'input_radius' => '0',
        'heading_weight' => '600',
        'hero_radius' => '0',
        'nav_style' => 'outlined',
        'nav_variant' => 'light',
        'nav_link_weight' => '600',
        'nav_font_size' => '.875rem',
        'nav_text_transform' => 'uppercase',
        'nav_border_width' => '1',
        'nav_radius' => '0',
        'nav_background' => '#ffffff',
        'nav_link' => '#55595c',
        'nav_hover' => '#1a1a1a',
        'nav_active' => '#1a1a1a',
        'nav_dropdown_background' => '#ffffff',
        'pagination_radius' => '0',
        'pagination_border_width' => '1',
        'pagination_gap' => '0',
        'pagination_font_weight' => '300',
        'pagination_color' => '#1A1A1A',
        'pagination_background' => '#FFFFFF',
        'pagination_border_color' => '#E0E1E2',
        'pagination_hover_color' => '#1A1A1A',
        'pagination_hover_background' => '#F0F1F2',
        'pagination_hover_border_color' => '#F0F1F2',
        'pagination_active_color' => '#FFFFFF',
        'pagination_active_background' => '#1A1A1A',
        'pagination_active_border_color' => '#1A1A1A',
    ],
    'yeti' => [
        'label' => 'Yeti',
        'description' => 'Bootswatch: A friendly foundation.',
        'colors' => [
            'accent' => '#008CBA',
            'text' => '#222222',
            'page_top' => '#fcfcfc',
            'page_bg' => '#ffffff',
            'surface' => '#FFFFFF',
        ],
        'card_background' => '#FFFFFF',
        'card_border_color' => '#dee2e6',
        'card_style' => 'outlined',
        'card_radius' => '0',
        'card_border_width' => '1',
        'button_radius' => '0',
        'input_radius' => '0',
        'heading_weight' => '300',
        'hero_radius' => '0',
        'nav_style' => 'solid',
        'nav_variant' => 'light',
        'nav_link_weight' => '300',
        'nav_font_size' => '1rem',
        'nav_text_transform' => 'none',
        'nav_border_width' => '1',
        'nav_radius' => '0',
        'nav_background' => '#FFFFFF',
        'nav_link' => '#222222',
        'nav_hover' => '#008CBA',
        'nav_active' => '#008CBA',
        'nav_dropdown_background' => '#EEEEEE',
        'pagination_radius' => '3px',
        'pagination_border_width' => '0',
        'pagination_gap' => '.1em',
        'pagination_font_weight' => '300',
        'pagination_color' => '#008CBA',
        'pagination_background' => '#FFFFFF',
        'pagination_border_color' => '#FFFFFF',
        'pagination_hover_color' => '#FFFFFF',
        'pagination_hover_background' => '#007095',
        'pagination_hover_border_color' => '#007095',
        'pagination_active_color' => '#FFFFFF',
        'pagination_active_background' => '#008CBA',
        'pagination_active_border_color' => '#008CBA',
    ],
    'slate' => [
        'label' => 'Slate',
        'description' => 'Bootswatch: Shades of gunmetal gray.',
        'colors' => [
            'accent' => '#3A89C9',
            'text' => '#C8C8C8',
            'page_top' => '#1C1E22',
            'page_bg' => '#272B30',
            'surface' => '#32383E',
        ],
        'card_background' => '#32383E',
        'card_border_color' => '#495057',
        'card_style' => 'outlined',
        'card_radius' => '0',
        'card_border_width' => '1',
        'button_radius' => '.35rem',
        'input_radius' => '.35rem',
        'heading_weight' => '500',
        'hero_radius' => '.75rem',
        'nav_style' => 'solid',
        'nav_variant' => 'dark',
        'nav_link_weight' => '300',
        'nav_font_size' => '1rem',
        'nav_text_transform' => 'none',
        'nav_border_width' => '1',
        'nav_radius' => '0',
        'nav_background' => '#272B30',
        'nav_link' => '#C8C8C8',
        'nav_hover' => '#3A89C9',
        'nav_active' => '#3A89C9',
        'nav_dropdown_background' => '#32383E',
        'pagination_radius' => '.25rem',
        'pagination_border_width' => '0',
        'pagination_gap' => '.1rem',
        'pagination_font_weight' => '300',
        'pagination_color' => '#3A89C9',
        'pagination_background' => '#32383E',
        'pagination_border_color' => '#32383E',
        'pagination_hover_color' => '#FFFFFF',
        'pagination_hover_background' => '#3A89C9',
        'pagination_hover_border_color' => '#3A89C9',
        'pagination_active_color' => '#FFFFFF',
        'pagination_active_background' => '#3A89C9',
        'pagination_active_border_color' => '#3A89C9',
    ],
    'brite' => [
        'label' => 'Brite',
        'description' => 'Bootswatch: Loud outlines and chunky contrast.',
        'colors' => [
            'accent' => '#A2E436',
            'text' => '#212529',
            'page_top' => '#FFFFFF',
            'page_bg' => '#FFFFFF',
            'surface' => '#FFFFFF',
        ],
        'card_background' => '#FFFFFF',
        'card_border_color' => '#000000',
        'card_style' => 'outlined',
        'card_radius' => '.375rem',
        'card_border_width' => '2',
        'button_radius' => '0',
        'input_radius' => '.375rem',
        'heading_weight' => '500',
        'hero_radius' => '.375rem',
        'nav_style' => 'outlined',
        'nav_variant' => 'light',
        'nav_link_weight' => '500',
        'nav_font_size' => '1.09375rem',
        'nav_text_transform' => 'none',
        'nav_border_width' => '2',
        'nav_radius' => '0',
        'nav_background' => '#FFFFFF',
        'nav_link' => '#000000',
        'nav_hover' => '#000000',
        'nav_active' => '#000000',
        'nav_dropdown_background' => '#FFFFFF',
        'pagination_radius' => '.375rem',
        'pagination_border_width' => '2',
        'pagination_gap' => '.25rem',
        'pagination_font_weight' => '500',
        'pagination_color' => '#000000',
        'pagination_background' => '#FFFFFF',
        'pagination_border_color' => '#000000',
        'pagination_hover_color' => '#000000',
        'pagination_hover_background' => '#A2E436',
        'pagination_hover_border_color' => '#000000',
        'pagination_active_color' => '#000000',
        'pagination_active_background' => '#A2E436',
        'pagination_active_border_color' => '#000000',
    ],
    'darkly' => [
        'label' => 'Darkly',
        'description' => 'Bootswatch: Flatly in night mode.',
        'colors' => [
            'accent' => '#375A7F',
            'text' => '#FFFFFF',
            'page_top' => '#222222',
            'page_bg' => '#222222',
            'surface' => '#303030',
        ],
        'card_background' => '#303030',
        'card_border_color' => '#444444',
        'card_style' => 'outlined',
        'card_radius' => '.375rem',
        'card_border_width' => '1',
        'button_radius' => '0',
        'input_radius' => '.375rem',
        'heading_weight' => '500',
        'hero_radius' => '.375rem',
        'nav_style' => 'solid',
        'nav_variant' => 'dark',
        'nav_link_weight' => '400',
        'nav_font_size' => '1rem',
        'nav_text_transform' => 'none',
        'nav_border_width' => '1',
        'nav_radius' => '0',
        'nav_background' => '#375A7F',
        'nav_link' => '#FFFFFF',
        'nav_hover' => '#00BC8C',
        'nav_active' => '#00BC8C',
        'nav_dropdown_background' => '#303030',
        'pagination_radius' => '.375rem',
        'pagination_border_width' => '0',
        'pagination_gap' => '.15rem',
        'pagination_font_weight' => '400',
        'pagination_color' => '#FFFFFF',
        'pagination_background' => '#375A7F',
        'pagination_border_color' => '#375A7F',
        'pagination_hover_color' => '#FFFFFF',
        'pagination_hover_background' => '#00EFB2',
        'pagination_hover_border_color' => '#00EFB2',
        'pagination_active_color' => '#FFFFFF',
        'pagination_active_background' => '#00EFB2',
        'pagination_active_border_color' => '#00EFB2',
    ],
    'morph' => [
        'label' => 'Morph',
        'description' => 'Bootswatch: Soft neumorphic surfaces.',
        'colors' => [
            'accent' => '#378DFC',
            'text' => '#7B8AB8',
            'page_top' => '#F0F5FA',
            'page_bg' => '#D9E3F1',
            'surface' => '#F0F5FA',
        ],
        'card_background' => '#D9E3F1',
        'card_border_color' => '#D9E3F1',
        'card_style' => 'soft',
        'card_radius' => '.375rem',
        'card_border_width' => '0',
        'button_radius' => '.375rem',
        'input_radius' => '.375rem',
        'heading_weight' => '500',
        'hero_radius' => '.375rem',
        'nav_style' => 'soft',
        'nav_variant' => 'light',
        'nav_link_weight' => '500',
        'nav_font_size' => '1rem',
        'nav_text_transform' => 'none',
        'nav_border_width' => '0',
        'nav_radius' => '12',
        'nav_background' => '#D9E3F1',
        'nav_link' => '#485785',
        'nav_hover' => '#378DFC',
        'nav_active' => '#378DFC',
        'nav_dropdown_background' => '#F0F5FA',
        'pagination_radius' => '999px',
        'pagination_border_width' => '0',
        'pagination_gap' => '.25rem',
        'pagination_font_weight' => '500',
        'pagination_color' => '#378DFC',
        'pagination_background' => '#F0F5FA',
        'pagination_border_color' => '#F0F5FA',
        'pagination_hover_color' => '#FFFFFF',
        'pagination_hover_background' => '#378DFC',
        'pagination_hover_border_color' => '#378DFC',
        'pagination_active_color' => '#FFFFFF',
        'pagination_active_background' => '#378DFC',
        'pagination_active_border_color' => '#378DFC',
    ],
];
$headlineStyles = [];
for ($i = 1; $i <= 10; $i++) {
    $key = 'head-boxes-' . $i;
    $headlineStyles[$key] = 'Headstyle ' . $i;
}
$headlineStyleSelected = 'head-boxes-4';
$headlineRes = safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id = 1 LIMIT 1");
if ($headlineRes && mysqli_num_rows($headlineRes) > 0) {
    $headlineRow = mysqli_fetch_assoc($headlineRes);
    if (!empty($headlineRow['selected_style'])) {
        $headlineStyleSelected = (string)$headlineRow['selected_style'];
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_theme_settings'])) {
    $postedCsrf = (string)($_POST['csrf_token'] ?? '');
    if ($csrf !== '' && hash_equals($csrf, $postedCsrf)) {
        $manifest = $themeManager->getActiveManifest();
        $currentThemeSettings = nx_theme_builder_generator_defaults($manifest, (string)($activeTheme['name'] ?? ucfirst($activeThemeSlug)));
        $savedThemeSettings = [
            'hero_title' => trim((string)($_POST['hero_title'] ?? $currentThemeSettings['hero_title'])),
            'hero_text' => trim((string)($_POST['hero_text'] ?? $currentThemeSettings['hero_text'])),
            'cta_label' => trim((string)($_POST['cta_label'] ?? $currentThemeSettings['cta_label'])),
            'layout_preset' => trim((string)($_POST['layout_preset'] ?? $currentThemeSettings['layout_preset'])),
            'page_width' => trim((string)($_POST['page_width'] ?? $currentThemeSettings['page_width'] ?? 'wide')),
            'content_width' => trim((string)($_POST['content_width'] ?? $currentThemeSettings['content_width'] ?? 'normal')),
            'column_ratio' => trim((string)($_POST['column_ratio'] ?? $currentThemeSettings['column_ratio'] ?? '75-25')),
            'two_sidebar_ratio' => trim((string)($_POST['two_sidebar_ratio'] ?? $currentThemeSettings['two_sidebar_ratio'] ?? '3-6-3')),
            'section_spacing' => trim((string)($_POST['section_spacing'] ?? $currentThemeSettings['section_spacing'] ?? 'normal')),
            'card_style' => trim((string)($_POST['card_style'] ?? $currentThemeSettings['card_style'] ?? 'elevated')),
            'card_radius' => trim((string)($_POST['card_radius'] ?? $currentThemeSettings['card_radius'] ?? '')),
            'card_background' => nx_theme_builder_sanitize_card_background((string)($_POST['card_background'] ?? $currentThemeSettings['card_background'] ?? '#ffffff'), '#ffffff'),
            'card_border_width' => trim((string)($_POST['card_border_width'] ?? $currentThemeSettings['card_border_width'] ?? '1')),
            'card_border_color' => nx_theme_builder_sanitize_hex((string)($_POST['card_border_color'] ?? $currentThemeSettings['card_border_color'] ?? '#d7dee8'), '#d7dee8'),
            'button_radius' => trim((string)($_POST['button_radius'] ?? $currentThemeSettings['button_radius'] ?? '999px')),
            'input_radius' => trim((string)($_POST['input_radius'] ?? $currentThemeSettings['input_radius'] ?? '.75rem')),
            'heading_weight' => trim((string)($_POST['heading_weight'] ?? $currentThemeSettings['heading_weight'] ?? '700')),
            'hero_radius' => trim((string)($_POST['hero_radius'] ?? $currentThemeSettings['hero_radius'] ?? '1.75rem')),
            'hero_style' => trim((string)($_POST['hero_style'] ?? $currentThemeSettings['hero_style'] ?? 'standard')),
            'nav_style' => trim((string)($_POST['nav_style'] ?? $currentThemeSettings['nav_style'] ?? 'solid')),
            'nav_variant' => trim((string)($_POST['nav_variant'] ?? $currentThemeSettings['nav_variant'] ?? 'light')),
            'nav_link_weight' => trim((string)($_POST['nav_link_weight'] ?? $currentThemeSettings['nav_link_weight'] ?? '500')),
            'nav_font_size' => trim((string)($_POST['nav_font_size'] ?? $currentThemeSettings['nav_font_size'] ?? '1rem')),
            'nav_text_transform' => trim((string)($_POST['nav_text_transform'] ?? $currentThemeSettings['nav_text_transform'] ?? 'none')),
            'nav_border_width' => trim((string)($_POST['nav_border_width'] ?? $currentThemeSettings['nav_border_width'] ?? '1')),
            'pagination_radius' => trim((string)($_POST['pagination_radius'] ?? $currentThemeSettings['pagination_radius'] ?? '999px')),
            'pagination_border_width' => trim((string)($_POST['pagination_border_width'] ?? $currentThemeSettings['pagination_border_width'] ?? '1')),
            'pagination_gap' => trim((string)($_POST['pagination_gap'] ?? $currentThemeSettings['pagination_gap'] ?? '.25rem')),
            'pagination_font_weight' => trim((string)($_POST['pagination_font_weight'] ?? $currentThemeSettings['pagination_font_weight'] ?? '600')),
            'pagination_color' => nx_theme_builder_sanitize_hex((string)($_POST['pagination_color'] ?? $currentThemeSettings['pagination_color'] ?? '#1f6feb'), '#1f6feb'),
            'pagination_background' => nx_theme_builder_sanitize_card_background((string)($_POST['pagination_background'] ?? $currentThemeSettings['pagination_background'] ?? '#ffffff'), '#ffffff'),
            'pagination_border_color' => nx_theme_builder_sanitize_hex((string)($_POST['pagination_border_color'] ?? $currentThemeSettings['pagination_border_color'] ?? '#d7dee8'), '#d7dee8'),
            'pagination_hover_color' => nx_theme_builder_sanitize_hex((string)($_POST['pagination_hover_color'] ?? $currentThemeSettings['pagination_hover_color'] ?? '#ffffff'), '#ffffff'),
            'pagination_hover_background' => nx_theme_builder_sanitize_card_background((string)($_POST['pagination_hover_background'] ?? $currentThemeSettings['pagination_hover_background'] ?? '#1f6feb'), '#1f6feb'),
            'pagination_hover_border_color' => nx_theme_builder_sanitize_hex((string)($_POST['pagination_hover_border_color'] ?? $currentThemeSettings['pagination_hover_border_color'] ?? '#1f6feb'), '#1f6feb'),
            'pagination_active_color' => nx_theme_builder_sanitize_hex((string)($_POST['pagination_active_color'] ?? $currentThemeSettings['pagination_active_color'] ?? '#ffffff'), '#ffffff'),
            'pagination_active_background' => nx_theme_builder_sanitize_card_background((string)($_POST['pagination_active_background'] ?? $currentThemeSettings['pagination_active_background'] ?? '#1f6feb'), '#1f6feb'),
            'pagination_active_border_color' => nx_theme_builder_sanitize_hex((string)($_POST['pagination_active_border_color'] ?? $currentThemeSettings['pagination_active_border_color'] ?? '#1f6feb'), '#1f6feb'),
            'color_preset_key' => trim((string)($_POST['color_preset_key'] ?? $currentThemeSettings['color_preset_key'] ?? '')),
            'nav_width' => trim((string)($_POST['nav_width'] ?? $currentThemeSettings['nav_width'] ?? 'full')),
            'nav_radius' => trim((string)($_POST['nav_radius'] ?? $currentThemeSettings['nav_radius'] ?? '0')),
            'nav_top_spacing' => trim((string)($_POST['nav_top_spacing'] ?? $currentThemeSettings['nav_top_spacing'] ?? '0')),
            'nav_background' => nx_theme_builder_sanitize_hex((string)($_POST['nav_background'] ?? $currentThemeSettings['nav_background'] ?? '#ffffff'), '#ffffff'),
            'nav_link' => nx_theme_builder_sanitize_hex((string)($_POST['nav_link'] ?? $currentThemeSettings['nav_link'] ?? '#212529'), '#212529'),
            'nav_hover' => nx_theme_builder_sanitize_hex((string)($_POST['nav_hover'] ?? $currentThemeSettings['nav_hover'] ?? '#1f6feb'), '#1f6feb'),
            'nav_active' => nx_theme_builder_sanitize_hex((string)($_POST['nav_active'] ?? $currentThemeSettings['nav_active'] ?? '#1f6feb'), '#1f6feb'),
            'nav_dropdown_background' => nx_theme_builder_sanitize_hex((string)($_POST['nav_dropdown_background'] ?? $currentThemeSettings['nav_dropdown_background'] ?? '#ffffff'), '#ffffff'),
            'show_hero' => !empty($_POST['show_hero']),
            'colors' => [
                'accent' => nx_theme_builder_sanitize_hex((string)($_POST['accent'] ?? $currentThemeSettings['colors']['accent']), '#1f6feb'),
                'page_top' => nx_theme_builder_sanitize_hex((string)($_POST['page_top'] ?? $currentThemeSettings['colors']['page_top']), '#1a2230'),
                'page_bg' => nx_theme_builder_sanitize_hex((string)($_POST['page_bg'] ?? $currentThemeSettings['colors']['page_bg']), '#11151b'),
                'surface' => nx_theme_builder_sanitize_hex((string)($_POST['surface'] ?? $currentThemeSettings['colors']['surface']), '#ffffff'),
                'text' => nx_theme_builder_sanitize_hex((string)($_POST['text'] ?? $currentThemeSettings['colors']['text']), '#0f172a'),
            ],
        ];
        $themeManager->saveOptions($activeThemeSlug, [
            'builder_runtime' => json_encode($savedThemeSettings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
        $headlineStyle = trim((string)($_POST['headline_style'] ?? $headlineStyleSelected));
        if (isset($headlineStyles[$headlineStyle])) {
            safe_query("UPDATE settings_headstyle_config SET selected_style = '" . escape($headlineStyle) . "' WHERE id = 1 LIMIT 1");
            $headlineStyleSelected = $headlineStyle;
        }
        $designerThemeStatus = 'Theme-Einstellungen gespeichert.';
    } else {
        $designerThemeStatus = 'Speichern fehlgeschlagen: CSRF ungueltig.';
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['reset_theme_settings'])) {
    $postedCsrf = (string)($_POST['csrf_token'] ?? '');
    if ($csrf !== '' && hash_equals($csrf, $postedCsrf)) {
        $themeManager->saveOptions($activeThemeSlug, [
            'builder_runtime' => '',
        ]);
        $designerThemeStatus = 'Theme-Einstellungen auf Standard zurueckgesetzt.';
    } else {
        $designerThemeStatus = 'Zuruecksetzen fehlgeschlagen: CSRF ungueltig.';
    }
}

$runtimeSettings = nx_theme_builder_runtime_settings($themeManager, $activeThemeSlug);
$previewVersion = substr(md5(json_encode($runtimeSettings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)), 0, 10);
$liveBuilderUrl = $liveBuilderUrlBase . '&nxv=' . rawurlencode($previewVersion);

if (!function_exists('nxd_normalize_allowed_zones')) {
    function nxd_normalize_allowed_zones(?array $zones): string
    {
        $all = nx_get_active_theme_zone_keys(false);
        if (empty($zones)) {
            return '';
        }
        $in = array_map('trim', $zones);
        $in = array_values(array_unique(array_filter($in, static fn($z) => in_array($z, $all, true))));
        $ordered = [];
        foreach ($all as $z) {
            if (in_array($z, $in, true)) {
                $ordered[] = $z;
            }
        }
        return implode(',', $ordered);
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_widget_zones'])) {
    $postedCsrf = (string)($_POST['csrf_token'] ?? '');
    if ($csrf !== '' && hash_equals($csrf, $postedCsrf)) {
        $widgetKey = trim((string)($_POST['widget_key'] ?? ''));
        if ($widgetKey !== '') {
            $allowed = nxd_normalize_allowed_zones($_POST['allowed_zones'] ?? null);
            safe_query("UPDATE settings_widgets SET allowed_zones = '" . escape($allowed) . "' WHERE widget_key = '" . escape($widgetKey) . "' LIMIT 1");
        }
    }
}

$designerZones = nx_get_active_theme_zone_keys(false);
$designerWidgets = [];
$widgetResult = safe_query("SELECT widget_key, title, plugin, modulname, allowed_zones FROM settings_widgets ORDER BY widget_key ASC");
if ($widgetResult && mysqli_num_rows($widgetResult) > 0) {
    while ($widgetRow = mysqli_fetch_assoc($widgetResult)) {
        $designerWidgets[] = $widgetRow;
    }
}

?>
<style>
  @import url('/components/css/headstyles.css');

  .nx-designer-layout {
    display: grid;
    grid-template-columns: 400px minmax(0, 1fr);
    gap: 1rem;
    align-items: start;
  }
  .nx-designer-sidebar {
    position: sticky;
    top: 1rem;
    min-width: 0;
  }
  .nx-designer-panel-toggle {
    display: flex;
    gap: .5rem;
    margin-bottom: .75rem;
  }
  .nx-settings-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding-bottom: .85rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--bs-border-color, #dee2e6);
  }
  .nx-settings-toolbar-icons {
    display: inline-flex;
    gap: .5rem;
  }
  .nx-settings-toolbar-btn {
    width: 2.5rem;
    height: 2.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: .85rem;
    background: #fff;
    color: #1f2937;
    font-size: 1.05rem;
    cursor: pointer;
    transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
  }
  .nx-settings-toolbar-btn:hover {
    border-color: rgba(31, 111, 235, .35);
    box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
    transform: translateY(-1px);
  }
  .nx-settings-toolbar-btn.is-active {
    border-color: rgba(31, 111, 235, .55);
    color: #1f6feb;
    box-shadow: 0 10px 22px rgba(31, 111, 235, .12);
  }
  .nx-settings-menu {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .75rem;
    margin-bottom: 1rem;
  }
  .nx-settings-card {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 1rem;
    background: #fff;
    padding: 1rem .9rem;
    text-align: left;
    transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
  }
  .nx-settings-card:hover {
    border-color: rgba(31, 111, 235, .35);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
    transform: translateY(-1px);
  }
  .nx-settings-card.is-active {
    border-color: rgba(31, 111, 235, .55);
    box-shadow: 0 14px 28px rgba(31, 111, 235, .12);
  }
  .nx-settings-card-icon {
    width: 2.75rem;
    height: 2.75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: .9rem;
    background: rgba(31, 111, 235, .08);
    color: #1f6feb;
    font-size: 1.15rem;
    margin-bottom: .75rem;
  }
  .nx-settings-card-title {
    display: block;
    font-weight: 600;
    margin-bottom: .2rem;
    color: #1f2937;
  }
  .nx-settings-card-copy {
    display: block;
    font-size: .82rem;
    color: #6b7280;
    line-height: 1.45;
  }
  .nx-headstyle-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 1rem;
    min-width: 0;
  }
  .nx-headstyle-card {
    display: block;
    border: 1px solid transparent;
    border-radius: 1rem;
    background: transparent;
    cursor: pointer;
    padding: 0;
    transition: border-color .15s ease, background-color .15s ease, transform .15s ease;
    overflow: hidden;
    min-width: 0;
  }
  .nx-headstyle-card:hover {
    border-color: rgba(31, 111, 235, .2);
    background: rgba(31, 111, 235, .025);
    transform: translateY(-1px);
  }
  .nx-headstyle-card.is-active {
    border-color: rgba(31, 111, 235, .5);
    background: rgba(31, 111, 235, .04);
  }
  .nx-headstyle-preview {
    background: transparent;
    padding: 0;
    border-radius: 0;
    min-width: 0;
    overflow: hidden;
  }
  .nx-headstyle-frame {
    width: 100%;
    height: 154px;
    display: block;
    border: 0;
    background: transparent;
    overflow: hidden;
    pointer-events: none;
  }
  .nx-settings-section.is-hidden {
    display: none;
  }
  .nx-designer-panel.is-hidden {
    display: none;
  }
  .nx-designer-layout.is-widget-mode {
    display: block;
    max-width: none;
  }
  .nx-designer-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    justify-content: flex-end;
  }
  .nx-designer-iframe {
    width: 100%;
    min-height: 78vh;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 1rem;
    background: #fff;
  }
  .nx-designer-preview-shell {
    width: 100%;
    overflow: auto;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 1rem;
    background: #eef2f7;
    padding: 1rem;
  }
  .nx-designer-preview-stage {
    width: 1440px;
    transform-origin: top left;
    transition: transform .15s ease;
  }
  .nx-designer-preview-stage .nx-designer-iframe {
    width: 1440px;
    min-height: 980px;
    display: block;
    background: #fff;
  }
  .nx-designer-preview-shell.is-builder-mode {
    padding: 0;
    background: #fff;
  }
  .nx-designer-preview-shell.is-builder-mode .nx-designer-preview-stage {
    width: 100%;
    transform: none !important;
  }
  .nx-designer-preview-shell.is-builder-mode .nx-designer-iframe {
    width: 100%;
    min-height: 78vh;
  }
  .nx-designer-preview-pane.is-hidden {
    display: none;
  }
  .nx-preview-mode-toggle .btn.is-active {
    background: var(--bs-primary, #0d6efd);
    border-color: var(--bs-primary, #0d6efd);
    color: #fff;
  }
  #nx-widget-zones-panel {
    grid-column: 1 / -1;
  }
  .nx-widget-zone-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
  }
  .nx-widget-zone-item {
    height: 100%;
  }
  .nx-designer-sidebar .form-label {
    font-size: .82rem;
    font-weight: 600;
    margin-bottom: .2rem;
  }
  .nx-designer-sidebar .form-text {
    font-size: .78rem;
  }
  .nx-color-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .75rem;
  }
  .nx-color-preset-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .75rem;
  }
  .nx-color-preset {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: .9rem;
    background: #fff;
    padding: .85rem;
    text-align: left;
    transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
  }
  .nx-color-preset:hover {
    border-color: rgba(31, 111, 235, .35);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    transform: translateY(-1px);
  }
  .nx-color-preset.is-active {
    border-color: rgba(31, 111, 235, .6);
    background: linear-gradient(180deg, rgba(31, 111, 235, .06) 0%, #ffffff 100%);
    box-shadow: 0 12px 28px rgba(31, 111, 235, .14);
  }
  .nx-color-preset-name {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .35rem;
    font-weight: 600;
  }
  .nx-color-preset-swatches {
    display: inline-flex;
    gap: .35rem;
  }
  .nx-color-preset-swatches span {
    width: .9rem;
    height: .9rem;
    border-radius: 999px;
    border: 1px solid rgba(15, 23, 42, 0.12);
  }
  .nx-contrast-warning.is-hidden {
    display: none;
  }
  .nx-color-card {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: .9rem;
    padding: .75rem;
    background: #fff;
  }
  .nx-nav-variant-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .75rem;
  }
  .nx-nav-variant-card {
    display: block;
    width: 100%;
    border: var(--nx-nav-preview-border-width, 1px) solid var(--bs-border-color, #dee2e6);
    border-radius: var(--nx-nav-preview-card-radius, 1rem);
    background: #fff;
    padding: .85rem;
    cursor: pointer;
    text-align: left;
    transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
  }
  .nx-nav-variant-card:hover {
    border-color: rgba(31, 111, 235, .35);
    box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
    transform: translateY(-1px);
  }
  .nx-nav-variant-card.is-active {
    border-color: rgba(31, 111, 235, .6);
    background: linear-gradient(180deg, rgba(31, 111, 235, .06) 0%, #ffffff 100%);
    box-shadow: 0 12px 28px rgba(31, 111, 235, .14);
  }
  .nx-nav-variant-preview {
    border: 1px solid rgba(15, 23, 42, .08);
    border-radius: calc(var(--nx-nav-preview-card-radius, 1rem) - .1rem);
    background: #f8fafc;
    padding: .65rem;
    margin-bottom: .7rem;
  }
  .nx-nav-variant-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    padding: .45rem .55rem;
    background: #ffffff;
    border: var(--nx-nav-preview-border-width, 1px) solid rgba(15, 23, 42, .1);
    border-radius: var(--nx-nav-preview-bar-radius, .8rem);
  }
  .nx-nav-variant-brand {
    width: 2.25rem;
    height: .55rem;
    border-radius: 999px;
    background: #1f2937;
    flex: 0 0 auto;
  }
  .nx-nav-variant-links {
    display: flex;
    align-items: center;
    gap: .35rem;
    justify-content: flex-end;
    flex: 1 1 auto;
  }
  .nx-nav-variant-links span {
    display: inline-block;
    height: .45rem;
    background: rgba(15, 23, 42, .22);
  }
  .nx-nav-variant-links span:nth-child(1) { width: 1.2rem; }
  .nx-nav-variant-links span:nth-child(2) { width: 1.55rem; }
  .nx-nav-variant-links span:nth-child(3) { width: 1rem; }
  .nx-nav-variant-card[data-variant="dark"] .nx-nav-variant-bar {
    background: #111827;
    border-color: #374151;
  }
  .nx-nav-variant-card[data-variant="dark"] .nx-nav-variant-brand,
  .nx-nav-variant-card[data-variant="dark"] .nx-nav-variant-links span {
    background: #60a5fa;
  }
  .nx-nav-variant-card[data-variant="light"] .nx-nav-variant-bar {
    background: #fffdf9;
  }
  .nx-nav-variant-card[data-variant="light"] .nx-nav-variant-links span {
    border-bottom: 2px solid #1f2937;
    background: transparent;
    height: .5rem;
  }
  .nx-nav-variant-card[data-variant="soft"] .nx-nav-variant-links span {
    border-radius: 999px;
    background: rgba(52, 89, 230, .2);
  }
  .nx-nav-variant-title {
    display: block;
    font-weight: 600;
    margin-bottom: .15rem;
    color: #1f2937;
  }
  .nx-nav-variant-copy {
    display: block;
    font-size: .8rem;
    color: #6b7280;
    line-height: 1.4;
  }
  .nx-color-card.full {
    grid-column: 1 / -1;
  }
  .nx-card-settings-group {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 1rem;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    padding: 1rem;
  }
  .nx-card-settings-title {
    display: flex;
    align-items: center;
    gap: .55rem;
    font-weight: 700;
    margin-bottom: .9rem;
    color: #1f2937;
  }
  .nx-card-settings-title i {
    color: #1f6feb;
  }
  .nx-color-header {
    display: flex;
    align-items: center;
    gap: .6rem;
    margin-bottom: .6rem;
  }
  .nx-color-swatch {
    width: 1.1rem;
    height: 1.1rem;
    border-radius: 999px;
    border: 1px solid rgba(15, 23, 42, 0.12);
    background: #fff;
    flex: 0 0 auto;
  }
  .nx-color-controls {
    display: grid;
    grid-template-columns: 3.2rem minmax(0, 1fr);
    gap: .6rem;
  }
  .nx-color-picker {
    width: 3.2rem;
    height: 2.5rem;
    padding: .15rem;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: .75rem;
    background: #fff;
  }
  .nx-color-text {
    font-family: var(--bs-font-monospace, monospace);
  }
  @media (max-width: 1199.98px) {
    .nx-designer-layout {
      grid-template-columns: 1fr;
    }
    .nx-designer-sidebar {
      position: static;
    }
    .nx-widget-zone-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }
  @media (max-width: 767.98px) {
    .nx-widget-zone-grid {
      grid-template-columns: 1fr;
    }
    .nx-color-grid {
      grid-template-columns: 1fr;
    }
    .nx-color-preset-grid {
      grid-template-columns: 1fr;
    }
    .nx-settings-menu {
      grid-template-columns: 1fr;
    }
    .nx-headstyle-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="card shadow-sm border-0 mb-4 mt-4">
  <div class="card-header">
    <div class="card-title">
      <i class="bi bi-columns-gap"></i>
      <span>Theme Designer</span>
      <small class="small-muted">Layout, Farben und Widgets auf einer Seite</small>
    </div>
  </div>
  <div class="card-body">
    <div class="alert alert-info">
      Aktives Theme: <strong><?= htmlspecialchars((string)($activeTheme['name'] ?? $activeThemeSlug), ENT_QUOTES, 'UTF-8') ?></strong>
      <br>Slug: <code><?= htmlspecialchars($activeThemeSlug, ENT_QUOTES, 'UTF-8') ?></code>
      <br>Theme-Einstellungen arbeiten mit Vorschau. Widget-Zonen bearbeitest du separat ohne Live-Bereich.
    </div>

    <form method="get" class="row g-3 align-items-end mb-4">
      <input type="hidden" name="site" value="theme_designer">
      <div class="col-md-4">
        <label class="form-label">Seite</label>
        <select name="target_page" id="nx-designer-page-select" class="form-select">
          <?php foreach ($pages as $pageKey => $pageLabel): ?>
            <option value="<?= htmlspecialchars($pageKey, ENT_QUOTES, 'UTF-8') ?>"<?= $pageKey === $selectedPage ? ' selected' : '' ?>>
              <?= htmlspecialchars($pageLabel, ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-8">
        <div class="nx-designer-actions">
        <button type="submit" class="btn btn-primary">Vorschau laden</button>
        <a href="admincenter.php?site=theme" class="btn btn-outline-secondary">Theme-Verwaltung</a>
        <a href="admincenter.php?site=theme_installer" class="btn btn-outline-secondary">Theme-Dateien</a>
        </div>
      </div>
    </form>

    <div class="nx-designer-layout">
      <div class="nx-designer-sidebar">
        <div class="nx-designer-panel-toggle">
          <button id="nx-panel-theme" class="btn btn-sm btn-primary" type="button">Theme-Einstellungen</button>
          <button id="nx-panel-widgets" class="btn btn-sm btn-outline-secondary" type="button">Widget-Zonen</button>
        </div>

        <div id="nx-theme-settings-panel" class="card border-0 shadow-sm nx-designer-panel">
          <div class="card-header">
            <strong>Theme-Einstellungen</strong>
          </div>
          <div class="card-body">
            <form id="nx-designer-theme-form" method="post" action="admincenter.php?site=theme_designer&amp;target_page=<?= urlencode($selectedPage) ?>" class="d-grid gap-3">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
              <div class="nx-settings-toolbar">
                <div class="nx-settings-toolbar-icons">
                  <button class="nx-settings-toolbar-btn is-active" type="button" data-settings-nav="general" title="Layout">
                    <i class="bi bi-grid-3x3-gap"></i>
                  </button>
                  <button class="nx-settings-toolbar-btn" type="button" data-settings-nav="colors" title="Farben">
                    <i class="bi bi-sliders"></i>
                  </button>
                </div>
                <small class="text-muted">Theme-Menue</small>
              </div>

              <div class="nx-settings-menu">
                <button class="nx-settings-card is-active" type="button" data-settings-nav="general">
                  <span class="nx-settings-card-icon"><i class="bi bi-layout-text-window-reverse"></i></span>
                  <span class="nx-settings-card-title">Layout</span>
                  <span class="nx-settings-card-copy">Grundstruktur und Seitenaufbau des Themes.</span>
                </button>
                <button class="nx-settings-card" type="button" data-settings-nav="hero">
                  <span class="nx-settings-card-icon"><i class="bi bi-type"></i></span>
                  <span class="nx-settings-card-title">Hero</span>
                  <span class="nx-settings-card-copy">Titel, Einleitung und CTA fuer den Startbereich.</span>
                </button>
                <button class="nx-settings-card" type="button" data-settings-nav="colors">
                  <span class="nx-settings-card-icon"><i class="bi bi-palette"></i></span>
                  <span class="nx-settings-card-title">Farben</span>
                  <span class="nx-settings-card-copy">Farbsets, Einzelfarben und Kontrastkontrolle.</span>
                </button>
                <button class="nx-settings-card" type="button" data-settings-nav="headline">
                  <span class="nx-settings-card-icon"><i class="bi bi-type-h1"></i></span>
                  <span class="nx-settings-card-title">Ueberschriften</span>
                  <span class="nx-settings-card-copy">Headstyle-Auswahl fuer Module und Widget-Titel.</span>
                </button>
                <button class="nx-settings-card" type="button" data-settings-nav="navigation">
                  <span class="nx-settings-card-icon"><i class="bi bi-menu-button-wide"></i></span>
                  <span class="nx-settings-card-title">Navigation</span>
                  <span class="nx-settings-card-copy">Look und Farben der Hauptnavigation.</span>
                </button>
              </div>

              <div class="nx-settings-section" data-settings-section="general">
                <div class="d-grid gap-3">
                  <input type="hidden" name="layout_preset" value="right-sidebar">
                  <div>
                    <label class="form-label">Seitenbreite</label>
                    <select class="form-select" name="page_width">
                      <option value="boxed">Boxed</option>
                      <option value="wide">Wide</option>
                      <option value="full">Full Width</option>
                    </select>
                  </div>

                  <div>
                    <label class="form-label">Content-Breite</label>
                    <select class="form-select" name="content_width">
                      <option value="narrow">Schmal</option>
                      <option value="normal">Normal</option>
                      <option value="wide">Breit</option>
                    </select>
                  </div>

                  <div>
                    <label class="form-label">Spaltenverhaeltnis</label>
                    <select class="form-select" name="column_ratio">
                      <option value="70-30">70 / 30</option>
                      <option value="75-25">75 / 25</option>
                      <option value="80-20">80 / 20</option>
                    </select>
                  </div>

                  <div>
                    <label class="form-label">2-Sidebar-Aufteilung</label>
                    <select class="form-select" name="two_sidebar_ratio">
                      <option value="3-6-3">3 / 6 / 3</option>
                      <option value="4-4-4">4 / 4 / 4</option>
                      <option value="3-5-4">3 / 5 / 4</option>
                      <option value="4-5-3">4 / 5 / 3</option>
                    </select>
                    <div class="form-text">Greift nur, wenn links und rechts eine Sidebar aktiv sind.</div>
                  </div>

                  <div>
                    <label class="form-label">Abstaende</label>
                    <select class="form-select" name="section_spacing">
                      <option value="compact">Kompakt</option>
                      <option value="normal">Normal</option>
                      <option value="relaxed">Grosszuegig</option>
                    </select>
                  </div>

                  <div>
                    <label class="form-label">Hero-Stil</label>
                    <select class="form-select" name="hero_style">
                      <option value="compact">Kompakt</option>
                      <option value="standard">Standard</option>
                      <option value="immersive">Immersive</option>
                    </select>
                  </div>
                </div>

                <div class="form-text mt-2">
                  Hier steuerst du Breite, Verhaeltnisse, Abstaende und die Grundoptik des Themes.
                </div>
              </div>

              <div class="nx-settings-section is-hidden" data-settings-section="hero">
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" value="1" id="nx-designer-show-hero" name="show_hero">
                  <label class="form-check-label" for="nx-designer-show-hero">Hero anzeigen</label>
                </div>

                <div class="mb-3">
                  <label class="form-label">Hero-Titel</label>
                  <input class="form-control" type="text" name="hero_title">
                </div>

                <div class="mb-3">
                  <label class="form-label">Hero-Text</label>
                  <textarea class="form-control" rows="4" name="hero_text"></textarea>
                </div>

                <div>
                  <label class="form-label">CTA-Text</label>
                  <input class="form-control" type="text" name="cta_label">
                </div>
              </div>

              <div class="nx-settings-section is-hidden" data-settings-section="colors">
                <div class="mb-3">
                  <label class="form-label">Farbsets</label>
                  <div class="nx-color-preset-grid">
                    <?php foreach ($themeColorPresets as $presetKey => $preset): ?>
                      <button
                        class="nx-color-preset"
                        type="button"
                        data-preset-key="<?= htmlspecialchars($presetKey, ENT_QUOTES, 'UTF-8') ?>"
                        data-color-preset='<?= htmlspecialchars(json_encode($preset['colors'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'
                        data-card-background="<?= htmlspecialchars((string)($preset['card_background'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-card-border-color="<?= htmlspecialchars((string)($preset['card_border_color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-card-style="<?= htmlspecialchars((string)($preset['card_style'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-card-radius="<?= htmlspecialchars((string)($preset['card_radius'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-card-border-width="<?= htmlspecialchars((string)($preset['card_border_width'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-button-radius="<?= htmlspecialchars((string)($preset['button_radius'] ?? '999px'), ENT_QUOTES, 'UTF-8') ?>"
                        data-input-radius="<?= htmlspecialchars((string)($preset['input_radius'] ?? '.75rem'), ENT_QUOTES, 'UTF-8') ?>"
                        data-heading-weight="<?= htmlspecialchars((string)($preset['heading_weight'] ?? '700'), ENT_QUOTES, 'UTF-8') ?>"
                        data-hero-radius="<?= htmlspecialchars((string)($preset['hero_radius'] ?? '1.75rem'), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-style="<?= htmlspecialchars((string)($preset['nav_style'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-variant="<?= htmlspecialchars((string)($preset['nav_variant'] ?? 'light'), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-link-weight="<?= htmlspecialchars((string)($preset['nav_link_weight'] ?? '500'), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-font-size="<?= htmlspecialchars((string)($preset['nav_font_size'] ?? '1rem'), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-text-transform="<?= htmlspecialchars((string)($preset['nav_text_transform'] ?? 'none'), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-border-width="<?= htmlspecialchars((string)($preset['nav_border_width'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-radius="<?= htmlspecialchars((string)($preset['nav_radius'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-background="<?= htmlspecialchars((string)($preset['nav_background'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-link="<?= htmlspecialchars((string)($preset['nav_link'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-hover="<?= htmlspecialchars((string)($preset['nav_hover'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-active="<?= htmlspecialchars((string)($preset['nav_active'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-nav-dropdown-background="<?= htmlspecialchars((string)($preset['nav_dropdown_background'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-radius="<?= htmlspecialchars((string)($preset['pagination_radius'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-border-width="<?= htmlspecialchars((string)($preset['pagination_border_width'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-gap="<?= htmlspecialchars((string)($preset['pagination_gap'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-font-weight="<?= htmlspecialchars((string)($preset['pagination_font_weight'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-color="<?= htmlspecialchars((string)($preset['pagination_color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-background="<?= htmlspecialchars((string)($preset['pagination_background'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-border-color="<?= htmlspecialchars((string)($preset['pagination_border_color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-hover-color="<?= htmlspecialchars((string)($preset['pagination_hover_color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-hover-background="<?= htmlspecialchars((string)($preset['pagination_hover_background'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-hover-border-color="<?= htmlspecialchars((string)($preset['pagination_hover_border_color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-active-color="<?= htmlspecialchars((string)($preset['pagination_active_color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-active-background="<?= htmlspecialchars((string)($preset['pagination_active_background'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-pagination-active-border-color="<?= htmlspecialchars((string)($preset['pagination_active_border_color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <span class="nx-color-preset-name">
                          <span><?= htmlspecialchars($preset['label'], ENT_QUOTES, 'UTF-8') ?></span>
                          <span class="nx-color-preset-swatches">
                            <?php foreach ($preset['colors'] as $presetColor): ?>
                              <span style="background: <?= htmlspecialchars($presetColor, ENT_QUOTES, 'UTF-8') ?>"></span>
                            <?php endforeach; ?>
                          </span>
                        </span>
                        <span class="small text-muted d-block"><?= htmlspecialchars($preset['description'], ENT_QUOTES, 'UTF-8') ?></span>
                      </button>
                    <?php endforeach; ?>
                  </div>
                </div>

                <div>
                  <label class="form-label">Farben</label>
                  <div class="nx-color-grid">
                    <div class="nx-color-card">
                      <div class="nx-color-header">
                        <span class="nx-color-swatch" data-swatch-for="accent"></span>
                        <span class="fw-semibold small">Akzent</span>
                      </div>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="accent" value="#1f6feb">
                        <input class="form-control nx-color-text" type="text" name="accent" placeholder="#1f6feb">
                      </div>
                    </div>
                    <div class="nx-color-card">
                      <div class="nx-color-header">
                        <span class="nx-color-swatch" data-swatch-for="text"></span>
                        <span class="fw-semibold small">Text</span>
                      </div>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="text" value="#0f172a">
                        <input class="form-control nx-color-text" type="text" name="text" placeholder="#0f172a">
                      </div>
                    </div>
                    <div class="nx-color-card">
                      <div class="nx-color-header">
                        <span class="nx-color-swatch" data-swatch-for="page_top"></span>
                        <span class="fw-semibold small">Top</span>
                      </div>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="page_top" value="#1a2230">
                        <input class="form-control nx-color-text" type="text" name="page_top" placeholder="#1a2230">
                      </div>
                    </div>
                    <div class="nx-color-card">
                      <div class="nx-color-header">
                        <span class="nx-color-swatch" data-swatch-for="page_bg"></span>
                        <span class="fw-semibold small">Seite</span>
                      </div>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="page_bg" value="#11151b">
                        <input class="form-control nx-color-text" type="text" name="page_bg" placeholder="#11151b">
                      </div>
                    </div>
                    <div class="nx-color-card full">
                      <div class="nx-color-header">
                        <span class="nx-color-swatch" data-swatch-for="surface"></span>
                        <span class="fw-semibold small">Surface</span>
                      </div>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="surface" value="#ffffff">
                        <input class="form-control nx-color-text" type="text" name="surface" placeholder="#ffffff">
                      </div>
                    </div>
                  </div>
                </div>

                <div>
                  <label class="form-label">Card-Einstellungen</label>
                  <div class="nx-color-grid">
                    <div class="nx-color-card">
                      <div class="nx-color-header">
                        <span class="nx-color-swatch" data-swatch-for="card_background"></span>
                        <span class="fw-semibold small">Card Background</span>
                      </div>
                      <select class="form-select mb-2" name="card_background_mode" id="nx-card-background-mode">
                        <option value="color">Farbe</option>
                        <option value="transparent">Transparent</option>
                      </select>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="card_background" value="#ffffff">
                        <input class="form-control nx-color-text" type="text" name="card_background" placeholder="#ffffff">
                      </div>
                    </div>

                    <div class="nx-color-card">
                      <div class="nx-color-header">
                        <span class="nx-color-swatch" data-swatch-for="card_border_color"></span>
                        <span class="fw-semibold small">Border-Farbe</span>
                      </div>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="card_border_color" value="#d7dee8">
                        <input class="form-control nx-color-text" type="text" name="card_border_color" placeholder="#d7dee8">
                      </div>
                    </div>

                    <div class="nx-color-card">
                      <input type="hidden" name="card_radius" value="<?= htmlspecialchars((string)($runtimeSettings['card_radius'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="button_radius" value="<?= htmlspecialchars((string)($runtimeSettings['button_radius'] ?? '999px'), ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="input_radius" value="<?= htmlspecialchars((string)($runtimeSettings['input_radius'] ?? '.75rem'), ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="heading_weight" value="<?= htmlspecialchars((string)($runtimeSettings['heading_weight'] ?? '700'), ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="hero_radius" value="<?= htmlspecialchars((string)($runtimeSettings['hero_radius'] ?? '1.75rem'), ENT_QUOTES, 'UTF-8') ?>">
                      <label class="form-label">Border-Breite</label>
                      <select class="form-select" name="card_border_width">
                        <option value="0">0 px</option>
                        <option value="1">1 px</option>
                        <option value="2">2 px</option>
                        <option value="3">3 px</option>
                      </select>
                    </div>

                    <div class="nx-color-card">
                      <label class="form-label">Kartenstil</label>
                      <select class="form-select" name="card_style">
                        <option value="flat">Flach</option>
                        <option value="outlined">Mit Rahmen</option>
                        <option value="elevated">Mit Schatten</option>
                        <option value="soft">Soft</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div id="nx-color-contrast-warning" class="alert alert-warning small mb-0 nx-contrast-warning is-hidden"></div>
                <input type="hidden" name="color_preset_key" value="<?= htmlspecialchars((string)($runtimeSettings['color_preset_key'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_radius" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_radius'] ?? '999px'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_border_width" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_border_width'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_gap" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_gap'] ?? '.25rem'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_font_weight" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_font_weight'] ?? '600'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_color" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_color'] ?? '#1f6feb'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_background" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_background'] ?? '#ffffff'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_border_color" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_border_color'] ?? '#d7dee8'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_hover_color" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_hover_color'] ?? '#ffffff'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_hover_background" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_hover_background'] ?? '#1f6feb'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_hover_border_color" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_hover_border_color'] ?? '#1f6feb'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_active_color" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_active_color'] ?? '#ffffff'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_active_background" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_active_background'] ?? '#1f6feb'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="pagination_active_border_color" value="<?= htmlspecialchars((string)($runtimeSettings['pagination_active_border_color'] ?? '#1f6feb'), ENT_QUOTES, 'UTF-8') ?>">
              </div>

              <div class="nx-settings-section is-hidden" data-settings-section="headline">
                <div>
                  <label class="form-label">Headstyle</label>
                  <div class="nx-headstyle-grid">
                    <?php foreach ($headlineStyles as $styleKey => $styleLabel): ?>
                      <?php
                        $previewAccent = (string)($runtimeSettings['colors']['accent'] ?? '#fe821d');
                        $previewText = '#212529';
                        $previewHtml = '<!DOCTYPE html><html><head><meta charset="utf-8">'
                          . '<meta name="viewport" content="width=device-width, initial-scale=1">'
                          . '<link rel="stylesheet" href="/components/bootstrap/css/bootstrap.min.css">'
                          . '<link rel="stylesheet" href="/components/css/headstyles.css">'
                          . '<style>'
                          . 'html,body{margin:0;padding:0;background:transparent;overflow:hidden;}'
                          . 'body{--primary:' . htmlspecialchars($previewAccent, ENT_QUOTES, 'UTF-8') . ';--bs-body-color:' . htmlspecialchars($previewText, ENT_QUOTES, 'UTF-8') . ';--bs-link-color:' . htmlspecialchars($previewAccent, ENT_QUOTES, 'UTF-8') . ';--nx-theme-accent:' . htmlspecialchars($previewAccent, ENT_QUOTES, 'UTF-8') . ';--nx-theme-text:' . htmlspecialchars($previewText, ENT_QUOTES, 'UTF-8') . ';font-family:Arial,sans-serif;}'
                          . '.preview-wrap{padding:0;background:transparent;overflow:hidden;width:100%;height:100vh;box-sizing:border-box;display:flex;align-items:center;justify-content:center;}'
                          . '.preview-scale{transform-origin:center center;display:inline-block;width:max-content;}'
                          . '.head-boxes{background:transparent !important;border:0 !important;box-shadow:none !important;}'
                          . '.head-boxes h2,.head-boxes .head-h2{white-space:nowrap !important;}'
                          . '.head-boxes-1 .head-boxes-head,.head-boxes-8 .head-boxes-head{display:none !important;}'
                          . '.head-boxes-1 .head-boxes-foot,.head-boxes-8 .head-boxes-foot{display:none !important;}'
                          . '.head-boxes-3 .head-boxes-head,.head-boxes-4 .head-boxes-head,.head-boxes-5 .head-boxes-head,.head-boxes-6 .head-boxes-head,.head-boxes-7 .head-boxes-head,.head-boxes-9 .head-boxes-head,.head-boxes-10 .head-boxes-head{display:none !important;}'
                          . '</style></head><body><div class="preview-wrap"><div class="preview-scale" id="preview-scale">'
                          . '<div class="head-boxes ' . htmlspecialchars($styleKey, ENT_QUOTES, 'UTF-8') . '">'
                          . '<span class="head-boxes-head">Startpage</span>'
                          . '<h2 class="head-h2"><span class="head-boxes-title">Startpage</span></h2>'
                          . '<p class="head-boxes-foot">Startpage</p>'
                          . '</div></div></div><script>'
                          . '(function(){'
                          . 'function fit(){'
                          . 'var wrap=document.querySelector(".preview-wrap");'
                          . 'var scaleNode=document.getElementById("preview-scale");'
                          . 'if(!wrap||!scaleNode){return;}'
                          . 'scaleNode.style.transform="scale(1)";'
                          . 'var padX=0;'
                          . 'var padY=0;'
                          . 'var width=Math.max(1, wrap.clientWidth-padX);'
                          . 'var height=Math.max(1, wrap.clientHeight-padY);'
                          . 'var contentWidth=Math.max(scaleNode.scrollWidth, 1);'
                          . 'var contentHeight=Math.max(scaleNode.scrollHeight, 1);'
                          . 'var scale=Math.min(width/contentWidth, height/contentHeight, 1);'
                          . 'scaleNode.style.transform="scale("+scale+")";'
                          . '}'
                          . 'window.addEventListener("load", fit);'
                          . 'window.addEventListener("resize", fit);'
                          . 'if(document.fonts&&document.fonts.ready){document.fonts.ready.then(fit);}'
                          . 'requestAnimationFrame(fit);'
                          . 'setTimeout(fit, 0);'
                          . 'setTimeout(fit, 120);'
                          . '})();'
                          . '</script></body></html>';
                      ?>
                      <label class="nx-headstyle-card<?= $headlineStyleSelected === $styleKey ? ' is-active' : '' ?>">
                        <input type="radio" class="d-none" name="headline_style" value="<?= htmlspecialchars($styleKey, ENT_QUOTES, 'UTF-8') ?>"<?= $headlineStyleSelected === $styleKey ? ' checked' : '' ?>>
                        <div class="nx-headstyle-preview">
                          <iframe
                            class="nx-headstyle-frame"
                            loading="lazy"
                            srcdoc="<?= htmlspecialchars($previewHtml, ENT_QUOTES, 'UTF-8') ?>"
                            title="<?= htmlspecialchars($styleLabel, ENT_QUOTES, 'UTF-8') ?>"></iframe>
                        </div>
                      </label>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="form-text">
                  Diese Auswahl nutzt dieselbe Headstyle-Konfiguration wie die bestehende `headstyle.php`.
                </div>
              </div>

              <div class="nx-settings-section is-hidden" data-settings-section="navigation">
                <div class="nx-card-settings-group">
                  <div class="nx-card-settings-title">
                    <i class="bi bi-menu-button-wide"></i>
                    <span>Navigation</span>
                  </div>

                  <div class="d-grid gap-3">
                    <div>
                      <label class="form-label">Navi-Variante</label>
                      <div class="nx-nav-variant-grid">
                        <button type="button" class="nx-nav-variant-card" data-variant="primary">
                          <div class="nx-nav-variant-preview">
                            <div class="nx-nav-variant-bar">
                              <span class="nx-nav-variant-brand"></span>
                              <span class="nx-nav-variant-links"><span></span><span></span><span></span></span>
                            </div>
                          </div>
                          <span class="nx-nav-variant-title">Primary</span>
                          <span class="nx-nav-variant-copy">Akzentfarbige Navbar wie im Theme-Beispiel.</span>
                        </button>
                        <button type="button" class="nx-nav-variant-card" data-variant="dark">
                          <div class="nx-nav-variant-preview">
                            <div class="nx-nav-variant-bar">
                              <span class="nx-nav-variant-brand"></span>
                              <span class="nx-nav-variant-links"><span></span><span></span><span></span></span>
                            </div>
                          </div>
                          <span class="nx-nav-variant-title">Dark</span>
                          <span class="nx-nav-variant-copy">Dunkle Variante mit hellem Kontrast.</span>
                        </button>
                        <button type="button" class="nx-nav-variant-card" data-variant="light">
                          <div class="nx-nav-variant-preview">
                            <div class="nx-nav-variant-bar">
                              <span class="nx-nav-variant-brand"></span>
                              <span class="nx-nav-variant-links"><span></span><span></span><span></span></span>
                            </div>
                          </div>
                          <span class="nx-nav-variant-title">Light</span>
                          <span class="nx-nav-variant-copy">Helle Navbar mit dezenter Unterstreichung.</span>
                        </button>
                        <button type="button" class="nx-nav-variant-card" data-variant="soft">
                          <div class="nx-nav-variant-preview">
                            <div class="nx-nav-variant-bar">
                              <span class="nx-nav-variant-brand"></span>
                              <span class="nx-nav-variant-links"><span></span><span></span><span></span></span>
                            </div>
                          </div>
                          <span class="nx-nav-variant-title">Soft</span>
                          <span class="nx-nav-variant-copy">Weiche, pillenfoermige Links.</span>
                        </button>
                      </div>
                      <input type="hidden" name="nav_variant" value="light">
                      <input type="hidden" name="nav_link_weight" value="<?= htmlspecialchars((string)($runtimeSettings['nav_link_weight'] ?? '500'), ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="nav_font_size" value="<?= htmlspecialchars((string)($runtimeSettings['nav_font_size'] ?? '1rem'), ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="nav_text_transform" value="<?= htmlspecialchars((string)($runtimeSettings['nav_text_transform'] ?? 'none'), ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="nav_border_width" value="<?= htmlspecialchars((string)($runtimeSettings['nav_border_width'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div>
                      <label class="form-label">Stil</label>
                      <select class="form-select" name="nav_style">
                        <option value="solid">Solid</option>
                        <option value="outlined">Outlined</option>
                        <option value="glass">Glass</option>
                      </select>
                    </div>

                    <div>
                      <label class="form-label">Breite</label>
                      <select class="form-select" name="nav_width">
                        <option value="full">Volle Breite</option>
                        <option value="content">Content-Breite</option>
                      </select>
                    </div>

                    <div>
                      <label class="form-label">Rundung</label>
                      <select class="form-select" name="nav_radius">
                        <option value="0">0 px</option>
                        <option value="8">8 px</option>
                        <option value="12">12 px</option>
                        <option value="18">18 px</option>
                        <option value="24">24 px</option>
                      </select>
                    </div>

                    <div>
                      <label class="form-label">Abstand zu Top</label>
                      <select class="form-select" name="nav_top_spacing">
                        <option value="0">0 px</option>
                        <option value="8">8 px</option>
                        <option value="12">12 px</option>
                        <option value="16">16 px</option>
                        <option value="24">24 px</option>
                      </select>
                    </div>

                    <div>
                      <label class="form-label">Navigation Background</label>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="nav_background" value="#ffffff">
                        <input class="form-control nx-color-text" type="text" name="nav_background" placeholder="#ffffff">
                      </div>
                    </div>

                    <div>
                      <label class="form-label">Link-Farbe</label>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="nav_link" value="#212529">
                        <input class="form-control nx-color-text" type="text" name="nav_link" placeholder="#212529">
                      </div>
                    </div>

                    <div>
                      <label class="form-label">Hover-Farbe</label>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="nav_hover" value="#1f6feb">
                        <input class="form-control nx-color-text" type="text" name="nav_hover" placeholder="#1f6feb">
                      </div>
                    </div>

                    <div>
                      <label class="form-label">Aktive Farbe</label>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="nav_active" value="#1f6feb">
                        <input class="form-control nx-color-text" type="text" name="nav_active" placeholder="#1f6feb">
                      </div>
                    </div>

                    <div>
                      <label class="form-label">Dropdown Background</label>
                      <div class="nx-color-controls">
                        <input class="nx-color-picker" type="color" data-color-target="nav_dropdown_background" value="#ffffff">
                        <input class="form-control nx-color-text" type="text" name="nav_dropdown_background" placeholder="#ffffff">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-text">
                Hier bearbeitest du die Basis-Einstellungen des Themes. Rechts siehst du wieder die Vorschau.
              </div>

              <div class="d-flex flex-wrap gap-2">
                <button id="nx-designer-theme-save" name="save_theme_settings" class="btn btn-dark" type="submit">Theme speichern</button>
                <button name="reset_theme_settings" class="btn btn-outline-secondary" type="submit">Reset auf Standard</button>
              </div>
              <div id="nx-designer-theme-status" class="small text-muted"><?= htmlspecialchars($designerThemeStatus, ENT_QUOTES, 'UTF-8') ?></div>
            </form>
          </div>
        </div>

        <div id="nx-widget-zones-panel" class="card border-0 shadow-sm mt-3 nx-designer-panel is-hidden">
          <div class="card-header">
            <strong>Widget-Zonen</strong>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <input id="nx-widget-zone-search" type="search" class="form-control" placeholder="Widget suchen">
            </div>
            <div class="small text-muted mb-3">
              Hier konfigurierst du die erlaubten Zonen pro Widget direkt im Designer.
            </div>
            <div class="nx-widget-zone-grid" id="nx-widget-zone-list">
              <?php foreach ($designerWidgets as $widget): ?>
                <?php $allowedZones = array_values(array_filter(array_map('trim', explode(',', (string)($widget['allowed_zones'] ?? ''))))); ?>
                <form method="post" action="admincenter.php?site=theme_designer&amp;target_page=<?= urlencode($selectedPage) ?>" class="card border shadow-sm nx-widget-zone-item">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="widget_key" value="<?= htmlspecialchars((string)$widget['widget_key'], ENT_QUOTES, 'UTF-8') ?>">
                  <div class="card-body">
                    <div class="fw-semibold"><?= htmlspecialchars((string)($widget['title'] ?: $widget['widget_key']), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-muted mb-2">
                      <code><?= htmlspecialchars((string)$widget['widget_key'], ENT_QUOTES, 'UTF-8') ?></code>
                      <?php if (!empty($widget['plugin'])): ?>
                        · <?= htmlspecialchars((string)$widget['plugin'], ENT_QUOTES, 'UTF-8') ?>
                      <?php endif; ?>
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                      <?php foreach ($designerZones as $zone): ?>
                        <?php $checked = in_array($zone, $allowedZones, true); ?>
                        <label class="form-check">
                          <input class="form-check-input" type="checkbox" name="allowed_zones[]" value="<?= htmlspecialchars($zone, ENT_QUOTES, 'UTF-8') ?>"<?= $checked ? ' checked' : '' ?>>
                          <span class="form-check-label"><?= htmlspecialchars(ucfirst($zone), ENT_QUOTES, 'UTF-8') ?></span>
                        </label>
                      <?php endforeach; ?>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                      <span class="small text-muted">Leer bedeutet: in allen Zonen erlaubt.</span>
                      <button type="submit" name="save_widget_zones" class="btn btn-sm btn-outline-dark">Zonen speichern</button>
                    </div>
                  </div>
                </form>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <div id="nx-designer-preview-pane" class="nx-designer-preview-pane">
        <div class="d-flex gap-2 mb-2 nx-preview-mode-toggle">
          <button type="button" class="btn btn-sm btn-outline-primary" data-preview-mode="builder">Widget-Builder</button>
          <button type="button" class="btn btn-sm btn-outline-secondary is-active" data-preview-mode="live">Live-Vorschau</button>
          <a href="<?= htmlspecialchars($widgetBuilderUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">Builder in neuem Tab</a>
          <a href="<?= htmlspecialchars($liveBuilderUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Vorschau in neuem Tab</a>
        </div>
        <div id="nx-designer-preview-shell" class="nx-designer-preview-shell">
          <div id="nx-designer-preview-stage" class="nx-designer-preview-stage">
            <iframe
              id="nx-designer-frame"
              src="<?= htmlspecialchars($liveBuilderUrl, ENT_QUOTES, 'UTF-8') ?>"
              title="Theme Designer Preview"
              class="nx-designer-iframe"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const themeForm = document.getElementById('nx-designer-theme-form');
  const widgetSearch = document.getElementById('nx-widget-zone-search');
  const widgetZoneItems = Array.from(document.querySelectorAll('.nx-widget-zone-item'));
  const layout = document.querySelector('.nx-designer-layout');
  const frame = document.getElementById('nx-designer-frame');
  const pageSelect = document.getElementById('nx-designer-page-select');
  const previewShell = document.getElementById('nx-designer-preview-shell');
  const previewStage = document.getElementById('nx-designer-preview-stage');
  const previewPane = document.getElementById('nx-designer-preview-pane');
  const previewModeButtons = Array.from(document.querySelectorAll('[data-preview-mode]'));
  const builderTabButton = document.querySelector('a[href*="/admin/plugin_widgets_preview.php"]');
  const liveTabButton = document.querySelector('a[href*="builder=1&designer=1"]');
  const themePanelButton = document.getElementById('nx-panel-theme');
  const widgetPanelButton = document.getElementById('nx-panel-widgets');
  const themeSettingsPanel = document.getElementById('nx-theme-settings-panel');
  const widgetZonesPanel = document.getElementById('nx-widget-zones-panel');
  const contrastWarning = document.getElementById('nx-color-contrast-warning');
  const colorPresetButtons = Array.from(document.querySelectorAll('[data-color-preset]'));
  const settingsNavButtons = Array.from(document.querySelectorAll('[data-settings-nav]'));
  const settingsSections = Array.from(document.querySelectorAll('[data-settings-section]'));
  const headlineStyleCards = Array.from(document.querySelectorAll('.nx-headstyle-card'));
  const navVariantCards = Array.from(document.querySelectorAll('.nx-nav-variant-card'));
  const cardBackgroundMode = document.getElementById('nx-card-background-mode');
  const cardBackgroundPresetHint = document.getElementById('nx-card-background-preset-hint');
  const cardBorderPresetHint = document.getElementById('nx-card-border-preset-hint');
  const initialSettings = <?= json_encode($runtimeSettings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  const previewUrls = {
    live: <?= json_encode($liveBuilderUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    builder: <?= json_encode($widgetBuilderUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
  };
  let currentPreviewMode = 'live';
  let cardBackgroundFromPreset = false;
  let cardBorderColorFromPreset = false;

  function updateCardPresetHints() {
    cardBackgroundPresetHint?.classList.toggle('is-hidden', !cardBackgroundFromPreset);
    cardBorderPresetHint?.classList.toggle('is-hidden', !cardBorderColorFromPreset);
  }

  function mixHex(hexA, hexB, ratio) {
    const a = String(hexA || '').trim();
    const b = String(hexB || '').trim();
    if (!/^#[0-9a-fA-F]{6}$/.test(a) || !/^#[0-9a-fA-F]{6}$/.test(b)) {
      return a || b || '#000000';
    }
    const clamp = Math.max(0, Math.min(1, Number(ratio)));
    const channels = [1, 3, 5].map((index) => {
      const av = parseInt(a.slice(index, index + 2), 16);
      const bv = parseInt(b.slice(index, index + 2), 16);
      return Math.round((av * (1 - clamp)) + (bv * clamp));
    });
    return `#${channels.map((value) => value.toString(16).padStart(2, '0')).join('')}`;
  }

  function normalizePresetValue(value) {
    return String(value || '').trim().toLowerCase();
  }

  function isPresetActive(button) {
    if (!themeForm || !button) return false;
    const presetKey = String(button.getAttribute('data-preset-key') || '').trim();
    const storedPresetKey = String(themeForm.elements.color_preset_key?.value || '').trim();
    if (presetKey !== '' && storedPresetKey !== '' && presetKey === storedPresetKey) {
      return true;
    }
    const raw = button.getAttribute('data-color-preset');
    if (!raw) return false;
    let presetColors;
    try {
      presetColors = JSON.parse(raw);
    } catch (error) {
      return false;
    }
    const colorFields = ['accent', 'text', 'page_top', 'page_bg', 'surface'];
    for (const fieldName of colorFields) {
      if (!themeForm.elements[fieldName]) return false;
      if (normalizePresetValue(themeForm.elements[fieldName].value) !== normalizePresetValue(presetColors[fieldName])) {
        return false;
      }
    }
    const extraFields = {
      card_background: button.getAttribute('data-card-background') || '',
      card_border_color: button.getAttribute('data-card-border-color') || '',
      card_style: button.getAttribute('data-card-style') || '',
      card_radius: button.getAttribute('data-card-radius') || '',
      card_border_width: button.getAttribute('data-card-border-width') || '',
      button_radius: button.getAttribute('data-button-radius') || '999px',
      input_radius: button.getAttribute('data-input-radius') || '.75rem',
      heading_weight: button.getAttribute('data-heading-weight') || '700',
      hero_radius: button.getAttribute('data-hero-radius') || '1.75rem',
      nav_style: button.getAttribute('data-nav-style') || '',
      nav_variant: button.getAttribute('data-nav-variant') || 'light',
      nav_link_weight: button.getAttribute('data-nav-link-weight') || '500',
      nav_font_size: button.getAttribute('data-nav-font-size') || '1rem',
      nav_text_transform: button.getAttribute('data-nav-text-transform') || 'none',
      nav_border_width: button.getAttribute('data-nav-border-width') || '1',
      nav_radius: button.getAttribute('data-nav-radius') || '',
      nav_background: button.getAttribute('data-nav-background') || '',
      nav_link: button.getAttribute('data-nav-link') || '',
      nav_hover: button.getAttribute('data-nav-hover') || '',
      nav_active: button.getAttribute('data-nav-active') || '',
      nav_dropdown_background: button.getAttribute('data-nav-dropdown-background') || '',
      pagination_radius: button.getAttribute('data-pagination-radius') || '',
      pagination_border_width: button.getAttribute('data-pagination-border-width') || '',
      pagination_gap: button.getAttribute('data-pagination-gap') || '',
      pagination_font_weight: button.getAttribute('data-pagination-font-weight') || '',
      pagination_color: button.getAttribute('data-pagination-color') || '',
      pagination_background: button.getAttribute('data-pagination-background') || '',
      pagination_border_color: button.getAttribute('data-pagination-border-color') || '',
      pagination_hover_color: button.getAttribute('data-pagination-hover-color') || '',
      pagination_hover_background: button.getAttribute('data-pagination-hover-background') || '',
      pagination_hover_border_color: button.getAttribute('data-pagination-hover-border-color') || '',
      pagination_active_color: button.getAttribute('data-pagination-active-color') || '',
      pagination_active_background: button.getAttribute('data-pagination-active-background') || '',
      pagination_active_border_color: button.getAttribute('data-pagination-active-border-color') || ''
    };
    for (const [fieldName, fieldValue] of Object.entries(extraFields)) {
      if (!themeForm.elements[fieldName]) return false;
      if (normalizePresetValue(themeForm.elements[fieldName].value) !== normalizePresetValue(fieldValue)) {
        return false;
      }
    }
    return true;
  }

  function updateActiveColorPreset() {
    colorPresetButtons.forEach((button) => {
      button.classList.toggle('is-active', isPresetActive(button));
    });
  }

  function hydrateForm(settings) {
    if (!themeForm || !settings) return;
    themeForm.elements.layout_preset.value = settings.layout_preset || 'right-sidebar';
    themeForm.elements.page_width.value = settings.page_width || 'wide';
    themeForm.elements.content_width.value = settings.content_width || 'normal';
    themeForm.elements.column_ratio.value = settings.column_ratio || '75-25';
    themeForm.elements.two_sidebar_ratio.value = settings.two_sidebar_ratio || '3-6-3';
    themeForm.elements.section_spacing.value = settings.section_spacing || 'normal';
    themeForm.elements.card_style.value = settings.card_style || 'elevated';
    if (themeForm.elements.card_radius) {
      themeForm.elements.card_radius.value = settings.card_radius || '';
    }
    if (themeForm.elements.button_radius) {
      themeForm.elements.button_radius.value = settings.button_radius || '999px';
    }
    if (themeForm.elements.input_radius) {
      themeForm.elements.input_radius.value = settings.input_radius || '.75rem';
    }
    if (themeForm.elements.heading_weight) {
      themeForm.elements.heading_weight.value = settings.heading_weight || '700';
    }
    if (themeForm.elements.hero_radius) {
      themeForm.elements.hero_radius.value = settings.hero_radius || '1.75rem';
    }
    themeForm.elements.card_background.value = settings.card_background || '';
    if (cardBackgroundMode) {
      cardBackgroundMode.value = settings.card_background === 'transparent' ? 'transparent' : 'color';
    }
    themeForm.elements.card_border_width.value = settings.card_border_width || '1';
    themeForm.elements.card_border_color.value = settings.card_border_color || '';
    themeForm.elements.hero_style.value = settings.hero_style || 'standard';
    themeForm.elements.nav_style.value = settings.nav_style || 'solid';
    if (themeForm.elements.nav_variant) {
      themeForm.elements.nav_variant.value = settings.nav_variant || 'light';
    }
    if (themeForm.elements.nav_link_weight) {
      themeForm.elements.nav_link_weight.value = settings.nav_link_weight || '500';
    }
    if (themeForm.elements.nav_font_size) {
      themeForm.elements.nav_font_size.value = settings.nav_font_size || '1rem';
    }
    if (themeForm.elements.nav_text_transform) {
      themeForm.elements.nav_text_transform.value = settings.nav_text_transform || 'none';
    }
    if (themeForm.elements.nav_border_width) {
      themeForm.elements.nav_border_width.value = settings.nav_border_width || '1';
    }
    themeForm.elements.nav_width.value = settings.nav_width || 'full';
    themeForm.elements.nav_radius.value = settings.nav_radius || '0';
    themeForm.elements.nav_top_spacing.value = settings.nav_top_spacing || '0';
    themeForm.elements.nav_background.value = settings.nav_background || '';
    themeForm.elements.nav_link.value = settings.nav_link || '';
    themeForm.elements.nav_hover.value = settings.nav_hover || '';
    themeForm.elements.nav_active.value = settings.nav_active || '';
    themeForm.elements.nav_dropdown_background.value = settings.nav_dropdown_background || '';
    themeForm.elements.show_hero.checked = !!settings.show_hero;
    themeForm.elements.hero_title.value = settings.hero_title || '';
    themeForm.elements.hero_text.value = settings.hero_text || '';
    themeForm.elements.cta_label.value = settings.cta_label || '';
    themeForm.elements.accent.value = settings.colors?.accent || '';
    themeForm.elements.text.value = settings.colors?.text || '';
    themeForm.elements.page_top.value = settings.colors?.page_top || '';
    themeForm.elements.page_bg.value = settings.colors?.page_bg || '';
    themeForm.elements.surface.value = settings.colors?.surface || '';
    if (themeForm.elements.color_preset_key) {
      themeForm.elements.color_preset_key.value = settings.color_preset_key || '';
    }
    ['pagination_radius', 'pagination_border_width', 'pagination_gap', 'pagination_font_weight', 'pagination_color', 'pagination_background', 'pagination_border_color', 'pagination_hover_color', 'pagination_hover_background', 'pagination_hover_border_color', 'pagination_active_color', 'pagination_active_background', 'pagination_active_border_color'].forEach((fieldName) => {
      if (themeForm.elements[fieldName] && Object.prototype.hasOwnProperty.call(settings, fieldName)) {
        themeForm.elements[fieldName].value = String(settings[fieldName] ?? '');
      }
    });
    cardBackgroundFromPreset = false;
    cardBorderColorFromPreset = false;
    updateCardPresetHints();
    syncColorControls();
    updateActiveColorPreset();
    updateActiveNavVariant();
    refreshThemeShapePreview();
    refreshNavVariantPreviews();
  }

  function updateActiveNavVariant() {
    const current = String(themeForm?.elements.nav_variant?.value || 'light');
    navVariantCards.forEach((card) => {
      const isActive = String(card.getAttribute('data-variant') || '') === current;
      card.classList.toggle('is-active', isActive);
    });
  }

  function readableNavText(background, fallback) {
    const bg = parseHex(background);
    const fb = parseHex(fallback);
    if (bg && fb) {
      const ratio = contrastRatio(fb, bg);
      if (ratio !== null && ratio >= 4.5) {
        return fb;
      }
    }
    const whiteRatio = bg ? contrastRatio('#ffffff', bg) : null;
    const darkRatio = bg ? contrastRatio('#111827', bg) : null;
    return (whiteRatio !== null && whiteRatio >= (darkRatio ?? 0)) ? '#ffffff' : '#111827';
  }

  function buildNavVariantColors(variant) {
    if (!themeForm) return null;
    const accent = parseHex(themeForm.elements.accent?.value) || '#1f6feb';
    const text = parseHex(themeForm.elements.text?.value) || '#0f172a';
    const pageTop = parseHex(themeForm.elements.page_top?.value) || '#1a2230';
    const surface = parseHex(themeForm.elements.surface?.value) || '#ffffff';
    switch (variant) {
      case 'primary':
        return {
          nav_background: accent,
          nav_link: readableNavText(accent, '#ffffff'),
          nav_hover: readableNavText(accent, '#ffffff'),
          nav_active: readableNavText(accent, '#ffffff'),
          nav_dropdown_background: surface,
          nav_style: 'solid',
          nav_link_weight: '500',
          nav_font_size: '1rem',
          nav_text_transform: 'none',
          nav_border_width: '1'
        };
      case 'dark':
        return {
          nav_background: pageTop,
          nav_link: readableNavText(pageTop, '#ffffff'),
          nav_hover: accent,
          nav_active: accent,
          nav_dropdown_background: pageTop,
          nav_style: 'solid',
          nav_link_weight: '600',
          nav_font_size: '1rem',
          nav_text_transform: 'none',
          nav_border_width: '1'
        };
      case 'soft':
        return {
          nav_background: mixHex(surface, accent, 0.08),
          nav_link: text,
          nav_hover: accent,
          nav_active: accent,
          nav_dropdown_background: surface,
          nav_style: 'outlined',
          nav_link_weight: '500',
          nav_font_size: '1rem',
          nav_text_transform: 'none',
          nav_border_width: '1'
        };
      case 'light':
      default:
        return {
          nav_background: surface,
          nav_link: text,
          nav_hover: accent,
          nav_active: accent,
          nav_dropdown_background: surface,
          nav_style: 'outlined',
          nav_link_weight: '300',
          nav_font_size: '1rem',
          nav_text_transform: 'none',
          nav_border_width: '1'
        };
    }
  }

  function refreshThemeShapePreview() {
    if (!themeForm) return;
    const cardStyle = String(themeForm.elements.card_style?.value || 'elevated');
    const cardRadiusValue = String(themeForm.elements.card_radius?.value || '').trim();
    const borderWidth = String(themeForm.elements.card_border_width?.value || '1');
    const navRadius = String(themeForm.elements.nav_radius?.value || '0');
    const radiusMap = {
      flat: '.9rem',
      outlined: '1rem',
      elevated: '1.25rem',
      soft: '1.5rem'
    };
    const cardRadius = cardRadiusValue !== '' ? cardRadiusValue : (radiusMap[cardStyle] || radiusMap.elevated);
    const barRadius = navRadius !== '0' ? `${navRadius}px` : cardRadius;
    document.documentElement.style.setProperty('--nx-nav-preview-card-radius', cardRadius);
    document.documentElement.style.setProperty('--nx-nav-preview-bar-radius', barRadius);
    document.documentElement.style.setProperty('--nx-nav-preview-border-width', `${borderWidth}px`);
  }

  function refreshNavVariantPreviews() {
    navVariantCards.forEach((card) => {
      const variant = String(card.getAttribute('data-variant') || 'light');
      const colors = buildNavVariantColors(variant);
      if (!colors) return;
      const bar = card.querySelector('.nx-nav-variant-bar');
      const brand = card.querySelector('.nx-nav-variant-brand');
      const links = Array.from(card.querySelectorAll('.nx-nav-variant-links span'));
      if (bar) {
        bar.style.background = colors.nav_background;
        bar.style.borderColor = mixHex(colors.nav_background, colors.nav_link, 0.18);
      }
      if (brand) {
        brand.style.background = colors.nav_link;
      }
      links.forEach((link) => {
        if (variant === 'light') {
          link.style.background = 'transparent';
          link.style.borderBottomColor = colors.nav_active;
        } else if (variant === 'soft') {
          link.style.background = mixHex(colors.nav_background, colors.nav_active, 0.35);
          link.style.borderBottomColor = 'transparent';
        } else {
          link.style.background = colors.nav_link;
          link.style.borderBottomColor = 'transparent';
        }
      });
    });
  }

  function syncColorControls() {
    if (!themeForm) return;
    themeForm.querySelectorAll('[data-color-target]').forEach((picker) => {
      const targetName = picker.getAttribute('data-color-target');
      const textInput = themeForm.elements[targetName];
      const swatch = themeForm.querySelector(`[data-swatch-for="${targetName}"]`);
      if (!textInput) return;
      const value = String(textInput.value || '').trim();
      if (targetName === 'card_background' && value === 'transparent') {
        picker.disabled = true;
        if (swatch) swatch.style.background = 'linear-gradient(135deg,#fff 0%,#fff 45%,#f87171 45%,#f87171 55%,#fff 55%,#fff 100%)';
        return;
      }
      picker.disabled = false;
      const isHex = /^#[0-9a-fA-F]{6}$/.test(value);
      if (isHex) {
        picker.value = value;
        if (swatch) swatch.style.background = value;
      } else {
        if (swatch) swatch.style.background = '#ffffff';
      }
    });
    updateContrastWarning();
  }

  function parseHex(value) {
    const normalized = String(value || '').trim();
    if (!/^#[0-9a-fA-F]{6}$/.test(normalized)) {
      return null;
    }
    return normalized;
  }

  function relativeLuminance(hex) {
    const normalized = parseHex(hex);
    if (!normalized) return null;
    const channels = [1, 3, 5].map((index) => parseInt(normalized.slice(index, index + 2), 16) / 255);
    const linear = channels.map((value) => (
      value <= 0.03928 ? value / 12.92 : Math.pow((value + 0.055) / 1.055, 2.4)
    ));
    return (0.2126 * linear[0]) + (0.7152 * linear[1]) + (0.0722 * linear[2]);
  }

  function contrastRatio(foreground, background) {
    const fg = relativeLuminance(foreground);
    const bg = relativeLuminance(background);
    if (fg === null || bg === null) return null;
    const lighter = Math.max(fg, bg);
    const darker = Math.min(fg, bg);
    return (lighter + 0.05) / (darker + 0.05);
  }

  function updateContrastWarning() {
    if (!themeForm || !contrastWarning) return;
    const text = themeForm.elements.text?.value;
    const surface = themeForm.elements.surface?.value;
    const pageBg = themeForm.elements.page_bg?.value;
    const accent = themeForm.elements.accent?.value;
    const issues = [];

    const textSurfaceRatio = contrastRatio(text, surface);
    if (textSurfaceRatio !== null && textSurfaceRatio < 4.5) {
      issues.push(`Text auf Surface ist mit ${textSurfaceRatio.toFixed(2)}:1 zu schwach.`);
    }

    const textPageRatio = contrastRatio(text, pageBg);
    if (textPageRatio !== null && textPageRatio < 4.5) {
      issues.push(`Text auf Seitenhintergrund ist mit ${textPageRatio.toFixed(2)}:1 zu schwach.`);
    }

    const accentSurfaceRatio = contrastRatio(accent, surface);
    if (accentSurfaceRatio !== null && accentSurfaceRatio < 3) {
      issues.push(`Akzent auf Surface ist mit ${accentSurfaceRatio.toFixed(2)}:1 kaum sichtbar.`);
    }

    if (!issues.length) {
      contrastWarning.classList.add('is-hidden');
      contrastWarning.textContent = '';
      return;
    }

    contrastWarning.classList.remove('is-hidden');
    contrastWarning.textContent = `Kontrastwarnung: ${issues.join(' ')}`;
  }

  function applyColorPreset(colors, options = {}) {
    if (!themeForm || !colors || typeof colors !== 'object') return;
    ['accent', 'text', 'page_top', 'page_bg', 'surface'].forEach((fieldName) => {
      if (!Object.prototype.hasOwnProperty.call(colors, fieldName) || !themeForm.elements[fieldName]) return;
      themeForm.elements[fieldName].value = String(colors[fieldName]);
    });
    const cardBackground = String(options.cardBackground || '').trim();
    const cardBorderColor = String(options.cardBorderColor || '').trim();
    if (themeForm.elements.card_background && cardBackground !== '') {
      themeForm.elements.card_background.value = cardBackground;
      if (cardBackgroundMode) {
        cardBackgroundMode.value = 'color';
      }
      cardBackgroundFromPreset = true;
    }
    if (themeForm.elements.card_border_color && cardBorderColor !== '') {
      themeForm.elements.card_border_color.value = cardBorderColor;
      cardBorderColorFromPreset = true;
    }
    ['card_style', 'card_radius', 'card_border_width', 'button_radius', 'input_radius', 'heading_weight', 'hero_radius', 'nav_style', 'nav_variant', 'nav_link_weight', 'nav_font_size', 'nav_text_transform', 'nav_border_width', 'nav_radius', 'pagination_radius', 'pagination_border_width', 'pagination_gap', 'pagination_font_weight'].forEach((fieldName) => {
      const fieldValue = String(options[fieldName] || '').trim();
      if (fieldValue !== '' && themeForm.elements[fieldName]) {
        themeForm.elements[fieldName].value = fieldValue;
      }
    });
    ['nav_background', 'nav_link', 'nav_hover', 'nav_active', 'nav_dropdown_background', 'pagination_color', 'pagination_background', 'pagination_border_color', 'pagination_hover_color', 'pagination_hover_background', 'pagination_hover_border_color', 'pagination_active_color', 'pagination_active_background', 'pagination_active_border_color'].forEach((fieldName) => {
      const fieldValue = String(options[fieldName] || '').trim();
      if (fieldValue !== '' && themeForm.elements[fieldName]) {
        themeForm.elements[fieldName].value = fieldValue;
      }
    });
    updateCardPresetHints();
    syncColorControls();
  }

  function setSidebarPanel(panel) {
    const showTheme = panel !== 'widgets';
    if (layout) {
      layout.classList.toggle('is-widget-mode', !showTheme);
    }
    if (themeSettingsPanel) {
      themeSettingsPanel.classList.toggle('is-hidden', !showTheme);
    }
    if (widgetZonesPanel) {
      widgetZonesPanel.classList.toggle('is-hidden', showTheme);
    }
    if (previewPane) {
      previewPane.classList.toggle('is-hidden', !showTheme);
    }
    if (themePanelButton) {
      themePanelButton.className = showTheme ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline-secondary';
    }
    if (widgetPanelButton) {
      widgetPanelButton.className = showTheme ? 'btn btn-sm btn-outline-secondary' : 'btn btn-sm btn-primary';
    }
    if (showTheme) {
      fitPreviewStage();
    }
  }

  function setSettingsSection(sectionName) {
    settingsSections.forEach((section) => {
      section.classList.toggle('is-hidden', section.getAttribute('data-settings-section') !== sectionName);
    });
    settingsNavButtons.forEach((button) => {
      button.classList.toggle('is-active', button.getAttribute('data-settings-nav') === sectionName);
    });
    fitPreviewStage();
  }

  function fitPreviewStage() {
    if (!previewShell || !previewStage) return;
    if (currentPreviewMode === 'builder') {
      previewShell.classList.add('is-builder-mode');
      previewShell.style.minHeight = '';
      previewStage.style.transform = 'none';
      return;
    }
    previewShell.classList.remove('is-builder-mode');
    const shellWidth = Math.max(320, previewShell.clientWidth - 32);
    const baseWidth = 1440;
    const scale = Math.min(1, shellWidth / baseWidth);
    previewStage.style.transform = `scale(${scale})`;
    previewShell.style.minHeight = `${Math.max(680, Math.round(980 * scale) + 32)}px`;
  }

  function setPreviewMode(mode) {
    if (!frame || !previewUrls[mode]) return;
    currentPreviewMode = mode;
    frame.src = previewUrls[mode];
    previewModeButtons.forEach((button) => {
      button.classList.toggle('is-active', button.getAttribute('data-preview-mode') === mode);
    });
    fitPreviewStage();
  }

  function slugToLivePath(page) {
    const slug = String(page || 'index').trim();
    const lang = <?= json_encode($currentLang, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    if (!slug || slug === 'index') {
      return `/${encodeURIComponent(lang)}/`;
    }
    return `/${encodeURIComponent(lang)}/${encodeURIComponent(slug)}/`;
  }

  function updatePreviewUrlsForPage(page) {
    const safePage = String(page || 'index').replace(/[^a-zA-Z0-9_-]/g, '') || 'index';
    previewUrls.builder = `/admin/plugin_widgets_preview.php?page=${encodeURIComponent(safePage)}`;
    previewUrls.live = `${slugToLivePath(safePage)}?builder=1&designer=1&nxv=<?= rawurlencode($previewVersion) ?>`;
    if (builderTabButton) builderTabButton.href = previewUrls.builder;
    if (liveTabButton) liveTabButton.href = previewUrls.live;
    if (currentPreviewMode === 'builder' || currentPreviewMode === 'live') {
      setPreviewMode(currentPreviewMode);
    }
  }

  hydrateForm(initialSettings);
  themeForm?.querySelectorAll('[data-color-target]').forEach((picker) => {
    picker.addEventListener('input', () => {
      const targetName = picker.getAttribute('data-color-target');
      const textInput = themeForm.elements[targetName];
      if (!textInput) return;
      if (themeForm.elements.color_preset_key) {
        themeForm.elements.color_preset_key.value = '';
      }
      textInput.value = picker.value;
      syncColorControls();
      updateActiveColorPreset();
      refreshThemeShapePreview();
      refreshNavVariantPreviews();
    });
  });
  ['accent', 'text', 'page_top', 'page_bg', 'surface', 'card_background', 'card_border_color', 'nav_background', 'nav_link', 'nav_hover', 'nav_active', 'nav_dropdown_background', 'card_style', 'card_radius', 'card_border_width', 'button_radius', 'input_radius', 'heading_weight', 'hero_radius', 'nav_style', 'nav_variant', 'nav_link_weight', 'nav_font_size', 'nav_text_transform', 'nav_border_width', 'nav_radius', 'pagination_radius', 'pagination_border_width', 'pagination_gap', 'pagination_font_weight', 'pagination_color', 'pagination_background', 'pagination_border_color', 'pagination_hover_color', 'pagination_hover_background', 'pagination_hover_border_color', 'pagination_active_color', 'pagination_active_background', 'pagination_active_border_color'].forEach((fieldName) => {
    const field = themeForm?.elements[fieldName];
    field?.addEventListener('input', () => {
      if (themeForm.elements.color_preset_key) {
        themeForm.elements.color_preset_key.value = '';
      }
      if (fieldName === 'card_background') {
        cardBackgroundFromPreset = false;
        updateCardPresetHints();
      }
      if (fieldName === 'card_border_color') {
        cardBorderColorFromPreset = false;
        updateCardPresetHints();
      }
      syncColorControls();
      updateActiveColorPreset();
      refreshThemeShapePreview();
      refreshNavVariantPreviews();
    });
    field?.addEventListener('blur', () => {
      if (themeForm.elements.color_preset_key) {
        themeForm.elements.color_preset_key.value = '';
      }
      if (fieldName === 'card_background') {
        cardBackgroundFromPreset = false;
        updateCardPresetHints();
      }
      if (fieldName === 'card_border_color') {
        cardBorderColorFromPreset = false;
        updateCardPresetHints();
      }
      syncColorControls();
      updateActiveColorPreset();
      refreshThemeShapePreview();
      refreshNavVariantPreviews();
    });
  });
  colorPresetButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const presetKey = button.getAttribute('data-preset-key') || '';
      const raw = button.getAttribute('data-color-preset');
      const cardBackground = button.getAttribute('data-card-background') || '';
      const cardBorderColor = button.getAttribute('data-card-border-color') || '';
      const cardStyle = button.getAttribute('data-card-style') || '';
      const cardRadius = button.getAttribute('data-card-radius') || '';
      const cardBorderWidth = button.getAttribute('data-card-border-width') || '';
      const buttonRadius = button.getAttribute('data-button-radius') || '999px';
      const inputRadius = button.getAttribute('data-input-radius') || '.75rem';
      const headingWeight = button.getAttribute('data-heading-weight') || '700';
      const heroRadius = button.getAttribute('data-hero-radius') || '1.75rem';
      const navStyle = button.getAttribute('data-nav-style') || '';
      const navVariant = button.getAttribute('data-nav-variant') || 'light';
      const navLinkWeight = button.getAttribute('data-nav-link-weight') || '500';
      const navFontSize = button.getAttribute('data-nav-font-size') || '1rem';
      const navTextTransform = button.getAttribute('data-nav-text-transform') || 'none';
      const navBorderWidth = button.getAttribute('data-nav-border-width') || '1';
      const navRadius = button.getAttribute('data-nav-radius') || '';
      const navBackground = button.getAttribute('data-nav-background') || '';
      const navLink = button.getAttribute('data-nav-link') || '';
      const navHover = button.getAttribute('data-nav-hover') || '';
      const navActive = button.getAttribute('data-nav-active') || '';
      const navDropdownBackground = button.getAttribute('data-nav-dropdown-background') || '';
      const paginationRadius = button.getAttribute('data-pagination-radius') || '';
      const paginationBorderWidth = button.getAttribute('data-pagination-border-width') || '';
      const paginationGap = button.getAttribute('data-pagination-gap') || '';
      const paginationFontWeight = button.getAttribute('data-pagination-font-weight') || '';
      const paginationColor = button.getAttribute('data-pagination-color') || '';
      const paginationBackground = button.getAttribute('data-pagination-background') || '';
      const paginationBorderColor = button.getAttribute('data-pagination-border-color') || '';
      const paginationHoverColor = button.getAttribute('data-pagination-hover-color') || '';
      const paginationHoverBackground = button.getAttribute('data-pagination-hover-background') || '';
      const paginationHoverBorderColor = button.getAttribute('data-pagination-hover-border-color') || '';
      const paginationActiveColor = button.getAttribute('data-pagination-active-color') || '';
      const paginationActiveBackground = button.getAttribute('data-pagination-active-background') || '';
      const paginationActiveBorderColor = button.getAttribute('data-pagination-active-border-color') || '';
      if (!raw) return;
      try {
        applyColorPreset(JSON.parse(raw), {
          cardBackground,
          cardBorderColor,
          card_style: cardStyle,
          card_radius: cardRadius,
          card_border_width: cardBorderWidth,
          button_radius: buttonRadius,
          input_radius: inputRadius,
          heading_weight: headingWeight,
          hero_radius: heroRadius,
          nav_style: navStyle,
          nav_variant: navVariant,
          nav_link_weight: navLinkWeight,
          nav_font_size: navFontSize,
          nav_text_transform: navTextTransform,
          nav_border_width: navBorderWidth,
          nav_radius: navRadius,
          nav_background: navBackground,
          nav_link: navLink,
          nav_hover: navHover,
          nav_active: navActive,
          nav_dropdown_background: navDropdownBackground,
          pagination_radius: paginationRadius,
          pagination_border_width: paginationBorderWidth,
          pagination_gap: paginationGap,
          pagination_font_weight: paginationFontWeight,
          pagination_color: paginationColor,
          pagination_background: paginationBackground,
          pagination_border_color: paginationBorderColor,
          pagination_hover_color: paginationHoverColor,
          pagination_hover_background: paginationHoverBackground,
          pagination_hover_border_color: paginationHoverBorderColor,
          pagination_active_color: paginationActiveColor,
          pagination_active_background: paginationActiveBackground,
          pagination_active_border_color: paginationActiveBorderColor
        });
        if (themeForm.elements.color_preset_key) {
          themeForm.elements.color_preset_key.value = presetKey;
        }
      } catch (error) {
        console.warn('Color preset could not be applied.', error);
      }
      updateActiveColorPreset();
      refreshNavVariantPreviews();
    });
  });
  navVariantCards.forEach((card) => {
    card.addEventListener('click', () => {
      if (!themeForm?.elements.nav_variant) return;
      if (themeForm.elements.color_preset_key) {
        themeForm.elements.color_preset_key.value = '';
      }
      const variant = String(card.getAttribute('data-variant') || 'light');
      themeForm.elements.nav_variant.value = variant;
      const navColors = buildNavVariantColors(variant);
      if (navColors) {
        themeForm.elements.nav_background.value = navColors.nav_background;
        themeForm.elements.nav_link.value = navColors.nav_link;
        themeForm.elements.nav_hover.value = navColors.nav_hover;
        themeForm.elements.nav_active.value = navColors.nav_active;
        themeForm.elements.nav_dropdown_background.value = navColors.nav_dropdown_background;
        if (themeForm.elements.nav_style) {
          themeForm.elements.nav_style.value = navColors.nav_style;
        }
        if (themeForm.elements.nav_link_weight) {
          themeForm.elements.nav_link_weight.value = navColors.nav_link_weight || '500';
        }
        if (themeForm.elements.nav_font_size) {
          themeForm.elements.nav_font_size.value = navColors.nav_font_size || '1rem';
        }
        if (themeForm.elements.nav_text_transform) {
          themeForm.elements.nav_text_transform.value = navColors.nav_text_transform || 'none';
        }
        if (themeForm.elements.nav_border_width) {
          themeForm.elements.nav_border_width.value = navColors.nav_border_width || '1';
        }
        syncColorControls();
      }
      updateActiveNavVariant();
      refreshNavVariantPreviews();
    });
  });
  cardBackgroundMode?.addEventListener('change', () => {
    if (!themeForm) return;
    if (themeForm.elements.color_preset_key) {
      themeForm.elements.color_preset_key.value = '';
    }
    if (cardBackgroundMode.value === 'transparent') {
      themeForm.elements.card_background.value = 'transparent';
    } else if (themeForm.elements.card_background.value === 'transparent') {
      themeForm.elements.card_background.value = '#FFFFFF';
    }
    cardBackgroundFromPreset = false;
    updateCardPresetHints();
    syncColorControls();
    updateActiveColorPreset();
    refreshThemeShapePreview();
    refreshNavVariantPreviews();
  });
  updateActiveColorPreset();
  updateActiveNavVariant();
  refreshThemeShapePreview();
  refreshNavVariantPreviews();
  settingsNavButtons.forEach((button) => {
    button.addEventListener('click', () => {
      setSettingsSection(button.getAttribute('data-settings-nav') || 'general');
    });
  });
  headlineStyleCards.forEach((card) => {
    card.addEventListener('click', () => {
      headlineStyleCards.forEach((item) => item.classList.remove('is-active'));
      card.classList.add('is-active');
      const radio = card.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = true;
      }
    });
  });
  previewModeButtons.forEach((button) => {
    button.addEventListener('click', () => {
      setPreviewMode(button.getAttribute('data-preview-mode') || 'live');
    });
  });
  themePanelButton?.addEventListener('click', () => setSidebarPanel('theme'));
  widgetPanelButton?.addEventListener('click', () => setSidebarPanel('widgets'));
  widgetSearch?.addEventListener('input', () => {
    const query = (widgetSearch.value || '').toLowerCase().trim();
    widgetZoneItems.forEach((item) => {
      const text = (item.textContent || '').toLowerCase();
      item.style.display = !query || text.includes(query) ? '' : 'none';
    });
  });
  pageSelect?.addEventListener('change', () => {
    updatePreviewUrlsForPage(pageSelect.value || 'index');
  });
  frame?.addEventListener('load', fitPreviewStage);
  window.addEventListener('resize', fitPreviewStage);
  updatePreviewUrlsForPage(pageSelect?.value || <?= json_encode($selectedPage, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>);
  setPreviewMode('live');
  fitPreviewStage();
  setSettingsSection('general');
  setSidebarPanel('theme');
})();
</script>
