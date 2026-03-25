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
        $columns = [
            'slug' => "ALTER TABLE `settings_themes` ADD COLUMN `slug` varchar(120) NOT NULL DEFAULT 'default' AFTER `modulname`",
            'manifest_path' => "ALTER TABLE `settings_themes` ADD COLUMN `manifest_path` varchar(255) DEFAULT NULL AFTER `pfad`",
            'layout_file' => "ALTER TABLE `settings_themes` ADD COLUMN `layout_file` varchar(255) DEFAULT NULL AFTER `manifest_path`",
            'preview_image' => "ALTER TABLE `settings_themes` ADD COLUMN `preview_image` varchar(255) DEFAULT NULL AFTER `layout_file`",
            'description' => "ALTER TABLE `settings_themes` ADD COLUMN `description` text DEFAULT NULL AFTER `preview_image`",
        ];

        foreach ($columns as $column => $sql) {
            if (!$this->columnExists('settings_themes', $column)) {
                $this->database->query($sql);
            }
        }

        $this->database->query(
            "CREATE TABLE IF NOT EXISTS `settings_theme_options` (
                `optionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `theme_slug` varchar(120) NOT NULL,
                `option_key` varchar(120) NOT NULL,
                `option_value` longtext DEFAULT NULL,
                `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`optionID`),
                UNIQUE KEY `uniq_theme_option` (`theme_slug`,`option_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        if ($this->tableExists('settings_themes')) {
            $this->database->query(
                "UPDATE `settings_themes`
                 SET `slug` = LOWER(COALESCE(NULLIF(`slug`, ''), NULLIF(`modulname`, ''), NULLIF(`pfad`, ''), 'default'))"
            );
            $this->database->query(
                "UPDATE `settings_themes`
                 SET `manifest_path` = CONCAT('includes/themes/', `pfad`, '/theme.json')
                 WHERE (`manifest_path` IS NULL OR `manifest_path` = '') AND `pfad` <> ''"
            );
        }
    }

    public function getActiveThemeRow(): array
    {
        if ($this->activeThemeRow !== null) {
            return $this->activeThemeRow;
        }

        $result = $this->database->query("SELECT * FROM `settings_themes` WHERE `active` = 1 ORDER BY `themeID` ASC LIMIT 1");
        $row = $result ? $result->fetch_assoc() : null;

        if (!$row) {
            $row = [
                'themeID' => 0,
                'name' => 'Default',
                'modulname' => 'default',
                'slug' => 'default',
                'pfad' => 'default',
                'manifest_path' => 'includes/themes/default/theme.json',
                'layout_file' => 'index.php',
                'preview_image' => null,
                'description' => null,
                'themename' => 'yeti',
                'navbar_class' => 'bg-dark',
                'navbar_theme' => 'dark',
                'logo_pic' => 'default_logo.png',
                'reg_pic' => 'default_login_bg.jpg',
                'headlines' => 'headlines_03.css',
            ];
        }

        if (empty($row['slug'])) {
            $row['slug'] = !empty($row['modulname']) ? strtolower((string)$row['modulname']) : strtolower((string)$row['pfad']);
        }
        if (empty($row['pfad'])) {
            $row['pfad'] = $row['slug'] ?: 'default';
        }
        if (empty($row['manifest_path'])) {
            $row['manifest_path'] = 'includes/themes/' . $row['pfad'] . '/theme.json';
        }

        $this->activeThemeRow = $row;
        return $row;
    }

    public function getActiveThemeSlug(): string
    {
        return (string)($this->getActiveThemeRow()['slug'] ?? 'default');
    }

    public function getActiveThemeFolder(): string
    {
        return (string)($this->getActiveThemeRow()['pfad'] ?? 'default');
    }

    public function getActiveThemePath(): string
    {
        return $this->themesRoot . '/' . $this->getActiveThemeFolder();
    }

    public function getActiveThemeRelativePath(): string
    {
        return 'includes/themes/' . $this->getActiveThemeFolder();
    }

    public function getActiveThemeWebPath(): string
    {
        return $this->themesWebRoot . '/' . $this->getActiveThemeFolder();
    }

    public function getActiveManifest(): array
    {
        if ($this->activeManifest !== null) {
            return $this->activeManifest;
        }

        $row = $this->getActiveThemeRow();
        $manifestPath = $this->getActiveThemePath() . '/theme.json';
        if (!empty($row['manifest_path'])) {
            $candidate = dirname(__DIR__, 2) . '/' . ltrim((string)$row['manifest_path'], '/');
            if (file_exists($candidate)) {
                $manifestPath = $candidate;
            }
        }

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
        $themes = [];
        if (!is_dir($this->themesRoot)) {
            return $themes;
        }

        $directories = scandir($this->themesRoot);
        if (!is_array($directories)) {
            return $themes;
        }

        $activeSlug = $this->getActiveThemeSlug();
        foreach ($directories as $directory) {
            if ($directory === '.' || $directory === '..') {
                continue;
            }

            $themePath = $this->themesRoot . '/' . $directory;
            if (!is_dir($themePath)) {
                continue;
            }

            $manifest = [];
            $manifestFile = $themePath . '/theme.json';
            if (file_exists($manifestFile)) {
                $decoded = json_decode((string)file_get_contents($manifestFile), true);
                if (is_array($decoded)) {
                    $manifest = $decoded;
                }
            }

            $theme = $this->mergeWithDefaults($manifest, [
                'slug' => $directory,
                'pfad' => $directory,
                'name' => ucfirst($directory),
                'themename' => 'yeti',
                'navbar_class' => 'bg-dark',
                'navbar_theme' => 'dark',
                'logo_pic' => 'default_logo.png',
                'reg_pic' => 'default_login_bg.jpg',
                'headlines' => 'headlines_03.css',
            ]);

            $theme['active'] = $theme['slug'] === $activeSlug;
            $theme['path'] = $themePath;
            $theme['web_path'] = $this->themesWebRoot . '/' . $directory;
            $themes[] = $theme;
        }

        usort($themes, static function (array $a, array $b): int {
            return strcasecmp((string)$a['name'], (string)$b['name']);
        });

        return $themes;
    }

    public function getThemeBySlug(string $slug): ?array
    {
        $slug = strtolower(trim($slug));
        if ($slug === '') {
            return null;
        }

        foreach ($this->getAllThemes() as $theme) {
            if (strtolower((string)($theme['slug'] ?? '')) === $slug) {
                return $theme;
            }
        }

        return null;
    }

    public function getAssetTags(): array
    {
        $manifest = $this->getActiveManifest();
        $webPath = $this->getActiveThemeWebPath();

        $coreCss = [
            '/components/bootstrap/css/bootstrap.min.css',
            '/components/bootstrap/css/bootstrap-icons.min.css',
            '/components/css/page.css',
            '/includes/plugins/navigation/css/navigation.css',
            '/includes/plugins/footer/css/footer.css',
        ];
        if (empty($manifest['settings']['self_hosted_headstyles'])) {
            $coreCss[] = '/components/css/headstyles.css';
        }
        $coreJs = [
            '/components/jquery/jquery.min.js',
            '/components/bootstrap/js/bootstrap.bundle.min.js',
            '/components/cookie/cookie-consent.js',
            '/includes/plugins/navigation/js/navigation.js',
            '/includes/plugins/footer/js/footer.js',
        ];

        $themeCss = [];
        $themeJs = [];

        if (!empty($manifest['settings']['supports_bootswatch_variant'])) {
            $themeVariant = (string)($this->getActiveThemeRow()['themename'] ?? 'yeti');
            $themeCss[] = $this->normalizeThemeAssetPath('css/dist/' . $themeVariant . '/bootstrap.min.css', $webPath);
        }

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
        $themeSlug = $themeSlug ?: $this->getActiveThemeSlug();
        $stmt = $this->database->prepare(
            "SELECT `option_value` FROM `settings_theme_options` WHERE `theme_slug` = ? AND `option_key` = ? LIMIT 1"
        );
        if (!$stmt) {
            return $default;
        }
        $stmt->bind_param('ss', $themeSlug, $key);
        $stmt->execute();
        $stmt->bind_result($value);
        $found = $stmt->fetch();
        $stmt->close();
        return $found ? (string)$value : $default;
    }

    public function saveOptions(string $themeSlug, array $options): void
    {
        $stmt = $this->database->prepare(
            "INSERT INTO `settings_theme_options` (`theme_slug`, `option_key`, `option_value`)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE `option_value` = VALUES(`option_value`)"
        );

        if (!$stmt) {
            return;
        }

        foreach ($options as $key => $value) {
            $optionKey = (string)$key;
            $optionValue = (string)$value;
            $stmt->bind_param('sss', $themeSlug, $optionKey, $optionValue);
            $stmt->execute();
        }

        $stmt->close();
    }

    public function saveManifest(string $slug, array $manifest): bool
    {
        $slug = strtolower(trim($slug));
        if ($slug === '') {
            return false;
        }

        $themePath = $this->themesRoot . '/' . $slug;
        if (!is_dir($themePath)) {
            return false;
        }

        $manifest['slug'] = $slug;
        if (empty($manifest['name'])) {
            $manifest['name'] = ucfirst($slug);
        }

        $json = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }

        $json .= PHP_EOL;
        return file_put_contents($themePath . '/theme.json', $json) !== false;
    }

    public function syncInstalledThemeRecord(string $slug, array $manifest, array $meta = []): bool
    {
        $slug = strtolower(trim($slug));
        if ($slug === '') {
            return false;
        }

        $name = (string)($meta['name'] ?? $manifest['name'] ?? ucfirst($slug));
        $version = (string)($meta['version'] ?? $manifest['version'] ?? '1.0.0');
        $author = (string)($meta['author'] ?? $manifest['author'] ?? 'nexpell');
        $url = (string)($meta['url'] ?? $manifest['url'] ?? '');
        $description = (string)($meta['description'] ?? $manifest['description'] ?? '');

        $stmt = $this->database->prepare("SELECT `themeID` FROM `settings_themes_installed` WHERE `folder` = ? OR `modulname` = ? LIMIT 1");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ss', $slug, $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if ($existing) {
            $stmt = $this->database->prepare(
                "UPDATE `settings_themes_installed`
                 SET `name` = ?, `modulname` = ?, `version` = ?, `author` = ?, `url` = ?, `folder` = ?, `description` = ?, `installed_date` = NOW()
                 WHERE `themeID` = ?"
            );
            if (!$stmt) {
                return false;
            }

            $themeID = (int)$existing['themeID'];
            $stmt->bind_param('sssssssi', $name, $slug, $version, $author, $url, $slug, $description, $themeID);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        }

        $stmt = $this->database->prepare(
            "INSERT INTO `settings_themes_installed`
                (`name`, `modulname`, `version`, `author`, `url`, `folder`, `description`, `installed_date`)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('sssssss', $name, $slug, $version, $author, $url, $slug, $description);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function activateTheme(string $slug): bool
    {
        $slug = strtolower(trim($slug));
        if ($slug === '') {
            return false;
        }

        $themeFolder = $this->themesRoot . '/' . $slug;
        if (!is_dir($themeFolder)) {
            return false;
        }

        $manifest = [];
        $manifestFile = $themeFolder . '/theme.json';
        if (file_exists($manifestFile)) {
            $decoded = json_decode((string)file_get_contents($manifestFile), true);
            if (is_array($decoded)) {
                $manifest = $decoded;
            }
        }

        $name = (string)($manifest['name'] ?? ucfirst($slug));
        $description = (string)($manifest['description'] ?? '');
        $preview = (string)($manifest['preview'] ?? '');
        $layout = (string)($manifest['layout']['file'] ?? 'index.php');

        $this->database->query("UPDATE `settings_themes` SET `active` = 0");

        $stmt = $this->database->prepare("SELECT `themeID` FROM `settings_themes` WHERE `slug` = ? OR `pfad` = ? LIMIT 1");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ss', $slug, $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if ($row) {
            $stmt = $this->database->prepare(
                "UPDATE `settings_themes`
                 SET `name` = ?, `modulname` = ?, `slug` = ?, `pfad` = ?, `manifest_path` = ?, `layout_file` = ?,
                     `preview_image` = ?, `description` = ?, `active` = 1
                 WHERE `themeID` = ?"
            );
            if (!$stmt) {
                return false;
            }
            $manifestPath = 'includes/themes/' . $slug . '/theme.json';
            $themeID = (int)$row['themeID'];
            $stmt->bind_param('ssssssssi', $name, $slug, $slug, $slug, $manifestPath, $layout, $preview, $description, $themeID);
            $ok = $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $this->database->prepare(
                "INSERT INTO `settings_themes`
                    (`name`, `modulname`, `slug`, `pfad`, `version`, `active`, `themename`, `navbar_class`, `navbar_theme`,
                     `express_active`, `logo_pic`, `reg_pic`, `headlines`, `sort`, `manifest_path`, `layout_file`, `preview_image`, `description`)
                 VALUES (?, ?, ?, ?, '1.0.0', 1, 'yeti', 'bg-dark', 'dark', 0, 'default_logo.png', 'default_login_bg.jpg',
                         'headlines_03.css', 0, ?, ?, ?, ?)"
            );
            if (!$stmt) {
                return false;
            }
            $manifestPath = 'includes/themes/' . $slug . '/theme.json';
            $stmt->bind_param('ssssssss', $name, $slug, $slug, $slug, $manifestPath, $layout, $preview, $description);
            $ok = $stmt->execute();
            $stmt->close();
        }

        $this->activeThemeRow = null;
        $this->activeManifest = null;

        return (bool)($ok ?? false);
    }

    private function mergeWithDefaults(array $manifest, array $row): array
    {
        $slug = strtolower((string)($manifest['slug'] ?? $row['slug'] ?? $row['modulname'] ?? $row['pfad'] ?? 'default'));
        $folder = (string)($manifest['folder'] ?? $row['pfad'] ?? $slug);
        $preview = trim((string)($manifest['preview'] ?? $row['preview_image'] ?? ''));
        if ($preview === '') {
            $preview = 'images/default_logo.png';
        }

        return array_replace_recursive(
            [
                'name' => (string)($row['name'] ?? ucfirst($slug)),
                'slug' => $slug,
                'folder' => $folder,
                'version' => (string)($row['version'] ?? '1.0.0'),
                'author' => (string)($row['author'] ?? ''),
                'url' => (string)($row['url'] ?? ''),
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

    private function tableExists(string $table): bool
    {
        $name = $this->database->real_escape_string($table);
        $result = $this->database->query("SHOW TABLES LIKE '{$name}'");
        return (bool)($result && $result->num_rows > 0);
    }

    private function columnExists(string $table, string $column): bool
    {
        $table = $this->database->real_escape_string($table);
        $column = $this->database->real_escape_string($column);
        $result = $this->database->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        return (bool)($result && $result->num_rows > 0);
    }
}
