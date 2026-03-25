<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

use nexpell\LanguageService;
use nexpell\AccessControl;
global $languageService;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/../../../'));
}

if (!file_exists(BASE_PATH . '/system/config.inc.php')) {
    nx_alert('danger', 'alert_not_found', false);
    exit;
}

require_once BASE_PATH . '/system/config.inc.php';
require_once BASE_PATH . '/system/core/init.php';
require_once __DIR__ . '/../system/ForumPermission.php';

// Admin-Rechte prüfen
AccessControl::checkAdminAccess('forum');

// Action bestimmen
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Helper
function forum_redirect(string $site = 'admin_forum', string $action = '')
{
    $url = "admincenter.php?site={$site}";
    if ($action !== "") $url .= "&action={$action}";
    header("Location: {$url}");
    exit;
}

function safeDate($value, string $format = "d.m.Y H:i")
{
    if (is_numeric($value)) {
        $ts = (int)$value;
    } elseif (trim((string)$value) !== '') {
        $ts = strtotime($value);
        if (!$ts) $ts = time();
    } else {
        $ts = time();
    }
    return date($format, $ts);
}

// POST: Kategorien bearbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'categories') {

    $form_action = $_POST['form_action'] ?? '';
    $catID       = (int)($_POST['catID'] ?? 0);
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $position    = (int)($_POST['position'] ?? 0);
    $boardID     = (int)($_POST['boardID'] ?? 0);

    if ($form_action === 'add' && $title !== '') {

        safe_query("
            INSERT INTO plugins_forum_categories (boardID, title, description, position)
            VALUES ($boardID, '" . escape($title) . "', '" . $description . "', $position)
        ");

        $newId = (int)($_database->insert_id ?? 0);
        nx_audit_create('admin_forum', (string)$newId, $title, 'admincenter.php?site=admin_forum&action=categories');
        nx_redirect('admincenter.php?site=admin_forum&action=categories', 'success', 'alert_saved', false);
    }

    if ($form_action === 'edit' && $catID > 0) {

        safe_query("
            UPDATE plugins_forum_categories
               SET boardID      = $boardID,
                   title        = '" . escape($title) . "',
                   description  = '" . $description . "',
                   position     = $position
             WHERE catID        = $catID
        ");

        nx_audit_update('admin_forum', (string)$catID, true, $title, 'admincenter.php?site=admin_forum&action=categories');
        nx_redirect('admincenter.php?site=admin_forum&action=categories', 'success', 'alert_saved', false);
    }

    if ($form_action === 'delete' && $catID > 0) {

        safe_query("DELETE FROM plugins_forum_categories WHERE catID=$catID");
        nx_audit_delete('admin_forum', (string)$catID, (string)$catID, 'admincenter.php?site=admin_forum&action=categories');
        nx_redirect('admincenter.php?site=admin_forum&action=categories', 'success', 'alert_deleted', false);
    }
}

// GET: Kategorien löschen (für zentrales Confirm-Modal) + CSRF
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'categories') {

    $form_action = $_GET['form_action'] ?? '';
    $catID       = (int)($_GET['catID'] ?? 0);
    $csrf        = $_GET['csrf'] ?? '';

    if ($form_action === 'delete' && $catID > 0) {

        if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
            nx_redirect('admincenter.php?site=admin_forum&action=categories', 'danger', 'alert_csrf', false);
        }

        safe_query("DELETE FROM plugins_forum_categories WHERE catID=$catID");
        nx_audit_delete('admin_forum', (string)$catID, (string)$catID, 'admincenter.php?site=admin_forum&action=categories');
        nx_redirect('admincenter.php?site=admin_forum&action=categories', 'success', 'alert_deleted', false);
    }
}

// POST: Thread löschen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'threads') {

    $form_action = $_POST['form_action'] ?? '';

    if ($form_action === 'delete_thread') {

        $threadID = (int)($_POST['threadID'] ?? 0);

        if ($threadID > 0) {
            safe_query("DELETE FROM plugins_forum_posts WHERE threadID=$threadID");
            safe_query("DELETE FROM plugins_forum_threads WHERE threadID=$threadID");

            nx_audit_delete('admin_forum', (string)$threadID, (string)$threadID, 'admincenter.php?site=admin_forum&action=threads');
            nx_redirect('admincenter.php?site=admin_forum&action=threads', 'success', 'alert_deleted', false);
        } else {
            nx_alert('danger', 'alert_not_found', false);
        }
    }
}

// POST: Thread bearbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit_thread') {

    $threadID = (int)($_GET['id'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $catID    = (int)($_POST['catID'] ?? 0);

    if ($threadID > 0 && $title !== '' && $catID > 0) {

        safe_query("
            UPDATE plugins_forum_threads
               SET title      = '" . escape($title) . "',
                   catID      = $catID,
                   updated_at = UNIX_TIMESTAMP()
             WHERE threadID   = $threadID
        ");

        nx_audit_update('admin_forum', (string)$threadID, true, $title, 'admincenter.php?site=admin_forum&action=threads');
        nx_redirect('admincenter.php?site=admin_forum&action=threads', 'success', 'alert_saved', false);
    } else {
        nx_alert('danger', 'alert_missing_required', false);
    }
}

// HTML LAYOUT
?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-chat-left-text"></i> <span>Forum verwalten</span>
        </div>

        <div>
            <a class="btn btn-secondary" href="admincenter.php?site=admin_forum&action=board">Boards</a>
            <a class="btn btn-secondary" href="admincenter.php?site=admin_forum&action=categories">Kategorien</a>
            <a class="btn btn-secondary" href="admincenter.php?site=admin_forum&action=threads">Threads</a>
            <a class="btn btn-secondary" href="admincenter.php?site=admin_forum_permissions">Rechte</a>
        </div>
    </div>

    <div class="card-body">

<?php

// ACTION: BOARD
if ($action === 'board') {

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form_action = $_POST['form_action'] ?? '';
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id          = (int)($_POST['id'] ?? 0);

    if ($form_action === 'add' && $title !== '') {

        safe_query("
            INSERT INTO plugins_forum_boards (title, description)
            VALUES ('" . escape($title) . "', '" . escape($description) . "')
        ");

        $newId = (int)($_database->insert_id ?? 0);
        nx_audit_create('admin_forum', (string)$newId, $title, 'admincenter.php?site=admin_forum&action=board');
        nx_redirect('admincenter.php?site=admin_forum&action=board', 'success', 'alert_saved', false);

        } elseif ($form_action === 'edit' && $id > 0) {

            safe_query("
                UPDATE plugins_forum_boards
                SET title        = '" . escape($title) . "',
                    description  = '" . escape($description) . "'
                WHERE boardID      = $id
            ");

            nx_audit_update('admin_forum', (string)$id, true, $title, 'admincenter.php?site=admin_forum&action=board');
            nx_redirect('admincenter.php?site=admin_forum&action=board', 'success', 'alert_saved', false);

        } elseif ($form_action === 'delete' && $id > 0) {

            safe_query("DELETE FROM plugins_forum_boards WHERE boardID=$id");

            nx_audit_delete('admin_forum', (string)$id, (string)$id, 'admincenter.php?site=admin_forum&action=board');
            nx_redirect('admincenter.php?site=admin_forum&action=board', 'success', 'alert_deleted', false);

        } else {
            nx_alert('danger', 'alert_missing_required', false);
        }

        forum_redirect('admin_forum', 'board');
    }

    $boards = safe_query("SELECT * FROM plugins_forum_boards ORDER BY position ASC");
?>

<h2>Board hinzufügen</h2>

<form method="post" class="mb-4">
    <input type="hidden" name="form_action" value="add">

    <input type="text" class="form-control mb-2" name="title" placeholder="Titel" required>
    <textarea class="form-control mb-2" name="description" placeholder="Beschreibung"></textarea>

    <button type="submit" class="btn btn-success">Board anlegen</button>
</form>

<h2>Bestehende Boards</h2>

<table class="table table-bordered table-hover">
<thead>
<tr>
    <th>ID</th>
    <th>Titel</th>
    <th>Beschreibung</th>
    <th>Rechte</th>
    <th>Aktionen</th>
</tr>
</thead>

<tbody>
<?php while ($b = mysqli_fetch_assoc($boards)): ?>

<?php
// Prüfen, ob Board eigene Rechte hat
$hasOwnPerms = ForumPermission::hasLocalPermission('forum', $b['boardID']);
?>
<form method="post">
<tr>
    <td><?= $b['boardID'] ?></td>

    <td>
        <input name="title" class="form-control"
               value="<?= htmlspecialchars($b['title']) ?>">
    </td>

    <td>
        <textarea name="description" class="form-control"><?= htmlspecialchars($b['description']) ?></textarea>
    </td>

    <td>
        <?php if ($hasOwnPerms): ?>
            <span class="badge bg-warning">Überschrieben</span>
        <?php else: ?>
            <span class="badge bg-success">Vererbt</span>
        <?php endif; ?>

        <br>
        <a href="admincenter.php?site=admin_forum_permissions&type=forum&id=<?= $b['boardID'] ?>"
           class="btn btn-outline-secondary mt-1">
           Rechte bearbeiten
        </a>
    </td>

    <td>
        <input type="hidden" name="id" value="<?= $b['boardID'] ?>">

        <button name="form_action"
                value="edit"
                class="btn btn-warning d-inline-flex align-items-center gap-1 w-auto">
            <i class="bi bi-pencil-square"></i> Speichern
        </button>

        <?php
        $deleteUrl = 'admincenter.php?site=admin_forum&action=board'
                . '&form_action=delete'
                . '&id=' . intval($b['boardID'])
                . '&csrf=' . urlencode($_SESSION['csrf']);
        ?>

        <button type="button"
                class="btn btn-danger d-inline-flex align-items-center gap-1 w-auto"
                data-bs-toggle="modal"
                data-bs-target="#confirmDeleteModal"
                data-delete-url="<?= htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') ?>">
            <i class="bi bi-trash3"></i> Löschen
        </button>
    </td>
</tr>
</form>
<?php endwhile; ?>
</tbody>
</table>

<?php

// ACTION: CATEGORIES
} elseif ($action === 'categories') {

    // Boards laden
    $boards = [];
    $res = safe_query("SELECT boardID, title FROM plugins_forum_boards ORDER BY title ASC");
    while ($row = mysqli_fetch_assoc($res)) $boards[] = $row;

    // Kategorien laden
    $categories = [];
    $res2 = safe_query("SELECT * FROM plugins_forum_categories ORDER BY position ASC");
    while ($row = mysqli_fetch_assoc($res2)) $categories[] = $row;
?>

<h2>Kategorie hinzufügen</h2>

<form method="post" class="mb-4">
    <input type="hidden" name="form_action" value="add">

    <select name="boardID" class="form-control mb-2" required>
        <option value="">Board auswählen</option>
        <?php foreach ($boards as $b): ?>
        <option value="<?= $b['boardID'] ?>">
            <?= htmlspecialchars($b['title']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="title" class="form-control mb-2" placeholder="Titel" required>
    <textarea name="description" class="form-control mb-2" placeholder="Beschreibung"></textarea>
    <input type="number" name="position" class="form-control mb-2" value="0">

    <button class="btn btn-success">Kategorie anlegen</button>
</form>

<h2>Bestehende Kategorien</h2>

<div class="table-responsive">
<table class="table">
<thead>
<tr>
    <th>ID</th>
    <th>Titel</th>
    <th>Beschreibung</th>
    <th>Board</th>
    <th>Position</th>
    <th>Rechte</th>
    <th>Aktion</th>
</tr>
</thead>

<tbody>
<?php foreach ($categories as $cat): ?>

<?php
$local = ForumPermission::hasLocalPermission('category', $cat['catID']);
?>
<form method="post">
<tr>
    <td><?= $cat['catID'] ?></td>

    <td>
        <input name="title" class="form-control"
               value="<?= htmlspecialchars($cat['title']) ?>">
    </td>

    <td>
        <textarea name="description" class="form-control"><?= htmlspecialchars($cat['description']) ?></textarea>
    </td>

    <td>
        <select name="boardID" class="form-control">
            <?php foreach ($boards as $b): ?>
            <option value="<?= $b['boardID'] ?>" <?= ($b['boardID'] == $cat['boardID']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['title']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </td>

    <td>
        <input type="number" name="position" class="form-control" value="<?= $cat['position'] ?>">
    </td>

    <td>
        <?php if ($local): ?>
            <span class="badge bg-warning">Überschrieben</span>
        <?php else: ?>
            <span class="badge bg-success">Vererbt</span>
        <?php endif; ?>

        <br>
        <a href="admincenter.php?site=admin_forum_permissions&type=category&id=<?= $cat['catID'] ?>"
           class="btn btn-outline-secondary mt-1">
            Rechte bearbeiten
        </a>
    </td>

    <td>
        <input type="hidden" name="catID" value="<?= $cat['catID'] ?>">

        <button name="form_action"
                value="edit"
                class="btn btn-warning d-inline-flex align-items-center gap-1 w-auto">
            <i class="bi bi-pencil-square"></i> Speichern
        </button>

        <?php
        $deleteUrl = 'admincenter.php?site=admin_forum'
                . '&action=categories'
                . '&form_action=delete'
                . '&catID=' . intval($cat['catID'])
                . '&csrf=' . urlencode($_SESSION['csrf']);
        ?>

        <button type="button"
                class="btn btn-danger d-inline-flex align-items-center gap-1 w-auto"
                data-bs-toggle="modal"
                data-bs-target="#confirmDeleteModal"
                data-delete-url="<?= htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') ?>">
            <i class="bi bi-trash3"></i> Löschen
        </button>
    </td>
</tr>
</form>
<?php endforeach; ?>
</tbody>
</table>
</div>

<?php

// ACTION: THREADS
} elseif ($action === 'threads') {

    $threads = [];
    $res = safe_query("
        SELECT t.*, c.title AS cat_title, u.username
          FROM plugins_forum_threads t
     LEFT JOIN plugins_forum_categories c ON t.catID = c.catID
     LEFT JOIN users u ON t.userID = u.userID
      ORDER BY t.created_at DESC
    ");

    while ($row = mysqli_fetch_assoc($res)) {

        $row['created_at'] = safeDate($row['created_at']);
        $row['localPerms'] = ForumPermission::hasLocalPermission('thread', $row['threadID']);

        $threads[] = $row;
    }
?>
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<h2>Threads</h2>
<div class="alert alert-info">
        <i class="bi bi-shield-lock me-1"></i>
        Thread-Steuerung &amp; Rechte

        <p class="mb-2">
            Jeder dieser Thread`s besitzt <strong>eigene Steuerungs- und Rechteoptionen</strong>.
            Diese gelten <strong>ausschließlich für diesen Thread</strong> und
            überschreiben die Einstellungen der Kategorie und des Forums.
        </p>

        <ul class="mb-0 small">
            <li>
                <strong>🔒 Sperren</strong> – verhindert neue Antworten.
                <br>
                <span class="text-muted">
                    Moderatoren und Administratoren können weiterhin antworten.
                </span>
            </li>

            <li>
                <strong>📌 Anpinnen</strong> – fixiert den Thread dauerhaft am Anfang der Liste.
            </li>

            <li>
                <strong>🛡 Rechte</strong> – bestimmt, wer diesen Thread sehen, lesen,
                beantworten oder moderieren darf.
            </li>
        </ul>
</div>
        <div class="alert alert-warning mt-3 mb-3 small">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            Änderungen an Kategorie- oder Forum-Rechten haben
            <strong>keinen Einfluss</strong>, solange dieser Thread eigene Regeln besitzt.
        </div>

<table class="table">
<thead>
<tr>
    <th>ID</th>
    <th>Titel</th>
    <th>Kategorie</th>
    <th>Autor</th>
    <th>Erstellt</th>
    <th>Rechte</th>
    <th>Status</th>
    <th>Moderation</th>
</tr>
</thead>

<tbody>
<?php foreach ($threads as $t): ?>
<tr>
    <td><?= $t['threadID'] ?></td>
    <td><?= htmlspecialchars($t['title']) ?></td>

    <td><?= htmlspecialchars($t['cat_title']) ?></td>
    <td><?= htmlspecialchars($t['username'] ?? 'Gast') ?></td>

    <td><?= $t['created_at'] ?></td>

    <td>
        <?php if ($t['localPerms']): ?>
            <span class="badge bg-warning">Überschrieben</span>
        <?php else: ?>
            <span class="badge bg-success">Vererbt</span>
        <?php endif; ?>

        <br>
        <a href="admincenter.php?site=admin_forum_permissions&type=thread&id=<?= $t['threadID'] ?>"
           class="btn btn-outline-secondary mt-1">
            Rechte bearbeiten
        </a>
    </td>

    <td>
    <?php if ((int)$t['is_locked'] === 1): ?>
        <span class="badge bg-danger me-1">
            <i class="bi bi-lock-fill"></i> Gesperrt
        </span>
    <?php endif; ?>

    <?php if ((int)$t['is_pinned'] === 1): ?>
        <span class="badge bg-warning text-dark">
            <i class="bi bi-pin-fill"></i> Gepinnt
        </span>
    <?php endif; ?>

    <?php if ((int)$t['is_locked'] === 0 && (int)$t['is_pinned'] === 0): ?>
        <span class="badge bg-success">Normal</span>
    <?php endif; ?>
</td>

    <td>
        <a href="admincenter.php?site=admin_forum&action=edit_thread&id=<?= $t['threadID'] ?>"
           class="btn btn-primary">Bearbeiten</a>

        <!-- Sperren / Entsperren -->
        <form method="post"
              action="/includes/plugins/forum/system/ForumThreadModerationActions.php"
              class="d-inline">

            <input type="hidden" name="action" value="toggle_lock">
            <input type="hidden" name="threadID" value="<?= (int)$t['threadID'] ?>">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

            <button type="submit" class="btn btn-warning">
                <i class="bi bi-lock"></i> Sperren / Entsperren
            </button>
        </form>

        <!-- Pin / Unpin -->
        <form method="post"
              action="/includes/plugins/forum/system/ForumThreadModerationActions.php"
              class="d-inline ms-1">

            <input type="hidden" name="action" value="toggle_pin">
            <input type="hidden" name="threadID" value="<?= (int)$t['threadID'] ?>">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

            <button type="submit" class="btn btn-info">
                <i class="bi bi-pin"></i> Pin / Unpin
            </button>
        </form>


        <form method="post"
              action="/includes/plugins/forum/system/ForumThreadModerationActions.php"
              class="d-inline ms-1"
              onsubmit="return confirm('Thread löschen?')">

            <input type="hidden" name="action" value="delete_thread">
            <input type="hidden" name="threadID" value="<?= (int)$t['threadID'] ?>">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

            <button type="submit" class="btn btn-danger">
                Löschen
            </button>
        </form>

    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php

// ACTION: THREAD EDIT
} elseif ($action === 'edit_thread') {

    $threadID = intval($_GET['id']);
    $thread = mysqli_fetch_assoc(safe_query("SELECT * FROM plugins_forum_threads WHERE threadID=$threadID"));
    $cats = safe_query("SELECT * FROM plugins_forum_categories ORDER BY position ASC");
?>

<h2>Thread bearbeiten</h2>

<form method="post">

    <select name="catID" class="form-select mb-2">
        <?php while ($cat = mysqli_fetch_assoc($cats)): ?>
        <option value="<?= $cat['catID'] ?>"
            <?= ($cat['catID'] == $thread['catID']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['title']) ?>
        </option>
        <?php endwhile; ?>
    </select>

    <input type="text" name="title" class="form-control mb-2"
           value="<?= htmlspecialchars($thread['title']) ?>">

    <button class="btn btn-primary">Speichern</button>
</form>

<?php

// DEFAULT: ÜBERSICHT
} else {

    $boards = [];

    $res_boards = safe_query("
        SELECT boardID, title, description 
          FROM plugins_forum_boards
      ORDER BY position ASC
    ");

    while ($board = mysqli_fetch_assoc($res_boards)) {

        $boardID = $board['boardID'];

        // Kategorien laden
        $categories = [];
        $res_cat = safe_query("
            SELECT catID, title, description, position
              FROM plugins_forum_categories
             WHERE boardID = $boardID
          ORDER BY position ASC
        ");

        while ($cat = mysqli_fetch_assoc($res_cat)) {

            // Threads laden
            $threads = [];
            $res_th = safe_query("
                SELECT t.threadID, t.title, t.created_at, u.username
                  FROM plugins_forum_threads t
             LEFT JOIN users u ON t.userID = u.userID
                 WHERE t.catID = {$cat['catID']}
              ORDER BY t.created_at DESC
            ");

            while ($th = mysqli_fetch_assoc($res_th)) {
                $th['created_at'] = safeDate($th['created_at']);
                $threads[] = $th;
            }

            $categories[] = [
                'catID' => $cat['catID'],
                'title' => $cat['title'],
                'description' => $cat['description'],
                'position' => $cat['position'],
                'threads' => $threads
            ];
        }

        $boards[] = [
            'id' => $boardID,
            'title' => $board['title'],
            'description' => $board['description'],
            'categories' => $categories
        ];
    }
?>

<h2>Forum Übersicht</h2>

<?php foreach ($boards as $board): ?>
<div class="card mb-3">
    <div class="card-header">
        <h4><?= htmlspecialchars($board['title']) ?></h4>
        <p><?= nl2br(htmlspecialchars($board['description'])) ?></p>

        <a class="btn btn-outline-secondary"
           href="admincenter.php?site=admin_forum_permissions&type=forum&id=<?= $board['id'] ?>">
            Rechte bearbeiten
        </a>
    </div>

    <div class="card-body">

    <?php if (!empty($board['categories'])): ?>

        <?php foreach ($board['categories'] as $cat): ?>
        <div class="card mb-3 ms-3">
            <div class="card-header">
                <strong><?= htmlspecialchars($cat['title']) ?></strong>
                <small class="text-muted">(Position: <?= $cat['position'] ?>)</small>

                <a class="btn btn-outline-secondary float-end"
                   href="admincenter.php?site=admin_forum_permissions&type=category&id=<?= $cat['catID'] ?>">
                    Rechte bearbeiten
                </a>
            </div>

            <div class="card-body">

                <p><?= nl2br(htmlspecialchars($cat['description'])) ?></p>

                <?php if (!empty($cat['threads'])): ?>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Autor</th>
                        <th>Datum</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($cat['threads'] as $th): ?>
                    <tr>
                        <td><?= htmlspecialchars($th['title']) ?></td>
                        <td><?= htmlspecialchars($th['username']) ?></td>
                        <td><?= $th['created_at'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>

                <?php else: ?>
                <div class="alert alert-info">Keine Threads in dieser Kategorie.</div>
                <?php endif; ?>

            </div>
        </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="alert alert-info">Keine Kategorien in diesem Board.</div>
    <?php endif; ?>

    </div>
</div>
<?php endforeach; ?>

<?php } ?>

    </div>
</div>
