<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
global $_database, $languageService;

// CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$CSRF = $_SESSION['csrf'];

// ACTION: DELETE (GET, für ConfirmDeleteModal)
if (
    ($_GET['action'] ?? '') === 'delete' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {
    $id = (int)($_GET['id'] ?? 0);

    if ($id <= 0) {
        nx_redirect('admincenter.php?site=admin_shoutbox', 'danger', 'alert_invalid_id', false);
    }

    safe_query("
        DELETE FROM plugins_shoutbox_messages
        WHERE id = {$id}
        LIMIT 1
    ");

    nx_audit_delete('admin_shoutbox', (string)$id, (string)$id, 'admincenter.php?site=admin_shoutbox');
    nx_redirect('admincenter.php?site=admin_shoutbox', 'success', 'alert_deleted', false);
}

// DATEN LADEN
$res = safe_query("
    SELECT
        id,
        username,
        message,
        created_at
    FROM plugins_shoutbox_messages
    ORDER BY created_at DESC
");
?>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-chat-dots"></i> <?= $languageService->get('title_shoutbox') ?>
            <small class="text-muted"><?= $languageService->get('overview') ?></small>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th style="width:60px"><?= $languageService->get('id') ?></th>
                <th style="width:180px"><?= $languageService->get('user') ?></th>
                <th><?= $languageService->get('th_message') ?></th>
                <th style="width:140px"><?= $languageService->get('th_time') ?></th>
                <th style="width:120px"><?= $languageService->get('actions') ?></th>
            </tr>
            </thead>
            <tbody>

            <?php if (mysqli_num_rows($res) === 0): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <?= $languageService->get('info_no_entries') ?>
                    </td>
                </tr>
            <?php else: ?>

                <?php while ($row = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                        <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                        <td>
                            <?= !empty($row['created_at'])
                                ? date('d.m.Y H:i', strtotime($row['created_at']))
                                : '--'
                            ?>
                        </td>

                        <td>
                            <?php
                                $deleteUrl = 'admincenter.php?site=admin_shoutbox&action=delete&id=' . (int)$row['id'];
                            ?>

                            <a
                            href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-url="<?= htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') ?>">
                                <?= $languageService->get('delete') ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php endif; ?>

            </tbody>
        </table>
        </div>
        </div>
    </div>
</div>