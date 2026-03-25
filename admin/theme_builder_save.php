<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../system/config.inc.php';
require_once __DIR__ . '/../system/classes/ThemeManager.php';
require_once __DIR__ . '/../system/core/theme_builder_helper.php';

$db = $GLOBALS['_database'] ?? new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$db instanceof mysqli || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'db connect failed'], JSON_UNESCAPED_UNICODE);
    exit;
}
$db->set_charset('utf8mb4');

$input = json_decode(file_get_contents('php://input') ?: '', true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'bad json'], JSON_UNESCAPED_UNICODE);
    exit;
}

$hdr = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
$sess = $_SESSION['csrf_token'] ?? '';
if (!$hdr || !$sess || !hash_equals($sess, $hdr)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'csrf invalid'], JSON_UNESCAPED_UNICODE);
    exit;
}

$themeManager = $GLOBALS['nx_theme_manager'] ?? new \nexpell\ThemeManager($db, dirname(__DIR__) . '/includes/themes', '/includes/themes');
$themeManager->ensureSchema();

$slug = strtolower(trim((string)($input['theme_slug'] ?? $themeManager->getActiveThemeSlug())));
if ($slug === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'missing theme slug'], JSON_UNESCAPED_UNICODE);
    exit;
}

$theme = $themeManager->getThemeBySlug($slug);
if (!$theme) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'theme not found'], JSON_UNESCAPED_UNICODE);
    exit;
}

$manifestPath = dirname(__DIR__) . '/includes/themes/' . $slug . '/theme.json';
$manifest = [];
if (is_file($manifestPath)) {
    $decoded = json_decode((string)file_get_contents($manifestPath), true);
    if (is_array($decoded)) {
        $manifest = $decoded;
    }
}

$current = nx_theme_builder_generator_defaults($manifest, (string)($theme['name'] ?? ucfirst($slug)));
$settings = [
    'hero_title' => trim((string)($input['hero_title'] ?? $current['hero_title'])),
    'hero_text' => trim((string)($input['hero_text'] ?? $current['hero_text'])),
    'cta_label' => trim((string)($input['cta_label'] ?? $current['cta_label'])),
    'layout_preset' => trim((string)($input['layout_preset'] ?? $current['layout_preset'])),
    'show_hero' => !empty($input['show_hero']),
    'colors' => [
        'accent' => nx_theme_builder_sanitize_hex((string)($input['accent'] ?? $current['colors']['accent']), '#1f6feb'),
        'page_top' => nx_theme_builder_sanitize_hex((string)($input['page_top'] ?? $current['colors']['page_top']), '#1a2230'),
        'page_bg' => nx_theme_builder_sanitize_hex((string)($input['page_bg'] ?? $current['colors']['page_bg']), '#11151b'),
        'surface' => nx_theme_builder_sanitize_hex((string)($input['surface'] ?? $current['colors']['surface']), '#ffffff'),
        'text' => nx_theme_builder_sanitize_hex((string)($input['text'] ?? $current['colors']['text']), '#0f172a'),
    ],
];

$themeDir = dirname(__DIR__) . '/includes/themes/' . $slug;
$manifest['settings'] = array_replace_recursive($manifest['settings'] ?? [], [
    'generator' => [
        'layout_preset' => $settings['layout_preset'],
        'show_hero' => $settings['show_hero'],
        'hero_title' => $settings['hero_title'],
        'hero_text' => $settings['hero_text'],
        'cta_label' => $settings['cta_label'],
        'colors' => $settings['colors'],
    ],
]);
$themeManager->saveOptions($slug, [
    'builder_runtime' => json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
]);

echo json_encode([
    'ok' => true,
    'settings' => $settings,
    'zones' => nx_theme_builder_zones($settings['layout_preset']),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
