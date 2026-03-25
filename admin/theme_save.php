<?php
declare(strict_types=1);

require_once __DIR__ . '/../system/config.inc.php';
require_once __DIR__ . '/../system/classes/ThemeManager.php';

use nexpell\ThemeManager;

header('Content-Type: text/plain; charset=utf-8');

try {
    $_database = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($_database->connect_error) {
        http_response_code(500);
        echo 'DB-Verbindungsfehler: ' . $_database->connect_error;
        exit;
    }

    $_database->set_charset('utf8mb4');

    $themeManager = new ThemeManager($_database, dirname(__DIR__) . '/includes/themes', '/includes/themes');
    $themeManager->ensureSchema();

    $themeSlug = strtolower(trim((string)($_POST['theme_slug'] ?? $_POST['theme'] ?? '')));
    if ($themeSlug === '') {
        http_response_code(400);
        echo "Fehlerhafte Eingabe: 'theme_slug' fehlt oder ist leer.";
        exit;
    }

    if (!$themeManager->activateTheme($themeSlug)) {
        http_response_code(400);
        echo 'Theme konnte nicht aktiviert werden.';
        exit;
    }

    $activeTheme = $themeManager->getActiveThemeRow();
    $themeID = (int)($activeTheme['themeID'] ?? 0);
    if ($themeID <= 0) {
        http_response_code(500);
        echo 'Aktives Theme konnte nicht geladen werden.';
        exit;
    }

    $bootswatchVariant = trim((string)($_POST['bootswatch_variant'] ?? ($activeTheme['themename'] ?? 'yeti')));
    $navbarClass = trim((string)($_POST['navbar_class'] ?? ($activeTheme['navbar_class'] ?? 'bg-dark')));
    $navbarTheme = trim((string)($_POST['navbar_theme'] ?? ($activeTheme['navbar_theme'] ?? 'dark')));
    $logoPic = trim((string)($_POST['logo_pic'] ?? ($activeTheme['logo_pic'] ?? 'default_logo.png')));
    $regPic = trim((string)($_POST['reg_pic'] ?? ($activeTheme['reg_pic'] ?? 'default_login_bg.jpg')));
    $headlines = trim((string)($_POST['headlines'] ?? ($activeTheme['headlines'] ?? 'headlines_03.css')));

    $stmt = $_database->prepare(
        "UPDATE `settings_themes`
         SET `themename` = ?, `navbar_class` = ?, `navbar_theme` = ?, `logo_pic` = ?, `reg_pic` = ?, `headlines` = ?
         WHERE `themeID` = ?"
    );
    if (!$stmt) {
        http_response_code(500);
        echo 'DB-Fehler beim Speichern der Theme-Einstellungen.';
        exit;
    }
    $stmt->bind_param('ssssssi', $bootswatchVariant, $navbarClass, $navbarTheme, $logoPic, $regPic, $headlines, $themeID);
    $stmt->execute();
    $stmt->close();

    if ($_database->query("SHOW TABLES LIKE 'navigation_website_settings'")->num_rows > 0) {
        $navStmt = $_database->prepare(
            "INSERT INTO `navigation_website_settings` (`setting_key`, `setting_value`)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)"
        );

        if ($navStmt) {
            $settings = [
                'navbar_shadow' => $navbarClass,
                'navbar_modus' => $navbarTheme,
                'navbar_class' => $navbarClass,
                'navbar_theme' => $navbarTheme,
            ];

            foreach ($settings as $key => $value) {
                $navStmt->bind_param('ss', $key, $value);
                $navStmt->execute();
            }

            $navStmt->close();
        }
    }

    $themeOptions = $_POST['theme_options'] ?? [];
    if (is_array($themeOptions) && !empty($themeOptions)) {
        $normalizedOptions = [];
        foreach ($themeOptions as $key => $value) {
            $normalizedKey = preg_replace('/[^a-z0-9_\-]/i', '', (string)$key);
            if ($normalizedKey === '') {
                continue;
            }
            $normalizedOptions[$normalizedKey] = (string)$value;
        }
        if (!empty($normalizedOptions)) {
            $themeManager->saveOptions($themeSlug, $normalizedOptions);
        }
    }

    echo 'OK';
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Save-Fehler: ' . $e->getMessage();
}
