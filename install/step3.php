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
$step_path = __DIR__ . "/languages/{$_SESSION['lang']}/step3.php";
$translations = require $step_path;

// Setzen der Sprache des HTML-Dokuments
$html_lang = htmlspecialchars($_SESSION['lang']);

ini_set('display_errors', 1);
error_reporting(E_ALL);

$configPath = dirname(__DIR__) . "/system/config.inc.php";
$error = "";
$success_messages = [];
$requirements_met = true;

// PHP-Version prüfen
$required_php_version = '8.1.0';
if (version_compare(PHP_VERSION, $required_php_version, '<')) {
    $error .= $translations['msg_php_version_low'] . PHP_VERSION . ")<br>";
    $requirements_met = false;
} else {
    $success_messages[] = $translations['msg_php_version_ok'] . PHP_VERSION . ")";
}

// MySQL-Verbindung über config.inc.php testen (wenn Datei existiert)
$_database = null;

if (file_exists($configPath)) {
    $config_content = file_get_contents($configPath);

    // Nur laden, wenn Konfiguration NICHT leer ist
    if (strpos($config_content, "new mysqli") !== false &&
        strpos($config_content, "''") === false) {
        include($configPath);

        if ($_database instanceof mysqli && !$_database->connect_error) {
            $mysql_version = $_database->server_info;

            if (strpos($mysql_version, 'MariaDB') !== false) {
                $success_messages[] = $translations['msg_mariadb_ok'] . $mysql_version . ")";
            } elseif (version_compare($mysql_version, '8.0', '<')) {
                $error .= $translations['msg_mysql_version_low'] . $mysql_version . ")<br>";
                $requirements_met = false;
            } else {
                $success_messages[] = $translations['msg_mysql_version_ok'] . $mysql_version . ")";
            }
        } else {
            $error .= $translations['msg_config_invalid'] . "<br>";
            $requirements_met = false;
        }
    }
}

// Schreibrechte prüfen
$css_file = __DIR__ . '/../includes/themes/default/css/stylesheet.css';
if (!is_writable($css_file)) {
    $error .= $translations['msg_css_not_writable'] . "<br>";
    $requirements_met = false;
} else {
    $success_messages[] = $translations['msg_css_writable_ok'];
}

// Funktion zum Generieren eines 32-Zeichen ASCII-Schlüssels
function generateAESKey(int $length = 32): string {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $key;
}

// Initialwerte
$DB_HOST = $DB_USER = $DB_PASS = $DB_NAME = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requirements_met) {
    $DB_HOST = trim($_POST['DB_HOST'] ?? '');
    $DB_USER = trim($_POST['DB_USER'] ?? '');
    $DB_PASS = $_POST['DB_PASS'] ?? '';
    $DB_NAME = trim($_POST['DB_NAME'] ?? '');

    try {
        $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

        if ($conn->connect_error) {
            throw new mysqli_sql_exception($translations['msg_error_db_connect'] . $conn->connect_error);
        }

        // AES_KEY generieren: 32 Zeichen ASCII (keine Hexkodierung)
        $aes_key = generateAESKey(32);

        // Datenbankverbindung war erfolgreich, also schreibe die config.inc.php
        $configContent = "<?php\n";
        $configContent .= "/**\n * nexpell Konfigurationsdatei\n * Automatisch generiert durch Installer\n */\n";
        $configContent .= "define('DB_HOST', '" . addslashes($DB_HOST) . "');\n";
        $configContent .= "define('DB_USER', '" . addslashes($DB_USER) . "');\n";
        $configContent .= "define('DB_PASS', '" . addslashes($DB_PASS) . "');\n";
        $configContent .= "define('DB_NAME', '" . addslashes($DB_NAME) . "');\n";
        $configContent .= "/**\n * AES-Schlüssel für Verschlüsselung (32 Zeichen ASCII)\n */\n";
        $configContent .= "define('AES_KEY', '" . $aes_key . "');\n";
        $configContent .= "?>";

        if (file_put_contents($configPath, $configContent)) {
            $success_messages[] = $translations['msg_success_config_create'];
        } else {
            $error_message = $translations['msg_error_config_write'];
        }

        $_SESSION['db_data'] = [
            'DB_HOST' => $DB_HOST,
            'DB_USER' => $DB_USER,
            'DB_PASS' => $DB_PASS,
            'DB_NAME' => $DB_NAME,
            'AES_KEY'  => $aes_key
        ];

        header("Location: step4.php");
        exit;

    } catch (mysqli_sql_exception $e) {
        $error_message = $e->getMessage();
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
            <h3><?= htmlspecialchars($translations['section_system_check']) ?></h3>

            <?php foreach ($success_messages as $msg): ?>
                <div class="alert alert-success" role="alert"><?= htmlspecialchars($msg) ?></div>
            <?php endforeach; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <?php if ($requirements_met): ?>
                <hr>
                <h3><?= htmlspecialchars($translations['section_db_setup']) ?></h3>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><?= htmlspecialchars($translations['field_host']) ?></label>
                        <input class="form-control" type="text" name="DB_HOST" value="<?= htmlspecialchars($DB_HOST ?: 'localhost') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= htmlspecialchars($translations['field_username']) ?></label>
                        <input class="form-control" type="text" name="DB_USER" value="<?= htmlspecialchars($DB_USER) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= htmlspecialchars($translations['field_password']) ?></label>
                        <input class="form-control" type="password" name="DB_PASS">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= htmlspecialchars($translations['field_dbname']) ?></label>
                        <input class="form-control" type="text" name="DB_NAME" value="<?= htmlspecialchars($DB_NAME) ?>" required>
                    </div>
                    <div class="mb-3">
                        <input class="btn btn-primary btn-lg w-100" type="submit" value="<?= htmlspecialchars($translations['button_continue']) ?>">
                    </div>
                </form>
            <?php else: ?>
                <p class="text-danger mt-4">❌ <?= htmlspecialchars($translations['msg_fix_errors']) ?></p>
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