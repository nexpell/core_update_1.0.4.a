<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../system/config.inc.php';
require_once __DIR__ . '/../system/classes/ThemeManager.php';

if (!defined('THEME_INSTALLER_CONTEXT')) {
    header('Location: admincenter.php?site=theme_installer&action=upload');
    exit;
}

$db = $GLOBALS['_database'] ?? null;
if (!$db instanceof mysqli) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
        echo '<div class="alert alert-danger">DB-Fehler: ' . htmlspecialchars($db->connect_error, ENT_QUOTES, 'UTF-8') . '</div>';
        return;
    }
    $db->set_charset('utf8mb4');
}

$themeManager = $GLOBALS['nx_theme_manager'] ?? new \nexpell\ThemeManager($db, dirname(__DIR__) . '/includes/themes', '/includes/themes');
$themeManager->ensureSchema();

$themesRoot = dirname(__DIR__) . '/includes/themes';
$defaultThemeRoot = $themesRoot . '/default';
$mode = isset($_GET['edit']) ? 'edit' : ((string)($_GET['mode'] ?? '') === 'generate' ? 'generate' : 'add');
$editSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower((string)($_GET['edit'] ?? '')));
$editingTheme = $editSlug !== '' ? $themeManager->getThemeBySlug($editSlug) : null;

function nx_theme_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9_-]+/', '-', $value);
    $value = trim((string)$value, '-_');
    return $value !== '' ? $value : 'theme-' . date('YmdHis');
}

function nx_delete_dir(string $path): void
{
    if (!is_dir($path)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $item) {
        if ($item->isDir()) {
            @rmdir($item->getPathname());
        } else {
            @unlink($item->getPathname());
        }
    }

    @rmdir($path);
}

function nx_copy_dir(string $source, string $target): void
{
    if (!is_dir($source)) {
        return;
    }

    if (!is_dir($target)) {
        @mkdir($target, 0755, true);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $destination = $target . '/' . $iterator->getSubPathName();
        if ($item->isDir()) {
            if (!is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }
            continue;
        }

        $destinationDir = dirname($destination);
        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0755, true);
        }
        @copy($item->getPathname(), $destination);
    }
}

function nx_find_first_existing(string $baseDir, array $candidates): ?string
{
    foreach ($candidates as $candidate) {
        $candidate = trim((string)$candidate, '/\\');
        if ($candidate === '') {
            continue;
        }
        if (file_exists($baseDir . '/' . $candidate)) {
            return str_replace('\\', '/', $candidate);
        }
    }

    return null;
}

function nx_find_preview_recursive(string $baseDir): ?string
{
    if (!is_dir($baseDir)) {
        return null;
    }

    $preferredNames = [
        'screenshot.png',
        'screenshot.jpg',
        'screenshot.jpeg',
        'preview.png',
        'preview.jpg',
        'preview.jpeg',
        'hero-bg.jpg',
        'hero-bg.jpeg',
        'hero-bg.png',
        'hero.png',
        'hero.jpg',
        'index.png',
        'index.jpg',
        'cover.png',
        'cover.jpg',
    ];

    $matches = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $item) {
        if (!$item->isFile()) {
            continue;
        }

        $filename = strtolower($item->getFilename());
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($extension, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            continue;
        }

        $relativePath = str_replace('\\', '/', $iterator->getSubPathName());
        $normalizedPath = strtolower($relativePath);
        $score = 0;

        if (in_array($filename, $preferredNames, true)) {
            $score += 100;
        }
        if (str_contains($filename, 'screenshot') || str_contains($filename, 'preview')) {
            $score += 80;
        }
        if (str_contains($normalizedPath, '/assets/img/')) {
            $score += 25;
        }
        if (str_contains($normalizedPath, '/images/')) {
            $score += 20;
        }
        if (str_contains($filename, 'hero') || str_contains($filename, 'cover')) {
            $score += 15;
        }
        if (str_contains($filename, 'logo') || str_contains($filename, 'icon') || str_contains($filename, 'favicon')) {
            $score -= 60;
        }

        if ($score > 0) {
            $matches[] = [
                'path' => $relativePath,
                'score' => $score,
            ];
        }
    }

    if ($matches === []) {
        return null;
    }

    usort($matches, static function (array $a, array $b): int {
        return $b['score'] <=> $a['score'] ?: strcmp($a['path'], $b['path']);
    });

    return $matches[0]['path'];
}

function nx_select_assets(string $baseDir, array $candidates): array
{
    $assets = [];
    foreach ($candidates as $candidate) {
        $candidate = trim((string)$candidate, '/\\');
        if ($candidate === '') {
            continue;
        }
        if (file_exists($baseDir . '/' . $candidate)) {
            $assets[] = str_replace('\\', '/', $candidate);
        }
    }
    return array_values(array_unique($assets));
}

function nx_build_manifest(string $slug, string $themeDir, array $meta): array
{
    $preview = nx_find_first_existing($themeDir, [
        'screenshot.png',
        'screenshot.jpg',
        'screenshot.jpeg',
        'preview.png',
        'preview.jpg',
        'preview.jpeg',
        'images/preview.png',
        'images/preview.jpg',
        'images/preview.jpeg',
        'assets/img/profile/profile-1.webp',
        'assets/img/profile/profile-bg-5.webp',
        'assets/img/hero-bg.jpg',
        'assets/img/hero-bg.jpeg',
        'assets/img/hero-bg.png',
        'assets/img/hero.png',
        'assets/img/hero.jpg',
        'assets/img/preview.png',
        'assets/img/preview.jpg',
        'assets/img/preview.jpeg',
    ]);
    $preview ??= nx_find_preview_recursive($themeDir);

    $favicons = [];
    $faviconCandidates = [
        'ico' => ['images/favicon.ico', 'assets/img/favicon.ico', 'assets/favicon.ico'],
        'png32' => ['assets/img/favicon.png', 'images/favicon-32.png', 'assets/img/favicon-32.png', 'assets/favicon-32.png'],
        'png192' => ['assets/img/favicon.png', 'images/favicon-192.png', 'assets/img/favicon-192.png', 'assets/favicon-192.png'],
        'apple180' => ['images/favicon-180.png', 'assets/img/apple-touch-icon.png', 'assets/favicon-180.png'],
    ];
    foreach ($faviconCandidates as $key => $candidates) {
        $found = nx_find_first_existing($themeDir, $candidates);
        if ($found !== null) {
            $favicons[$key] = $found;
        }
    }

    $cssAssets = nx_select_assets($themeDir, [
        'assets/vendor/bootstrap/css/bootstrap.min.css',
        'assets/vendor/bootstrap-icons/bootstrap-icons.min.css',
        'assets/vendor/bootstrap-icons/bootstrap-icons.css',
        'assets/vendor/aos/aos.css',
        'assets/vendor/glightbox/css/glightbox.min.css',
        'assets/vendor/glightbox/css/glightbox.css',
        'assets/vendor/swiper/swiper-bundle.min.css',
        'assets/css/main.css',
        'css/main.css',
    ]);

    $jsAssets = nx_select_assets($themeDir, [
        'assets/vendor/bootstrap/js/bootstrap.bundle.min.js',
        'assets/vendor/bootstrap/js/bootstrap.bundle.js',
        'assets/vendor/aos/aos.js',
        'assets/vendor/glightbox/js/glightbox.min.js',
        'assets/vendor/glightbox/js/glightbox.js',
        'assets/vendor/imagesloaded/imagesloaded.pkgd.min.js',
        'assets/vendor/isotope-layout/isotope.pkgd.min.js',
        'assets/vendor/isotope-layout/isotope.pkgd.js',
        'assets/vendor/swiper/swiper-bundle.min.js',
        'assets/vendor/typed.js/typed.umd.js',
        'assets/vendor/waypoints/noframework.waypoints.js',
        'assets/vendor/php-email-form/validate.js',
        'assets/js/main.js',
        'js/main.js',
    ]);

    return [
        'name' => (string)($meta['name'] ?? ucfirst($slug)),
        'slug' => $slug,
        'version' => (string)($meta['version'] ?? '1.0.0'),
        'author' => (string)($meta['author'] ?? 'nexpell'),
        'url' => (string)($meta['url'] ?? ''),
        'description' => (string)($meta['description'] ?? ''),
        'preview' => $preview ?? '',
        'template_dir' => 'templates',
        'layout' => [
            'file' => 'index.php',
        ],
        'assets' => [
            'css' => $cssAssets,
            'js' => $jsAssets,
        ],
        'favicons' => $favicons,
        'settings' => [
            'supports_bootswatch_variant' => false,
        ],
    ];
}

function nx_ensure_theme_scaffold(string $themeDir, string $defaultThemeRoot): void
{
    $themeSlug = basename($themeDir);

    $indexTarget = $themeDir . '/index.php';
    if (!file_exists($indexTarget)) {
        $adapter = "<?php\n\$importedThemeSlug = '" . addslashes($themeSlug) . "';\nrequire BASE_PATH . '/includes/themes/default/imported_portfolio_layout.php';\n?>\n";
        @file_put_contents($indexTarget, $adapter);
    }

    foreach (['header.php', 'footer.php'] as $file) {
        $target = $themeDir . '/' . $file;
        $source = $defaultThemeRoot . '/' . $file;
        if (!file_exists($target) && file_exists($source)) {
            @copy($source, $target);
        }
    }

    if (!is_dir($themeDir . '/templates') && is_dir($defaultThemeRoot . '/templates')) {
        nx_copy_dir($defaultThemeRoot . '/templates', $themeDir . '/templates');
    }
}

function nx_create_theme_starter_files(string $themeDir, string $themeName, array $options = []): void
{
    $cssDir = $themeDir . '/assets/css';
    $jsDir = $themeDir . '/assets/js';
    $forceIndex = !empty($options['force_index']);
    $forceCss = !empty($options['force_css']);
    $forceJs = !empty($options['force_js']);

    if (!is_dir($cssDir)) {
        @mkdir($cssDir, 0755, true);
    }
    if (!is_dir($jsDir)) {
        @mkdir($jsDir, 0755, true);
    }

    $indexFile = $themeDir . '/index.php';
    if ($forceIndex || !file_exists($indexFile)) {
        $layout = <<<'PHP'
<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once BASE_PATH . '/system/core/theme_runtime_renderer.php';
PHP;
        @file_put_contents($indexFile, $layout);
    }

    $cssFile = $cssDir . '/main.css';
    if ($forceCss || !file_exists($cssFile)) {
        $sourceCssFile = dirname(__DIR__) . '/includes/themes/easytest/assets/css/main.css';
        if (file_exists($sourceCssFile)) {
            @copy($sourceCssFile, $cssFile);
        }
    }

    $jsFile = $jsDir . '/main.js';
    if ($forceJs || !file_exists($jsFile)) {
        $sourceJsFile = dirname(__DIR__) . '/includes/themes/easytest/assets/js/main.js';
        if (file_exists($sourceJsFile)) {
            @copy($sourceJsFile, $jsFile);
        } else {
            @file_put_contents($jsFile, "document.documentElement.classList.add('nx-theme-generated');\n");
        }
    }
}

function nx_import_theme_archive(string $tmpFile, string $targetDir): bool
{
    $zip = new ZipArchive();
    if ($zip->open($tmpFile) !== true) {
        return false;
    }

    $tempDir = sys_get_temp_dir() . '/nexpell_theme_' . uniqid('', true);
    if (!@mkdir($tempDir, 0755, true) && !is_dir($tempDir)) {
        $zip->close();
        return false;
    }

    if (!$zip->extractTo($tempDir)) {
        $zip->close();
        nx_delete_dir($tempDir);
        return false;
    }
    $zip->close();

    $entries = array_values(array_diff(scandir($tempDir) ?: [], ['.', '..']));
    $sourceDir = $tempDir;
    if (count($entries) === 1 && is_dir($tempDir . '/' . $entries[0])) {
        $sourceDir = $tempDir . '/' . $entries[0];
    }

    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }

    nx_copy_dir($sourceDir, $targetDir);
    nx_delete_dir($tempDir);

    return true;
}

function nx_redirect_theme_installer(string $message, string $type = 'success'): void
{
    nx_redirect('admincenter.php?site=theme_installer', $type, $message, true, true);
}

function nx_generator_settings_from_theme(?array $theme, string $fallbackName = ''): array
{
    $generator = is_array($theme['settings']['generator'] ?? null) ? $theme['settings']['generator'] : [];
    $colors = is_array($generator['colors'] ?? null) ? $generator['colors'] : [];

    return [
        'generator_title' => (string)($generator['hero_title'] ?? $fallbackName),
        'generator_text' => (string)($generator['hero_text'] ?? 'Eigenes Nexpell Theme als Startpunkt.'),
        'generator_cta' => (string)($generator['cta_label'] ?? 'Mehr erfahren'),
        'generator_layout' => (string)($generator['layout_preset'] ?? 'right-sidebar'),
        'generator_show_hero' => !empty($generator['show_hero']) ? '1' : '0',
        'generator_accent' => (string)($colors['accent'] ?? '#1f6feb'),
        'generator_page_top' => (string)($colors['page_top'] ?? '#1a2230'),
        'generator_page_bg' => (string)($colors['page_bg'] ?? '#11151b'),
        'generator_surface' => (string)($colors['surface'] ?? '#ffffff'),
        'generator_text_color' => (string)($colors['text'] ?? '#0f172a'),
    ];
}

function nx_generator_builder_zones(string $layoutPreset): array
{
    $zones = [
        'top' => ['key' => 'top', 'label' => 'Top', 'enabled' => false],
        'undertop' => ['key' => 'undertop', 'label' => 'Unter Top', 'enabled' => false],
        'left' => ['key' => 'left', 'label' => 'Linke Sidebar', 'enabled' => false],
        'maintop' => ['key' => 'maintop', 'label' => 'Main Top', 'enabled' => true],
        'mainbottom' => ['key' => 'mainbottom', 'label' => 'Main Bottom', 'enabled' => true],
        'right' => ['key' => 'right', 'label' => 'Rechte Sidebar', 'enabled' => false],
        'bottom' => ['key' => 'bottom', 'label' => 'Bottom', 'enabled' => true],
    ];

    switch ($layoutPreset) {
        case 'content':
            $zones['undertop']['enabled'] = true;
            break;
        case 'two-sidebars':
            $zones['undertop']['enabled'] = true;
            $zones['left']['enabled'] = true;
            $zones['right']['enabled'] = true;
            break;
        case 'landing':
            $zones['top']['enabled'] = true;
            $zones['undertop']['enabled'] = true;
            break;
        case 'right-sidebar':
        default:
            $zones['undertop']['enabled'] = true;
            $zones['right']['enabled'] = true;
            break;
    }

    return array_values($zones);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['theme_name'] ?? ''));
    $slug = nx_theme_slug((string)($_POST['theme_slug'] ?? $name));
    $version = trim((string)($_POST['version'] ?? '1.0.0'));
    $author = trim((string)($_POST['author'] ?? 'nexpell'));
    $url = trim((string)($_POST['url'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $generatorTitle = trim((string)($_POST['generator_title'] ?? $name));
    $generatorText = trim((string)($_POST['generator_text'] ?? 'Eigenes Nexpell Theme als Startpunkt.'));
    $generatorCta = trim((string)($_POST['generator_cta'] ?? 'Mehr erfahren'));
    $generatorLayout = trim((string)($_POST['generator_layout'] ?? 'right-sidebar'));
    $generatorShowHero = isset($_POST['generator_show_hero']) ? '1' : '0';
    $generatorAccent = trim((string)($_POST['generator_accent'] ?? '#1f6feb'));
    $generatorPageTop = trim((string)($_POST['generator_page_top'] ?? '#1a2230'));
    $generatorPageBg = trim((string)($_POST['generator_page_bg'] ?? '#11151b'));
    $generatorSurface = trim((string)($_POST['generator_surface'] ?? '#ffffff'));
    $generatorTextColor = trim((string)($_POST['generator_text_color'] ?? '#0f172a'));

    if ($name === '') {
        $name = ucfirst($slug);
    }

    $themeDir = $themesRoot . '/' . $slug;
    $manifestFile = $themeDir . '/theme.json';

    if ($mode === 'edit') {
        if ($editingTheme === null || !is_dir($themeDir)) {
            nx_redirect_theme_installer('Theme zum Bearbeiten nicht gefunden.', 'danger');
        }

        $manifest = [];
        if (file_exists($manifestFile)) {
            $decoded = json_decode((string)file_get_contents($manifestFile), true);
            if (is_array($decoded)) {
                $manifest = $decoded;
            }
        }

        $manifest = array_replace_recursive($manifest, [
            'name' => $name,
            'slug' => $slug,
            'version' => $version,
            'author' => $author,
            'url' => $url,
            'description' => $description,
        ]);

        $manifest['settings'] = array_replace_recursive($manifest['settings'] ?? [], [
            'supports_bootswatch_variant' => false,
            'self_hosted_headstyles' => true,
            'generator' => [
                'layout_preset' => $generatorLayout,
                'show_hero' => $generatorShowHero === '1',
                'hero_title' => $generatorTitle !== '' ? $generatorTitle : $name,
                'hero_text' => $generatorText,
                'cta_label' => $generatorCta,
                'colors' => [
                    'accent' => $generatorAccent,
                    'page_top' => $generatorPageTop,
                    'page_bg' => $generatorPageBg,
                    'surface' => $generatorSurface,
                    'text' => $generatorTextColor,
                ],
            ],
        ]);
        $manifest['builder'] = [
            'zones' => nx_generator_builder_zones($generatorLayout),
        ];

        nx_create_theme_starter_files($themeDir, $name, [
            'force_index' => true,
            'force_css' => true,
            'force_js' => true,
        ]);

        if (!$themeManager->saveManifest($slug, $manifest)) {
            nx_redirect_theme_installer('theme.json konnte nicht gespeichert werden.', 'danger');
        }

        $themeManager->syncInstalledThemeRecord($slug, $manifest);
        nx_redirect_theme_installer('Theme-Einstellungen wurden gespeichert und das Layout neu erzeugt.');
    }

    if ($mode === 'generate') {
        if (is_dir($themeDir)) {
            nx_redirect_theme_installer('Der Theme-Ordner existiert bereits. Bitte einen anderen Slug verwenden oder das Theme bearbeiten.', 'warning');
        }

        if (!@mkdir($themeDir, 0755, true) && !is_dir($themeDir)) {
            nx_redirect_theme_installer('Theme-Ordner konnte nicht erstellt werden.', 'danger');
        }

        nx_ensure_theme_scaffold($themeDir, $defaultThemeRoot);
        nx_create_theme_starter_files($themeDir, $name, [
            'force_index' => true,
            'force_css' => true,
            'force_js' => true,
        ]);

        $manifest = nx_build_manifest($slug, $themeDir, [
            'name' => $name,
            'version' => $version,
            'author' => $author,
            'url' => $url,
            'description' => $description,
        ]);

        $manifest['assets']['css'] = array_values(array_unique(array_merge(
            ['assets/css/main.css'],
            $manifest['assets']['css'] ?? []
        )));
        $manifest['assets']['js'] = array_values(array_unique(array_merge(
            ['assets/js/main.js'],
            $manifest['assets']['js'] ?? []
        )));
        $manifest['settings'] = array_replace_recursive($manifest['settings'] ?? [], [
            'supports_bootswatch_variant' => false,
            'self_hosted_headstyles' => true,
            'generator' => [
                'layout_preset' => $generatorLayout,
                'show_hero' => $generatorShowHero === '1',
                'hero_title' => $generatorTitle !== '' ? $generatorTitle : $name,
                'hero_text' => $generatorText,
                'cta_label' => $generatorCta,
                'colors' => [
                    'accent' => $generatorAccent,
                    'page_top' => $generatorPageTop,
                    'page_bg' => $generatorPageBg,
                    'surface' => $generatorSurface,
                    'text' => $generatorTextColor,
                ],
            ],
        ]);
        $manifest['builder'] = [
            'zones' => nx_generator_builder_zones($generatorLayout),
        ];

        if (!$themeManager->saveManifest($slug, $manifest)) {
            nx_delete_dir($themeDir);
            nx_redirect_theme_installer('theme.json konnte nicht geschrieben werden.', 'danger');
        }

        if (!$themeManager->syncInstalledThemeRecord($slug, $manifest)) {
            nx_redirect_theme_installer('Theme wurde erzeugt, aber der Installationsdatensatz konnte nicht gespeichert werden.', 'warning');
        }

        nx_audit_action(
            'theme_installer',
            'audit_action_theme_installed',
            $slug,
            null,
            'admincenter.php?site=theme_installer',
            ['theme' => $slug, 'version' => $manifest['version'] ?? '1.0.0', 'source' => 'generator']
        );

        nx_redirect_theme_installer('Eigenes Theme wurde erzeugt. Aktivierung erfolgt anschliessend in der Theme-Verwaltung.');
    }

    if (!isset($_FILES['themefile']) || !is_uploaded_file($_FILES['themefile']['tmp_name'])) {
        nx_redirect_theme_installer('Bitte eine ZIP-Datei auswaehlen.', 'danger');
    }

    $extension = strtolower((string)pathinfo((string)$_FILES['themefile']['name'], PATHINFO_EXTENSION));
    if ($extension !== 'zip') {
        nx_redirect_theme_installer('Es werden nur ZIP-Dateien fuer den Theme-Import unterstuetzt.', 'danger');
    }

    if (is_dir($themeDir)) {
        nx_redirect_theme_installer('Der Theme-Ordner existiert bereits. Bitte einen anderen Slug verwenden oder das Theme bearbeiten.', 'warning');
    }

    if (!@mkdir($themeDir, 0755, true) && !is_dir($themeDir)) {
        nx_redirect_theme_installer('Theme-Ordner konnte nicht erstellt werden.', 'danger');
    }

    if (!nx_import_theme_archive((string)$_FILES['themefile']['tmp_name'], $themeDir)) {
        nx_delete_dir($themeDir);
        nx_redirect_theme_installer('ZIP-Datei konnte nicht entpackt werden.', 'danger');
    }

    nx_ensure_theme_scaffold($themeDir, $defaultThemeRoot);
    nx_create_theme_starter_files($themeDir, $name, [
        'force_index' => !file_exists($themeDir . '/index.php'),
        'force_css' => !file_exists($themeDir . '/assets/css/main.css') && !file_exists($themeDir . '/css/main.css'),
        'force_js' => !file_exists($themeDir . '/assets/js/main.js') && !file_exists($themeDir . '/js/main.js'),
    ]);

    $manifest = [];
    if (file_exists($manifestFile)) {
        $decoded = json_decode((string)file_get_contents($manifestFile), true);
        if (is_array($decoded)) {
            $manifest = $decoded;
        }
    }

    if (empty($manifest)) {
        $manifest = nx_build_manifest($slug, $themeDir, [
            'name' => $name,
            'version' => $version,
            'author' => $author,
            'url' => $url,
            'description' => $description,
        ]);
    } else {
        $manifest = array_replace_recursive($manifest, [
            'name' => $name,
            'slug' => $slug,
            'version' => $version !== '' ? $version : (string)($manifest['version'] ?? '1.0.0'),
            'author' => $author !== '' ? $author : (string)($manifest['author'] ?? 'nexpell'),
            'url' => $url !== '' ? $url : (string)($manifest['url'] ?? ''),
            'description' => $description !== '' ? $description : (string)($manifest['description'] ?? ''),
            'template_dir' => (string)($manifest['template_dir'] ?? 'templates'),
            'layout' => [
                'file' => (string)($manifest['layout']['file'] ?? 'index.php'),
            ],
        ]);
    }

    $manifest['settings'] = array_replace_recursive($manifest['settings'] ?? [], [
        'supports_bootswatch_variant' => false,
        'self_hosted_headstyles' => true,
    ]);

    $manifest['assets']['css'] = array_values(array_unique(array_merge(
        file_exists($themeDir . '/assets/css/main.css') ? ['assets/css/main.css'] : [],
        $manifest['assets']['css'] ?? []
    )));
    $manifest['assets']['js'] = array_values(array_unique(array_merge(
        file_exists($themeDir . '/assets/js/main.js') ? ['assets/js/main.js'] : [],
        $manifest['assets']['js'] ?? []
    )));

    if (!$themeManager->saveManifest($slug, $manifest)) {
        nx_delete_dir($themeDir);
        nx_redirect_theme_installer('theme.json konnte nicht geschrieben werden.', 'danger');
    }

    if (!$themeManager->syncInstalledThemeRecord($slug, $manifest)) {
        nx_redirect_theme_installer('Theme wurde entpackt, aber der Installationsdatensatz konnte nicht gespeichert werden.', 'warning');
    }

    nx_audit_action(
        'theme_installer',
        'audit_action_theme_installed',
        $slug,
        null,
        'admincenter.php?site=theme_installer',
        ['theme' => $slug, 'version' => $manifest['version'] ?? '1.0.0']
    );

    nx_redirect_theme_installer('Theme wurde importiert. Aktivierung erfolgt anschliessend in der Theme-Verwaltung.');
}

$allThemes = $themeManager->getAllThemes();
$generatorDefaults = nx_generator_settings_from_theme($editingTheme, (string)($editingTheme['name'] ?? ''));
$formValues = [
    'theme_name' => (string)($editingTheme['name'] ?? ''),
    'theme_slug' => (string)($editingTheme['slug'] ?? ''),
    'version' => (string)($editingTheme['version'] ?? ''),
    'author' => (string)($editingTheme['author'] ?? ''),
    'url' => (string)($editingTheme['url'] ?? ''),
    'description' => (string)($editingTheme['description'] ?? ''),
    'generator_title' => $generatorDefaults['generator_title'],
    'generator_text' => $generatorDefaults['generator_text'],
    'generator_cta' => $generatorDefaults['generator_cta'],
    'generator_layout' => $generatorDefaults['generator_layout'],
    'generator_show_hero' => $generatorDefaults['generator_show_hero'],
    'generator_accent' => $generatorDefaults['generator_accent'],
    'generator_page_top' => $generatorDefaults['generator_page_top'],
    'generator_page_bg' => $generatorDefaults['generator_page_bg'],
    'generator_surface' => $generatorDefaults['generator_surface'],
    'generator_text_color' => $generatorDefaults['generator_text_color'],
];

echo '<div class="card shadow-sm border-0 mb-4 mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi ' . ($mode === 'generate' ? 'bi-magic' : 'bi-cloud-arrow-up') . '"></i>
            <span>' . ($mode === 'edit' ? 'Theme-Metadaten bearbeiten' : ($mode === 'generate' ? 'Eigenes Theme generieren' : 'Theme per ZIP importieren')) . '</span>
            <small class="small-muted">' . ($mode === 'generate'
                ? 'Leeres Start-Theme direkt in <code>includes/themes/&lt;slug&gt;</code> erzeugen'
                : 'Externe Templates werden nach <code>includes/themes/&lt;slug&gt;</code> entpackt') . '</small>
        </div>
    </div>
    <div class="card-body">';

if ($mode === 'generate' || $mode === 'edit') {
    echo '<div class="alert alert-info">
            ' . ($mode === 'edit'
                ? 'Hier bearbeitest du Layout-Preset, Hero und Farben eines generierten Themes. Beim Speichern werden <code>index.php</code> und <code>assets/css/main.css</code> neu erzeugt.'
                : 'Der Generator erstellt ein startbares Theme mit <code>theme.json</code>, eigenem <code>index.php</code>, Basis-CSS/JS und kopierten CMS-Dateien aus dem Default-Theme.
            Danach kannst du das Layout direkt weiterentwickeln und spaeter genauso anbieten oder exportieren wie importierte Themes.') . '
          </div>';
} else {
    echo '<div class="alert alert-info">
            Erwartet wird eine ZIP-Datei mit statischen Assets wie <code>assets/</code>, <code>vendor/</code>, <code>css/</code> oder <code>js/</code>.
            Falls kein <code>theme.json</code> vorhanden ist, erzeugt nexpell automatisch ein Manifest und ergaenzt fehlende CMS-Dateien aus dem Default-Theme.
          </div>';
}

echo '<form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Theme-Name</label>
                <input type="text" name="theme_name" class="form-control" value="' . htmlspecialchars($formValues['theme_name'], ENT_QUOTES, 'UTF-8') . '" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Slug / Ordnername</label>
                <input type="text" name="theme_slug" class="form-control" value="' . htmlspecialchars($formValues['theme_slug'], ENT_QUOTES, 'UTF-8') . '" ' . ($mode === 'edit' ? 'readonly' : '') . ' required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Version</label>
                <input type="text" name="version" class="form-control" value="' . htmlspecialchars($formValues['version'], ENT_QUOTES, 'UTF-8') . '" placeholder="1.0.0">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Autor</label>
                <input type="text" name="author" class="form-control" value="' . htmlspecialchars($formValues['author'], ENT_QUOTES, 'UTF-8') . '" placeholder="BootstrapMade / eigenes Team">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">URL</label>
                <input type="text" name="url" class="form-control" value="' . htmlspecialchars($formValues['url'], ENT_QUOTES, 'UTF-8') . '" placeholder="https://example.com">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Beschreibung</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Kurze Beschreibung des Themes">' . htmlspecialchars($formValues['description'], ENT_QUOTES, 'UTF-8') . '</textarea>
        </div>';

if ($mode === 'generate' || $mode === 'edit') {
    echo '<hr class="my-4">
          <h5 class="mb-3">Startlayout</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Hero-Titel</label>
                <input type="text" name="generator_title" class="form-control" value="' . htmlspecialchars($formValues['generator_title'], ENT_QUOTES, 'UTF-8') . '" placeholder="Mein neues Theme">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">CTA-Button</label>
                <input type="text" name="generator_cta" class="form-control" value="' . htmlspecialchars($formValues['generator_cta'], ENT_QUOTES, 'UTF-8') . '" placeholder="Mehr erfahren">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Akzentfarbe</label>
                <input type="text" name="generator_accent" class="form-control" value="' . htmlspecialchars($formValues['generator_accent'], ENT_QUOTES, 'UTF-8') . '" placeholder="#1f6feb">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Hero-Text</label>
                <textarea name="generator_text" class="form-control" rows="3" placeholder="Kurzer Einleitungstext fuer das Startlayout">' . htmlspecialchars($formValues['generator_text'], ENT_QUOTES, 'UTF-8') . '</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Layout-Preset</label>
                <select name="generator_layout" class="form-select">
                    <option value="content"' . ($formValues['generator_layout'] === 'content' ? ' selected' : '') . '>Nur Content</option>
                    <option value="right-sidebar"' . ($formValues['generator_layout'] === 'right-sidebar' ? ' selected' : '') . '>Content + rechte Sidebar</option>
                    <option value="two-sidebars"' . ($formValues['generator_layout'] === 'two-sidebars' ? ' selected' : '') . '>Content + zwei Sidebars</option>
                    <option value="landing"' . ($formValues['generator_layout'] === 'landing' ? ' selected' : '') . '>Landingpage</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 d-flex align-items-end">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="1" id="generator_show_hero" name="generator_show_hero"' . ($formValues['generator_show_hero'] === '1' ? ' checked' : '') . '>
                    <label class="form-check-label" for="generator_show_hero">Hero-Bereich anzeigen</label>
                </div>
            </div>
          </div>
          <hr class="my-4">
          <h5 class="mb-3">Farben</h5>
          <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Oberer Hintergrund</label>
                <input type="text" name="generator_page_top" class="form-control" value="' . htmlspecialchars($formValues['generator_page_top'], ENT_QUOTES, 'UTF-8') . '" placeholder="#1a2230">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Seitenhintergrund</label>
                <input type="text" name="generator_page_bg" class="form-control" value="' . htmlspecialchars($formValues['generator_page_bg'], ENT_QUOTES, 'UTF-8') . '" placeholder="#11151b">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Surface / Karten</label>
                <input type="text" name="generator_surface" class="form-control" value="' . htmlspecialchars($formValues['generator_surface'], ENT_QUOTES, 'UTF-8') . '" placeholder="#ffffff">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Textfarbe</label>
                <input type="text" name="generator_text_color" class="form-control" value="' . htmlspecialchars($formValues['generator_text_color'], ENT_QUOTES, 'UTF-8') . '" placeholder="#0f172a">
            </div>
          </div>';
}

if ($mode !== 'edit') {
    if ($mode === 'generate') {
        echo '<div class="alert alert-secondary">
                Erzeugt werden unter anderem <code>index.php</code>, <code>theme.json</code>, <code>assets/css/main.css</code>, <code>assets/js/main.js</code> sowie <code>header.php</code>, <code>footer.php</code> und <code>templates/</code>.
              </div>';
    } else {
        echo '<div class="mb-3">
                <label class="form-label">ZIP-Datei</label>
                <input type="file" name="themefile" class="form-control" accept=".zip" required>
              </div>';
    }
}

if ($mode === 'edit') {
    echo '<div class="alert alert-secondary">
            Beim Bearbeiten eines Generator-Themes werden <code>index.php</code> und <code>assets/css/main.css</code> mit den neuen Werten aktualisiert.
          </div>';
}

echo '  <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">' . ($mode === 'edit' ? 'Speichern' : ($mode === 'generate' ? 'Theme erzeugen' : 'Import starten')) . '</button>
            <a href="admincenter.php?site=theme_installer" class="btn btn-outline-secondary">Zurueck</a>
            <a href="admincenter.php?site=theme" class="btn btn-outline-secondary">Theme-Verwaltung</a>
        </div>
      </form>';

if ($mode !== 'edit' && $mode !== 'generate') {
    echo '<hr class="my-4">
          <h5 class="mb-3">Bereits erkannte Theme-Ordner</h5>';

    if (empty($allThemes)) {
        echo '<div class="text-muted">Noch keine lokalen Themes vorhanden.</div>';
    } else {
        echo '<div class="row g-2">';
        foreach ($allThemes as $theme) {
            echo '<div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="fw-semibold">' . htmlspecialchars((string)$theme['name'], ENT_QUOTES, 'UTF-8') . '</div>
                        <div class="small text-muted"><code>' . htmlspecialchars((string)$theme['slug'], ENT_QUOTES, 'UTF-8') . '</code></div>
                    </div>
                  </div>';
        }
        echo '</div>';
    }
}

echo '</div></div>';
