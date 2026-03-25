<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Sicherheitscheck lock
require __DIR__ . '/system/check_lock.php';

// Nur die Sprachlogik laden
require __DIR__ . '/system/language_handler.php';

// Übersetzungen aus der entsprechenden Sprachdatei laden.
$step_path = __DIR__ . "/languages/{$_SESSION['lang']}/step4.php";
$translations = require $step_path;

// Setzen der Sprache des HTML-Dokuments
$html_lang = htmlspecialchars($_SESSION['lang']);

$configPath = dirname(__DIR__) . "/system/config.inc.php";
$sqlFile = __DIR__ . "/data/database.sql";
$error = "";
$success = false;

if (!file_exists($configPath)) {
    die($translations['error_config_not_found']);
}

require_once $configPath;

// Verbindung herstellen
$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die($translations['error_db_connect'] . $mysqli->connect_error);
}

// HIER Charset setzen!
$mysqli->set_charset("utf8mb4");

// SQL-Datei laden und verarbeiten
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!file_exists($sqlFile)) {
        $error = $translations['error_sql_not_found'];
    } else {
        $sqlContent = file_get_contents($sqlFile);
        $queries = array_filter(array_map('trim', explode(";", $sqlContent)));

        $mysqli->begin_transaction();
        try {
            foreach ($queries as $query) {
                if (!empty($query)) {
                    $mysqli->query($query);
                    if ($mysqli->error) {
                        throw new Exception($mysqli->error);
                    }
                }
            }
            $mysqli->commit();
            $success = true;
        } catch (Exception $e) {
            $mysqli->rollback();
            $errorMessage = $e->getMessage();
            $error = $translations['error_import'] . $errorMessage;

            // Überprüfe, ob es ein spezifischer Fehler für einen doppelten Schlüssel ist
            if (strpos($errorMessage, 'Duplicate entry') !== false) {
                $error .= '<br>' . $translations['error_import_duplicate_key'];
            }
        }
    }
}
?>
?>
<!DOCTYPE html>
<html lang="<?= $html_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($translations['step_title']) ?></title>
    <link href="/install/css/bootstrap.min.css" rel="stylesheet">
    <link href="/install/css/installer.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <div class="text-center">
            <img src="/install/images/logo.png" alt="nexpell Logo" class="install-logo mb-4">
            <h2><?= htmlspecialchars($translations['step_title']) ?></h2>
        </div>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h3><?= htmlspecialchars($translations['installer_title']) ?></h3>

                <?php if (!$success): ?>
                    <p><?= $translations['intro_paragraph_1'] ?></p>
                    <p><?= htmlspecialchars($translations['intro_paragraph_2']) ?></p>
                    <ul>
                        <?php foreach ($translations['list_items'] as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p><?= htmlspecialchars($translations['intro_paragraph_3']) ?></p>
                    <p><?= htmlspecialchars($translations['intro_paragraph_4']) ?></p>

                    <form method="post">
                        <input type="submit" class="btn btn-primary btn-lg w-100" value="<?= htmlspecialchars($translations['button_import']) ?>">
                    </form>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success mt-3">
                        <?= $translations['success_import'] ?>
                    </div>
                    <a href="step5.php" class="btn btn-primary btn-lg w-100"><?= htmlspecialchars($translations['button_continue']) ?></a>
                <?php endif; ?>
            </div>

            <div class="card-footer text-center text-muted small">
                &copy; <?= date("Y") ?> nexpell Installer
            </div>
        </div>
    </div>

    <script src="/install/js/bootstrap.bundle.min.js"></script>
</body>
</html>