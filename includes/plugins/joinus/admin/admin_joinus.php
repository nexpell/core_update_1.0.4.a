<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\AccessControl;
use nexpell\Email;

// Admin-Zugriff prüfen
AccessControl::checkAdminAccess('joinus');

global $_database, $languageService;

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Logikdateien IMMER laden
require_once __DIR__ . '/../system/joinus_user.php';

// CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($action === 'save_setting') {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        nx_redirect('admincenter.php', 'danger', 'transaction_invalid', false);
    }

    // ROLLEN SPEICHERN
    $roles = array_map('intval', $_POST['roles'] ?? []);

// alle JoinUs-Rollen deaktivieren
safe_query("UPDATE plugins_joinus_roles SET is_enabled = 0");

// ausgewählte aktivieren
foreach ($roles as $roleId) {
    safe_query("
        INSERT INTO plugins_joinus_roles (role_id, is_enabled)
        VALUES ($roleId, 1)
        ON DUPLICATE KEY UPDATE is_enabled = 1
    ");
}

// SQUADS SPEICHERN
$squads = array_map('intval', $_POST['squads'] ?? []);

safe_query("UPDATE plugins_joinus_squads SET is_enabled = 0");

foreach ($squads as $id) {
    safe_query("
        UPDATE plugins_joinus_squads
        SET is_enabled = 1
        WHERE id = $id
    ");
}

foreach ($_POST['types'] as $key => $data) {

    $label   = trim($data['label'] ?? '');
    $enabled = isset($data['enabled']) ? 1 : 0;
    $sort    = (int)($data['sort'] ?? 0);

    safe_query("
        UPDATE plugins_joinus_types
        SET
            label = '" . escape($label) . "',
            is_enabled = $enabled,
            sort_order = $sort
        WHERE type_key = '" . escape($key) . "'
    ");
}

nx_audit_update('admin_joinus', null, true, null, 'admincenter.php?site=admin_joinus&action=setting');
nx_alert('success', 'alert_saved', true);
nx_redirect('admincenter.php?site=admin_joinus&action=setting');
}

// POST ACTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        nx_redirect('admincenter.php?site=admin_joinus', 'danger', 'transaction_invalid', false);
    }

    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($id > 0) {

        // STATUS
        if ($action === 'status') {

            $allowedStatus = ['new','review','accepted','rejected'];

            $newStatus = $_POST['status'] ?? '';
            if (!in_array($newStatus, $allowedStatus, true)) {
                nx_redirect('admincenter.php?site=admin_joinus', 'danger', 'transaction_invalid', false);
            }

            // alten Status holen
            $oldStmt = $_database->prepare(
                "SELECT status, email, name, type
                 FROM plugins_joinus_applications
                 WHERE id=?"
            );
            $oldStmt->bind_param("i", $id);
            $oldStmt->execute();
            $old = $oldStmt->get_result()->fetch_assoc();
            $oldStmt->close();

            if ($old) {
                $stmt = $_database->prepare(
                    "UPDATE plugins_joinus_applications
                     SET status = ?, 
                         last_status = ?, 
                         processed_at = NOW()
                     WHERE id = ?"
                );
                $stmt->bind_param("ssi", $newStatus, $old['status'], $id);
                $hasChanged = ($old['status'] !== $newStatus);
                if ($stmt->execute() && $stmt->affected_rows > 0) $hasChanged = true;
                $stmt->close();

                if ($hasChanged) {
                    nx_audit_update('admin_joinus', (string)$id, true, $old['name'] ?? null, 'admincenter.php?site=admin_joinus');
                    nx_alert('success', 'alert_saved', true);
                } else {
                    nx_audit_update('admin_joinus', (string)$id, false, $old['name'] ?? null, 'admincenter.php?site=admin_joinus');
                    nx_alert('warning', 'alert_no_changes', true);
                }

                // Mail nur bei echter Änderung
                if ($old['status'] !== $newStatus) {
                    joinusSendStatusMail(
                        (int)$id,
                        $old['email'],
                        $old['name'],
                        $newStatus,
                        $old['type']
                    );
                }
            } else {
                nx_alert('danger', 'alert_not_found', true);
            }
        }

        // NOTE
        if ($action === 'note') {
            $note = trim($_POST['note'] ?? '');
            $stmt = $_database->prepare(
                "UPDATE plugins_joinus_applications SET admin_note=? WHERE id=?"
            );
            $stmt->bind_param("si", $note, $id);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                nx_audit_update('admin_joinus', (string)$id, true, null, 'admincenter.php?site=admin_joinus');
                nx_alert('success', 'alert_saved', true);
            } else {
                nx_audit_update('admin_joinus', (string)$id, false, null, 'admincenter.php?site=admin_joinus');
                nx_alert('warning', 'alert_no_changes', true);
            }
            $stmt->close();
        }

        // DELETE
        if ($action === 'delete') {
            $stmt_name = $_database->prepare("SELECT name FROM plugins_joinus_applications WHERE id=? LIMIT 1");
            $stmt_name->bind_param("i", $id);
            $stmt_name->execute();
            $r = $stmt_name->get_result()->fetch_assoc();
            $appName = trim((string)($r['name'] ?? ''));
            $stmt_name->close();

            $stmt = $_database->prepare(
                "DELETE FROM plugins_joinus_applications WHERE id=?"
            );
            $stmt->bind_param("i", $id);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                nx_audit_delete('admin_joinus', (string)$id, ($appName !== '' ? $appName : (string)$id), 'admincenter.php?site=admin_joinus');
                nx_alert('success', 'alert_deleted', true);
            } else {
                nx_alert('danger', 'alert_not_found', true);
            }
            $stmt->close();
        }

        // CREATE USER
        if ($action === 'create_user') {
            $result = createUserFromJoinUs($id);
            nx_audit_action('admin_joinus', 'create_user', 'application', (string)$id, 'admincenter.php?site=admin_joinus', ['id' => (int)$id]);

            if (!empty($result['error'])) {
                nx_alert('danger', (string)($result['message'] ?? 'alert_not_found'), true);
            } elseif (!empty($result['success'])) {
                nx_alert('success', (string)($result['message'] ?? 'alert_saved'), true, true, false);
            } else {
                nx_alert('warning', 'alert_no_changes', true);
            }
        }
    }

    nx_redirect('admincenter.php?site=admin_joinus');
}

// FILTER (GET)
$allowedStatus = ['new','review','accepted','rejected'];
$filterStatus  = $_GET['status'] ?? '';
if (!in_array($filterStatus, $allowedStatus, true)) {
    $filterStatus = '';
}

// LOAD DATA
$applications = [];

if ($filterStatus !== '') {
    $stmt = $_database->prepare(
        "SELECT * FROM plugins_joinus_applications
         WHERE status=?
         ORDER BY created_at DESC"
    );
    $stmt->bind_param("s", $filterStatus);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $_database->query(
        "SELECT * FROM plugins_joinus_applications
         ORDER BY created_at DESC"
    );
}

while ($row = $res->fetch_assoc()) {
    $applications[] = $row;
}

function getJoinusTypeLabelFromDB(string $typeKey): string
    {
        global $_database;

        $stmt = $_database->prepare("
            SELECT label
            FROM plugins_joinus_types
            WHERE type_key = ?
            LIMIT 1
        ");
        $stmt->bind_param('s', $typeKey);
        $stmt->execute();
        $stmt->bind_result($label);
        $stmt->fetch();
        $stmt->close();

        return $label ?? $typeKey;
    }

// HELPERS
function joinusSendStatusMail(
    int $applicationId,
    string $email,
    string $name,
    string $status,
    string $type
): void {
    global $languageService;

    $settings = mysqli_fetch_assoc(
        safe_query("SELECT * FROM settings LIMIT 1")
    );

    $hp_title    = $settings['hptitle'] ?? 'nexpell';
    $hp_url      = $settings['hpurl'] ?? ('https://' . $_SERVER['HTTP_HOST']);
    $admin_email = $settings['adminemail'] ?? ('info@' . $_SERVER['HTTP_HOST']);

    $subjectTpl = $languageService->get("mail_{$status}_subject");
    $textTpl    = $languageService->get("mail_{$status}_text");

    $vars = ['%name%','%type%','%hp_title%','%hp_url%'];
    $typeLabel = getJoinusTypeLabelFromDB($type);

    $repl = [
        $name,
        $typeLabel,
        $hp_title,
        $hp_url
    ];

    $subject = str_replace($vars, $repl, $subjectTpl);
    $text    = str_replace($vars, $repl, $textTpl);

    $fromModule = trim($languageService->get('mail_from_module'));
    if ($fromModule === '' || str_starts_with($fromModule, '[')) {
        $fromModule = 'Join Request';
    }

    $html = joinusWrapMailHtml($subject, $text, $hp_title, $hp_url);

    $result = \nexpell\Email::sendEmail(
        $admin_email,
        $fromModule,
        $email,
        $subject,
        $html
    );

    if (is_array($result) && $result['result'] === 'done') {
        safe_query("
            UPDATE plugins_joinus_applications
            SET mail_sent_at = NOW()
            WHERE id = {$applicationId}
        ");
    }
}

function joinusWrapMailHtml(
    string $subject,
    string $content,
    string $hp_title,
    string $hp_url
): string {
    global $languageService;

    $mailGreeting = $languageService->get('mail_greeting');
    $mailTeamSuffix = $languageService->get('mail_team_suffix');

    return '<!DOCTYPE html>
<html lang="en">
<body style="margin:0;padding:0;background-color:#f4f6f8;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:30px 0;">
<tr><td align="center">

<table width="100%" cellpadding="0" cellspacing="0"
style="max-width:620px;background:#ffffff;border-radius:12px;
box-shadow:0 8px 30px rgba(0,0,0,0.08);
font-family:Arial,Helvetica,sans-serif;overflow:hidden;">

<tr>
<td style="background:linear-gradient(135deg,#fe821d,#ff9b3d);padding:28px 32px;">
<h1 style="margin:0;font-size:22px;color:#fff;">'.htmlspecialchars($hp_title).'</h1>
<p style="margin:6px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">
'.$subject.'
</p>
</td>
</tr>

<tr>
<td style="padding:32px;font-size:15px;line-height:1.6;color:#333;">
'.nl2br($content).'

<p style="margin-top:28px;">
'.htmlspecialchars($mailGreeting).'<br>
<strong>'.$hp_title.' '.htmlspecialchars($mailTeamSuffix).'</strong>
</p>
</td>
</tr>

<tr>
<td style="background:#f9fafb;padding:18px 32px;font-size:13px;color:#777;text-align:center;">
<a href="'.$hp_url.'" style="color:#fe821d;text-decoration:none;">'.$hp_url.'</a><br>
&copy; '.$hp_title.'
</td>
</tr>

</table>

</td></tr>
</table>
</body>
</html>';
}

if ($action === 'setting') {

    // ROLLEN + JOINUS-FREIGABE LADEN
    $roles = [];
    $res = $_database->query("
        SELECT 
            r.roleID,
            r.role_name,
            COALESCE(jr.is_enabled, 0) AS joinus_enabled
        FROM user_roles r
        LEFT JOIN plugins_joinus_roles jr
            ON jr.role_id = r.roleID
        WHERE r.is_active = 1
        ORDER BY r.role_name ASC
    ");

    while ($row = $res->fetch_assoc()) {
        $roles[] = $row;
    }

    // SQUADS (STATISCH + AKTIV AUS SETTINGS)
    // aktive Squads laden
    $squads = [];

    $res = $_database->query("
        SELECT id, name, is_enabled
        FROM plugins_joinus_squads
        ORDER BY name ASC
    ");

    while ($row = $res->fetch_assoc()) {
        $squads[] = $row;
    }

    $types = [];
    $res = $_database->query("
        SELECT *
        FROM plugins_joinus_types
        ORDER BY sort_order ASC
    ");

    while ($row = $res->fetch_assoc()) {
        $types[] = $row;
    }
    ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="save_setting">

        <div class="row g-4">

        <!-- ROLES -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0" style="border-radius:12px;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary"
                        style="width:36px;height:36px;border-radius:10px;">
                    <i class="bi bi-person-badge"></i>
                    </span>
                    <div>
                    <div class="fw-semibold"><?= $languageService->get('admin_roles_title') ?></div>
                    <div class="text-muted small"><?= $languageService->get('admin_roles_subtitle') ?></div>
                    </div>
                </div>

                <span class="badge rounded-pill text-bg-light border text-nowrap">
                    <?= count($roles) ?>
                </span>
                </div>

                <hr class="my-3">

                <!-- 3-column grid -->
                <div class="row g-2">
                <?php foreach ($roles as $role): ?>
                    <?php $isLocked = ((int)$role['roleID'] === 1); ?>
                    <div class="col-12 col-sm-6 col-md-4">
                    <div class="p-2 bg-light bg-opacity-50" style="border-radius:10px;">
                        <div class="form-check m-0">
                        <input class="form-check-input"
                                type="checkbox"
                                name="roles[]"
                                value="<?= (int)$role['roleID'] ?>"
                                <?= (int)$role['joinus_enabled'] === 1 ? 'checked' : '' ?>
                                <?= $isLocked ? 'disabled' : '' ?>>

                        <label class="form-check-label <?= $isLocked ? 'text-muted' : '' ?>">
                            <?= htmlspecialchars($role['role_name']) ?>
                            <?php if ($isLocked): ?>
                            <span class="badge rounded-pill text-bg-light border ms-1"><?= $languageService->get('admin_system_role') ?></span>
                            <?php endif; ?>
                        </label>
                        </div>
                    </div>
                    </div>
                <?php endforeach; ?>
                </div>

                <div class="form-text mt-3">
                <?= $languageService->get('admin_roles_help') ?>
                </div>
            </div>
            </div>
        </div>

        <!-- SQUADS -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0" style="border-radius:12px;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning"
                        style="width:36px;height:36px;border-radius:10px;">
                    <i class="bi bi-people"></i>
                    </span>
                    <div>
                    <div class="fw-semibold"><?= $languageService->get('admin_squads_title') ?></div>
                    <div class="text-muted small"><?= $languageService->get('admin_squads_subtitle') ?></div>
                    </div>
                </div>

                <span class="badge rounded-pill text-bg-light border text-nowrap">
                    <?= count($squads) ?>
                </span>
                </div>

                <hr class="my-3">

                <!-- 2-column grid keeps it readable -->
                <div class="row g-2">
                <?php foreach ($squads as $squad): ?>
                    <div class="col-12 col-sm-6">
                    <div class="p-2 bg-light bg-opacity-50" style="border-radius:10px;">
                        <div class="form-check m-0">
                        <input class="form-check-input"
                                type="checkbox"
                                name="squads[]"
                                value="<?= (int)$squad['id'] ?>"
                                <?= (int)$squad['is_enabled'] === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label">
                            <?= htmlspecialchars($squad['name']) ?>
                        </label>
                        </div>
                    </div>
                    </div>
                <?php endforeach; ?>
                </div>

                <div class="form-text mt-3">
                <?= $languageService->get('admin_squads_help') ?>
                </div>
            </div>
            </div>
        </div>

        <!-- TYPES -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0" style="border-radius:12px;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success"
                        style="width:36px;height:36px;border-radius:10px;">
                    <i class="bi bi-ui-checks-grid"></i>
                    </span>
                    <div>
                    <div class="fw-semibold"><?= $languageService->get('admin_types_title') ?></div>
                    <div class="text-muted small"><?= $languageService->get('admin_types_subtitle') ?></div>
                    </div>
                </div>

                <span class="badge rounded-pill text-bg-light border text-nowrap">
                    <?= count($types) ?>
                </span>
                </div>

                <hr class="my-3">

                <?php
                $lastType = end($types);
                $lastTypeKey = $lastType['type_key'] ?? null;
                reset($types);
                ?>

                <div class="d-flex px-1 pb-2 text-muted small">
                <div class="flex-grow-1"><?= $languageService->get('admin_types_label') ?></div>
                <div class="text-center" style="width:84px;"><?= $languageService->get('admin_types_sort') ?></div>
                <div class="text-center" style="width:68px;"><?= $languageService->get('admin_types_enabled') ?></div>
                </div>

                <div class="d-flex flex-column gap-2">
                <?php foreach ($types as $type): ?>
                    <div class="p-2 bg-light bg-opacity-50" style="border-radius:10px;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="flex-grow-1">
                        <input type="text"
                                name="types[<?= htmlspecialchars($type['type_key']) ?>][label]"
                                value="<?= htmlspecialchars($type['label']) ?>"
                                class="form-control form-control-sm">
                        </div>

                        <div style="width:84px;">
                        <input type="number"
                                name="types[<?= htmlspecialchars($type['type_key']) ?>][sort]"
                                value="<?= (int)$type['sort_order'] ?>"
                                class="form-control form-control-sm text-center">
                        </div>

                        <div class="text-center" style="width:68px;">
                        <div class="form-check d-inline-block m-0">
                            <input class="form-check-input"
                                type="checkbox"
                                name="types[<?= htmlspecialchars($type['type_key']) ?>][enabled]"
                                value="1"
                                <?= $type['is_enabled'] ? 'checked' : '' ?>>
                        </div>
                        </div>
                    </div>

                    <?php if ($type['type_key'] === $lastTypeKey): ?>
                        <div class="text-muted small mt-2">
                        <?= $languageService->get('admin_types_help') ?>
                        </div>
                    <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>

            </div>
            </div>
        </div>

        </div>

    </form>
    <?php
}
 else {

// UI CONFIG
$statusClasses = [
    'new'      => 'secondary',
    'review'   => 'warning',
    'accepted' => 'success',
    'rejected' => 'danger',
];

$typeBadgeClasses = [
    'team'    => 'primary',
    'partner' => 'warning',
    'squad'   => 'success',
];

$applications = [];

$res = $_database->query("
    SELECT 
        a.*,
        s.name AS squad_name,
        r.role_name
    FROM plugins_joinus_applications a
    LEFT JOIN plugins_joinus_squads s
        ON s.id = a.squad_id
    LEFT JOIN user_roles r
        ON r.roleID = a.role
    ORDER BY a.created_at DESC
");

while ($row = $res->fetch_assoc()) {
    $applications[] = $row;
}
?>

<a href="admincenter.php?site=admin_joinus&action=setting" class="btn btn-secondary mb-4 mt-2">
    <?= $languageService->get('setting') ?>
</a>

<?= $joinusNotice ?>

<?php
$kanban = [
    'new'      => [],
    'review'   => [],
    'accepted' => [],
    'rejected' => [],
];

$roleBadgeClasses = [
    'admin'     => 'danger',
    'moderator' => 'warning',
    'member'    => 'primary',
    'user'      => 'secondary',
];

foreach ($applications as $app) {
    $kanban[$app['status']][] = $app;
}
?>

<div class="row g-3">

    <?php foreach ($kanban as $status => $items): ?>

        <div class="col-xl-3 col-lg-6">

            <div class="card joinus-board h-100 d-flex flex-column" data-status="<?= htmlspecialchars($status) ?>">

                <div class="joinus-board-header">
                    <div class="joinus-board-title">
                        <span class="joinus-status-dot" aria-hidden="true"></span>
                        <span><?= $languageService->get('status_'.$status) ?></span>
                    </div>

                    <span class="badge rounded-pill joinus-count">
                        <?= count($items) ?>
                    </span>
                </div>

                <div class="card-body p-2 kanban-column flex-grow-1 joinus-board-body">

                    <?php if (empty($items)): ?>
                        <div class="joinus-empty">
                            <div class="joinus-empty-icon"><i class="bi bi-mailbox"></i></div>
                            <div class="joinus-empty-text"><?= $languageService->get('admin_empty') ?></div>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($items as $app): ?>

                        <?php
                        $type = $app['type'] ?? 'team';
                        if ($type === 'squad' && (int)$app['squad_id'] === 0) {
                            $type = 'team';
                        }
                        $badgeClass = $typeBadgeClasses[$type] ?? 'secondary';

                        $roleName  = $app['role_custom'] ?: ($app['role_name'] ?? '-');
                        $roleKey   = strtolower($app['role_name'] ?? '');
                        $roleClass = $roleBadgeClasses[$roleKey] ?? 'secondary';
                        ?>

                        <div class="mb-2">
                            <div class="p-3">

                                <!-- HEADER -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong><?= htmlspecialchars($app['name']) ?></strong>
                                        <div class="small text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('d.m.Y H:i', strtotime($app['created_at'])) ?>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <?php if ($app['mail_sent_at']): ?>
                                            <i class="bi bi-envelope-check text-success fs-4"
                                                title="<?= htmlspecialchars(str_replace('%date%', date('d.m.Y H:i', strtotime($app['mail_sent_at'])), $languageService->get('admin_mail_sent_at'))) ?>"></i>
                                        <?php else: ?>
                                            <i class="bi bi-envelope-exclamation text-secondary fs-4"
                                                title="<?= $languageService->get('admin_mail_not_sent') ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- EMAIL -->
                                <div class="small mb-2">
                                    <i class="bi bi-envelope me-1"></i>
                                    <a href="mailto:<?= htmlspecialchars($app['email']) ?>">
                                        <?= htmlspecialchars($app['email']) ?>
                                    </a>
                                </div>

                                <hr class="my-2">

                                <!-- META -->
                                <div class="mb-2">
                                    <div class="mb-1">
                                        <i class="bi bi-diagram-3 me-1"></i>
                                        <strong><?= $languageService->get('admin_type') ?>:</strong>
                                        <?php
                                        $stmt = $_database->prepare("
                                            SELECT label
                                            FROM plugins_joinus_types
                                            WHERE type_key = ?
                                            LIMIT 1
                                        ");
                                        $stmt->bind_param('s', $type);
                                        $stmt->execute();
                                        $stmt->bind_result($typeLabel);
                                        $stmt->fetch();
                                        $stmt->close();
                                        ?>

                                        <span class="badge bg-<?= $badgeClass ?> ms-1">
                                            <?= htmlspecialchars($typeLabel ?: $type) ?>
                                        </span>

                                        <?php if ($type === 'squad' && !empty($app['squad_name'])): ?>
                                            <span class="badge bg-primary ms-1">
                                                <?= htmlspecialchars($app['squad_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="small">
                                        <i class="bi bi-person-badge me-1"></i>
                                        <strong><?= $languageService->get('admin_role') ?>:</strong>
                                        <span class="badge bg-<?= $roleClass ?> ms-1">
                                            <?= htmlspecialchars($roleName) ?>
                                        </span>
                                    </div>
                                </div>

                                <hr class="my-2">

                                <!-- MESSAGE -->
                                <div class="joinus-message joinus-toggle p-2 rounded"
                                    data-short="<?= htmlspecialchars(mb_strimwidth($app['message'], 0, 40, '…'), ENT_QUOTES) ?>"
                                    data-full="<?= htmlspecialchars($app['message'], ENT_QUOTES) ?>"
                                    onclick="toggleJoinUsMessage(this)">

                                    <i class="bi bi-chat-left-text me-1 text-primary"></i>

                                    <span class="joinus-message-text">
                                        <?= nl2br(htmlspecialchars(mb_strimwidth($app['message'], 0, 40, '…'))) ?>
                                    </span>

                                    <span class="float-end text-primary">
                                        <i class="bi bi-chevron-down"></i>
                                    </span>
                                </div>

                                <!-- STATUS -->
                                <div class="mb-2">
                                    <div class="small text-muted mb-1">
                                        <i class="bi bi-flag me-1"></i><?= $languageService->get('admin_change_status') ?>
                                        <span class="badge bg-<?= $statusClasses[$status] ?> mb-1">
                                            <?= $languageService->get('status_'.$status) ?>
                                        </span>
                                    </div>
                                    <form method="post" class="d-flex gap-2 mt-1">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="status">
                                        <input type="hidden" name="id" value="<?= (int)$app['id'] ?>">

                                        <select name="status" class="form-select form-select-sm">
                                            <?php foreach ($statusClasses as $key => $c): ?>
                                                <option value="<?= $key ?>" <?= $app['status'] === $key ? 'selected' : '' ?>>
                                                    <?= $languageService->get('status_'.$key) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <button class="btn btn-sm btn-primary">
                                            <i class="bi bi-save"></i>
                                        </button>
                                    </form>
                                </div>

                                <?php if ($app['status'] === 'accepted'): ?>
                                    <form method="post" class="mb-2">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="create_user">
                                        <input type="hidden" name="id" value="<?= (int)$app['id'] ?>">
                                        <button class="btn btn-sm btn-success w-100">
                                            <i class="bi bi-person-plus me-1"></i>
                                            <?= $languageService->get('create_user') ?>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <hr class="my-2">

                                <!-- ADMIN NOTE -->
                                <form method="post" class="mb-2">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="note">
                                    <input type="hidden" name="id" value="<?= (int)$app['id'] ?>">

                                    <textarea name="note"
                                                rows="2"
                                                class="form-control form-control-sm mb-1"
                                                placeholder="<?= $languageService->get('admin_note_placeholder') ?>"><?= htmlspecialchars($app['admin_note'] ?? '') ?></textarea>

                                    <button class="btn btn-primary w-100">
                                        <?= $languageService->get('save') ?>
                                    </button>
                                </form>

                                <!-- DELETE -->
                                <form method="post"
                                        onsubmit="return confirm('<?= $languageService->get('delete') ?>?')">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int)$app['id'] ?>">

                                    <button class="btn btn-danger w-100">
                                        <i class="bi bi-trash me-1"></i>
                                        <?= $languageService->get('delete') ?>
                                    </button>
                                </form>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            </div>
        </div>

    <?php endforeach; ?>

</div>
<!-- HTML -->
<?php
}
?>
<script>
function escapeHtml(s) {
  return String(s)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function nl2brSafe(s) {
  return escapeHtml(s).replaceAll("\n", "<br>");
}

function toggleJoinUsMessage(el) {
  const shortText = el.dataset.short || "";
  const fullText  = el.dataset.full || "";
  const textEl = el.querySelector(".joinus-message-text");
  if (!textEl) return;

  const isOpen = el.classList.toggle("open");

  textEl.innerHTML = isOpen ? nl2brSafe(fullText) : escapeHtml(shortText);
}
</script>
<style>
.joinus-board {
  border: 1px solid var(--bs-border-color);
  overflow: hidden;
  box-shadow: 0 1px 2px rgba(0,0,0,.05);
  background: var(--bs-body-bg);
}

.joinus-board-header {
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding: .75rem .85rem;
  border-bottom: 1px solid var(--bs-border-color);
  position: relative;
  background: var(--bs-body-bg);
}

.joinus-board-header::before {
  content:"";
  position:absolute;
  top:0;
  left:0;
  right:0;
  height: 3px;
  background: var(--joinus-status-color);
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
  opacity: .9;
}

.joinus-board-title {
  display:flex;
  align-items:center;
  gap: .55rem;
  font-weight: 600;
}

.joinus-status-dot {
  width: .55rem;
  height: .55rem;
  border-radius: 999px;
  background: var(--joinus-status-color);
  box-shadow: 0 0 0 4px rgba(0,0,0,.03);
}

.joinus-count {
  background: rgba(0,0,0,.06);
  color: var(--bs-body-color);
  font-weight: 600;
  padding: .35rem .55rem;
}

.joinus-board-body {
  border-bottom-left-radius: 12px;
  border-bottom-right-radius: 12px;
}

.joinus-empty {
  text-align:center;
  padding: 1.25rem .75rem;
  border: 1px dashed rgba(0,0,0,.12);
  border-radius: .75rem;
  margin: .5rem;
  background: rgba(255,255,255,.65);
}
.joinus-empty-icon {
  font-size: 1.25rem;
  margin-bottom: .25rem;
  opacity: .8;
}
.joinus-empty-text {
  font-size: .9rem;
  color: rgba(0,0,0,.55);
}

.joinus-board[data-status="new"]      { --joinus-status-color: var(--bs-secondary); }
.joinus-board[data-status="review"]   { --joinus-status-color: var(--bs-warning); }
.joinus-board[data-status="accepted"] { --joinus-status-color: var(--bs-success); }
.joinus-board[data-status="rejected"] { --joinus-status-color: var(--bs-danger); }

.joinus-toggle {
    background: #f8f9fa;
    cursor: pointer;
    transition: background 0.2s ease;
}

.joinus-toggle:hover {
    background: #eef3ff;
}

.joinus-toggle .bi-chevron-down {
  transition: transform .25s ease;
  display: inline-block;
  transform-origin: 50% 50%;
}

.joinus-toggle.open .bi-chevron-down {
  transform: rotate(180deg);
}
</style>
