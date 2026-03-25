<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Sicherheitscheck lock
$lockOk = require __DIR__ . '/system/check_lock.php';

if ($lockOk === false) {
    header('Location: locked.php');
    exit;
}


// Nur die Sprachlogik laden
require __DIR__ . '/system/language_handler.php';

// Übersetzungen aus der entsprechenden Sprachdatei laden.
$step_path = __DIR__ . "/languages/{$_SESSION['lang']}/step1.php";
$translations = require $step_path;

// Setzen der Sprache des HTML-Dokuments
$html_lang = htmlspecialchars($_SESSION['lang']);

// Wenn der Benutzer das Formular abgesendet hat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob die Checkbox angekreuzt wurde
    if (isset($_POST['accept_license']) && $_POST['accept_license'] == '1') {
        $_SESSION['license_accepted'] = true;
        header("Location: step2.php");  // Weiterleitung zur nächsten Installationsseite
        exit;
    } else {
        $error = htmlspecialchars($translations['error_message']);
    }
}
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
                <h3><?= htmlspecialchars($translations['license_title']) ?></h3>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="alert alert-info">
                    <h4 class="alert-heading"><?= htmlspecialchars($translations['alert_heading']) ?></h4>
                    <p><?= htmlspecialchars($translations['gpl_paragraph_1']) ?></p>
                    <p><?= htmlspecialchars($translations['gpl_paragraph_2']) ?></p>
                    <p><a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" rel="noopener"><?= htmlspecialchars($translations['gpl_link']) ?></a></p>
                </div>

                <form method="post" class="mt-4">
                    <div class="form-check">
                        <input type="checkbox" name="accept_license" value="1" class="form-check-input" id="accept_license" required>
                        <label class="form-check-label" for="accept_license">
                            <?= htmlspecialchars($translations['checkbox_label']) ?>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><?= htmlspecialchars($translations['button_continue']) ?></button>
                </form>

            </div>
            <div class="card-footer text-center text-muted small">
                &copy; <?= date("Y") ?> nexpell Installer
            </div>
        </div>
    </div>

    <script src="/install/js/bootstrap.bundle.min.js"></script>
</body>
</html>