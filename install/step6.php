<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();

    // installed.lock schreiben
    $lock_file = __DIR__ . '/../system/installed.lock';
    file_put_contents($lock_file, "Installation erfolgreich am " . date('Y-m-d H:i:s'));
}

// Sicherheitscheck lock
#require __DIR__ . '/system/check_lock.php';

// Nur die Sprachlogik laden
require __DIR__ . '/system/language_handler.php';

// Übersetzungen aus der entsprechenden Sprachdatei laden.
$step_path = __DIR__ . "/languages/{$_SESSION['lang']}/step6.php";
$translations = require $step_path;

// Setzen der Sprache des HTML-Dokuments
$html_lang = htmlspecialchars($_SESSION['lang']);

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
        <h2><?= htmlspecialchars($translations['installer_title']) ?></h2>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body text-center">
            <h3><?= htmlspecialchars($translations['installer_title']) ?></h3>
            <p><?= htmlspecialchars($translations['text_success']) ?></p>

            <div class="alert alert-warning mt-3 text-start">
                <strong><?= htmlspecialchars($translations['security_title']) ?></strong><br>
                <?= $translations['security_text'] ?>
            </div>

            <p class="mt-3"><?= htmlspecialchars($translations['text_login_info']) ?></p>
            <br>
            <a class="btn btn-primary" href="../index.php"><?= htmlspecialchars($translations['button_homepage']) ?></a>
            <a class="btn btn-primary" href="../admin/login.php"><?= htmlspecialchars($translations['button_admin_login']) ?></a>
        </div>
        <div class="card-footer text-center text-muted small">
            &copy; <?= date("Y") ?> nexpell Installer
        </div>
    </div>
</div>
<script src="/install/js/bootstrap.bundle.min.js"></script>
</body>
</html>