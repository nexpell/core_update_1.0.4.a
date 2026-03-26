<?php
declare(strict_types=1);

function nx_theme_builder_sanitize_hex(string $value, string $fallback): string
{
    $value = trim($value);
    return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $fallback;
}

function nx_theme_builder_sanitize_card_background(string $value, string $fallback): string
{
    $value = trim($value);
    if ($value === 'transparent') {
        return 'transparent';
    }
    return nx_theme_builder_sanitize_hex($value, $fallback);
}

function nx_theme_builder_extract_css_variables(string $css): array
{
    $variables = [];
    if ($css === '') {
        return $variables;
    }

    if (preg_match_all('/--([a-zA-Z0-9_-]+)\s*:\s*([^;]+);/', $css, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $name = strtolower(trim((string)$match[1]));
            $value = trim((string)$match[2]);
            if ($name !== '' && $value !== '') {
                $variables[$name] = $value;
            }
        }
    }

    return $variables;
}

function nx_theme_builder_detect_css_defaults(array $manifest, string $themePath): array
{
    if ($themePath === '' || !is_dir($themePath)) {
        return [];
    }

    $cssCandidates = [];
    foreach ((array)($manifest['assets']['css'] ?? []) as $asset) {
        $asset = trim((string)$asset, '/\\');
        if ($asset !== '') {
            $cssCandidates[] = $asset;
        }
    }
    $cssCandidates = array_merge($cssCandidates, [
        'assets/css/main.css',
        'css/main.css',
        'assets/css/style.css',
        'css/style.css',
    ]);
    $cssCandidates = array_values(array_unique($cssCandidates));

    $variables = [];
    foreach ($cssCandidates as $candidate) {
        $file = $themePath . '/' . str_replace('\\', '/', $candidate);
        if (!is_file($file)) {
            continue;
        }
        $css = (string)@file_get_contents($file);
        if ($css === '') {
            continue;
        }
        $variables = array_replace($variables, nx_theme_builder_extract_css_variables($css));
        if ($variables !== []) {
            break;
        }
    }

    if ($variables === []) {
        return [];
    }

    $pickHex = static function (array $vars, array $keys): ?string {
        foreach ($keys as $key) {
            $value = trim((string)($vars[strtolower($key)] ?? ''));
            if ($value !== '' && preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                return $value;
            }
        }
        return null;
    };

    $pageBg = $pickHex($variables, ['background-color', 'nx-theme-page-bg', 'bs-body-bg']);
    $surface = $pickHex($variables, ['surface-color', 'nx-theme-surface-2', 'nx-theme-surface', 'bs-secondary-bg', 'bs-tertiary-bg']);
    $accent = $pickHex($variables, ['accent-color', 'nx-theme-accent', 'primary', 'bs-link-color']);
    $text = $pickHex($variables, ['default-color', 'nx-theme-text', 'bs-body-color', 'heading-color']);
    $pageTop = $pickHex($variables, ['page-top-color', 'nx-theme-page-top']);

    return array_filter([
        'accent' => $accent,
        'page_top' => $pageTop ?: $pageBg,
        'page_bg' => $pageBg,
        'surface' => $surface,
        'text' => $text,
    ]);
}

function nx_theme_builder_generator_defaults(array $manifest, string $fallbackName = ''): array
{
    $generator = is_array($manifest['settings']['generator'] ?? null) ? $manifest['settings']['generator'] : [];
    $colors = is_array($generator['colors'] ?? null) ? $generator['colors'] : [];

    return [
        'hero_title' => (string)($generator['hero_title'] ?? $fallbackName),
        'hero_text' => (string)($generator['hero_text'] ?? 'Eigenes Nexpell Theme als Startpunkt.'),
        'cta_label' => (string)($generator['cta_label'] ?? 'Mehr erfahren'),
        'layout_preset' => (string)($generator['layout_preset'] ?? 'right-sidebar'),
        'page_width' => (string)($generator['page_width'] ?? 'wide'),
        'content_width' => (string)($generator['content_width'] ?? 'normal'),
        'column_ratio' => (string)($generator['column_ratio'] ?? '75-25'),
        'two_sidebar_ratio' => (string)($generator['two_sidebar_ratio'] ?? '3-6-3'),
        'section_spacing' => (string)($generator['section_spacing'] ?? 'normal'),
        'card_style' => (string)($generator['card_style'] ?? 'elevated'),
        'card_radius' => (string)($generator['card_radius'] ?? ''),
        'card_background' => (string)($generator['card_background'] ?? '#ffffff'),
        'card_border_width' => (string)($generator['card_border_width'] ?? '1'),
        'card_border_color' => (string)($generator['card_border_color'] ?? '#d7dee8'),
        'button_radius' => (string)($generator['button_radius'] ?? '999px'),
        'input_radius' => (string)($generator['input_radius'] ?? '.75rem'),
        'heading_weight' => (string)($generator['heading_weight'] ?? '700'),
        'hero_radius' => (string)($generator['hero_radius'] ?? '1.75rem'),
        'hero_style' => (string)($generator['hero_style'] ?? 'standard'),
        'nav_style' => (string)($generator['nav_style'] ?? 'solid'),
        'nav_variant' => (string)($generator['nav_variant'] ?? 'light'),
        'nav_link_weight' => (string)($generator['nav_link_weight'] ?? '500'),
        'nav_font_size' => (string)($generator['nav_font_size'] ?? '1rem'),
        'nav_text_transform' => (string)($generator['nav_text_transform'] ?? 'none'),
        'nav_border_width' => (string)($generator['nav_border_width'] ?? '1'),
        'pagination_radius' => (string)($generator['pagination_radius'] ?? '999px'),
        'pagination_border_width' => (string)($generator['pagination_border_width'] ?? '1'),
        'pagination_gap' => (string)($generator['pagination_gap'] ?? '.25rem'),
        'pagination_font_weight' => (string)($generator['pagination_font_weight'] ?? '600'),
        'pagination_color' => (string)($generator['pagination_color'] ?? '#1f6feb'),
        'pagination_background' => (string)($generator['pagination_background'] ?? '#ffffff'),
        'pagination_border_color' => (string)($generator['pagination_border_color'] ?? '#d7dee8'),
        'pagination_hover_color' => (string)($generator['pagination_hover_color'] ?? '#ffffff'),
        'pagination_hover_background' => (string)($generator['pagination_hover_background'] ?? '#1f6feb'),
        'pagination_hover_border_color' => (string)($generator['pagination_hover_border_color'] ?? '#1f6feb'),
        'pagination_active_color' => (string)($generator['pagination_active_color'] ?? '#ffffff'),
        'pagination_active_background' => (string)($generator['pagination_active_background'] ?? '#1f6feb'),
        'pagination_active_border_color' => (string)($generator['pagination_active_border_color'] ?? '#1f6feb'),
        'color_preset_key' => (string)($generator['color_preset_key'] ?? ''),
        'nav_width' => (string)($generator['nav_width'] ?? 'full'),
        'nav_radius' => (string)($generator['nav_radius'] ?? '0'),
        'nav_top_spacing' => (string)($generator['nav_top_spacing'] ?? '0'),
        'nav_background' => (string)($generator['nav_background'] ?? '#ffffff'),
        'nav_link' => (string)($generator['nav_link'] ?? '#212529'),
        'nav_hover' => (string)($generator['nav_hover'] ?? '#1f6feb'),
        'nav_active' => (string)($generator['nav_active'] ?? '#1f6feb'),
        'nav_dropdown_background' => (string)($generator['nav_dropdown_background'] ?? '#ffffff'),
        'show_hero' => !empty($generator['show_hero']),
        'colors' => [
            'accent' => (string)($colors['accent'] ?? '#1f6feb'),
            'page_top' => (string)($colors['page_top'] ?? '#1a2230'),
            'page_bg' => (string)($colors['page_bg'] ?? '#11151b'),
            'surface' => (string)($colors['surface'] ?? '#ffffff'),
            'text' => (string)($colors['text'] ?? '#0f172a'),
        ],
    ];
}

function nx_theme_builder_runtime_settings(\nexpell\ThemeManager $themeManager, ?string $themeSlug = null): array
{
    $themeSlug = $themeSlug ?: $themeManager->getActiveThemeSlug();
    $manifest = $themeSlug === $themeManager->getActiveThemeSlug()
        ? $themeManager->getActiveManifest()
        : ($themeManager->getThemeBySlug($themeSlug) ?? []);
    $themeRow = $themeSlug === $themeManager->getActiveThemeSlug()
        ? ['path' => $themeManager->getActiveThemePath()]
        : ($themeManager->getThemeBySlug($themeSlug) ?? []);

    $defaults = nx_theme_builder_generator_defaults(is_array($manifest) ? $manifest : [], (string)($manifest['name'] ?? ucfirst($themeSlug)));
    $detectedColors = nx_theme_builder_detect_css_defaults(is_array($manifest) ? $manifest : [], (string)($themeRow['path'] ?? ''));
    if ($detectedColors !== []) {
        $defaults['colors'] = array_replace($defaults['colors'] ?? [], $detectedColors);
    }
    $raw = $themeManager->getOption('builder_runtime', $themeSlug, '');
    $runtime = [];
    if ($raw !== null && $raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $runtime = $decoded;
        }
    }

    $merged = array_replace_recursive($defaults, $runtime);
    $merged['layout_preset'] = (string)($merged['layout_preset'] ?? 'right-sidebar');
    $merged['page_width'] = in_array((string)($merged['page_width'] ?? ''), ['boxed', 'wide', 'full'], true) ? (string)$merged['page_width'] : 'wide';
    $merged['content_width'] = in_array((string)($merged['content_width'] ?? ''), ['narrow', 'normal', 'wide'], true) ? (string)$merged['content_width'] : 'normal';
    $merged['column_ratio'] = in_array((string)($merged['column_ratio'] ?? ''), ['70-30', '75-25', '80-20'], true) ? (string)$merged['column_ratio'] : '75-25';
    $merged['two_sidebar_ratio'] = in_array((string)($merged['two_sidebar_ratio'] ?? ''), ['3-6-3', '4-4-4', '3-5-4', '4-5-3'], true) ? (string)$merged['two_sidebar_ratio'] : '3-6-3';
    $merged['section_spacing'] = in_array((string)($merged['section_spacing'] ?? ''), ['compact', 'normal', 'relaxed'], true) ? (string)$merged['section_spacing'] : 'normal';
    $merged['card_style'] = in_array((string)($merged['card_style'] ?? ''), ['flat', 'outlined', 'elevated', 'soft'], true) ? (string)$merged['card_style'] : 'elevated';
    $merged['card_radius'] = preg_match('/^(0|0?\.[0-9]+|[0-9]+(\.[0-9]+)?(rem|px))$/', (string)($merged['card_radius'] ?? '')) ? (string)$merged['card_radius'] : '';
    $merged['card_background'] = nx_theme_builder_sanitize_card_background((string)($merged['card_background'] ?? '#ffffff'), '#ffffff');
    $merged['card_border_width'] = in_array((string)($merged['card_border_width'] ?? ''), ['0', '1', '2', '3'], true) ? (string)$merged['card_border_width'] : '1';
    $merged['card_border_color'] = nx_theme_builder_sanitize_hex((string)($merged['card_border_color'] ?? '#d7dee8'), '#d7dee8');
    $merged['button_radius'] = preg_match('/^(0|0?\.[0-9]+|[0-9]+(\.[0-9]+)?(rem|px))$/', (string)($merged['button_radius'] ?? '')) ? (string)$merged['button_radius'] : '999px';
    $merged['input_radius'] = preg_match('/^(0|0?\.[0-9]+|[0-9]+(\.[0-9]+)?(rem|px))$/', (string)($merged['input_radius'] ?? '')) ? (string)$merged['input_radius'] : '.75rem';
    $merged['heading_weight'] = in_array((string)($merged['heading_weight'] ?? ''), ['300', '400', '500', '600', '700', '800'], true) ? (string)$merged['heading_weight'] : '700';
    $merged['hero_radius'] = preg_match('/^(0|0?\.[0-9]+|[0-9]+(\.[0-9]+)?(rem|px))$/', (string)($merged['hero_radius'] ?? '')) ? (string)$merged['hero_radius'] : '1.75rem';
    $merged['hero_style'] = in_array((string)($merged['hero_style'] ?? ''), ['compact', 'standard', 'immersive'], true) ? (string)$merged['hero_style'] : 'standard';
    $merged['nav_style'] = in_array((string)($merged['nav_style'] ?? ''), ['solid', 'outlined', 'glass'], true) ? (string)$merged['nav_style'] : 'solid';
    $merged['nav_variant'] = in_array((string)($merged['nav_variant'] ?? ''), ['primary', 'dark', 'light', 'soft'], true) ? (string)$merged['nav_variant'] : 'light';
    $merged['nav_link_weight'] = in_array((string)($merged['nav_link_weight'] ?? ''), ['300', '400', '500', '600', '700'], true) ? (string)$merged['nav_link_weight'] : '500';
    $merged['nav_font_size'] = preg_match('/^(0|[0-9]+(\.[0-9]+)?(rem|px)|\.[0-9]+rem)$/', (string)($merged['nav_font_size'] ?? '')) ? (string)$merged['nav_font_size'] : '1rem';
    $merged['nav_text_transform'] = in_array((string)($merged['nav_text_transform'] ?? ''), ['none', 'uppercase'], true) ? (string)$merged['nav_text_transform'] : 'none';
    $merged['nav_border_width'] = in_array((string)($merged['nav_border_width'] ?? ''), ['0', '1', '2', '3'], true) ? (string)$merged['nav_border_width'] : '1';
    $merged['pagination_radius'] = preg_match('/^(0|0?\.[0-9]+|[0-9]+(\.[0-9]+)?(rem|px))$/', (string)($merged['pagination_radius'] ?? '')) ? (string)$merged['pagination_radius'] : '999px';
    $merged['pagination_border_width'] = in_array((string)($merged['pagination_border_width'] ?? ''), ['0', '1', '2', '3'], true) ? (string)$merged['pagination_border_width'] : '1';
    $merged['pagination_gap'] = preg_match('/^(0|0?\.[0-9]+|[0-9]+(\.[0-9]+)?(rem|px|em))$/', (string)($merged['pagination_gap'] ?? '')) ? (string)$merged['pagination_gap'] : '.25rem';
    $merged['pagination_font_weight'] = in_array((string)($merged['pagination_font_weight'] ?? ''), ['300', '400', '500', '600', '700'], true) ? (string)$merged['pagination_font_weight'] : '600';
    $merged['pagination_color'] = nx_theme_builder_sanitize_hex((string)($merged['pagination_color'] ?? '#1f6feb'), '#1f6feb');
    $merged['pagination_background'] = nx_theme_builder_sanitize_card_background((string)($merged['pagination_background'] ?? '#ffffff'), '#ffffff');
    $merged['pagination_border_color'] = nx_theme_builder_sanitize_hex((string)($merged['pagination_border_color'] ?? '#d7dee8'), '#d7dee8');
    $merged['pagination_hover_color'] = nx_theme_builder_sanitize_hex((string)($merged['pagination_hover_color'] ?? '#ffffff'), '#ffffff');
    $merged['pagination_hover_background'] = nx_theme_builder_sanitize_card_background((string)($merged['pagination_hover_background'] ?? '#1f6feb'), '#1f6feb');
    $merged['pagination_hover_border_color'] = nx_theme_builder_sanitize_hex((string)($merged['pagination_hover_border_color'] ?? '#1f6feb'), '#1f6feb');
    $merged['pagination_active_color'] = nx_theme_builder_sanitize_hex((string)($merged['pagination_active_color'] ?? '#ffffff'), '#ffffff');
    $merged['pagination_active_background'] = nx_theme_builder_sanitize_card_background((string)($merged['pagination_active_background'] ?? '#1f6feb'), '#1f6feb');
    $merged['pagination_active_border_color'] = nx_theme_builder_sanitize_hex((string)($merged['pagination_active_border_color'] ?? '#1f6feb'), '#1f6feb');
    $merged['color_preset_key'] = preg_match('/^[a-z0-9_-]{1,64}$/', (string)($merged['color_preset_key'] ?? '')) ? (string)$merged['color_preset_key'] : '';
    $merged['nav_width'] = in_array((string)($merged['nav_width'] ?? ''), ['full', 'content'], true) ? (string)$merged['nav_width'] : 'full';
    $merged['nav_radius'] = in_array((string)($merged['nav_radius'] ?? ''), ['0', '8', '12', '18', '20', '24'], true) ? (string)$merged['nav_radius'] : '0';
    $merged['nav_top_spacing'] = in_array((string)($merged['nav_top_spacing'] ?? ''), ['0', '8', '12', '16', '24'], true) ? (string)$merged['nav_top_spacing'] : '0';
    $merged['nav_background'] = nx_theme_builder_sanitize_hex((string)($merged['nav_background'] ?? '#ffffff'), '#ffffff');
    $merged['nav_link'] = nx_theme_builder_sanitize_hex((string)($merged['nav_link'] ?? '#212529'), '#212529');
    $merged['nav_hover'] = nx_theme_builder_sanitize_hex((string)($merged['nav_hover'] ?? '#1f6feb'), '#1f6feb');
    $merged['nav_active'] = nx_theme_builder_sanitize_hex((string)($merged['nav_active'] ?? '#1f6feb'), '#1f6feb');
    $merged['nav_dropdown_background'] = nx_theme_builder_sanitize_hex((string)($merged['nav_dropdown_background'] ?? '#ffffff'), '#ffffff');
    $merged['show_hero'] = !empty($merged['show_hero']);
    $merged['hero_title'] = (string)($merged['hero_title'] ?? $defaults['hero_title']);
    $merged['hero_text'] = (string)($merged['hero_text'] ?? $defaults['hero_text']);
    $merged['cta_label'] = (string)($merged['cta_label'] ?? $defaults['cta_label']);
    $merged['colors'] = [
        'accent' => nx_theme_builder_sanitize_hex((string)($merged['colors']['accent'] ?? $defaults['colors']['accent']), '#1f6feb'),
        'page_top' => nx_theme_builder_sanitize_hex((string)($merged['colors']['page_top'] ?? $defaults['colors']['page_top']), '#1a2230'),
        'page_bg' => nx_theme_builder_sanitize_hex((string)($merged['colors']['page_bg'] ?? $defaults['colors']['page_bg']), '#11151b'),
        'surface' => nx_theme_builder_sanitize_hex((string)($merged['colors']['surface'] ?? $defaults['colors']['surface']), '#ffffff'),
        'text' => nx_theme_builder_sanitize_hex((string)($merged['colors']['text'] ?? $defaults['colors']['text']), '#0f172a'),
    ];

    return $merged;
}

function nx_theme_builder_zones(string $layoutPreset): array
{
    return [
        ['key' => 'top', 'label' => 'Top', 'enabled' => true],
        ['key' => 'undertop', 'label' => 'Unter Top', 'enabled' => true],
        ['key' => 'left', 'label' => 'Linke Sidebar', 'enabled' => true],
        ['key' => 'main', 'label' => 'Hauptinhalt', 'enabled' => true],
        ['key' => 'maintop', 'label' => 'Main Top', 'enabled' => true],
        ['key' => 'mainbottom', 'label' => 'Main Bottom', 'enabled' => true],
        ['key' => 'right', 'label' => 'Rechte Sidebar', 'enabled' => true],
        ['key' => 'bottom', 'label' => 'Bottom', 'enabled' => true],
    ];
}

if (!function_exists('nx_generator_builder_zones')) {
    function nx_generator_builder_zones(string $layoutPreset): array
    {
        return nx_theme_builder_zones($layoutPreset);
    }
}

function nx_theme_builder_write_files(string $themeDir, string $themeName, array $settings): void
{
    $cssDir = $themeDir . '/assets/css';
    $jsDir = $themeDir . '/assets/js';

    if (!is_dir($cssDir)) {
        @mkdir($cssDir, 0755, true);
    }
    if (!is_dir($jsDir)) {
        @mkdir($jsDir, 0755, true);
    }

    $indexFile = $themeDir . '/index.php';
    $layout = <<<'PHP'
<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once BASE_PATH . '/system/core/theme_runtime_renderer.php';
PHP;

    @file_put_contents($indexFile, $layout);

    $cssFile = $cssDir . '/main.css';
    $css = <<<'CSS'
:root {
  --nx-theme-accent: #1f6feb;
  --nx-theme-accent-strong: color-mix(in srgb, var(--nx-theme-accent), black 20%);
  --nx-theme-page-bg: #11151b;
  --nx-theme-page-top: #1a2230;
  --nx-theme-surface: color-mix(in srgb, #ffffff, #f4f7fb 28%);
  --nx-theme-surface-2: #ffffff;
  --nx-theme-divider: rgba(15, 23, 42, 0.08);
  --nx-theme-text: #0f172a;
  --nx-theme-muted: #607086;
  --nx-theme-text-inverse: #f8fbff;
}

body {
  background:
    radial-gradient(circle at top center, color-mix(in srgb, var(--nx-theme-accent), transparent 78%), transparent 28rem),
    linear-gradient(180deg, var(--nx-theme-page-top) 0, var(--nx-theme-page-bg) 24rem, var(--nx-theme-page-bg) 24rem, var(--nx-theme-page-bg) 100%);
  color: var(--nx-theme-text);
}

.nx-theme-shell { min-height: 60vh; padding: 1.5rem 0 3rem; }
.nx-theme-hero { margin-bottom: 2rem; }
.nx-theme-hero-panel {
  color: var(--nx-theme-text-inverse);
  padding: clamp(1.5rem, 3vw, 2.5rem);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 1.75rem;
  background:
    linear-gradient(135deg, rgba(255,255,255,.06), rgba(255,255,255,.02)),
    linear-gradient(160deg, rgba(17,21,27,.92), rgba(23,29,40,.92));
  box-shadow: 0 24px 64px rgba(3, 7, 18, 0.28);
}
.nx-theme-hero h1 { font-size: clamp(2.4rem, 4vw, 4.6rem); font-weight: 800; letter-spacing: -0.04em; line-height: 0.95; margin-bottom: 0.9rem; }
.nx-theme-hero .lead { max-width: 56rem; color: rgba(248, 251, 255, 0.8); font-size: 1.05rem; }
.nx-theme-kicker { display:inline-block; margin-bottom:1rem; padding:.42rem .8rem; border-radius:999px; background: color-mix(in srgb, var(--nx-theme-accent), transparent 82%); color:#d8e8ff; font-size:.78rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
.nx-theme-actions { display:flex; flex-wrap:wrap; gap:.75rem; margin-top:1.5rem; }
.nx-theme-status { display:flex; flex-wrap:wrap; gap:.75rem 1rem; align-items:center; margin-top:1.5rem; padding-top:1rem; border-top:1px solid rgba(255,255,255,.12); color:rgba(248,251,255,.76); font-size:.95rem; }
.nx-theme-status code { color:#fff; }
.nx-theme-zone, .nx-theme-content { margin-bottom:1.5rem; }
.nx-theme-surface, .nx-theme-content .card, .nx-theme-content .widget, .nx-theme-content .module, .nx-theme-content .forumlastposts, .nx-theme-content .card-body, .nx-theme-content .panel {
  background: var(--nx-theme-surface-2); color: var(--nx-theme-text); border-radius: 1.25rem;
}
.nx-theme-main-surface { padding:1.25rem; border:1px solid var(--nx-theme-divider); box-shadow:0 24px 60px rgba(15,23,42,.08); }
.nx-theme-content aside > *, .nx-theme-zone > *, .nx-theme-content .row > [class*="col-"] > * { margin-bottom:1rem; }
.nx-theme-content .card, .nx-theme-content .widget, .nx-theme-content .module, .nx-theme-content .panel { border:1px solid var(--nx-theme-divider); box-shadow:0 12px 28px rgba(15,23,42,.06); }
.nx-theme-content .card-header, .nx-theme-content .panel-heading, .nx-theme-content .head-boxes { background: var(--nx-theme-surface); color: var(--nx-theme-text); border-bottom: 1px solid var(--nx-theme-divider); }
.nx-theme-content, .nx-theme-content p, .nx-theme-content li, .nx-theme-content span, .nx-theme-content small, .nx-theme-content strong, .nx-theme-content em, .nx-theme-content label, .nx-theme-content td, .nx-theme-content th, .nx-theme-content h1, .nx-theme-content h2, .nx-theme-content h3, .nx-theme-content h4, .nx-theme-content h5, .nx-theme-content h6, .nx-theme-content .card-title, .nx-theme-content .card-text, .nx-theme-content .widget-title, .nx-theme-content .module-title, .nx-theme-content .text-body, .nx-theme-content .text-dark, .nx-theme-content .text-muted { color: var(--nx-theme-text) !important; }
.nx-theme-content input, .nx-theme-content textarea, .nx-theme-content select { color: var(--nx-theme-text); }
.nx-theme-content a { color: var(--nx-theme-accent-strong); }
.nx-theme-content a:hover { color: var(--nx-theme-accent); }
.btn-primary, .nx-theme-hero .btn-primary { background: var(--nx-theme-accent); border-color: var(--nx-theme-accent); color:#fff; }
.btn-primary:hover, .btn-primary:focus, .nx-theme-hero .btn-primary:hover, .nx-theme-hero .btn-primary:focus { background: var(--nx-theme-accent-strong); border-color: var(--nx-theme-accent-strong); }
.nx-theme-hero .btn-outline-light { border-color: rgba(255,255,255,.26); color:#fff; }
.nx-theme-hero .btn-outline-light:hover, .nx-theme-hero .btn-outline-light:focus { background: rgba(255,255,255,.08); color:#fff; }
.sticky-footer-wrapper { background: transparent; }
@media (max-width: 991.98px) { .nx-theme-shell { padding-top:1rem; } .nx-theme-main-surface { padding:1rem; } }
@media (max-width: 767.98px) { .nx-theme-hero-panel { border-radius:1.25rem; } }
CSS;
    @file_put_contents($cssFile, $css);

    $jsFile = $jsDir . '/main.js';
    @file_put_contents($jsFile, "document.documentElement.classList.add('nx-theme-generated');\n");
}
