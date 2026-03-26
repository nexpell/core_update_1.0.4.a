<?php
/**
 * Basis-Design (Theme-Optionen).
 * Speichert in navigation_website_settings mit Keys "theme_*".
 * Ausgabe als CSS Custom Properties (Bootstrap 5 + eigene) für Frontend & Live-Builder.
 */

if (!function_exists('nx_get_theme_options')) {

    function nx_theme_opt_parse_hex_rgb(string $value): ?array
    {
        $value = trim($value);
        if (preg_match('/^#([0-9a-fA-F]{6})$/', $value, $m)) {
            return [
                hexdec(substr($m[1], 0, 2)),
                hexdec(substr($m[1], 2, 2)),
                hexdec(substr($m[1], 4, 2)),
            ];
        }
        if (preg_match('/^#([0-9a-fA-F]{3})$/', $value, $m)) {
            return [
                hexdec(str_repeat($m[1][0], 2)),
                hexdec(str_repeat($m[1][1], 2)),
                hexdec(str_repeat($m[1][2], 2)),
            ];
        }
        return null;
    }

    function nx_theme_opt_auto_surface_color(string $background): string
    {
        $rgb = nx_theme_opt_parse_hex_rgb($background);
        if (!$rgb) {
            return '#ffffff';
        }
        [$r, $g, $b] = $rgb;
        $luminance = ((0.2126 * $r) + (0.7152 * $g) + (0.0722 * $b)) / 255;
        if ($luminance < 0.45) {
            return 'rgba(255,255,255,0.08)';
        }
        if ($luminance < 0.7) {
            return 'rgba(255,255,255,0.78)';
        }
        return 'rgba(255,255,255,0.96)';
    }

    function nx_get_theme_options(): array
    {
        global $_database;
        if (!isset($_database) || !($_database instanceof \mysqli)) {
            return [];
        }
        $stmt = $_database->prepare("
            SELECT setting_key, setting_value
            FROM navigation_website_settings
            WHERE setting_key LIKE 'theme_%'
        ");
        if (!$stmt || !$stmt->execute()) {
            return [];
        }
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[(string)$row['setting_key']] = (string)$row['setting_value'];
        }
        $stmt->close();
        return $out;
    }

    function nx_get_theme_option(string $key, string $default = ''): string
    {
        $key = 'theme_' . ltrim($key, 'theme_');
        $all = nx_get_theme_options();
        return isset($all[$key]) ? trim($all[$key]) : $default;
    }

    /**
     * @param array<string, string> $options Keys mit theme_*
     */
    function nx_set_theme_options(array $options): void
    {
        global $_database;
        if (!isset($_database) || !($_database instanceof \mysqli)) {
            return;
        }
        $stmt = $_database->prepare("
            INSERT INTO navigation_website_settings (setting_key, setting_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        if (!$stmt) {
            return;
        }
        foreach ($options as $k => $v) {
            if (strpos($k, 'theme_') !== 0) {
                continue;
            }
            $v = is_string($v) ? trim($v) : '';
            $stmt->bind_param('ss', $k, $v);
            $stmt->execute();
        }
        $stmt->close();
    }

    /**
     * Gibt <style id="nx-theme-options"> mit Bootstrap-5- und body-Variablen aus.
     */
    function nx_render_theme_options_css(): string
    {
        $opts = nx_get_theme_options();
        $get = static function (string $k, string $def = '') use ($opts): string {
            return isset($opts[$k]) ? trim($opts[$k]) : $def;
        };
        $css = "/* Basis-Design (Live-Builder) */\n";
        $css .= ":root {\n";
        $vars = [];
        if ($get('theme_bg_color') !== '') {
            $vars[] = '  --bs-body-bg: ' . nx_theme_opt_sanitize_css_value($get('theme_bg_color'), 'color') . ';';
        }
        if ($get('theme_text_color') !== '') {
            $vars[] = '  --bs-body-color: ' . nx_theme_opt_sanitize_css_value($get('theme_text_color'), 'color') . ';';
        }
        if ($get('theme_primary') !== '') {
            $vars[] = '  --bs-primary: ' . nx_theme_opt_sanitize_css_value($get('theme_primary'), 'color') . ';';
            $vars[] = '  --bs-primary-rgb: ' . (function () use ($get) {
                $c = $get('theme_primary');
                if (preg_match('/^#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/', $c, $m)) {
                    return (int)hexdec($m[1]) . ', ' . (int)hexdec($m[2]) . ', ' . (int)hexdec($m[3]);
                }
                return '13, 110, 253';
            })() . ';';
        }
        if ($get('theme_secondary') !== '') {
            $vars[] = '  --bs-secondary: ' . nx_theme_opt_sanitize_css_value($get('theme_secondary'), 'color') . ';';
            $sec = $get('theme_secondary');
            if (preg_match('/^#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/', $sec, $m)) {
                $vars[] = '  --bs-secondary-rgb: ' . (int)hexdec($m[1]) . ',' . (int)hexdec($m[2]) . ',' . (int)hexdec($m[3]) . ';';
            }
        }
        if ($get('theme_link_color') !== '') {
            $linkColorVal = nx_theme_opt_sanitize_css_value($get('theme_link_color'), 'color');
            $vars[] = '  --bs-link-color: ' . $linkColorVal . ';';
            $vars[] = '  --bs-nav-link-color: ' . $linkColorVal . ';';
        }
        if ($get('theme_link_decoration') !== '') {
            $vars[] = '  --bs-link-decoration: ' . preg_replace('/[^a-z\-]/', '', $get('theme_link_decoration')) . ';';
        }
        if ($get('theme_link_hover_color') !== '') {
            $linkHoverColorVal = nx_theme_opt_sanitize_css_value($get('theme_link_hover_color'), 'color');
            $vars[] = '  --bs-link-hover-color: ' . $linkHoverColorVal . ';';
            $vars[] = '  --bs-nav-link-hover-color: ' . $linkHoverColorVal . ';';
        } else {
            $vars[] = '  --bs-link-hover-color: var(--bs-primary);';
            $vars[] = '  --bs-nav-link-hover-color: var(--bs-primary);';
        }
        if ($get('theme_link_hover_decoration') !== '') {
            $vars[] = '  --bs-link-hover-decoration: ' . preg_replace('/[^a-z\-]/', '', $get('theme_link_hover_decoration')) . ';';
        }
        if ($get('theme_font_size') !== '') {
            $vars[] = '  --bs-body-font-size: ' . nx_theme_opt_sanitize_css_value($get('theme_font_size'), 'size') . ';';
        }
        $surface = '';
        if ($get('theme_surface_bg') !== '') {
            $surface = nx_theme_opt_sanitize_css_value($get('theme_surface_bg'), 'color');
        } elseif ($get('theme_bg_color') !== '') {
            $surface = nx_theme_opt_auto_surface_color(nx_theme_opt_sanitize_css_value($get('theme_bg_color'), 'color'));
        }
        if ($surface !== '') {
            $vars[] = '  --nx-surface-bg: ' . $surface . ';';
            $vars[] = '  --bs-card-bg: ' . $surface . ';';
            $vars[] = '  --bs-secondary-bg: ' . $surface . ';';
            $vars[] = '  --bs-tertiary-bg: ' . $surface . ';';
        }
        $css .= implode("\n", $vars) . "\n}\n";
        $bg  = $get('theme_bg_color') !== '' ? nx_theme_opt_sanitize_css_value($get('theme_bg_color'), 'color') : '';
        $txt = $get('theme_text_color') !== '' ? nx_theme_opt_sanitize_css_value($get('theme_text_color'), 'color') : '';
        if ($bg !== '' || $txt !== '') {
            $css .= "html, html body, body, .sticky-footer-wrapper { ";
            if ($bg !== '') {
                $css .= "background-color: {$bg} !important; ";
            }
            if ($txt !== '') {
                $css .= "color: {$txt} !important; ";
            }
            $css .= "}\n";
            $css .= "main.flex-fill, .sticky-footer-wrapper > main.flex-fill > .container, .sticky-footer-wrapper [data-nx-zone=\"content\"] { ";
            if ($bg !== '') {
                $css .= "background-color: {$bg} !important; ";
            }
            if ($txt !== '') {
                $css .= "color: {$txt} !important; ";
            }
            $css .= "}\n";
        }
        if ($surface !== '') {
            $css .= ".card,.accordion-item,.list-group-item,.dropdown-menu,.modal-content,.offcanvas,.nx-surface{ background-color: {$surface} !important; }\n";
            $css .= ".card,.accordion-item,.list-group-item,.modal-content,.offcanvas{ border-color: rgba(0,0,0,.08) !important; }\n";
        }
        /* Live-Builder: Hintergrundfarbe nur in der Content-Zone (zwischen Header und Footer), nicht auf html/body */
        if ($bg !== '') {
            $css .= "body.builder-active html, body.builder-active, body.builder-active body, body.builder-active .sticky-footer-wrapper { background-color: #fff !important; }\n";
            $css .= "body.builder-active main.flex-fill, body.builder-active main.flex-fill > .container, body.builder-active [data-nx-zone=\"content\"], body.builder-active .index-page, body.builder-active .nx-imported-content { background-color: {$bg} !important; border: 1px solid #c5c5c5; }\n";
        }
        $css .= "html body a { color: var(--bs-link-color, inherit); text-decoration: var(--bs-link-decoration, none) !important; }\n";
        $css .= "html body a:hover, html body a:focus { color: var(--bs-link-hover-color, var(--bs-primary)); text-decoration: var(--bs-link-hover-decoration, var(--bs-link-decoration, none)) !important; }\n";
        // Einheitliche Hero-Höhen (greifen in Frontend & Live-Builder)
        $css .= ".nx-hero-h-40{min-height:40vh;display:flex;align-items:center;}\n";
        $css .= ".nx-hero-h-50{min-height:50vh;display:flex;align-items:center;}\n";
        $css .= ".nx-hero-h-60{min-height:60vh;display:flex;align-items:center;}\n";
        $css .= ".nx-hero-h-80{min-height:80vh;display:flex;align-items:center;}\n";
        $css .= ".nx-hero-h-100{min-height:100vh;display:flex;align-items:center;}\n";
        return '<style id="nx-theme-options">' . $css . '</style>';
    }

    function nx_render_theme_options_google_fonts_link(): string
    {
        $families = nx_get_theme_option('google_fonts', '');
        if ($families === '') {
            return '';
        }
        $list = array_filter(array_map('trim', explode(',', $families)));
        if (empty($list)) {
            return '';
        }
        $param = [];
        foreach ($list as $f) {
            $param[] = 'family=' . rawurlencode($f) . ':wght@400;600;700';
        }
        $url = 'https://fonts.googleapis.com/css2?' . implode('&', $param) . '&display=swap';
        return '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" id="nx-google-fonts">';
    }

    function nx_theme_opt_sanitize_css_value(string $value, string $type = ''): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if ($type === 'color') {
            if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
                return $value;
            }
            if (preg_match('/^([0-9a-fA-F]{6})$/', $value)) {
                return '#' . $value;
            }
            if (preg_match('/^rgb\(|^rgba\(|^hsl\(|^hsla\(/', $value)) {
                return preg_replace('/[^a-z0-9(),.%\s\-]/', '', $value);
            }
        }
        if ($type === 'size') {
            if (preg_match('/^\d+(\.\d+)?(rem|em|px|%)$/', $value)) {
                return $value;
            }
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
