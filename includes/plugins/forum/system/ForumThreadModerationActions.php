<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

/* ============================================================
   SESSION
============================================================ */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ============================================================
   FORUM CONTEXT
============================================================ */
if (!defined('IS_FORUM')) {
    define('IS_FORUM', true);
}

/* ============================================================
   LOAD ACL
============================================================ */
require_once __DIR__ . '/ForumACL.php';
use nexpell\forum\ForumACL;

/* ============================================================
   CSRF
============================================================ */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/* ============================================================
   DB BOOTSTRAP (NO CORE)
============================================================ */
$root = realpath(__DIR__ . '/../../../../');
require_once $root . '/system/config.inc.php';

$_database = $_database ?? new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($_database->connect_errno) {
    http_response_code(500);
    die("DB Error: " . $_database->connect_error);
}
$_database->set_charset('utf8mb4');

/* ============================================================
   BASIS
============================================================ */
$userID   = (int)($_SESSION['userID'] ?? 0);
$threadID = (int)($_POST['threadID'] ?? $_GET['threadID'] ?? 0);
$action   = $_POST['action'] ?? $_GET['action'] ?? '';

if ($userID <= 0 || $threadID <= 0) {
    http_response_code(404);
    die("<div class='alert alert-danger'>Ungültige Anfrage.</div>");
}

/* ============================================================
   ROLLEN ERMITTELN
============================================================ */
$roleIDs = [];

if ($userID === 0) {
    $roleIDs = [11];
} elseif (!empty($_SESSION['roles']) && is_array($_SESSION['roles'])) {
    $roleIDs = array_map('intval', $_SESSION['roles']);
} elseif (!empty($_SESSION['roleID'])) {
    $roleIDs = [(int)$_SESSION['roleID']];
} else {
    $roleIDs = [12];
}

/* ============================================================
   THREAD LADEN (inkl. BOARD)
============================================================ */
$res = $_database->query("
    SELECT 
        t.threadID,
        t.is_locked,
        t.catID,
        c.boardID
    FROM plugins_forum_threads t
    JOIN plugins_forum_categories c ON c.catID = t.catID
    WHERE t.threadID = {$threadID}
      AND t.is_deleted = 0
    LIMIT 1
");

$thread = $res?->fetch_assoc();
if (!$thread) {
    http_response_code(404);
    die("<div class='alert alert-danger'>Thread nicht gefunden.</div>");
}

/* ============================================================
   MOD-RECHT PRÜFEN
============================================================ */
$canModerate = false;

foreach ($roleIDs as $rid) {
    if (ForumACL::check(
        $userID,
        $rid,
        (int)$thread['boardID'],
        (int)$thread['catID'],
        (int)$thread['threadID'],
        'is_mod'
    )) {
        $canModerate = true;
        break;
    }
}

if (!$canModerate) {
    die("<div class='alert alert-danger'>Keine Berechtigung.</div>");
}

/* ============================================================
   CSRF CHECK
============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("<div class='alert alert-danger'>CSRF Fehler.</div>");
    }
}

/* ============================================================
   ACTIONS
============================================================ */
switch ($action) {

    case 'toggle_lock':
        $_database->query("
            UPDATE plugins_forum_threads
            SET is_locked = IF(is_locked = 1, 0, 1)
            WHERE threadID = {$threadID}
        ");
        $_SESSION['flash_success'] = 'Thread-Status geändert.';
        break;

    case 'toggle_pin':
        $_database->query("
            UPDATE plugins_forum_threads
            SET is_pinned = IF(is_pinned = 1, 0, 1)
            WHERE threadID = {$threadID}
        ");
        $_SESSION['flash_success'] = 'Pin-Status geändert.';
        break;

    case 'delete_thread':
        $_database->begin_transaction();
        $_database->query("
            UPDATE plugins_forum_posts
            SET is_deleted = 1
            WHERE threadID = {$threadID}
        ");
        $_database->query("
            UPDATE plugins_forum_threads
            SET is_deleted = 1
            WHERE threadID = {$threadID}
        ");
        $_database->commit();
        $_SESSION['flash_success'] = 'Thread wurde gelöscht.';
        break;

    default:
        $_SESSION['flash_error'] = 'Ungültige Aktion.';
}

/* ============================================================
   REDIRECT
============================================================ */
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/index.php?site=forum'));
exit;





/*    // 🔀 MOVE
    case 'move_thread':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $cats = safe_query("
                SELECT catID, title
                FROM plugins_forum_categories
                ORDER BY position ASC
            ");

            echo "<form method='post'>
                    <input type='hidden' name='csrf' value='{$_SESSION['csrf']}'>
                    <h4>Thread verschieben</h4>
                    <select name='newcat' class='form-select'>";

            while ($c = mysqli_fetch_assoc($cats)) {
                echo "<option value='{$c['catID']}'>{$c['title']}</option>";
            }

            echo "</select>
                  <button class='btn btn-primary mt-2'>Verschieben</button>
                  </form>";
            exit;
        }

        $newCat = (int)$_POST['newcat'];

        safe_query("
            UPDATE plugins_forum_threads
            SET catID = {$newCat}
            WHERE threadID = {$threadID}
        ");

        echo "<div class='alert alert-success'>Thread verschoben.</div>";
        break;

    // ✏ TITLE
    case 'edit_title':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            echo "<form method='post'>
                    <input type='hidden' name='csrf' value='{$_SESSION['csrf']}'>
                    <h4>Threadtitel bearbeiten</h4>
                    <input class='form-control' name='title'
                           value='" . htmlspecialchars($thread['title'], ENT_QUOTES) . "'>
                    <button class='btn btn-primary mt-2'>Speichern</button>
                  </form>";
            exit;
        }

        $newTitle = escape(trim($_POST['title']));

        safe_query("
            UPDATE plugins_forum_threads
            SET title = '{$newTitle}'
            WHERE threadID = {$threadID}
        ");

        echo "<div class='alert alert-success'>Titel geändert.</div>";
        break;

    default:
        echo "<div class='alert alert-danger'>Ungültige Aktion.</div>";
}

exit;
*/
