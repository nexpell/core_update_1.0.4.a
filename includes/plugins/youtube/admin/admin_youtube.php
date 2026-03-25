<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\AccessControl;
use nexpell\NavigationUpdater;// SEO Anpassung
global $languageService,$_database;

if (isset($languageService) && method_exists($languageService, 'readModule')) {
    $languageService->readModule('youtube');
}

AccessControl::checkAdminAccess('youtube');

$message   = '';
$action    = $_GET['action'] ?? '';
$edit_key  = $_GET['key'] ?? '';

// Hilfsfunktionen
function getSetting($key, $default = null) {
    global $_database;
    $key_safe = $_database->real_escape_string($key);
    $result = $_database->query("
        SELECT setting_value 
        FROM plugins_youtube_settings 
        WHERE plugin_name='youtube' AND setting_key='$key_safe' 
        LIMIT 1
    ");
    if ($result && $row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    return $default;
}

function setSetting($key, $value) {
    global $_database;
    $key_safe   = $_database->real_escape_string($key);
    $value_safe = $_database->real_escape_string($value);

    $result = $_database->query("
        SELECT id 
        FROM plugins_youtube_settings 
        WHERE plugin_name='youtube' AND setting_key='$key_safe' 
        LIMIT 1
    ");

    if ($result && $result->num_rows > 0) {
        $_database->query("
            UPDATE plugins_youtube_settings 
            SET setting_value='$value_safe', updated_at=NOW() 
            WHERE plugin_name='youtube' AND setting_key='$key_safe'
        ");
    } else {
        $_database->query("
            INSERT INTO plugins_youtube_settings 
            (plugin_name, setting_key, setting_value, updated_at) 
            VALUES ('youtube', '$key_safe', '$value_safe', NOW())
        ");
    }
}

// Einstellungen speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    setSetting('default_video_id', trim($_POST['default_video_id']));
    setSetting('videos_per_page', (int)$_POST['videos_per_page']);
    setSetting('videos_per_page_other', (int)$_POST['videos_per_page_other']);
    setSetting('display_mode', ($_POST['display_mode'] === 'list') ? 'list' : 'grid');
    setSetting('first_full_width', isset($_POST['first_full_width']) ? 1 : 0);

    nx_redirect('admincenter.php?site=admin_youtube', 'success', 'alert_saved', false);
}

// Videos löschen
if (isset($_GET['delete'])) {
    $videoId = (string)($_GET['delete'] ?? '');

    $stmt = $_database->prepare("
        SELECT setting_key
        FROM plugins_youtube
        WHERE plugin_name='youtube' AND setting_value=?
        LIMIT 1
    ");
    $stmt->bind_param("s", $videoId);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && ($row = $res->fetch_assoc())) {
        $stmtDel = $_database->prepare("
            DELETE FROM plugins_youtube 
            WHERE plugin_name='youtube' AND setting_key=?
        ");
        $stmtDel->bind_param("s", $row['setting_key']);
        if ($stmtDel->execute() && $stmtDel->affected_rows > 0) {
            nx_audit_delete('admin_youtube', (string)$row['setting_key'], (string)$videoId_safe, 'admincenter.php?site=admin_youtube');
            nx_redirect('admincenter.php?site=admin_youtube', 'success', 'alert_deleted', false);
        }
        $stmtDel->close();
    }

    nx_redirect('admincenter.php?site=admin_youtube', 'danger', 'alert_not_found', false);
}

// Add/Edit Videos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add','edit'], true)) {
    $video_id = trim($_POST['video_id'] ?? '');
    $set_as_first = isset($_POST['set_as_first']) ? 1 : 0;

    if (!empty($video_id)) {
        if ($action === 'add') {
            $res = $_database->query("
            SELECT COALESCE(MAX(CAST(SUBSTRING(setting_key, 7) AS UNSIGNED)), 0) AS max_n
            FROM plugins_youtube
            WHERE plugin_name='youtube'
                AND setting_key LIKE 'video_%'
            ");
            $max = (int)$res->fetch_assoc()['max_n'];
            $newKey = 'video_' . ($max + 1);

            if ($set_as_first) {
                $_database->query("UPDATE plugins_youtube SET is_first=0 WHERE plugin_name='youtube'");
            }

            $stmt = $_database->prepare("
                INSERT INTO plugins_youtube 
                (plugin_name, setting_key, setting_value, is_first, updated_at) 
                VALUES ('youtube', ?, ?, ?, NOW())
            ");
            $stmt->bind_param("ssi", $newKey, $video_id, $set_as_first);
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok) nx_audit_create('admin_youtube', (string)$newKey, $video_id, 'admincenter.php?site=admin_youtube');

            $edit_key = $newKey;
        }

        if ($action === 'edit' && !empty($_POST['edit_video_key'])) {
            $edit_key = $_POST['edit_video_key'];

            if ($set_as_first) {
                $_database->query("UPDATE plugins_youtube SET is_first=0 WHERE plugin_name='youtube'");
            }

            $stmt = $_database->prepare("
                UPDATE plugins_youtube 
                SET setting_value=?, is_first=?, updated_at=NOW() 
                WHERE plugin_name='youtube' AND setting_key=?
            ");
            $stmt->bind_param("sis", $video_id, $set_as_first, $edit_key);
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok) nx_audit_update('admin_youtube', (string)$edit_key, true, $video_id, 'admincenter.php?site=admin_youtube');
        }

        $admin_file = basename(__FILE__, '.php');
        echo NavigationUpdater::updateFromAdminFile($admin_file);

        nx_redirect('admincenter.php?site=admin_youtube', 'success', 'alert_saved', false);
    }

    nx_alert('danger', 'alert_missing_required', false);
}

// Einstellungen laden
$defaultVideoId    = getSetting('default_video_id', 'D_x8ms9nGQw');
$videosPerPage     = getSetting('videos_per_page', 4);
$videosPerPageOther= getSetting('videos_per_page_other', 6);
$displayMode       = getSetting('display_mode', 'grid');
$firstFullWidth    = getSetting('first_full_width', 0);

// Add/Edit Formular
if (in_array($action, ['add','edit'])) {
    $isEdit = ($action === 'edit');

    $currentVideoId = '';
    $isFirst = 0;

    if ($isEdit && $edit_key) {
        $stmt = $_database->prepare("
            SELECT setting_value, is_first 
            FROM plugins_youtube 
            WHERE plugin_name='youtube' AND setting_key=?
        ");
        $stmt->bind_param("s", $edit_key);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            $currentVideoId = $row['setting_value'];
            $isFirst = (int)$row['is_first'];
        }
    }

    $defaultVideoId = getSetting('default_video_id', '');
?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-youtube"></i> <?= $languageService->get('title_youtube') ?>
            <small class="text-muted"><?= $languageService->get($action === 'add' ? 'add' : 'edit') ?></small>
        </div>
    <div>

    <div class="card-body">
        <!-- Add/Edit Formular -->
        <form method="POST" class="row g-3">
            <?php if($isEdit): ?>
                <input type="hidden" name="edit_video_key" value="<?= htmlspecialchars($edit_key); ?>">
            <?php endif; ?>

            <!-- Video ID -->
            <div class="col-12">
                <label for="video_id" class="form-label">
                    <?= $languageService->get('placeholder_video_id') ?>
                </label>
                <input
                    type="text"
                    class="form-control"
                    name="video_id"
                    id="video_id"
                    value="<?= htmlspecialchars($currentVideoId); ?>"
                    required
                >
            </div>

            <!-- Toggle -->
            <div class="col-12">
                <div class="form-check form-switch">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        name="set_as_first"
                        id="set_as_first"
                        value="1"
                        <?= ($isFirst) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="set_as_first">
                        <?= $languageService->get('label_first_video') ?>
                    </label>
                </div>
            </div>

            <!-- Button -->
            <div class="col-12 text-start">
                <button class="btn btn-primary" type="submit">
                    <?= $languageService->get('save') ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php
    return;
}

// Settings Formular
if ($action === 'settings') {
?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-youtube"></i> <?= $languageService->get('title_youtube') ?>
            <small class="text-muted"><?= $languageService->get('settings') ?></small>
        </div>
    <div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <input type="hidden" name="save_settings" value="1">

            <!-- Default video ID + Anzeigeart -->
            <div class="col-12 col-md-6">
                <label for="default_video_id" class="form-label">
                    <?= $languageService->get('label_default_id') ?>
                </label>
                <input
                    type="text"
                    class="form-control"
                    id="default_video_id"
                    name="default_video_id"
                    value="<?= htmlspecialchars($defaultVideoId); ?>"
                    required
                >
            </div>

            <div class="col-12 col-md-6">
                <label for="display_mode" class="form-label">
                    <?= $languageService->get('label_display') ?>
                </label>
                <select class="form-select" id="display_mode" name="display_mode">
                    <option value="grid" <?= ($displayMode === 'grid') ? 'selected' : ''; ?>>
                        <?= $languageService->get('label_next_to_it') ?>
                    </option>
                    <option value="list" <?= ($displayMode === 'list') ? 'selected' : ''; ?>>
                        <?= $languageService->get('label_below') ?>
                    </option>
                </select>
            </div>

            <!-- Videos pro Seite (erste Seite) + Videos pro Seite (Folgeseite) -->
            <div class="col-12 col-md-6">
                <label for="videos_per_page" class="form-label">
                    <?= $languageService->get('label_video_per_side') ?>
                </label>
                <input
                    type="number"
                    class="form-control"
                    id="videos_per_page"
                    name="videos_per_page"
                    value="<?= htmlspecialchars($videosPerPage); ?>"
                    min="1"
                    required
                >
            </div>

            <div class="col-12 col-md-6">
                <label for="videos_per_page_other" class="form-label">
                    <?= $languageService->get('label_video_per_side_follow') ?>
                </label>
                <input
                    type="number"
                    class="form-control"
                    id="videos_per_page_other"
                    name="videos_per_page_other"
                    value="<?= htmlspecialchars($videosPerPageOther); ?>"
                    min="1"
                    required
                >
            </div>

            <!-- Toggle -->
            <div class="col-12">
                <div class="form-check form-switch">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        name="first_full_width"
                        id="first_full_width"
                        <?= ($firstFullWidth) ? 'checked' : ''; ?>
                    >
                    <label class="form-check-label" for="first_full_width">
                        <?= $languageService->get('label_first_video_grid') ?>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <?= $languageService->get('save') ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php
    return;
}

// Übersicht (Listing)
$videos = [];
$result = $_database->query("
    SELECT setting_key, setting_value, is_first 
    FROM plugins_youtube 
    WHERE plugin_name='youtube' AND setting_key LIKE 'video_%' 
    ORDER BY id DESC
");
if ($result) while ($row = $result->fetch_assoc()) $videos[] = $row;
?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-youtube"></i> <?= $languageService->get('title_youtube') ?>
            <small class="text-muted"><?= $languageService->get('overview') ?></small>
        </div>
        <a href="admincenter.php?site=admin_youtube&action=add" class="btn btn-secondary"><?= $languageService->get('add') ?></a>
        <a href="admincenter.php?site=admin_youtube&action=settings" class="btn btn-secondary"><?= $languageService->get('settings') ?></a>
    <div>
    <div class="card-body">
        <?php if(!empty($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="table-responsive">
        <table class="table">
        <thead>
            <tr>
                <th><?= $languageService->get('preview') ?></th>
                <th><?= $languageService->get('th_video_id') ?></th>
                <th class="text-end"><?= $languageService->get('actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($videos as $v): ?>
        <tr>
            <td>
        <?php
            $videoId = htmlspecialchars($v['setting_value']);
            $thumbnailUrl = "https://img.youtube.com/vi/$videoId/hqdefault.jpg";
        ?>
        <img class="rounded" src="<?php echo $thumbnailUrl; ?>" width="180" height="78" alt="<?= $languageService->get('alt_thumbnail') ?>">
        <?php if($v['is_first']): ?>
            <i class="bi bi-star-fill text-warning ms-2" title="<?= $languageService->get('icon_first_video') ?>"></i>
        <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($v['setting_value']); ?></td>
                <?php
                    $deleteUrl = 'admincenter.php?site=admin_youtube&delete=' . urlencode($v['setting_value']);
                ?>
                <td class="text-end">
                    <a href="admincenter.php?site=admin_youtube&action=edit&key=<?php echo urlencode($v['setting_key']); ?>" class="btn btn-warning d-inline-flex align-items-center gap-1 w-auto"><i class="bi bi-pencil-square"></i> <?= $languageService->get('edit') ?></a>
                    <a
                        href="#"
                        class="btn btn-danger d-inline-flex align-items-center gap-1 w-auto"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmDeleteModal"
                        data-confirm-url="<?= htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') ?>">
                        <i class="bi bi-trash3"></i> <?= $languageService->get('delete') ?>
                        </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
    </div>
</div>
