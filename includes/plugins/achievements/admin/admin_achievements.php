<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Zugriff auf Konfigurations-Funktion
require_once dirname(__FILE__) . '/../engine_achievements.php';

use nexpell\LanguageService;
use nexpell\AccessControl;

global $languageService,$_database;

AccessControl::checkAdminAccess('achievements');

// CSRF-Token erstellen, falls es nicht existiert
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Funktion zur CSRF-Validierung
function validate_csrf_token($token) {
    if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        // Bei Fehlschlag, Skript beenden.
        die('CSRF token validation failed.');
    }
    return true;
}

// Globale CSRF-Prüfung für alle POST-Anfragen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token($_POST['csrf_token'] ?? '');
}

$action = $_GET['action'] ?? '';

// Server-Pfad für Dateioperationen (z.B. Upload, Löschen)
$upload_path_server = dirname(__FILE__) . '/../images/icons/';
// Web-Pfad für die Anzeige im Browser (<img> src)
$upload_path_web = '/includes/plugins/achievements/images/icons/';

// Clan-Namen aus der Datenbank abrufen
$clan_name_result = $_database->query("SELECT clanname FROM settings LIMIT 1");
$clan_name = $clan_name_result ? mysqli_fetch_assoc($clan_name_result)['clanname'] : 'Clan-Name';

// BONUSPUNKTE HINZUFÜGEN (SPEICHERN)
if ($action === 'add_points' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings_result = $_database->query("SELECT setting_key, setting_value FROM plugins_achievements_settings");
    $settings = [];
    while($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    $max_bonus_points = (int)($settings['max_bonus_points'] ?? 2000);
    
    $target_user_id = (int)($_POST['user_id'] ?? 0);
    $points_to_add  = (int)($_POST['points'] ?? 0);
    $admin_id       = (int)($_SESSION['userID'] ?? 0);

    if ($points_to_add <= 0 || $points_to_add > $max_bonus_points) {
        nx_alert('warning', nx_translate('points_between').' '.$max_bonus_points.' '.nx_translate('lay'), false, true, true);
        return;
    }

    if (empty($error)) {
        $stmt_check_user = $_database->prepare("SELECT id FROM plugins_achievements_admin_log WHERE user_id = ? AND log_type = 'bonus_points'");
        $stmt_check_user->bind_param("i", $target_user_id);
        $stmt_check_user->execute();
        if ($stmt_check_user->get_result()->num_rows > 0) {
            nx_alert('warning', 'points_already_gotten', false);
            $stmt_check_user->close();
            return;
        }
        $stmt_check_user->close();
    }

    if (empty($error)) {
        $award_limit = (int)($settings['admin_bonus_award_limit'] ?? 1);

        if ($award_limit != -1) {
            $stmt_check_admin = $_database->prepare("SELECT COUNT(id) as count FROM plugins_achievements_admin_log WHERE admin_id = ? AND log_type = 'bonus_points'");
            $stmt_check_admin->bind_param("i", $admin_id);
            $stmt_check_admin->execute();
            $admin_award_count = (int)$stmt_check_admin->get_result()->fetch_assoc()['count'];
            $stmt_check_admin->close();

            if ($admin_award_count >= $award_limit) {
                nx_alert('warning', nx_translate('points_limit').' '.$award_limit.' '.nx_translate('points_limit2'), false, true, true);
                return;
            }
        }
    }

    if (empty($error)) {
        $stmt_insert = $_database->prepare("INSERT INTO plugins_achievements_admin_log (user_id, admin_id, log_type, value) VALUES (?, ?, 'bonus_points', ?)");
        $stmt_insert->bind_param("iii", $target_user_id, $admin_id, $points_to_add);
        if ($stmt_insert->execute()) {
            nx_audit_action('admin_achievements', 'add_points', 'user', (string)$target_user_id, 'admincenter.php?site=achievements&action=add_points', ['user_id' => (int)$target_user_id, 'points' => (int)$points_to_add]);
            nx_redirect('admincenter.php?site=achievements&action=add_points', 'success', 'alert_saved', false);
        } else {
            nx_alert('danger', nx_translate('points_error').' '.htmlspecialchars($stmt_insert->error), false, true, true);
            $stmt_insert->close();
            return;
        }
        $stmt_insert->close();
    }
}

// ACHIEVEMENT MANUELL VERGEBEN (SPEICHERN)
if ($action === 'manual_award' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_user_id  = (int)($_POST['user_id'] ?? 0);
    $achievement_id  = (int)($_POST['achievement_id'] ?? 0);
    $admin_id        = (int)($_SESSION['userID'] ?? 0);

    if ($target_user_id === 0 || $achievement_id === 0) {
        nx_alert('warning', nx_translate('award_user_achiv'), false, true, true);
        return;
    }

    if (empty($error)) {
        $stmt_check = $_database->prepare("SELECT id FROM plugins_achievements_admin_log WHERE user_id = ? AND related_id = ? AND log_type = 'manual_award'");
        $stmt_check->bind_param("ii", $target_user_id, $achievement_id);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            nx_alert('warning', nx_translate('award_user_achiv'), false, true, true);
            $stmt_check->close();
            return;
        }
        $stmt_check->close();
    }

    if (empty($error)) {
        $stmt_insert = $_database->prepare("INSERT INTO plugins_achievements_admin_log (user_id, admin_id, log_type, related_id) VALUES (?, ?, 'manual_award', ?)");
        $stmt_insert->bind_param("iii", $target_user_id, $admin_id, $achievement_id);
        if ($stmt_insert->execute()) {
            nx_audit_action('admin_achievements', 'manual_award', 'achievement', (string)$achievement_id, 'admincenter.php?site=achievements&action=manual_award', ['user_id' => (int)$target_user_id, 'achievement_id' => (int)$achievement_id]);
            nx_redirect('admincenter.php?site=achievements&action=manual_award', 'success', 'alert_saved', false);
        } else {
            nx_alert('danger', nx_translate('award_user_error').' '.$stmt_insert->error, false, true, true);
            $stmt_insert->close();
            return;
        }
        $stmt_insert->close();
    }
}

// BONUSPUNKTE-LOG EINTRAG LÖSCHEN
if ($action === 'delete_bonus_log' && isset($_GET['log_id'])) {
    validate_csrf_token($_GET['csrf_token'] ?? '');

    $log_id = (int)$_GET['log_id'];

    $stmt = $_database->prepare("DELETE FROM plugins_achievements_admin_log WHERE id = ?");
    $stmt->bind_param("i", $log_id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        nx_audit_delete('admin_achievements', (string)$log_id, (string)$log_id, 'admincenter.php?site=achievements&action=add_points');
        nx_redirect('admincenter.php?site=achievements&action=add_points', 'success', 'alert_deleted', false);
    }
    $stmt->close();

    nx_redirect('admincenter.php?site=achievements&action=add_points', 'danger', 'alert_not_found', false);
}

// MANUELLEN AWARD LÖSCHEN
if ($action === 'delete_manual_award' && isset($_GET['id'])) {
    validate_csrf_token($_GET['csrf_token'] ?? '');

    $log_id = (int)$_GET['id'];

    $stmt = $_database->prepare(
        "DELETE FROM plugins_achievements_admin_log WHERE id = ?"
    );
    $stmt->bind_param("i", $log_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        nx_redirect(
            'admincenter.php?site=achievements&action=manual_award',
            'success',
            'alert_deleted',
            false
        );
    }

    $stmt->close();

    nx_redirect(
        'admincenter.php?site=achievements&action=manual_award',
        'danger',
        'alert_not_found',
        false
    );
}

// EINSTELLUNGEN SPEICHERN
if ($action === 'settings' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['custom_locked_icon']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (in_array($_FILES['custom_locked_icon']['type'], $allowedTypes, true)) {
            $ext = strtolower(pathinfo($_FILES['custom_locked_icon']['name'], PATHINFO_EXTENSION));
            $new_filename = 'custom_locked_icon_' . time() . '.' . $ext;
            $targetPath = $upload_path_server . $new_filename;

            $old_icon_stmt = $_database->query("SELECT setting_value FROM plugins_achievements_settings WHERE setting_key = 'custom_locked_icon'");
            if ($old_icon_row = $old_icon_stmt->fetch_assoc()) {
                $old_icon_path = $upload_path_server . $old_icon_row['setting_value'];
                if (file_exists($old_icon_path) && $old_icon_row['setting_value'] !== 'locked.png') {
                    @unlink($old_icon_path);
                }
            }

            if (move_uploaded_file($_FILES['custom_locked_icon']['tmp_name'], $targetPath)) {
                $stmt_update_icon = $_database->prepare("UPDATE plugins_achievements_settings SET setting_value = ? WHERE setting_key = 'custom_locked_icon'");
                $stmt_update_icon->bind_param("s", $new_filename);
                $stmt_update_icon->execute();
                $stmt_update_icon->close();
            } else {
                nx_alert('danger', 'alert_upload_failed', false);
                return;
            }
        }
    }

    $settings_to_save = ['points_per_level', 'hide_locked_icon', 'max_bonus_points', 'admin_bonus_award_limit'];
    $weight_keys = array_filter(array_keys($_POST), function($k) { return strpos($k, 'weight_') === 0; });
    $settings_to_save = array_merge($settings_to_save, $weight_keys);

    foreach ($settings_to_save as $key) {
        if (array_key_exists($key, $_POST)) {
            $value = (string)$_POST[$key];

            $stmt = $_database->prepare("
                INSERT INTO plugins_achievements_settings (setting_key, setting_value)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
            $stmt->close();
        }
    }

    nx_audit_update('admin_achievements', null, true, null, 'admincenter.php?site=achievements&action=settings');
    nx_redirect('admincenter.php?site=achievements&action=settings', 'success', 'alert_saved', false);
}

// AJAX-LÖSCHUNG (ACHIEVEMENT)
if ($action === 'delete' && isset($_GET['id'])) {
    validate_csrf_token($_GET['csrf_token'] ?? '');

    $id = (int)$_GET['id'];

    // Name für Audit vor dem Löschen laden
    $achName = '';
    $stmt_name = $_database->prepare("SELECT name FROM plugins_achievements WHERE id = ? LIMIT 1");
    $stmt_name->bind_param("i", $id);
    $stmt_name->execute();
    $res_name = $stmt_name->get_result();
    if ($res_name && ($r = $res_name->fetch_assoc())) {
        $achName = trim((string)($r['name'] ?? ''));
    }
    $stmt_name->close();

    $stmt_img = $_database->prepare("SELECT image FROM plugins_achievements WHERE id = ?");
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    if ($row_img = $result_img->fetch_assoc()) {
        if (!empty($row_img['image']) && file_exists($upload_path_server . $row_img['image'])) {
            @unlink($upload_path_server . $row_img['image']);
        }
    }
    $stmt_img->close();

    $stmt_log = $_database->prepare("DELETE FROM plugins_achievements_admin_log WHERE related_id = ? AND log_type = 'manual_award'");
    $stmt_log->bind_param("i", $id);
    $stmt_log->execute();
    $stmt_log->close();

    $stmt = $_database->prepare("DELETE FROM plugins_achievements WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        nx_audit_delete('admin_achievements', (string)$id, ($achName !== '' ? $achName : (string)$id), 'admincenter.php?site=achievements');
        nx_redirect('admincenter.php?site=achievements', 'success', 'alert_deleted', false);
    }
    $stmt->close();

    nx_alert('danger', 'manual_error_db', false);
    return;
}

// AJAX-LÖSCHUNG (KATEGORIE)
if ($action === 'deletecategory' && isset($_GET['id'])) {
    validate_csrf_token($_GET['csrf_token'] ?? '');

    $id = (int)$_GET['id'];

    // Name für Audit vor dem Löschen laden
    $catName = '';
    $stmt_name = $_database->prepare("SELECT name FROM plugins_achievements_categories WHERE id = ? LIMIT 1");
    $stmt_name->bind_param("i", $id);
    $stmt_name->execute();
    $res_name = $stmt_name->get_result();
    if ($res_name && ($r = $res_name->fetch_assoc())) {
        $catName = trim((string)($r['name'] ?? ''));
    }
    $stmt_name->close();

    $stmt = $_database->prepare("DELETE FROM plugins_achievements_categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        nx_audit_delete('admin_achievements', (string)$id, ($catName !== '' ? $catName : (string)$id), 'admincenter.php?site=achievements&action=categories');
        nx_redirect('admincenter.php?site=achievements&action=categories', 'success', 'alert_deleted', false);
    }
    $stmt->close();

    nx_alert('danger', 'manual_error_delete', false);
    return;
}

// ACHIEVEMENT HINZUFÜGEN / BEARBEITEN SEITE
if ($action === "add" || $action === "edit") {
    $id = (int)($_GET['id'] ?? 0);
    $isEdit = $id > 0;
    
    $data = [
        'category_id' => 0, 'name' => '', 'description' => '', 'type' => 'level', 'trigger_value' => '',
        'trigger_condition' => '', 'image' => '', 'is_standalone' => 0, 'show_in_overview' => 1, 'allow_html' => 0
    ];
    $isSystemAchievement = false;

    if ($isEdit) {
        $stmt = $_database->prepare("SELECT * FROM plugins_achievements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            if ($data['name'] === 'Admin' || $data['type'] === 'bonus_points') {
                $isSystemAchievement = true;
                if ($data['name'] === 'Admin') {
                    $data['description'] = str_replace('{clan_name}', htmlspecialchars($clan_name), $data['description']);
                }
            }
        } else {
            nx_redirect('admincenter.php?site=achievements', 'danger', 'alert_not_found', false);
        }
        $stmt->close();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $original_type = $data['type'] ?? null;
        
        $data['name'] = trim($_POST['name'] ?? '');
        $data['description'] = trim($_POST['description'] ?? '');
        $data['image'] = $data['image'];

        if (!$isSystemAchievement) {
            $data['category_id'] = (int)($_POST['category_id'] ?? 0);
            $data['is_standalone'] = (int)($_POST['is_standalone'] ?? 0);
            $data['show_in_overview'] = isset($_POST['show_in_overview']) ? 1 : 0;
            $data['allow_html'] = isset($_POST['allow_html']) ? 1 : 0;
            $data['type'] = trim($_POST['type'] ?? 'level');
            
            if ($data['type'] === 'manual') {
                $data['trigger_value'] = '';
                $data['trigger_condition'] = '';
                $data['show_in_overview'] = 0;
            } else if ($data['type'] === 'role') {
                $data['trigger_value'] = trim($_POST['trigger_value_role'] ?? '');
                $data['trigger_condition'] = '';
            } else if ($data['type'] === 'activity_count' || $data['type'] === 'category_points') {
                $data['trigger_value'] = trim($_POST['trigger_value_text'] ?? '');
                $data['trigger_condition'] = trim($_POST['trigger_condition_select'] ?? '');
            } else if ($data['type'] === 'registration_time') {
                $data['trigger_value'] = trim($_POST['trigger_value_text'] ?? '');
                $data['trigger_condition'] = trim($_POST['trigger_condition_time_select'] ?? 'days');
            } else {
                $data['trigger_value'] = trim($_POST['trigger_value_text'] ?? '');
                $data['trigger_condition'] = '';
            }
        }

        if (!empty($_FILES['image']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            if (in_array($_FILES['image']['type'], $allowedTypes, true)) {

                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $eventNameSafe = preg_replace('/[^a-z0-9_-]/', '', strtolower(str_replace(' ', '_', $data['name'])));
                $new_filename = $eventNameSafe . '.' . $ext;
                $targetPath = $upload_path_server . $new_filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    if ($isEdit && !empty($data['image']) && file_exists($upload_path_server . $data['image'])) {
                        @unlink($upload_path_server . $data['image']);
                    }
                    $data['image'] = $new_filename;
                } else {
                    nx_alert('danger', nx_translate('error_picture'), false, true, true);
                }
            } else {
                nx_alert('danger', nx_translate('error_picture_type'), false, true, true);
            }
        }

        if (!$isSystemAchievement) {
            if ($data['type'] === 'manual') {
                if (empty($data['name']) || empty($data['image'])) {
                    nx_alert('danger', nx_translate('errors'), false, true, true);
                }
            } else {
                if (empty($data['name']) || empty($data['image']) || empty($data['trigger_value'])) {
                    nx_alert('danger', nx_translate('errors2'), false, true, true);
                }
            }
        }
        
        if (!$error) {
            if ($isEdit && $isSystemAchievement && $data['name'] === 'Admin') {
                $description_to_save = str_replace(htmlspecialchars($clan_name), '{clan_name}', $data['description']);
                $stmt = $_database->prepare("UPDATE plugins_achievements SET name=?, description=?, image=? WHERE id=?");
                $stmt->bind_param("sssi", $data['name'], $description_to_save, $data['image'], $id);
            } else {
                if ($isEdit) {
                    $stmt = $_database->prepare("UPDATE plugins_achievements SET category_id=?, name=?, description=?, type=?, trigger_value=?, trigger_condition=?, image=?, is_standalone=?, show_in_overview=?, allow_html=? WHERE id=?");
                    $stmt->bind_param("issssssiiii", $data['category_id'], $data['name'], $data['description'], $data['type'], $data['trigger_value'], $data['trigger_condition'], $data['image'], $data['is_standalone'], $data['show_in_overview'], $data['allow_html'], $id);
                } else {
                    $stmt = $_database->prepare("INSERT INTO plugins_achievements (category_id, name, description, type, trigger_value, trigger_condition, image, is_standalone, show_in_overview, allow_html) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("issssssiii", $data['category_id'], $data['name'], $data['description'], $data['type'], $data['trigger_value'], $data['trigger_condition'], $data['image'], $data['is_standalone'], $data['show_in_overview'], $data['allow_html']);
                }
            }
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                if ($isEdit) {
                    nx_audit_update('admin_achievements', (string)$id, true, $data['name'] ?? null, 'admincenter.php?site=achievements');
                } else {
                    $newId = (int)($_database->insert_id ?? 0);
                    nx_audit_create('admin_achievements', (string)$newId, $data['name'] ?? null, 'admincenter.php?site=achievements');
                }
                nx_redirect('admincenter.php?site=achievements', 'success', 'alert_saved', false);
            } else {
                nx_alert('danger', nx_translate('error_save').' '.$stmt->error, false, true, true);
            }
            $stmt->close();
        }
    }
    
    $all_roles = [];
    $roles_result = $_database->query("SELECT role_name FROM user_roles ORDER BY role_name ASC");
    while($row = $roles_result->fetch_assoc()) {
        $all_roles[] = $row['role_name'];
    }
    
    $weighted_categories = [];
    $weights_result = $_database->query("SELECT setting_key FROM plugins_achievements_settings WHERE setting_key LIKE 'weight_%'");
    while($row = $weights_result->fetch_assoc()) {
        $weighted_categories[] = str_replace('weight_', '', $row['setting_key']);
    }
?>
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <div class="card-title">
                <i class="bi bi-trophy"></i> <span><?= $languageService->get('achievement') ?></span>
                <small class="text-muted"><?= $isEdit ? $languageService->get('edit') : $languageService->get('add') ?></small>
            </div>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" action="admincenter.php?site=achievements&action=<?= $isEdit ? 'edit&id='.$id : 'add' ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">Name:</label>
                                <input class="form-control" type="text" name="name" id="name" value="<?= htmlspecialchars($data['name']) ?>" required <?= $isSystemAchievement ? 'readonly' : '' ?>>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label"><?= $languageService->get('category') ?></label>
                                <select class="form-select" name="category_id" id="category_id" <?= $isSystemAchievement ? 'disabled' : '' ?>>
                                    <option value="0"><?= $languageService->get('no_category') ?></option>
                                    <?php
                                    $cat_result = $_database->query("SELECT id, name FROM plugins_achievements_categories ORDER BY name ASC");
                                    while($cat_row = $cat_result->fetch_assoc()) {
                                        $selected = ($data['category_id'] == $cat_row['id']) ? 'selected' : '';
                                        echo '<option value="' . $cat_row['id'] . '" ' . $selected . '>' . htmlspecialchars($cat_row['name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label"><?= $languageService->get('description') ?>:</label>
                            <textarea class="form-control" name="description" id="description" rows="5" <?= ($isSystemAchievement && $data['name'] !== 'Admin') ? 'readonly' : '' ?>><?= htmlspecialchars($data['description']) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="image" class="form-label"><?= $languageService->get('upload_picture') ?></label>
                        <input class="form-control" type="file" name="image" id="image">
                        <?php if ($isEdit && !empty($data['image'])): ?>
                            <div class="mt-2 text-center p-2 border rounded">
                                <?= $languageService->get('current_picture') ?><br>
                                <img src="<?= htmlspecialchars($upload_path_web . $data['image']) ?>" style="max-width: 120px; max-height: 120px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>
                <h5><?= $languageService->get('conditions') ?></h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Typ:</label>
                            <select class="form-select" name="type" id="type" <?= $isSystemAchievement ? 'disabled' : '' ?>>
                                <option value="level" <?= $data['type'] == 'level' ? 'selected' : '' ?>>Level</option>
                                <option value="points" <?= $data['type'] == 'points' ? 'selected' : '' ?>><?= $languageService->get('points_total') ?></option>
                                <option value="role" <?= $data['type'] == 'role' ? 'selected' : '' ?>><?= $languageService->get('role') ?></option>
                                <option value="activity_count" <?= $data['type'] == 'activity_count' ? 'selected' : '' ?>><?= $languageService->get('activity_count') ?></option>
                                <option value="category_points" <?= $data['type'] == 'category_points' ? 'selected' : '' ?>><?= $languageService->get('activity_points') ?></option>
                                <option value="registration_time" <?= $data['type'] == 'registration_time' ? 'selected' : '' ?>><?= $languageService->get('membership_duration') ?></option>
                                <option value="manual" <?= $data['type'] == 'manual' ? 'selected' : '' ?>><?= $languageService->get('manually_given_by_admin') ?></option> <?php if ($isSystemAchievement): ?>
                                    <option value="bonus_points" selected><?= $languageService->get('bonuspoints_system') ?></option>
                                <?php endif; ?>
                            </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="is_standalone" class="form-label"><?= $languageService->get('behave') ?></label>
                        <select class="form-select" name="is_standalone" id="is_standalone" <?= $isSystemAchievement ? 'disabled' : '' ?>>
                            <option value="0" <?= !$data['is_standalone'] ? 'selected' : '' ?>><?= $languageService->get('highest_overwrite') ?></option>
                            <option value="1" <?= $data['is_standalone'] ? 'selected' : '' ?>><?= $languageService->get('always_standalone') ?></option>
                        </select>
                    </div>
                </div>
                
                <div id="trigger-fields">
                    <div class="row" id="group-numeric">
                        <div class="col-md-6 mb-3">
                            <label for="trigger_value_text" class="form-label" id="label-trigger-value"><?= $languageService->get('required value') ?></label>
                            <input class="form-control" type="number" name="trigger_value_text" id="trigger_value_text" value="<?= htmlspecialchars($data['trigger_value']) ?>" <?= $isSystemAchievement ? 'readonly' : '' ?>>
                        </div>
                        <div class="col-md-6 mb-3" id="group-activity">
                            <label for="trigger_condition_select" class="form-label"><?= $languageService->get('activity') ?></label>
                            <select class="form-select" name="trigger_condition_select" id="trigger_condition_select" <?= $isSystemAchievement ? 'disabled' : '' ?>>
                                <?php foreach($weighted_categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($data['trigger_condition'] == $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="group-time-unit">
                            <label for="trigger_condition_time_select" class="form-label"><?= $languageService->get('activity') ?></label>
                            <select class="form-select" name="trigger_condition_time_select" id="trigger_condition_time_select" <?= $isSystemAchievement ? 'disabled' : '' ?>>
                                <option value="days" <?= $data['trigger_condition'] == 'days' ? 'selected' : '' ?>><?= $languageService->get('days') ?></option>
                                <option value="weeks" <?= $data['trigger_condition'] == 'weeks' ? 'selected' : '' ?>><?= $languageService->get('weeks') ?></option>
                                <option value="months" <?= $data['trigger_condition'] == 'months' ? 'selected' : '' ?>><?= $languageService->get('months') ?></option>
                                <option value="years" <?= $data['trigger_condition'] == 'years' ? 'selected' : '' ?>><?= $languageService->get('years') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="group-role">
                        <div class="col-md-12 mb-3">
                            <label for="trigger_value_role" class="form-label"><?= $languageService->get('role') ?>:</label>
                            <select class="form-select" name="trigger_value_role" id="trigger_value_role" <?= $isSystemAchievement ? 'disabled' : '' ?>>
                                <?php foreach($all_roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role) ?>" <?= ($data['type'] == 'role' && $data['trigger_value'] == $role) ? 'selected' : '' ?>><?= htmlspecialchars($role) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="show_in_overview" name="show_in_overview" value="1" <?= ($data['show_in_overview'] ?? 1) ? 'checked' : '' ?> <?= $isSystemAchievement ? 'disabled' : '' ?>>
                    <label class="form-check-label" for="show_in_overview"><?= $languageService->get('show_in_overview') ?></label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="allow_html" name="allow_html" value="1" <?= ($data['allow_html'] ?? 0) ? 'checked' : '' ?> <?= $isSystemAchievement ? 'disabled' : '' ?>>
                    <label class="form-check-label" for="allow_html"><?= $languageService->get('allow_html') ?></label>
                </div><br>
                <button type="submit" class="btn btn-primary"><?= $languageService->get('save') ?></button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');

            function toggleFields() {
                const selectedType = typeSelect.value;
                const triggerFieldsContainer = document.getElementById('trigger-fields');
                const showInOverviewSwitch = document.getElementById('show_in_overview');
                
                if (selectedType === 'manual') {
                    triggerFieldsContainer.style.display = 'none';
                    showInOverviewSwitch.checked = false;
                    showInOverviewSwitch.disabled = true;
                } else {
                    triggerFieldsContainer.style.display = 'block';
                    showInOverviewSwitch.disabled = false;
                    
                    const groupNumeric = document.getElementById('group-numeric');
                    const groupRole = document.getElementById('group-role');
                    const groupActivity = document.getElementById('group-activity');
                    const labelTriggerValue = document.getElementById('label-trigger-value');
                    const groupTimeUnit = document.getElementById('group-time-unit');

                    groupNumeric.style.display = 'none';
                    groupRole.style.display = 'none';
                    groupActivity.style.display = 'none';
                    groupTimeUnit.style.display = 'none';

                    if (selectedType === 'role') {
                        groupRole.style.display = 'flex';
                    } else if (selectedType === 'activity_count') {
                        groupNumeric.style.display = 'flex';
                        groupActivity.style.display = 'block';
                        labelTriggerValue.textContent = <?= json_encode($languageService->get('required_number')) ?>;
                    } else if (selectedType === 'category_points') {
                        groupNumeric.style.display = 'flex';
                        groupActivity.style.display = 'block';
                        labelTriggerValue.textContent = <?= json_encode($languageService->get('required_points')) ?>;
                    } else if (selectedType === 'registration_time') {
                        groupNumeric.style.display = 'flex';
                        groupTimeUnit.style.display = 'block';
                        labelTriggerValue.textContent = <?= json_encode($languageService->get('required_duration')) ?>;
                    } else {
                        groupNumeric.style.display = 'flex';
                        labelTriggerValue.textContent = <?= json_encode($languageService->get('required_value')) ?>;
                    }
                }
            }

            typeSelect.addEventListener('change', toggleFields);
            toggleFields();

            const achievementName = '<?= htmlspecialchars($data['name'] ?? '') ?>';
            const achievementType = '<?= htmlspecialchars($data['type'] ?? '') ?>';
            
            if (achievementName === 'Admin' || achievementType === 'bonus_points') {
                document.getElementById('type').disabled = true;
                document.getElementById('is_standalone').disabled = true;
                document.getElementById('show_in_overview').disabled = true;
                document.getElementById('allow_html').disabled = true;

                const triggerFields = document.getElementById('trigger-fields');
                const inputs = triggerFields.querySelectorAll('input, select');
                inputs.forEach(input => input.disabled = true);
                
                const infoBox = document.createElement('div');
                infoBox.className = 'alert alert-info';
                infoBox.innerHTML = <?= json_encode($languageService->get('info_system_achiv')) ?>;
                
                const conditionsHeadline = document.querySelector('h5');
                if (conditionsHeadline) {
                    conditionsHeadline.before(infoBox);
                }
            }
            
            if (achievementName === 'Admin') {
                document.getElementById('name').readOnly = true;
                document.getElementById('category_id').disabled = true;
                document.getElementById('trigger_value_role').disabled = true;
            }
            else if (achievementType === 'bonus_points') {
                document.getElementById('name').readOnly = true;
                document.getElementById('description').readOnly = true;
            }
        });
    </script>
    <?php
}

// KATEGORIEN VERWALTEN SEITE
elseif ($action === 'categories' || $action === 'addcategory' || $action === 'editcategory') {
    $isEditCat = $action === 'editcategory';
    $catId = intval($_GET['id'] ?? 0);
    $catData = ['name' => '', 'description' => ''];
    $errorCat = '';

    if ($isEditCat && $catId > 0) {
        $stmt = $_database->prepare("SELECT * FROM plugins_achievements_categories WHERE id = ?");
        $stmt->bind_param("i", $catId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $catData = $result->fetch_assoc();
        } else {
            nx_alert('warning', nx_translate('category_not_found'), false, true, true);
        }
        $stmt->close();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
        $catData['name'] = trim($_POST['name'] ?? '');
        $catData['description'] = trim($_POST['description'] ?? '');

        if (empty($catData['name'])) {
            nx_alert('warning', nx_translate('required_category'), false, true, true);
        } else {
            if ($isEditCat) {
                $stmt = $_database->prepare("UPDATE plugins_achievements_categories SET name=?, description=? WHERE id=?");
                $stmt->bind_param("ssi", $catData['name'], $catData['description'], $catId);
            } else {
                $stmt = $_database->prepare("INSERT INTO plugins_achievements_categories (name, description) VALUES (?, ?)");
                $stmt->bind_param("ss", $catData['name'], $catData['description']);
            }
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                if ($isEditCat) nx_audit_update('admin_achievements', (string)$catId, true, $catData['name'], 'admincenter.php?site=achievements&action=categories');
                else {
                    $newId = (int)($_database->insert_id ?? 0);
                    nx_audit_create('admin_achievements', (string)$newId, $catData['name'], 'admincenter.php?site=achievements&action=categories');
                }
                nx_redirect('admincenter.php?site=achievements&action=categories', 'success', 'alert_saved', false);
            } else {
                nx_alert('warning', nx_translate('required_category'), false, true, true);
            }
            $stmt->close();
        }
    }
    ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bi bi-tags"></i> <span><?= $languageService->get('categories') ?></span>
                        <small class="text-muted"><?= $languageService->get('overview') ?></small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th><?= $languageService->get('description') ?></th>
                                <th class="text-end"><?= $languageService->get('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $catResult = $_database->query("SELECT * FROM plugins_achievements_categories ORDER BY name ASC");
                        while($catRow = $catResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($catRow['name']) ?></td>
                                <td><?= htmlspecialchars($catRow['description']) ?></td>
                                <td class="text-end">
                                    <a href="admincenter.php?site=achievements&action=editcategory&id=<?= (int)$catRow['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil-square"></i> <?= $languageService->get('edit') ?></a>
                                    <?php
                                        $deleteUrl = 'admincenter.php?site=achievements' . '&action=deletecategory' . '&id=' . (int)$catRow['id'] . '&csrf_token=' . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
                                    ?>

                                    <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-url="<?= htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="bi bi-trash3"></i> <?= $languageService->get('delete') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bi bi-plus-circle"></i> <span><?= $languageService->get('categories') ?></span>
                        <small class="text-muted"><?= $isEditCat ? $languageService->get('edit_category') : $languageService->get('new_category') ?></small>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($errorCat): ?><div class="alert alert-danger"><?= htmlspecialchars($errorCat) ?></div><?php endif; ?>
                    <form method="post" action="admincenter.php?site=achievements&action=<?= $isEditCat ? 'editcategory&id='.$catId : 'addcategory' ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <div class="mb-3"><label class="form-label" for="name">Name:</label><input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($catData['name']) ?>"></div>
                        <div class="mb-3"><label class="form-label" for="description"><?= $languageService->get('description') ?></label><textarea name="description" id="description" class="form-control" rows="3"><?= htmlspecialchars($catData['description']) ?></textarea></div>
                        <input type="hidden" name="save_category" value="1">
                        <button type="submit" class="btn btn-primary"><?= $languageService->get('save') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// EINSTELLUNGEN SEITE
elseif ($action === 'settings') {
    $settings_result = $_database->query("SELECT * FROM plugins_achievements_settings");
    $settings = [];
    while($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    $trackable_activities = achievements_get_trackable_activities_config();
    $weights_for_template = [];

    foreach ($trackable_activities as $activity) {
        $weight_key = 'weight_' . $activity['type'];

        $source = $activity['source'] ?? 'plugin';
        if ($source === 'core') {
            $isAvailable = true;
            $missingReason = null;
        } else {
            $tableName = $activity['table'] ?? '';
            $isAvailable = ($tableName !== '' && table_Exists($tableName));
            $missingReason = $isAvailable ? null : 'plugin_missing';
        }

        $weights_for_template[] = [
            'name'      => $activity['type'],
            'label'     => $languageService->get($activity['lang_key']),
            'value'     => $settings[$weight_key] ?? 0,
            'available' => $isAvailable,
            'plugin'    => $activity['plugin'] ?? null,
            'source'    => $source,
            'missing'   => $missingReason,
        ];
    }

    usort($weights_for_template, fn($a, $b) => strcoll($a['label'], $b['label']));
    
    $sample_icon_res = $_database->query("SELECT image FROM plugins_achievements WHERE image != '' AND image IS NOT NULL LIMIT 1");
    $sample_icon = ($sample_icon_res && $sample_icon_res->num_rows > 0) ? $sample_icon_res->fetch_assoc()['image'] : '';

    ?>
    <form method="post" action="admincenter.php?site=achievements&action=settings" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="row g-3">
            <!-- LINKS: Cards -->
            <div class="col-12 col-lg-6">
                <!-- Card: Allgemein + Sichtbarkeit -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bi bi-gear-fill"></i> <span><?= $languageService->get('settings') ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="points_per_level" class="form-label">
                                <?= $languageService->get('required_points_lvl') ?>:
                            </label>
                            <input type="number" class="form-control" name="points_per_level" id="points_per_level"
                                value="<?= htmlspecialchars($settings['points_per_level'] ?? 100) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="hide_locked_icon" class="form-label">
                                <?= $languageService->get('achievement_blocked_vis') ?>
                            </label>
                            <select class="form-select" name="hide_locked_icon" id="hide_locked_icon">
                                <option value="yes" <?= ($settings['hide_locked_icon'] ?? 'yes') == 'yes' ? 'selected' : '' ?>>
                                    <?= $languageService->get('blocked_icon') ?>
                                </option>
                                <option value="no" <?= ($settings['hide_locked_icon'] ?? 'yes') == 'no' ? 'selected' : '' ?>>
                                    <?= $languageService->get('blocked_icon_visible') ?>
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div id="preview-yes" class="alert alert-light">
                                <h6 class="alert-heading"><?= $languageService->get('preview_blocked') ?></h6>
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($upload_path_web . ($settings['custom_locked_icon'] ?? 'locked-archv.png')) ?>"
                                        alt="Vorschau" style="width: 50px; height: 50px; margin-right: 15px;">
                                    <div><?= $languageService->get('blocked_info') ?></div>
                                </div>
                            </div>

                            <div id="preview-no" class="alert alert-light">
                                <h6 class="alert-heading"><?= $languageService->get('preview_blocked_visible') ?></h6>
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($upload_path_web . ($sample_icon ?: 'visible.png')) ?>"
                                        alt="Vorschau" style="width: 50px; height: 50px; margin-right: 15px; filter: grayscale(90%);">
                                    <div><?= $languageService->get('blocked_info_visible') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="custom_locked_icon_section">
                            <label for="custom_locked_icon" class="form-label">
                                <?= $languageService->get('blocked_own_icon') ?>:
                            </label>
                            <input type="file" class="form-control" name="custom_locked_icon" id="custom_locked_icon">
                            <div class="form-text">
                                <?= $languageService->get('blocked_own_icon_info') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Bonuspunkte -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bi bi-coin"></i> <span><?= $languageService->get('bonuspoints') ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="max_bonus_points" class="form-label">
                                <?= $languageService->get('bonuspoints') ?>:
                            </label>
                            <input type="number" class="form-control" name="max_bonus_points" id="max_bonus_points"
                                value="<?= htmlspecialchars($settings['max_bonus_points'] ?? 2000) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="admin_bonus_award_limit" class="form-label">
                                <?= $languageService->get('bonuspoints_limit_admin') ?>:
                            </label>
                            <input type="number" class="form-control" name="admin_bonus_award_limit" id="admin_bonus_award_limit"
                                value="<?= htmlspecialchars($settings['admin_bonus_award_limit'] ?? 1) ?>">
                            <div class="form-text"><?= $languageService->get('bonuspoints_unlimited') ?></div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-4"><?= $languageService->get('save') ?></button>
            </div>

            <!-- RECHTS: Punkte-Gewichtung -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm mt-3">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bi bi-sliders"></i> <span><?= $languageService->get('points_weight') ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($weights_for_template as $weight): ?>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="weight_<?= htmlspecialchars($weight['name']) ?>" class="form-label">
                                            <?= htmlspecialchars($weight['label']) ?>:
                                        </label>

                                        <input type="number"
                                            class="form-control"
                                            name="weight_<?= htmlspecialchars($weight['name']) ?>"
                                            id="weight_<?= htmlspecialchars($weight['name']) ?>"
                                            value="<?= htmlspecialchars($weight['value']) ?>"
                                            <?= !$weight['available'] ? 'disabled' : '' ?>>

                                        <?php if (!$weight['available'] && ($weight['source'] ?? 'plugin') === 'plugin'): ?>
                                            <div class="form-text text-muted">
                                                <?= sprintf( $languageService->get('formtext_missing_plugin'), htmlspecialchars($weight['plugin'] ?? $weight['label'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('hide_locked_icon');
        const previewYes = document.getElementById('preview-yes');
        const previewNo = document.getElementById('preview-no');
        const customIconSection = document.getElementById('custom_locked_icon_section');

        function toggleVisibility() {
            if (select.value === 'yes') {
                previewYes.style.display = 'block';
                previewNo.style.display = 'none';
                customIconSection.style.display = 'block';
            } else {
                previewYes.style.display = 'none';
                previewNo.style.display = 'block';
                customIconSection.style.display = 'none';
            }
        }

        select.addEventListener('change', toggleVisibility);
        toggleVisibility();
    });
    </script>
    <?php
}

// MANUELLEN VERGABE
elseif ($action === 'manual_award') {
    $users_result = $_database->query("SELECT userID, username FROM users ORDER BY username ASC");
    $regular_achievements_result = $_database->query("SELECT id, name FROM plugins_achievements WHERE type NOT IN ('manual', 'bonus_points') AND name != 'Admin' ORDER BY name ASC");
    $manual_achievements_result = $_database->query("SELECT id, name FROM plugins_achievements WHERE type = 'manual' ORDER BY name ASC");
?>
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bi bi-person-check"></i> <span><?= $languageService->get('manual_achiv') ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['saved'])): ?><div class="alert alert-success"><?= $languageService->get('manual_achiv_success') ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                    <form method="post" action="admincenter.php?site=achievements&action=manual_award" id="manualAwardForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="achievement_id" id="hidden_achievement_id" value="">

                        <div class="mb-3">
                            <label for="user_id" class="form-label"><?= $languageService->get('manual_achiv_user') ?>:</label>
                            <select class="form-select" name="user_id" id="user_id" required>
                                <option value=""><?= $languageService->get('manual_achiv_choose') ?></option>
                                <?php
                                $current_admin_id = $_SESSION['userID'] ?? 0;
                                while($user = $users_result->fetch_assoc()):
                                    if ((int)$user['userID'] === $current_admin_id) continue;
                                ?>
                                    <option value="<?= (int)$user['userID'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="regular_achievement_select" class="form-label"><?= $languageService->get('all_achievements') ?>:</label>
                            <select class="form-select" id="regular_achievement_select" required>
                                <option value=""><?= $languageService->get('manual_achiv_choose') ?></option>
                                <?php while($ach = $regular_achievements_result->fetch_assoc()): ?>
                                    <option value="<?= (int)$ach['id'] ?>"><?= htmlspecialchars($ach['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                            <div class="form-text"><?= $languageService->get('achiev_automatic') ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="manual_achievement_select" class="form-label"><?= $languageService->get('manual_achiv_excl') ?>:</label>
                            <select class="form-select" id="manual_achievement_select" required>
                                <option value=""><?= $languageService->get('manual_achiv_choose') ?></option>
                                <?php while($ach = $manual_achievements_result->fetch_assoc()): ?>
                                    <option value="<?= (int)$ach['id'] ?>"><?= htmlspecialchars($ach['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                            <div class="form-text"><?= $languageService->get('achiev_admin') ?></div>
                        </div>

                        <button type="submit" class="btn btn-primary"><?= $languageService->get('given') ?></button>
                    </form>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const manualSelect = document.getElementById('manual_achievement_select');
                        const regularSelect = document.getElementById('regular_achievement_select');
                        const hiddenInput = document.getElementById('hidden_achievement_id');
                        const form = document.getElementById('manualAwardForm');

                        function updateHiddenInput() {
                            if (manualSelect.value) {
                                hiddenInput.value = manualSelect.value;
                                regularSelect.value = '';
                                regularSelect.required = false;
                                manualSelect.required = true;
                            } else if (regularSelect.value) {
                                hiddenInput.value = regularSelect.value;
                                manualSelect.value = '';
                                manualSelect.required = false;
                                regularSelect.required = true;
                            } else {
                                hiddenInput.value = '';
                                manualSelect.required = true;
                                regularSelect.required = true;
                            }
                        }

                        manualSelect.addEventListener('change', updateHiddenInput);
                        regularSelect.addEventListener('change', updateHiddenInput);

                        form.addEventListener('submit', function(e) {
                            if (!manualSelect.value && !regularSelect.value) {
                                e.preventDefault();
                                alert(<?= json_encode($languageService->get('required_achiv')) ?>);
                            }
                        });
                        
                        updateHiddenInput();
                    });
                    </script>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bi bi-list-task"></i> <span><?= $languageService->get('log_manual') ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-log-middle-align"> <thead>
                            <tr>
                                <th><?= $languageService->get('user') ?></th>
                                <th><?= $languageService->get('achievement') ?></th>
                                <th><?= $languageService->get('achievement_type') ?></th>
                                <th><?= $languageService->get('given_by') ?></th>
                                <th class="text-end"><?= $languageService->get('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $log_sql = "
                                    SELECT 
                                        l.id, u.username as user_name, adm.username as admin_name, 
                                        a.name as achievement_name, a.type as achievement_type, a.image as achievement_image
                                    FROM plugins_achievements_admin_log l
                                    JOIN users u ON l.user_id = u.userID
                                    JOIN users adm ON l.admin_id = adm.userID
                                    LEFT JOIN plugins_achievements a ON l.related_id = a.id
                                    WHERE l.log_type = 'manual_award'
                                    ORDER BY l.id DESC
                                ";
                                $log_result = $_database->query($log_sql);
                                while($log_row = $log_result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($log_row['user_name']) ?></td>
                                <td>
                                    <?php if (isset($log_row['achievement_name'])): ?>
                                        <div class="d-flex align-items-center"> <img src="<?= htmlspecialchars($upload_path_web . $log_row['achievement_image']) ?>" alt="" class="me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                            <span><?= htmlspecialchars($log_row['achievement_name']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="fst-italic text-muted"><?= $languageService->get('achievement_deleted') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (($log_row['achievement_type'] ?? '') === 'manual'): ?>
                                        <span class="badge text-bg-info"><?= $languageService->get('achievement_exclusive') ?></span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary"><?= $languageService->get('achievement_normal') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($log_row['admin_name']) ?></td>
                                <td class="text-end">
                                    <?php
                                        $deleteUrl = 'admincenter.php?site=achievements' . '&action=delete_manual_award' . '&id=' . (int)$log_row['id'] . '&csrf_token=' . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
                                    ?>

                                    <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-url="<?= $deleteUrl ?>">
                                        <i class="bi bi-trash3"></i> <?= $languageService->get('delete') ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// BONUSPUNKTE
elseif ($action === 'add_points') {
    $settings_result = $_database->query("SELECT setting_key, setting_value FROM plugins_achievements_settings");
    $settings = [];
    while($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    $max_bonus_points = (int)($settings['max_bonus_points'] ?? 2000);
    $award_limit = (int)($settings['admin_bonus_award_limit'] ?? 1);
    $admin_id = $_SESSION['userID'] ?? 0;
    $stmt_count = $_database->prepare("SELECT COUNT(id) as award_count FROM plugins_achievements_admin_log WHERE admin_id = ? AND log_type = 'bonus_points'");
    $stmt_count->bind_param("i", $admin_id);
    $stmt_count->execute();
    $admin_award_count = $stmt_count->get_result()->fetch_assoc()['award_count'] ?? 0;
    $stmt_count->close();
    
    $users_result = $_database->query("SELECT userID, username FROM users ORDER BY username ASC");
    ?>
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bi bi-gift"></i> <span><?= $languageService->get('bonuspoints_give') ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <ul style="margin-bottom: 0px;">
                            <li><?= $languageService->get('bonuspoints_info_max') ?> <strong><?= $max_bonus_points ?></strong> <?= $languageService->get('bonuspoints_info_max_pts') ?></li>
                            <?php if ($award_limit == -1): ?>
                                <li><?= $languageService->get('bonuspoints_info_infinity') ?></li>
                            <?php else: 
                                $awards_left = $award_limit - $admin_award_count;
                            ?>
                                <li>
                                    <?= $languageService->get('bonuspoints_text') ?> <strong><?= $award_limit ?></strong> <?= $languageService->get('bonuspoints_text_times') ?>.
                                    <?= $languageService->get('bonuspoints_text2') ?> <strong><?= max(0, $awards_left) ?></strong> <?= $languageService->get('bonuspoints_text3') ?>.
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php if (isset($_GET['saved'])): ?><div class="alert alert-success"><?= $languageService->get('bonuspoints_success') ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                    <form method="post" action="admincenter.php?site=achievements&action=add_points">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <div class="mb-3">
                            <label for="user_id" class="form-label"><?= $languageService->get('manual_achiv_user') ?>:</label>
                            <select class="form-select" name="user_id" id="user_id" required>
                                <option value=""><?= $languageService->get('manual_achiv_choose') ?></option>
                                <?php
                                $current_admin_id = $_SESSION['userID'] ?? 0;
                                while($user = $users_result->fetch_assoc()):
                                    if ((int)$user['userID'] === $current_admin_id) continue;
                                ?>
                                    <option value="<?= (int)$user['userID'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="points" class="form-label"><?= $languageService->get('points_total2') ?>:</label>
                            <input type="number" class="form-control" name="points" id="points" required min="1" max="<?= (int)$max_bonus_points ?>">
                            <div class="form-text"><?= $languageService->get('maximum') ?> <?= (int)$max_bonus_points ?> <?= $languageService->get('points_allowed') ?>.</div>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= $languageService->get('give_points') ?></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bi bi-list-task"></i> <span><?= $languageService->get('bonuspoints_log') ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                     <table class="table">
                        <thead>
                            <tr>
                                <th><?= $languageService->get('user') ?></th>
                                <th><?= $languageService->get('points') ?></th>
                                <th><?= $languageService->get('given_by') ?></th>
                                <th class="text-end"><?= $languageService->get('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $bonus_log_sql = "
                                SELECT l.id, u.username as user_name, l.value as points_added, adm.username as admin_name
                                FROM plugins_achievements_admin_log l
                                JOIN users u ON l.user_id = u.userID
                                JOIN users adm ON l.admin_id = adm.userID
                                WHERE l.log_type = 'bonus_points'
                                ORDER BY l.id DESC
                            ";
                            $log_result = $_database->query($bonus_log_sql);
                            while($log_row = $log_result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($log_row['user_name']) ?></td>
                            <td><?= (int)$log_row['points_added'] ?></td>
                            <td><?= htmlspecialchars($log_row['admin_name']) ?></td>
                            <td class="text-end">
                                <?php
                                    $deleteUrl = 'admincenter.php?site=achievements' . '&action=delete_bonus_log' . '&log_id=' . (int)$log_row['id'] . '&csrf_token=' . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
                                ?>

                                <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-url="<?= $deleteUrl ?>">
                                    <i class="bi bi-trash"></i> <?= $languageService->get('delete') ?>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// HAUPTANSICHT (LISTE)
else {
    // Filter & Sortierung
    $filter_category = intval($_GET['filter_category'] ?? 0);
    $filter_type = $_GET['filter_type'] ?? '';
    $sort_by = $_GET['sort_by'] ?? 'name';
    $sort_dir = ($_GET['sort_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
    
    $allowed_sorts = ['name', 'category_name', 'type'];
    if(!in_array($sort_by, $allowed_sorts)) $sort_by = 'name';

    $sql = "SELECT a.*, c.name as category_name FROM plugins_achievements a LEFT JOIN plugins_achievements_categories c ON a.category_id = c.id";
    $where_clauses = [];
    $params = [];
    $types = '';

    if ($filter_category > 0) {
        $where_clauses[] = "a.category_id = ?";
        $params[] = $filter_category;
        $types .= 'i';
    }
    if (!empty($filter_type)) {
        $where_clauses[] = "a.type = ?";
        $params[] = $filter_type;
        $types .= 's';
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    
    $order_column = $sort_by === 'category_name' ? 'c.name' : 'a.' . $sort_by;
    $sql .= " ORDER BY $order_column $sort_dir";

    $stmt = $_database->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $categories = $_database->query("SELECT id, name FROM plugins_achievements_categories ORDER BY name ASC");
    $achievement_types = ['role', 'level', 'points', 'activity_count', 'category_points', 'registration_time', 'manual'];
    ?>

    <link rel="stylesheet" href="../includes/plugins/achievements/css/achievements.css">
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <div class="card-title">
                <i class="bi bi-trophy"></i> <span><?= $languageService->get('achievement_maintain') ?></span>
            </div>
            <div>
                <a href="admincenter.php?site=achievements&action=add" class="btn btn-secondary"><?= $languageService->get('new_achievement') ?></a>
                <div class="btn-group ms-2 dropdown-hover">
                    <button type="button" class="btn btn-secondary dropdown-toggle" aria-expanded="false">
                        <?= $languageService->get('administration') ?>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="admincenter.php?site=achievements&action=categories">
                            <i class="bi bi-tags me-2"></i> <?= $languageService->get('categories') ?>
                        </a></li>
                        <li><a class="dropdown-item" href="admincenter.php?site=achievements&action=settings">
                            <i class="bi bi-sliders me-2"></i> <?= $languageService->get('settings') ?>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="admincenter.php?site=achievements&action=add_points">
                            <i class="bi bi-gift me-2"></i> <?= $languageService->get('bonuspoints_give') ?>
                        </a></li>
                        <li><a class="dropdown-item" href="admincenter.php?site=achievements&action=manual_award">
                            <i class="bi bi-person-check me-2"></i> <?= $languageService->get('achiev_manually') ?>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="card bg-light">
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <input type="hidden" name="site" value="achievements">
                        <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sort_by) ?>">
                        <input type="hidden" name="sort_dir" value="<?= htmlspecialchars($sort_dir) ?>">
                        <div class="col-md-4">
                            <label for="filter_category" class="form-label"><?= $languageService->get('filter_by_category') ?></label>
                            <select name="filter_category" id="filter_category" class="form-select">
                                <option value=""><?= $languageService->get('all_categories') ?></option>
                                <?php while($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $filter_category == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="filter_type" class="form-label"><?= $languageService->get('filter_by_type') ?></label>
                            <select class="form-select" name="filter_type" id="filter_type">
                                <option value=""><?= $languageService->get('all_types') ?></option>
                                    <?php foreach ($achievement_types as $type): ?>
                                        <?php
                                            $langKey = 'achievement_type_' . $type;
                                            $label = $languageService->get($langKey);

                                            if ($label === $langKey) {
                                                $label = ucfirst(str_replace('_', ' ', $type));
                                            }
                                        ?>
                                        <option value="<?= $type ?>" <?= $filter_type == $type ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2"><?= $languageService->get('filter') ?></button>
                            <a href="admincenter.php?site=achievements" class="btn btn-outline-secondary"><?= $languageService->get('reset') ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-lg-3 mb-4 mt-4">
            <div class="card h-100 shadow-sm achievement-card-bg" style="--bg-icon-url: url('<?= htmlspecialchars($upload_path_web . $row['image']) ?>');">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 d-flex justify-content-center align-items-center">
                            <img src="<?= htmlspecialchars($upload_path_web . $row['image']) ?>" style="width: 80px; height: 80px;">
                        </div>
                        <div class="col-md-8 d-flex flex-column">
                            <h4 class="card-title mb-3"><?= htmlspecialchars($row['name']) ?></h4>
                            <dl class="row mb-0">
                                <dt class="col-sm-5"><?= $languageService->get('category') ?></dt>
                                <dd class="col-sm-7"><?= htmlspecialchars($row['category_name'] ?? $languageService->get('none')) ?></dd>
                                <dt class="col-sm-5">Typ:</dt>
                                <dd class="col-sm-7">
                                    <?php
                                        $langKey = 'achievement_type_' . $row['type'];
                                        $label = $languageService->get($langKey);

                                        if ($label === $langKey) {
                                            $label = ucfirst(str_replace('_', ' ', $row['type']));
                                        }
                                    ?>
                                    <?= htmlspecialchars($label) ?>
                                </dd>
                                <dt class="col-sm-5"><?= $languageService->get('visible') ?>:</dt>
                                <dd class="col-sm-7">
                                    <?php if ($row['show_in_overview']): ?>
                                        <span class="badge text-bg-success"><?= $languageService->get('public') ?></span>
                                    <?php else: ?>
                                        <span class="badge text-bg-danger"><?= $languageService->get('hidden') ?></span>
                                    <?php endif; ?>
                                </dd>
                                <dt class="col-sm-5"><?= $languageService->get('trigger') ?>:</dt>
                                <dd class="col-sm-7">
                                    <?php if (!empty($row['trigger_value'])): ?>
                                        <?= htmlspecialchars($row['trigger_value']) ?>
                                        <?php if (!empty($row['trigger_condition'])): ?>
                                            <?php
                                                $langKey = 'achievement_condition_' . $row['trigger_condition'];
                                                $condition_display = $languageService->get($langKey);

                                                if ($condition_display === $langKey) {
                                                    $condition_display = $row['trigger_condition'];
                                                }
                                            ?>
                                            (<?= htmlspecialchars($condition_display) ?>)
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic"><?= $languageService->get('no_trigger_needed') ?></span>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                            <hr class="my-3">
                            <div>
                                <span class="fw-semibold"><?= $languageService->get('description') ?>:</span>
                                <p class="fst-italic mb-0">
                                    <?= !empty($row['description']) ? nl2br(htmlspecialchars($row['description'])) : 'Keine Beschreibung vorhanden.' ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-1 text-end mt-4">
                        <a href="admincenter.php?site=achievements&action=edit&id=<?= (int)$row['id'] ?>" class="btn btn-warning d-inline-flex align-items-center gap-1 w-auto"><i class="bi bi-pencil-square"></i> <?= $languageService->get('edit') ?></a>
                        <?php
                            $deleteUrl = 'admincenter.php?site=achievements' . '&action=delete' . '&id=' . (int)$row['id'] . '&csrf_token=' . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
                        ?>

                        <a href="#" class="btn btn-danger d-inline-flex align-items-center gap-1 w-auto" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-confirm-url="<?= $deleteUrl ?>">
                            <i class="bi bi-trash3"></i> <?= $languageService->get('delete') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
</div>
<?php
}
?>