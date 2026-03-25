<?php
declare(strict_types=1);

/**********************************************************************
 * NEXPELL – FORUM ACL SAVE API (FINAL & CLEAN)
 *
 * - echtes SQL NULL (Vererbung)
 * - 1 = erlauben
 * - (optional) 0 = explizit verbieten
 * - löscht Datensatz automatisch, wenn alles NULL ist
 **********************************************************************/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once BASE_PATH . '/system/config.inc.php';
require_once BASE_PATH . '/system/core/init.php';

header('Content-Type: application/json');

global $_database;

/* ============================================================
   AUTH (NUR ADMIN)
============================================================ */
if ((int)($_SESSION['roleID'] ?? 0) !== 1) {
    echo json_encode(['saved' => false, 'error' => 'No permission']);
    exit;
}

/* ============================================================
   JSON INPUT
============================================================ */
$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    echo json_encode(['saved' => false, 'error' => 'Invalid JSON']);
    exit;
}

/* ============================================================
   CSRF
============================================================ */
if (
    empty($data['csrf']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $data['csrf'])
) {
    echo json_encode(['saved' => false, 'error' => 'CSRF']);
    exit;
}

/* ============================================================
   INPUT
============================================================ */
$type   = $data['type'] ?? '';
$id     = (int)($data['id'] ?? 0);
$roleID = (int)($data['role_id'] ?? 0);
$field  = $data['field'] ?? '';
$value  = $data['value'] ?? null;

/* ============================================================
   FIELD MAP
============================================================ */
$map = [
    'view'   => 'can_view',
    'read'   => 'can_read',
    'post'   => 'can_post',
    'reply'  => 'can_reply',
    'edit'   => 'can_edit',
    'delete' => 'can_delete',
    'mod'    => 'is_mod'
];

if (!isset($map[$field])) {
    echo json_encode(['saved' => false, 'error' => 'Invalid field']);
    exit;
}

$dbField = $map[$field];

/* ============================================================
   VALUE HANDLING (WICHTIG)
   NULL  = erben
   1     = erlauben
   0     = explizit verbieten (optional)
============================================================ */
if ($value === null || $value === false || $value === 'inherit') {
    $sqlValue = 'NULL';          // 🔑 echtes SQL NULL
} else {
    $sqlValue = (string)(int)$value; // 0 oder 1
}

/* ============================================================
   TABLE MAP
============================================================ */
switch ($type) {
    case 'forum':
        $table = 'plugins_forum_permissions_board';
        $idCol = 'boardID';
        break;

    case 'category':
        $table = 'plugins_forum_permissions_categories';
        $idCol = 'catID';
        break;

    case 'thread':
        $table = 'plugins_forum_permissions_threads';
        $idCol = 'threadID';
        break;

    default:
        echo json_encode(['saved' => false, 'error' => 'Invalid type']);
        exit;
}

if ($id <= 0 || $roleID <= 0) {
    echo json_encode(['saved' => false, 'error' => 'Invalid ID']);
    exit;
}

/* ============================================================
   UPSERT (ECHTES NULL!)
============================================================ */
safe_query("
    INSERT INTO {$table} ({$idCol}, role_id, {$dbField})
    VALUES ({$id}, {$roleID}, {$sqlValue})
    ON DUPLICATE KEY UPDATE {$dbField} = {$sqlValue}
");

/* ============================================================
   CLEANUP:
   Wenn ALLE Felder NULL → Datensatz löschen
============================================================ */
$res = safe_query("
    SELECT can_view, can_read, can_post, can_reply, can_edit, can_delete, is_mod
    FROM {$table}
    WHERE {$idCol} = {$id}
      AND role_id = {$roleID}
    LIMIT 1
");

$row = mysqli_fetch_assoc($res);

if ($row) {
    $hasAnyValue = false;

    foreach ($row as $v) {
        if ($v !== null) {
            $hasAnyValue = true;
            break;
        }
    }

    // 🔥 alles NULL → Vererbung → Datensatz weg
    if (!$hasAnyValue) {
        safe_query("
            DELETE FROM {$table}
            WHERE {$idCol} = {$id}
              AND role_id = {$roleID}
        ");
    }
}

/* ============================================================
   SQL ERROR DEBUG
============================================================ */
if ($_database->error) {
    echo json_encode([
        'saved' => false,
        'error' => $_database->error
    ]);
    exit;
}

echo json_encode(['saved' => true]);
