<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================================================
   Fehler nur loggen – kein Output!
========================================================= */
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/shoutbox_error.log');

/* =========================================================
   JSON Helper
========================================================= */
function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/* =========================================================
   System bootstrap
========================================================= */
$settingsPath = __DIR__ . '/../../../system/settings.php';
if (!file_exists($settingsPath) || !file_exists(__DIR__ . '/../../../system/functions.php')) {
    json_response(['status' => 'error', 'message' => 'System bootstrap files not found'], 500);
}

require_once $settingsPath;
require_once __DIR__ . '/../../../system/functions.php';

$_database = $GLOBALS['_database'] ?? ($_database ?? null);
if (!$_database instanceof mysqli) {
    json_response(['status' => 'error', 'message' => 'DB Verbindung fehlgeschlagen'], 500);
}

/* =========================================================
   Username
========================================================= */
$usernameSession = trim($_SESSION['username'] ?? '');
$recaptcha = nx_get_recaptcha_config();
$recaptchaEnabled = $recaptcha['enabled'];
$loggedin = !empty($_SESSION['userID']);

/* =========================================================
   POST → Nachricht speichern
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$loggedin && $recaptchaEnabled && empty($_SESSION['shoutbox_guest_verified'])) {
        json_response(['status' => 'error', 'message' => 'Captcha verification required'], 403);
    }

    if (!nx_rate_limit_consume('shoutbox_burst', 1, 5, (string)($_SERVER['REMOTE_ADDR'] ?? ''))) {
        json_response(['status' => 'error', 'message' => 'Please wait a moment before posting again'], 429);
    }

    if (!nx_rate_limit_consume('shoutbox_window', 6, 60, (string)($_SERVER['REMOTE_ADDR'] ?? ''))) {
        json_response(['status' => 'error', 'message' => 'Too many messages in a short time'], 429);
    }

    $username = $usernameSession !== ''
        ? $usernameSession
        : trim($_POST['username'] ?? '');

    $message  = trim($_POST['message'] ?? '');
    $honeypot = trim((string)($_POST['company'] ?? ''));

    if ($honeypot !== '') {
        json_response(['status' => 'error', 'message' => 'Spam detected'], 403);
    }

    if ($username === '' || $message === '') {
        json_response(['status' => 'error', 'message' => 'Name und Nachricht sind erforderlich'], 400);
    }

    if (mb_strlen($message) > 500) {
        json_response(['status' => 'error', 'message' => 'Nachricht max. 500 Zeichen'], 400);
    }

    $stmt = $_database->prepare("
        INSERT INTO plugins_shoutbox_messages
        (created_at, username, message)
        VALUES (NOW(), ?, ?)
    ");

    if (!$stmt) {
        json_response(['status' => 'error', 'message' => $_database->error], 500);
    }

    $stmt->bind_param('ss', $username, $message);

    if (!$stmt->execute()) {
        json_response(['status' => 'error', 'message' => $stmt->error], 500);
    }

    $stmt->close();

    json_response(['status' => 'success']);
}

/* =========================================================
   GET → Nachrichten laden
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $result = $_database->query("
        SELECT
            id,
            created_at AS timestamp,
            username,
            message
        FROM plugins_shoutbox_messages
        ORDER BY created_at DESC
        LIMIT 100
    ");

    if (!$result) {
        json_response(['status' => 'error', 'message' => $_database->error], 500);
    }

    $messages = [];

    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'id'        => (int)$row['id'],
            'timestamp' => $row['timestamp'], // DATETIME
            'username'  => $row['username'],
            'message'   => $row['message'],
        ];
    }

    $result->free();

    json_response(['status' => 'success', 'messages' => $messages]);
}

/* =========================================================
   Fallback
========================================================= */
json_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
