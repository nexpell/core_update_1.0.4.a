<?php
/**
 * ─────────────────────────────────────────────────────────────────────────────
 * nexpell – Das moderne CMS für Communitys, Teams & digitale Projekte
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * @version       1.0.0
 * @build         Stable Release
 * @release       2025
 * @copyright     © 2025 nexpell | https://www.nexpell.de
 * 
 * @description   nexpell ist ein modernes Open-Source-CMS für Gaming-Communities,
 *                E-Sport-Teams, Vereine und digitale Projekte jeder Art.
 * 
 * @author        Entwickelt vom nexpell Development Team
 * 
 * @license       GNU General Public License (GPL)
 *                Dieses System unterliegt der GPL und darf frei verwendet,
 *                verändert und verbreitet werden. Weitere Details unter:
 *                https://www.gnu.org/licenses/gpl.html
 * 
 * @support       Support, Updates und Plugins erhältlich unter:
 *                → Website: https://www.nexpell.de
 *                → Forum:   https://www.nexpell.de/forum
 *                → Wiki:    https://www.nexpell.de/wiki
 * 
 * ─────────────────────────────────────────────────────────────────────────────
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Sicherheitscheck lock
require __DIR__ . '/system/check_lock.php';

// Liste der unterstützten Sprachen
// Nur die Sprachlogik laden
require __DIR__ . '/system/language_handler.php';

// Übersetzungen aus der entsprechenden Sprachdatei laden.
// Die Variable $_SESSION['lang'] wird in language_handler.php gesetzt.
// Beachten Sie, dass der Pfad nun 'install/languages' ist.
$step_path = __DIR__ . "/languages/{$_SESSION['lang']}/index.php";
$translations = require $step_path;

// Setzen der Sprache des HTML-Dokuments
$html_lang = htmlspecialchars($_SESSION['lang']);

// Dokumentationslinks erstellen
$doc_link_html = '<a href="https://www.nexpell.de/wiki" target="_blank">' . htmlspecialchars($translations['link_text_documentation']) . '</a>';
$web_link_html = '<a href="https://www.nexpell.de" target="_blank">' . htmlspecialchars($translations['link_text_website']) . '</a>';

// Den finalen Satz mit den Links zusammenfügen
$documentation_output = sprintf(
    $translations['documentation_info'],
    $doc_link_html,
    $web_link_html
);
?>
<!DOCTYPE html>
<html lang="<?= $html_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="15;url=step1.php">
    <title><?= htmlspecialchars($translations['welcome_title']) ?></title>
    <link href="/install/css/bootstrap.min.css" rel="stylesheet">
    <link href="/install/css/installer.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <div class="text-center">
        <img src="/install/images/logo.png" alt="nexpell Logo" class="install-logo mb-4">
        <h2><?= htmlspecialchars($translations['welcome_title']) ?></h2>
    </div>

    <div class="card shadow-lg">
        <div class="card-body">
            <div class="language-selector text-center mb-4">
                <div class="language-selector text-center mb-4">
                    <a href="?lang=de"><img src="/install/images/flags/de.svg" alt="Deutsch" style="width:30px; margin: 0 5px; border: 1px solid gray;"></a>
                    <a href="?lang=en"><img src="/install/images/flags/en.svg" alt="English" style="width:30px; margin: 0 5px; border: 1px solid gray;"></a>
                    <a href="?lang=it"><img src="/install/images/flags/it.svg" alt="Italiano" style="width:30px; margin: 0 5px; border: 1px solid gray;"></a>
                </div>
            </div>

            <h3 class="card-title text-center mb-4"><?= htmlspecialchars($translations['welcome_title']) ?></h3>

            <p><?= htmlspecialchars($translations['intro_paragraph_1']) ?></p>

            <p><strong>nexpell</strong><?= htmlspecialchars($translations['intro_paragraph_2']) ?></p>

            <p><?= htmlspecialchars($translations['redirect_info']) ?></p>

            <p><?= $documentation_output ?></p>

            <div class="text-center mt-4">
                <a href="step1.php" class="btn btn-primary btn-lg w-100"><?= htmlspecialchars($translations['start_button']) ?></a>
                <p class="text-muted mt-2"><?= htmlspecialchars($translations['auto_redirect']) ?></p>
            </div>
        </div>

        <div class="card-footer text-center text-muted small">
            &copy; <?= date("Y") ?> nexpell Installer
        </div>
    </div>
</div>

<script src="/install/js/bootstrap.bundle.min.js"></script>

</body>
</html>