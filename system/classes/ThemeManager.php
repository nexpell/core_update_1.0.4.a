<?php

namespace nexpell;

class ThemeManager
{
    private \mysqli $database;
    private string $themesRoot;
    private string $themesWebRoot;
    private ?array $activeThemeRow = null;
    private ?array $activeManifest = null;

    public function __construct(\mysqli $database, ?string $themesRoot = null, string $themesWebRoot = '/includes/themes')
    {
        $this->database = $database;
        $this->themesRoot = rtrim($themesRoot ?? dirname(__DIR__, 2) . '/includes/themes', '/\\');
        $this->themesWebRoot = rtrim($themesWebRoot, '/');
    }

    public function ensureSchema(): void
    {
        // Theme runtime is builder-driven via navigation_website_settings.
    }

    public function getActiveThemeRow(): array
    {
        if ($this->activeThemeRow !== null) {
            return $this->activeThemeRow;
        }

        $settings = $this->getWebsiteSettings();
        $this->activeThemeRow = [
            'themeID' => 1,
            'name' => 'Default',
            'modulname' => 'default',
            'slug' => 'default',
            'pfad' => 'default',
            'manifest_path' => 'includes/themes/default/theme.json',
            'layout_file' => 'index.php',
            'preview_image' => 'images/default_logo.png',
            'description' => 'Builder-driven default theme.',
            'themename' => 'default',
            'navbar_class' => (string)($settings['navbar_class'] ?? 'bg-light'),
            'navbar_theme' => (string)($settings['navbar_theme'] ?? $settings['navbar_modus'] ?? 'light'),
            'logo_pic' => (string)($settings['logo_light'] ?? 'default_logo.png'),
            'reg_pic' => (string)($settings['register_bg'] ?? 'default_login_bg.jpg'),
            'headlines' => (string)($settings['headstyle_variant'] ?? 'headlines_03.css'),
        ];

        return $this->activeThemeRow;
    }

    public function getActiveThemeSlug(): string
    {
        return 'default';
    }

    public function getActiveThemeFolder(): string
    {
        return 'default';
    }

    public function getActiveThemePath(): string
    {
        return $this->themesRoot . '/default';
    }

    public function getActiveThemeRelativePath(): string
    {
        return 'includes/themes/default';
    }

    public function getActiveThemeWebPath(): string
    {
        return $this->themesWebRoot . '/default';
    }

    public function getActiveManifest(): array
    {
        if ($this->activeManifest !== null) {
            return $this->activeManifest;
        }

        $row = $this->getActiveThemeRow();
        $manifestPath = $this->getActiveThemePath() . '/theme.json';
        $manifest = [];

        if (file_exists($manifestPath)) {
            $decoded = json_decode((string)file_get_contents($manifestPath), true);
            if (is_array($decoded)) {
                $manifest = $decoded;
            }
        }

        $this->activeManifest = $this->mergeWithDefaults($manifest, $row);
        return $this->activeManifest;
    }

    public function getAllThemes(): array
    {
        $theme = $this->getActiveManifest();
        $theme['active'] = true;
        $theme['path'] = $this->getActiveThemePath();
        $theme['web_path'] = $this->getActiveThemeWebPath();
        return [$theme];
    }

    public function getThemeBySlug(string $slug): ?array
    {
        $slug = strtolower(trim($slug));
        if ($slug !== '' && $slug !== 'default') {
            return null;
        }

        $theme = $this->getActiveManifest();
        $theme['active'] = true;
        $theme['path'] = $this->getActiveThemePath();
        $theme['web_path'] = $this->getActiveThemeWebPath();
        return $theme;
    }

    public function getAssetTags(): array
    {
        $manifest = $this->getActiveManifest();
        $webPath = $this->getActiveThemeWebPath();

        $coreCss = [
            '/components/bootstrap/css/bootstrap.min.css',
            '/components/bootstrap/css/bootstrap-icons.min.css',
            '/components/css/page.css',
        ];
        if (empty($manifest['settings']['self_hosted_headstyles'])) {
            $coreCss[] = '/components/css/headstyles.css';
        }
        $coreJs = [
            '/components/jquery/jquery.min.js',
            '/components/bootstrap/js/bootstrap.bundle.min.js',
            '/components/cookie/cookie-consent.js',
        ];

        $themeCss = [];
        $themeJs = [];

        foreach (($manifest['assets']['css'] ?? []) as $asset) {
            $themeCss[] = $this->normalizeThemeAssetPath((string)$asset, $webPath);
        }
        foreach (($manifest['assets']['js'] ?? []) as $asset) {
            $themeJs[] = $this->normalizeThemeAssetPath((string)$asset, $webPath);
        }

        return [
            'css' => array_values(array_unique(array_merge($coreCss, array_filter($themeCss)))),
            'js' => array_values(array_unique(array_merge($coreJs, array_filter($themeJs)))),
        ];
    }

    public function getTemplateDirectory(): string
    {
        $manifest = $this->getActiveManifest();
        return trim((string)($manifest['template_dir'] ?? 'templates'), '/\\');
    }

    public function getLayoutFile(): string
    {
        $manifest = $this->getActiveManifest();
        return trim((string)($manifest['layout']['file'] ?? 'index.php'), '/\\');
    }

    public function getLayoutPath(): string
    {
        return $this->getActiveThemePath() . '/' . $this->getLayoutFile();
    }

    public function getFaviconPaths(): array
    {
        $manifest = $this->getActiveManifest();
        $webRoot = $this->getActiveThemeWebPath();
        $fallback = $webRoot . '/images';
        $favicons = $manifest['favicons'] ?? [];

        return [
            'ico' => $this->normalizeThemeAssetPath((string)($favicons['ico'] ?? 'images/favicon.ico'), $webRoot),
            'png32' => $this->normalizeThemeAssetPath((string)($favicons['png32'] ?? 'images/favicon-32.png'), $webRoot),
            'png192' => $this->normalizeThemeAssetPath((string)($favicons['png192'] ?? 'images/favicon-192.png'), $webRoot),
            'apple180' => $this->normalizeThemeAssetPath((string)($favicons['apple180'] ?? 'images/favicon-180.png'), $webRoot),
            'fallback_root' => $fallback,
        ];
    }

    public function getOption(string $key, ?string $themeSlug = null, ?string $default = null): ?string
    {
        $settings = $this->getWebsiteSettings();
        if (array_key_exists($key, $settings)) {
            return (string)$settings[$key];
        }

        $prefixedKey = 'theme_' . ltrim($key, '_');
        if (array_key_exists($prefixedKey, $settings)) {
            return (string)$settings[$prefixedKey];
        }

        return $default;
    }

    public function saveOptions(string $themeSlug, array $options): void
    {
        $stmt = $this->database->prepare(
            "INSERT INTO `navigation_website_settings` (`setting_key`, `setting_value`)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)"
        );
        if (!$stmt) {
            return;
        }

        foreach ($options as $key => $value) {
            $optionKey = (string)$key;
            $optionValue = (string)$value;
            $stmt->bind_param('ss', $optionKey, $optionValue);
            $stmt->execute();
        }

        $stmt->close();
    }

    public function saveManifest(string $slug, array $manifest): bool
    {
        $slug = strtolower(trim($slug));
        if ($slug !== '' && $slug !== 'default') {
            return false;
        }

        $themePath = $this->getActiveThemePath();
        if (!is_dir($themePath)) {
            return false;
        }

        $manifest['slug'] = 'default';
        if (empty($manifest['name'])) {
            $manifest['name'] = 'Default';
        }

        $json = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }

        return file_put_contents($themePath . '/theme.json', $json . PHP_EOL) !== false;
    }

    public function syncInstalledThemeRecord(string $slug, array $manifest, array $meta = []): bool
    {
        return strtolower(trim($slug)) === 'default';
    }

    public function activateTheme(string $slug): bool
    {
        $slug = strtolower(trim($slug));
        $this->activeThemeRow = null;
        $this->activeManifest = null;
        return $slug === '' || $slug === 'default';
    }

    private function mergeWithDefaults(array $manifest, array $row): array
    {
        $preview = trim((string)($manifest['preview'] ?? $row['preview_image'] ?? ''));
        if ($preview === '') {
            $preview = 'images/default_logo.png';
        }

        return array_replace_recursive(
            [
                'name' => (string)($row['name'] ?? 'Default'),
                'slug' => 'default',
                'folder' => 'default',
                'version' => '1.0.0',
                'author' => '',
                'url' => '',
                'description' => (string)($row['description'] ?? ''),
                'preview' => $preview,
                'template_dir' => 'templates',
                'layout' => [
                    'file' => (string)($row['layout_file'] ?? 'index.php'),
                ],
                'assets' => [
                    'css' => [],
                    'js' => [],
                ],
                'favicons' => [
                    'ico' => 'images/favicon.ico',
                    'png32' => 'images/favicon-32.png',
                    'png192' => 'images/favicon-192.png',
                    'apple180' => 'images/favicon-180.png',
                ],
                'settings' => [],
            ],
            $manifest
        );
    }

    private function normalizeThemeAssetPath(string $asset, string $webPath): string
    {
        $asset = trim($asset);
        if ($asset === '') {
            return '';
        }
        if (preg_match('#^(https?:)?//#i', $asset) || str_starts_with($asset, '/')) {
            return $asset;
        }
        return $webPath . '/' . ltrim($asset, '/');
    }

    private function getWebsiteSettings(): array
    {
        $settings = [];
        $result = $this->database->query("SELECT setting_key, setting_value FROM navigation_website_settings");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $settings[(string)$row['setting_key']] = (string)$row['setting_value'];
            }
            $result->free();
        }
        return $settings;
    }
}
