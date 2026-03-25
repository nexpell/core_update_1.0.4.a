<?php
declare(strict_types=1);

use nexpell\LanguageService;
use nexpell\AccessControl;

global $_database, $languageService;

if (isset($languageService) && method_exists($languageService, 'readModule')) {
    $languageService->readModule('teamspeak');
}

AccessControl::checkAdminAccess('teamspeak');

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// SAVE (ADD / EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($action === 'add') {

        safe_query("
            INSERT INTO plugins_teamspeak
            (title, host, query_port, server_port, cache_time, enabled)
            VALUES (
                '".escape($_POST['title'])."',
                '".escape($_POST['host'])."',
                ".(int)$_POST['query_port'].",
                ".(int)$_POST['server_port'].",
                ".(int)$_POST['cache_time'].",
                ".(int)!empty($_POST['enabled'])."
            )
        ");

        $newId = (int)($_database->insert_id ?? 0);
        nx_audit_create('admin_teamspeak', (string)$newId, trim((string)($_POST['title'] ?? '')), 'admincenter.php?site=admin_teamspeak');
        nx_redirect('admincenter.php?site=admin_teamspeak', 'success', 'alert_saved', false);
    }

    if ($action === 'edit' && $id > 0) {

        safe_query("
            UPDATE plugins_teamspeak SET
                title        = '".escape($_POST['title'])."',
                host         = '".escape($_POST['host'])."',
                query_port  = ".(int)$_POST['query_port'].",
                server_port = ".(int)$_POST['server_port'].",
                cache_time  = ".(int)$_POST['cache_time'].",
                enabled     = ".(int)!empty($_POST['enabled'])."
            WHERE id = $id
            LIMIT 1
        ");

        nx_audit_update('admin_teamspeak', (string)$id, true, trim((string)($_POST['title'] ?? '')), 'admincenter.php?site=admin_teamspeak');
        nx_redirect('admincenter.php?site=admin_teamspeak', 'success', 'alert_saved', false);
    }
}

// DELETE
if ($action === 'delete' && $id > 0) {

    safe_query("DELETE FROM plugins_teamspeak WHERE id = $id LIMIT 1");

    nx_audit_delete('admin_teamspeak', (string)$id, (string)$id, 'admincenter.php?site=admin_teamspeak');
    nx_redirect('admincenter.php?site=admin_teamspeak', 'success', 'alert_deleted', false);
}

// ADD / EDIT FORM
if ($action === 'add' || ($action === 'edit' && $id > 0)) {

    $srv = [
        'title' => '',
        'host' => '',
        'query_port' => 10011,
        'server_port' => 9987,
        'query_user' => '',
        'query_pass' => '',
        'cache_time' => 60,
        'enabled' => 1
    ];

    if ($action === 'edit') {
        $res = safe_query("SELECT * FROM plugins_teamspeak WHERE id = $id LIMIT 1");
        $srv = mysqli_fetch_assoc($res);
    }
    ?>

    <form method="post">
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <div class="card-title">
                <i class="bi bi-headset"></i> <?= $languageService->get('title_teamspeak') ?>
                <small class="text-muted"><?= $languageService->get($action === 'add' ? 'add' : 'edit') ?></small>
            </div>
        </div>

        <div class="card-body p-4">

            <div class="mb-3">
                <label class="form-label fw-medium"><?= $languageService->get('label_name') ?></label>
                <input class="form-control" name="title"
                    value="<?= htmlspecialchars($srv['title']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Host</label>
                <input class="form-control" name="host"
                    value="<?= htmlspecialchars($srv['host']) ?>">
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium"><?= $languageService->get('label_queryport') ?></label>
                    <input class="form-control" name="query_port"
                        value="<?= (int)$srv['query_port'] ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium"><?= $languageService->get('label_serverport') ?></label>
                    <input class="form-control" name="server_port"
                        value="<?= (int)$srv['server_port'] ?>">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label fw-medium">Cache (Sek.)</label>
                <input class="form-control" name="cache_time"
                    value="<?= (int)$srv['cache_time'] ?>">
            </div>

            <div class="d-flex flex-column align-items-start gap-2 mt-4">

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                        name="enabled" value="1"
                        <?= $srv['enabled'] ? 'checked' : '' ?>>
                    <label class="form-check-label"><?= $languageService->get('active') ?></label>
                </div>

                <button class="btn btn-primary mt-2">
                    <?= $languageService->get('save') ?>
                </button>

            </div>

        </div>
    </div>
</form>
<?php
return;
}

// LIST
$res = safe_query("
    SELECT *
    FROM plugins_teamspeak
    ORDER BY sort_order ASC, id ASC
");

echo '<div class="card shadow-sm mt-4">
        <div class="card-header">
            <div class="card-title">
                <i class="bi bi-headset"></i> '. $languageService->get('title_teamspeak') . '';
echo '          <small class="text-muted">' . $languageService->get($action === 'add' ? 'server_add' : 'server_edit') . '</small>';
echo'       </div>
        <a href="admincenter.php?site=admin_teamspeak&action=add" class="btn btn-secondary">
            '. $languageService->get('add') . '
        </a>
        </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>' . $languageService->get('label_name') . '</th>
                    <th>' . $languageService->get('th_host') . '</th>
                    <th>' . $languageService->get('th_status') . '</th>
                    <th width="140" class="text-end">' . $languageService->get('actions') . '</th>
                </tr>
            </thead>
            <tbody>';

        while ($srv = mysqli_fetch_assoc($res)) {

            $deleteUrl = 'admincenter.php?site=admin_teamspeak&action=delete&id=' . (int)$srv['id'];

            echo '<tr>
                    <td>' . htmlspecialchars($srv['title']) . '</td>
                    <td>' . htmlspecialchars($srv['host']) . '</td>
                    <td>' .
                        ($srv['enabled']
                            ? '<span class="badge bg-success">' . $languageService->get('active') . '</span>'
                            : '<span class="badge bg-secondary">' . $languageService->get('inactive') . '</span>'
                        ) .
                    '</td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-2">
                            <a href="admincenter.php?site=admin_teamspeak&action=edit&id=' . (int)$srv['id'] . '"
                            class="btn btn-warning d-inline-flex align-items-center gap-1">
                                <i class="bi bi-pencil"></i> ' . $languageService->get('edit') . '
                            </a>
                            <a href="#"
                            class="btn btn-danger d-inline-flex align-items-center gap-1"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal"
                            data-confirm-url="' . htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') . '">
                                <i class="bi bi-trash3"></i> ' . $languageService->get('delete') . '
                            </a>
                        </div>
                    </td>
                </tr>';
        }
        echo '</tbody>
        </table>';
        if (mysqli_num_rows($res) === 0) {
            echo '<div class="alert alert-info mb-0">
                    ' . $languageService->get('alert_no_entries') . '
                </div>';
        }
echo '</div>
</div>';
