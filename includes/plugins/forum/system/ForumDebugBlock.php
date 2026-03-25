<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\forum\ForumACL;
use nexpell\forum\ForumContext;

require_once __DIR__ . '/ForumContext.php';
require_once __DIR__ . '/ForumACL.php';

global $_database;

/* ============================================================
   USER / ROLE KONTEXT (NUR MULTI-ROLE!)
============================================================ */
$userID = (int)($_SESSION['userID'] ?? 0);

if ($userID === 0) {
    $roleIDs = [11]; // Gast
} else {
    $roleIDs = $_SESSION['roles'] ?? [];
    if (empty($roleIDs)) {
        $roleIDs = [12]; // Fallback User
    }
}

/* Rollen-Namen laden */
$roleNames = [];
$ids = implode(',', array_map('intval', $roleIDs));

$res = safe_query("
    SELECT roleID, role_name
    FROM user_roles
    WHERE roleID IN ($ids)
");

while ($r = mysqli_fetch_assoc($res)) {
    $roleNames[(int)$r['roleID']] = $r['role_name'];
}

/* ============================================================
   KONTEXT
============================================================ */
$ctx = ForumContext::fromRequest();

$boardID    = (int)$ctx->boardID;
$categoryID = (int)$ctx->categoryID;
$threadID   = (int)$ctx->threadID;

/* ============================================================
   DEBUG AKTIV
============================================================ */
$ACL_DEBUG = true;
if (!$ACL_DEBUG) {
    return;
}

/* ============================================================
   ACL LABELS
============================================================ */
$aclPermissions = [
    'view'   => 'Sehen',
    'read'   => 'Lesen',
    'post'   => 'Neues Thema',
    'reply'  => 'Antworten',
    'edit'   => 'Bearbeiten',
    'delete' => 'Löschen',
    'mod'    => 'Moderator'
];

/* ============================================================
   UI START
============================================================ */
echo '<div class="card border-dark mb-4" style="font-size:13px">';
echo '<div class="card-header bg-dark text-white fw-semibold">
<i class="bi bi-shield-lock me-1"></i>
ACL DEBUG – Rollen:
<span class="badge bg-info ms-2">'
    . htmlspecialchars(implode(', ', $roleNames ?: ['Unbekannt'])) .
'</span>
</div>';

echo '<div class="card-body">';

/* ============================================================
   USER / KONTEXT INFO
============================================================ */
echo '
<div class="row mb-3 small">
    <div class="col-md-4">
        <strong>User</strong><br>
        UserID: <span class="badge bg-secondary">'.$userID.'</span><br>
        Rollen: <span class="badge bg-secondary">'.implode(', ', $roleIDs).'</span>
    </div>
    <div class="col-md-4">
        <strong>Forum</strong><br>
        BoardID: <span class="badge bg-secondary">'.$boardID.'</span><br>
        CategoryID: <span class="badge bg-secondary">'.$categoryID.'</span>
    </div>
    <div class="col-md-4">
        <strong>Thread</strong><br>
        ThreadID: <span class="badge bg-secondary">'.$threadID.'</span>
    </div>
</div>
<hr>';

/* ============================================================
   ACL MATRIX
============================================================ */
echo '<h6 class="mb-2">Effektive Rechte (OR über alle Rollen)</h6>';

echo '<div class="table-responsive">';
echo '<table class="table table-sm table-bordered align-middle">';
echo '<thead class="table-light">';
echo '<tr>';
echo '<th>Recht</th>';

foreach ($roleIDs as $rid) {
    $name = $roleNames[$rid] ?? ('Rolle '.$rid);
    echo '<th class="text-center">'.htmlspecialchars($name).' (#'.$rid.')</th>';
}

echo '<th class="text-center">Effektiv</th>';
echo '</tr>';
echo '</thead><tbody>';

foreach ($aclPermissions as $perm => $label) {

    $effective = false;
    $perRole   = [];

    foreach ($roleIDs as $rid) {
        $allowed = ForumACL::check(
            userID: $userID,
            roleID: $rid,
            boardID: $boardID,
            categoryID: $categoryID,
            threadID: $threadID,
            permission: $perm
        );

        $perRole[$rid] = $allowed;
        if ($allowed) {
            $effective = true;
        }
    }

    echo '<tr>';
    echo '<td><strong>'.$label.'</strong></td>';

    foreach ($roleIDs as $rid) {
        $allowed = $perRole[$rid];
        $class   = $allowed ? 'bg-success' : 'bg-danger';
        $text    = $allowed ? 'ERLAUBT' : 'VERBOTEN';

        echo '<td class="text-center">
                <span class="badge '.$class.'">'.$text.'</span>
              </td>';
    }

    $class = $effective ? 'bg-success' : 'bg-danger';
    $text  = $effective ? 'ERLAUBT' : 'VERBOTEN';

    echo '<td class="text-center">
            <span class="badge '.$class.'">'.$text.'</span>
          </td>';

    echo '</tr>';
}

echo '</tbody></table>';
echo '</div>';

/* ============================================================
   LEGENDE
============================================================ */
echo '
<hr>
<div class="small text-muted">
    <strong>ℹ️ Erklärung</strong><br>
    • Jede Rolle wird einzeln geprüft<br>
    • Effektives Recht = <strong>OR</strong> über alle Rollen<br>
    • Sobald eine Rolle erlaubt → ERLAUBT<br>
    • Debug nutzt <strong>keine</strong> Legacy-RoleID
</div>';

echo '</div></div>';
