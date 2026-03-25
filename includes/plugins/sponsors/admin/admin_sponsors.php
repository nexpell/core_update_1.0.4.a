<?php

// Session absichern
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\AccessControl;
global $_database,$languageService;

if (isset($languageService) && method_exists($languageService, 'readModule')) {
    $languageService->readModule('sponsors');
}

// Admin-Zugriff prüfen
AccessControl::checkAdminAccess('sponsors');

// Einfaches Routing: action aus GET/POST
$action = $_GET['action'] ?? ($_POST['action'] ?? null);

$currentLang = null;
if (!empty($_SESSION['sponsors_active_lang'])) {
    $currentLang = (string)$_SESSION['sponsors_active_lang'];
    unset($_SESSION['sponsors_active_lang']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['active_lang'])) {
    $currentLang = (string)$_POST['active_lang'];
} elseif (!empty($_SESSION['language'])) {
    $currentLang = (string)$_SESSION['language'];
} else {
    $currentLang = (string)$languageService->detectLanguage();
}

if ($action === 'save_sort') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['status' => 'error']);
        exit;
    }

    foreach ($data as $row) {
        $sponsorID = (int)($row['id'] ?? 0);
        $sortOrder = (int)($row['sort_order'] ?? 0);
        if ($sponsorID > 0) {
            $stmtSort = $_database->prepare("UPDATE plugins_sponsors SET sort_order = ? WHERE id = ?");
            $stmtSort->bind_param("ii", $sortOrder, $sponsorID);
            $stmtSort->execute();
            $stmtSort->close();
        }
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

// Pfad zu Logo-Uploads
$uploadDir = dirname(__DIR__) . '/images/';

function sponsors_get_languages(mysqli $db): array {
    $languages = [];
    $res = mysqli_query($db, "SELECT iso_639_1, name_de FROM settings_languages WHERE active = 1 ORDER BY id ASC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $languages[$row['iso_639_1']] = $row['name_de'];
        }
    }
    if (!$languages) {
        $languages = ['de' => 'Deutsch', 'en' => 'English', 'it' => 'Italiano'];
    }
    return $languages;
}

function sponsors_get_content_map(mysqli $db): array {
    $map = [];
    $res = mysqli_query($db, "SELECT content_key, language, content FROM settings_plugins_lang WHERE content_key IN ('sponsors_headline','sponsors_intro')");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $map[(string)$row['content_key']][strtolower((string)$row['language'])] = (string)$row['content'];
        }
    }
    return $map;
}

// Helper Funktion: Datei-Upload verarbeiten
function handleLogoUpload($file, $oldFile = null) {
    global $uploadDir, $languageService;

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed, true)) {
            return ['error' => $languageService->get('upload_logo_invalid_type')];
        }

        $filename = uniqid('sponsor_') . '.' . $ext;
        $target = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            if ($oldFile && file_exists($uploadDir . $oldFile)) {
                unlink($uploadDir . $oldFile);
            }
            return ['filename' => $filename];
        }

        return ['error' => $languageService->get('upload_logo_failed')];
    }

    return ['filename' => $oldFile];
}

// DELETE (Modal-GET + POST-Fallback)
$deleteId = 0;

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = (int)$_GET['id'];
} elseif (isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
}

if ($deleteId > 0) {
    $id = $deleteId;

    $sponsorName = '';
    $stmt_name = $_database->prepare("SELECT name, logo FROM plugins_sponsors WHERE id = ? LIMIT 1");
    $stmt_name->bind_param("i", $id);
    $stmt_name->execute();
    $res = $stmt_name->get_result();
    $row = $res ? $res->fetch_assoc() : null;

    if ($row) {
        $sponsorName = trim((string)($row['name'] ?? ''));
        if (!empty($row['logo'])) {
            @unlink($uploadDir . $row['logo']);
        }
    }
    $stmt_name->close();

    $stmt = $_database->prepare("DELETE FROM plugins_sponsors WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $stmt->close();
        nx_audit_delete('admin_sponsors', (string)$id, ($sponsorName !== '' ? $sponsorName : (string)$id), 'admincenter.php?site=admin_sponsors');
        nx_redirect('admincenter.php?site=admin_sponsors', 'success', 'alert_deleted', false);
    }

    $stmt->close();
    nx_redirect('admincenter.php?site=admin_sponsors', 'danger', 'alert_not_found', false);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_content'])) {
    $activeLang = strtolower(trim((string)($_POST['active_lang'] ?? $currentLang)));
    $_SESSION['sponsors_active_lang'] = $activeLang;

    $headlineLang = $_POST['headline_lang'] ?? [];
    $introLang = $_POST['intro_lang'] ?? [];

    foreach (['sponsors_headline' => $headlineLang, 'sponsors_intro' => $introLang] as $contentKey => $values) {
        if (!is_array($values)) {
            continue;
        }

        foreach ($values as $iso => $content) {
            $iso = strtolower(trim((string)$iso));
            if ($iso === '') {
                continue;
            }

            $stmtContent = $_database->prepare("
                INSERT INTO settings_plugins_lang (content_key, language, content, modulname, updated_at)
                VALUES (?, ?, ?, 'sponsors', NOW())
                ON DUPLICATE KEY UPDATE
                    content = VALUES(content),
                    modulname = VALUES(modulname),
                    updated_at = NOW()
            ");
            $content = (string)$content;
            $stmtContent->bind_param("sss", $contentKey, $iso, $content);
            $stmtContent->execute();
            $stmtContent->close();
        }
    }

    nx_redirect('admincenter.php?site=admin_sponsors&action=content', 'success', 'alert_saved', false);
}

// POST: Add/Edit Sponsor speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_sponsor'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = $_database->real_escape_string(trim($_POST['name']));
    $slug = $_database->real_escape_string(trim($_POST['slug']));
    $level = $_database->real_escape_string(trim($_POST['level']));
    $sort_order = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $userID      = (int)$_SESSION['userID'];

    $oldLogo = '';
    if ($id > 0) {
        $res = $_database->query("SELECT logo FROM plugins_sponsors WHERE id = $id");
        $row = $res->fetch_assoc();
        $oldLogo = $row['logo'] ?? '';
    }

    $uploadResult = handleLogoUpload($_FILES['logo'] ?? null, $oldLogo);

    if (isset($uploadResult['error'])) {
        nx_alert('danger', $uploadResult['error'], true, true, true);
    } else {
        $logo = $uploadResult['filename'];

        if ($id > 0) {
            // Update
            $stmt = $_database->prepare("UPDATE plugins_sponsors SET name=?, slug=?, userID=?, level=?, logo=?, is_active=?, sort_order=? WHERE id=?");
            $stmt->bind_param("ssissiii", $name, $slug, $userID, $level, $logo, $is_active, $sort_order, $id);
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok) nx_audit_update('admin_sponsors', (string)$id, true, $name, 'admincenter.php?site=admin_sponsors');
        } else {
            // Insert
            $stmt = $_database->prepare("INSERT INTO plugins_sponsors (name, slug, level, userID, logo, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssisii", $name, $slug, $level, $userID, $logo, $is_active, $sort_order);
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok) {
                $newId = (int)($_database->insert_id ?? 0);
                nx_audit_create('admin_sponsors', (string)$newId, $name, 'admincenter.php?site=admin_sponsors');
            }
        }

        nx_redirect('admincenter.php?site=admin_sponsors', 'success', 'alert_saved', false);
    }
}

// Sponsor-Level Auswahl (für Formular)
$levels = ['Platin Sponsor', 'Gold Sponsor', 'Silber Sponsor', 'Bronze Sponsor', 'Partner', 'Unterstützer'];

if ($action === 'content') {
    $languages = sponsors_get_languages($_database);
    if (!isset($languages[$currentLang])) {
        $currentLang = array_key_first($languages) ?: 'de';
    }

    $contentMap = sponsors_get_content_map($_database);
    $headlineByLang = $contentMap['sponsors_headline'] ?? [];
    $introByLang = $contentMap['sponsors_intro'] ?? [];
?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title d-flex align-items-center justify-content-between w-100">
            <span>
                <i class="bi bi-card-text"></i> <?= $languageService->get('content') ?>
                <small class="text-muted"><?= $languageService->get('edit') ?></small>
            </span>
            <div class="btn-group" id="lang-switch">
                <?php foreach ($languages as $iso => $label): ?>
                    <button type="button" class="btn <?= $iso === $currentLang ? 'btn-primary' : 'btn-secondary' ?>" data-lang="<?= htmlspecialchars($iso, ENT_QUOTES, 'UTF-8') ?>">
                        <?= strtoupper($iso) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="post" action="admincenter.php?site=admin_sponsors&action=content">
            <input type="hidden" name="save_content" value="1">
            <input type="hidden" name="active_lang" id="active_lang" value="<?= htmlspecialchars($currentLang, ENT_QUOTES, 'UTF-8') ?>">

            <div class="row g-3">
                <div class="col-12">
                    <label for="headline_main" class="form-label"><?= $languageService->get('info_title') ?></label>
                    <input type="text" class="form-control" id="headline_main" value="<?= htmlspecialchars((string)($headlineByLang[$currentLang] ?? $languageService->get('headline')), ENT_QUOTES, 'UTF-8') ?>">
                    <?php foreach ($languages as $iso => $label): ?>
                        <input type="hidden" id="headline_<?= htmlspecialchars($iso, ENT_QUOTES, 'UTF-8') ?>" name="headline_lang[<?= htmlspecialchars($iso, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars((string)($headlineByLang[$iso] ?? ($iso === $currentLang ? $languageService->get('headline') : '')), ENT_QUOTES, 'UTF-8') ?>">
                    <?php endforeach; ?>
                </div>

                <div class="col-12">
                    <label for="intro_main" class="form-label"><?= $languageService->get('info_text') ?></label>
                    <textarea class="form-control" rows="6" id="intro_main"><?= htmlspecialchars((string)($introByLang[$currentLang] ?? $languageService->get('text')), ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php foreach ($languages as $iso => $label): ?>
                        <input type="hidden" id="intro_<?= htmlspecialchars($iso, ENT_QUOTES, 'UTF-8') ?>" name="intro_lang[<?= htmlspecialchars($iso, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars((string)($introByLang[$iso] ?? ($iso === $currentLang ? $languageService->get('text') : '')), ENT_QUOTES, 'UTF-8') ?>">
                    <?php endforeach; ?>
                </div>

                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary"><?= $languageService->get('save') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const switchRoot = document.getElementById('lang-switch');
    if (!switchRoot) return;

    const activeInput = document.getElementById('active_lang');
    const headlineMain = document.getElementById('headline_main');
    const introMain = document.getElementById('intro_main');

    function syncCurrent() {
        const lang = activeInput.value;
        const headlineHidden = document.getElementById('headline_' + lang);
        const introHidden = document.getElementById('intro_' + lang);
        if (headlineHidden) headlineHidden.value = headlineMain.value;
        if (introHidden) introHidden.value = introMain.value;
    }

    switchRoot.querySelectorAll('[data-lang]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            syncCurrent();
            const lang = btn.getAttribute('data-lang');
            activeInput.value = lang;
            headlineMain.value = document.getElementById('headline_' + lang)?.value || '';
            introMain.value = document.getElementById('intro_' + lang)?.value || '';

            switchRoot.querySelectorAll('.btn').forEach(function (b) {
                b.classList.remove('btn-primary');
                b.classList.add('btn-secondary');
            });
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-primary');
        });
    });

    const form = switchRoot.closest('.card').querySelector('form');
    if (form) {
        form.addEventListener('submit', function () {
            syncCurrent();
        });
    }
});
</script>
<?php
    return;
}

// Anzeige abhängig von action
if ($action === 'add' || $action === 'edit') {

    $editSponsor = null;
    $isEdit = ($action === 'edit');

    if ($action === 'edit' && isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $id = (int)$_GET['edit'];
        $res = $_database->query("SELECT * FROM plugins_sponsors WHERE id = $id");
        $editSponsor = $res->fetch_assoc();
    }

?>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-link-45deg"></i> <span><?= $languageService->get('sponsors_manage') ?></span>
            <small class="text-muted"><?= $isEdit ? $languageService->get('edit') : $languageService->get('add') ?></small>
        </div>
    </div>

    <div class="card-body">

        <form method="post" enctype="multipart/form-data"
          action="admincenter.php?site=admin_sponsors&action=<?= $action ?><?= $editSponsor ? '&edit=' . (int)$editSponsor['id'] : '' ?>">

        <input type="hidden" name="id" value="<?= htmlspecialchars($editSponsor['id'] ?? '') ?>">

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="row g-3">

                    <!-- Name / URL / Sortierung -->
                    <div class="col-12 col-md-5">
                        <label for="name" class="form-label"><?= $languageService->get('name') ?> *</label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            required
                            value="<?= htmlspecialchars($editSponsor['name'] ?? '') ?>"
                        >
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="slug" class="form-label"><?= $languageService->get('slug') ?></label>
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="slug"
                                name="slug"
                                value="<?= htmlspecialchars($editSponsor['slug'] ?? '') ?>"
                            >
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="sort_order" class="form-label"><?= $languageService->get('sort_order') ?></label>
                        <input
                            type="number"
                            class="form-control"
                            id="sort_order"
                            name="sort_order"
                            min="0"
                            step="1"
                            value="<?= htmlspecialchars($editSponsor['sort_order'] ?? 0) ?>"
                        >
                    </div>

                    <!-- LEVEL -->
                    <div class="col-12">
                        <label for="level" class="form-label"><?= $languageService->get('sponsor_level') ?> *</label>
                        <select id="level" name="level" class="form-select" required>
                            <option value=""><?= $languageService->get('please_select') ?></option>
                            <?php foreach ($levels as $level): ?>
                                <option value="<?= htmlspecialchars($level) ?>" <?= (isset($editSponsor['level']) && $editSponsor['level'] === $level) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($level) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- AKTIV TOGGLE -->
                    <div class="col-12">
                        <div class="form-check form-switch mt-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                id="is_active"
                                name="is_active"
                                <?= (!isset($editSponsor['is_active']) || $editSponsor['is_active'] == 1) ? 'checked' : '' ?>
                            >
                            <label for="is_active" class="form-check-label"><?= $languageService->get('is_active') ?></label>
                        </div>
                    </div>

                    <!-- ACTIONS -->
                    <div class="col-12 pt-2">
                        <div class="d-flex gap-2">
                            <button type="submit" name="save_sponsor" class="btn btn-primary">
                                <?= $languageService->get('save') ?>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- SPONSOR LOGO -->
            <div class="col-12 col-lg-4">
                <div class="p-3 h-100">

                    <div class="fw-semibold mb-2"><?= $languageService->get('logo') ?></div>

                    <?php if (!empty($editSponsor['logo'])): ?>
                        <div class="mb-3">
                            <img src="/includes/plugins/sponsors/images/<?= htmlspecialchars($editSponsor['logo']) ?>" class="img-thumbnail" style="max-width:100%;height:auto;" alt="Logo">
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml" <?= $editSponsor ? '' : 'required' ?>>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
</div>
<?php
} else {
    // Standard: Liste aller Sponsoren anzeigen
    $resSponsors = $_database->query("
    SELECT s.*, 
           COALESCE(k.click_count, 0) AS clicks
    FROM plugins_sponsors s
    LEFT JOIN (
        SELECT itemID, COUNT(*) AS click_count
        FROM link_clicks
        WHERE plugin = 'sponsors'
        GROUP BY itemID
    ) k ON s.id = k.itemID
    ORDER BY s.sort_order ASC
");
    ?>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-link-45deg"></i> <span><?= $languageService->get('sponsors_manage') ?></span>
            <small class="text-muted"><?= $languageService->get('overview') ?></small>
        </div>
        <a href="admincenter.php?site=admin_sponsors&action=content" class="btn btn-secondary">
            <?= $languageService->get('content') ?>
        </a>
        <a href="admincenter.php?site=admin_sponsors&action=add" class="btn btn-secondary">
            <?= $languageService->get('add') ?>
        </a>
    </div>
    <div class="card-body">

        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th><?= $languageService->get('logo') ?></th>
                    <th><?= $languageService->get('name') ?></th>
                    <th><?= $languageService->get('slug') ?></th>
                    <th><?= $languageService->get('sponsor_level') ?></th>
                    <th><?= $languageService->get('clicks_per_day') ?></th>
                    <th><?= $languageService->get('is_active') ?></th>
                    <th><?= $languageService->get('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sponsor = $resSponsors->fetch_assoc()):
                    $createdTimestamp = isset($sponsor['created_at']) ? strtotime($sponsor['created_at']) : time();
                    $days = max(1, round((time() - $createdTimestamp) / (60 * 60 * 24))); 
                    $perday = round($sponsor['clicks'] / $days, 2);
                ?>
                <tr data-id="<?= (int)$sponsor['id'] ?>">
                    <td class="text-muted cursor-move"><i class="bi bi-list"></i></td>
                    <td>
                        <?php if ($sponsor['logo'] && file_exists($uploadDir . $sponsor['logo'])): ?>
                            <img src="/includes/plugins/sponsors/images/<?= htmlspecialchars($sponsor['logo']) ?>" alt="<?= htmlspecialchars($sponsor['name']) ?> Logo" style="max-height:40px;">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($sponsor['name']) ?></td>
                    <td>
                        <?php if ($sponsor['slug']): ?>
                            <a href="<?= htmlspecialchars($sponsor['slug']) ?>" target="_blank" rel="nofollow"><?= htmlspecialchars($sponsor['slug']) ?></a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($sponsor['level']) ?></td>
                    <td>
                        <?= (int)$sponsor['clicks'] ?> (Ø <?= $perday ?>/<?= $languageService->get('clicks_per_day') ?>)
                    </td>
                    <td>
                        <?= $sponsor['is_active']
                            ? '<span class="badge bg-success">'
                                . htmlspecialchars($languageService->get('yes'), ENT_QUOTES, 'UTF-8')
                                . '</span>'
                            : '<span class="badge bg-secondary">'
                                . htmlspecialchars($languageService->get('no'), ENT_QUOTES, 'UTF-8')
                                . '</span>'
                        ?>
                    </td>
                    <td>
                        <a href="admincenter.php?site=admin_sponsors&action=edit&edit=<?= (int)$sponsor['id'] ?>" class="btn btn-warning d-inline-flex align-items-center gap-1 w-auto">
                            <i class="bi bi-pencil-square"></i> <?= $languageService->get('edit') ?>
                        </a>

                        <?php
                            $deleteUrl = 'admincenter.php?site=admin_sponsors&action=delete&id=' . (int)$sponsor['id'];
                        ?>

                        <a href="#" class="btn btn-danger d-inline-flex align-items-center gap-1 w-auto"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal"
                            data-confirm-url="<?= htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-trash3"></i> <?= $languageService->get('delete') ?>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($resSponsors->num_rows === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center"><?= $languageService->get('no_sponsors_found') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php
}
?>
<style>
.cursor-move {
    cursor: grab;
}
.sortable-ghost {
    opacity: 0.5;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('.table tbody');
    if (tbody && typeof Sortable !== 'undefined' && tbody.querySelector('tr[data-id]')) {
        Sortable.create(tbody, {
            handle: '.cursor-move',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                const order = [];
                tbody.querySelectorAll('tr[data-id]').forEach((tr, i) => {
                    order.push({
                        id: tr.dataset.id,
                        sort_order: i + 1
                    });
                });

                fetch('admincenter.php?site=admin_sponsors&action=save_sort', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(order)
                });
            }
        });
    }
});
</script>
