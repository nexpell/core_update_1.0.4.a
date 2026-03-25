<?php
use nexpell\LanguageService;
use nexpell\AccessControl;

if (session_status() === PHP_SESSION_NONE) session_start();

global $_database;
AccessControl::checkAdminAccess('rules');

$CAPCLASS = new \nexpell\Captcha;
$action   = $_GET['action'] ?? '';
$content_key = 'rules'; // Der Key für diese Seite

if (isset($languageService) && $languageService instanceof LanguageService) {
    $languageService->readModule('rules');
}

// 1. Sprachen laden
$languages = [];
$res = mysqli_query($_database, "SELECT iso_639_1, name_de FROM settings_languages WHERE active = 1 ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($res)) {
    $languages[$row['iso_639_1']] = $row['name_de'];
}

// 2. Aktive Sprache bestimmen
$currentLang = null;

// 2. Aktive Sprache bestimmen

if (!empty($_SESSION['rules_active_lang'])) {
    $currentLang = $_SESSION['rules_active_lang'];
    unset($_SESSION['rules_active_lang']);
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['active_lang'])) {
    $currentLang = $_POST['active_lang'];
}
elseif (!empty($_SESSION['language'])) {
    $currentLang = $_SESSION['language'];
}
else {
    $currentLang = $languageService->detectLanguage();
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

        $ruleID = (int)$row['id'];
        $sort   = (int)$row['sort_order'];

        $_database->query("
            UPDATE plugins_rules
            SET sort_order = $sort
            WHERE content_key LIKE 'rule_{$ruleID}_%'
        ");
    }

    echo json_encode(['status' => 'ok']);
    exit;
}

if (isset($_GET['delete'], $_GET['id'])) {

    if ($CAPCLASS->checkCaptcha(0, $_GET['captcha_hash'] ?? '')) {

        $ruleID = (int)$_GET['id'];

        $_database->query("
            DELETE FROM plugins_rules
            WHERE content_key LIKE 'rule_{$ruleID}_%'
        ");

        nx_redirect(
            'admincenter.php?site=admin_rules',
            'success',
            'alert_deleted',
            false
        );
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    if (!$CAPCLASS->checkCaptcha(0, $_POST['captcha_hash'] ?? '')) {
        nx_redirect('admincenter.php?site=admin_rules','danger','alert_transaction_invalid',false);
    }

    $activeLang = $_POST['active_lang'] ?? 'de';
    $is_active  = isset($_POST['is_active']) ? 1 : 0;
    $ruleID     = (int)($_POST['ruleID'] ?? 0);
    $userID     = (int)($_SESSION['userID'] ?? 0);

    // Neue ID nur bei Add
    if ($ruleID === 0) {

        $resMax = $_database->query("
            SELECT MAX(
                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(content_key,'_',2),'_',-1) AS UNSIGNED)
            ) AS max_id
            FROM plugins_rules
            WHERE content_key LIKE 'rule_%_title'
        ");

        $rowMax = $resMax->fetch_assoc();
        $ruleID = ((int)($rowMax['max_id'] ?? 0)) + 1;
    }

    $title = $_database->real_escape_string(trim($_POST['title_lang'][$activeLang] ?? ''));
    $text  = $_database->real_escape_string(trim($_POST['content'][$activeLang] ?? ''));
    $lang  = $_database->real_escape_string($activeLang);

    // TITLE
    $_database->query("
        INSERT INTO plugins_rules 
        (content_key, language, content, userID, is_active, updated_at)
        VALUES 
        ('rule_{$ruleID}_title', '$lang', '$title', $userID, $is_active, NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            userID = VALUES(userID),
            is_active = VALUES(is_active),
            updated_at = NOW()
    ");

    // TEXT
    $_database->query("
        INSERT INTO plugins_rules 
        (content_key, language, content, userID, is_active, updated_at)
        VALUES 
        ('rule_{$ruleID}_text', '$lang', '$text', $userID, $is_active, NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            userID = VALUES(userID),
            updated_at = NOW()
    ");

    nx_redirect(
        'admincenter.php?site=admin_rules',
        'success',
        'alert_saved',
        false
    );
}

if (isset($_GET['action']) && ($_GET['action'] == "add" || $_GET['action'] == "edit")) {


$ruleID = (int)($_GET['edit'] ?? $_POST['ruleID'] ?? 0);

$content    = [];
$titles     = [];
$lastUpdate = [];
$editrule   = ['is_active' => 0];

if ($ruleID > 0) {

    // Inhalte laden
    $res_lang = $_database->query("
        SELECT content_key, language, content, updated_at, is_active
        FROM plugins_rules
        WHERE content_key LIKE 'rule_{$ruleID}_%'
    ");

    while ($row = $res_lang->fetch_assoc()) {

        $lang = $row['language'];

        if (str_contains($row['content_key'], '_text')) {
            $content[$lang]    = $row['content'];
            $lastUpdate[$lang] = $row['updated_at'];
        }

        if (str_contains($row['content_key'], '_title')) {
            $titles[$lang] = $row['content'];
            $editrule['is_active'] = (int)$row['is_active'];
        }
    }
}

$CAPCLASS->createTransaction();
$hash = $CAPCLASS->getHash();
?>
<style>
.cursor-move {
    cursor: grab;
}
.sortable-ghost {
    opacity: 0.5;
}
</style>
<script>
    const lastUpdateByLang = <?= json_encode($lastUpdate, JSON_UNESCAPED_UNICODE) ?>;
</script>
<form method="post" action="admincenter.php?site=admin_rules&action=edit&edit=<?= $ruleID ?>">
    <div class="nx-lang-editor">

    <input type="hidden" name="captcha_hash" value="<?= $hash ?>">
    <input type="hidden" name="active_lang" id="active_lang" value="<?= $currentLang ?>">
    <input type="hidden" name="ruleID" value="<?= (int)($_GET['edit'] ?? 0) ?>">

    <div class="card shadow-sm border-0 mb-4 mt-3">

        <!-- CARD HEADER -->
        <div class="card-header d-flex align-items-center">
            <div class="card-title mb-0">
                <i class="bi bi-house-gear"></i> <?= $languageService->get('rules_title') ?>
            </div>

            <div class="ms-auto d-flex align-items-center gap-4">
                <div class="btn-group" id="lang-switch">
                    <?php foreach ($languages as $iso => $label): ?>
                        <button type="button"
                                class="btn <?= $iso === $currentLang ? 'btn-primary' : 'btn-secondary' ?>"
                                data-lang="<?= $iso ?>">
                            <?= strtoupper($iso) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="text-end ps-3">
                    <div class="text-muted small" id="last-update-box">
                        <?php if (!empty($lastUpdate[$currentLang])): ?>
                            <i class="bi bi-clock-history me-1"></i>
                            <span id="last-update-text">
                                <?= date('d.m.Y H:i', strtotime($lastUpdate[$currentLang])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD BODY -->
        <div class="card-body">

            <div class="row mb-4">
                <div class="col-md-8">
                    <label class="form-label fw-bold"><?= $languageService->get('title_head') ?></label>

                    <input type="text"
       id="nx-title-input"
       name="title_lang[<?= $currentLang ?>]"
       class="form-control mb-2"
       value="<?= htmlspecialchars($titles[$currentLang] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <?php foreach ($languages as $iso => $label): ?>
                        <input type="hidden"
                               name="title_lang[<?= $iso ?>]"
                               id="title_<?= $iso ?>"
                               value="<?= htmlspecialchars($titles[$iso] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php endforeach; ?>
                </div>
            </div>

            <textarea class="form-control"
          id="nx-editor-main"
          name="content[<?= $currentLang ?>]"
          data-editor="nx_editor"
          rows="15"><?= htmlspecialchars($content[$currentLang] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

            <?php foreach ($languages as $iso => $label): ?>
                <input type="hidden"
                       name="content[<?= $iso ?>]"
                       id="content_<?= $iso ?>"
                       value="<?= htmlspecialchars($content[$iso] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <?php endforeach; ?>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input"
                       type="checkbox"
                       name="is_active"
                       <?= !empty($editrule['is_active']) ? 'checked' : '' ?>>
                <label class="form-check-label">
                    <?= $languageService->get('rules_active') ?>
                </label>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save"></i> <?= $languageService->get('save') ?>
                </button>
            </div>

        </div>
    </div>

    </div>
</form>

<?php }else{
/* =========================================
   2. STANDARD LISTE
========================================= */


$currentLang = strtolower($languageService->detectLanguage());

$resrules = safe_query("
    SELECT 
        SUBSTRING_INDEX(SUBSTRING_INDEX(content_key, '_', 2), '_', -1) AS rule_id,
        MAX(sort_order) AS sort_order,
        MAX(is_active) AS is_active,
        MAX(updated_at) AS updated_at
    FROM plugins_rules
    WHERE content_key LIKE 'rule_%_title'
    GROUP BY rule_id
    ORDER BY sort_order ASC, updated_at DESC
");

$CAPCLASS = new \nexpell\Captcha;
$CAPCLASS->createTransaction();
$hash = $CAPCLASS->getHash();
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

<div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="card-title">
            <i class="bi bi-card-text"></i>
            <?= $languageService->get('rules_admin_title') ?>
        </div>

        <a href="admincenter.php?site=admin_rules&action=add"
           class="btn btn-secondary">
            <?= $languageService->get('rules_add') ?>
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th><?= $languageService->get('rules_title') ?></th>
                        <th><?= $languageService->get('rules_date') ?></th>
                        <th><?= $languageService->get('rules_active') ?></th>
                        <th class="text-end"><?= $languageService->get('actions') ?></th>
                    </tr>
                </thead>

                <tbody id="rules-sortable">

                <?php if (mysqli_num_rows($resrules) > 0): ?>
                    <?php while ($rule = mysqli_fetch_assoc($resrules)):

                        $ruleID = (int)$rule['rule_id'];

                        $keyTitle = "rule_" . $ruleID . "_title";

                        $resTitle = safe_query("
                            SELECT content
                            FROM plugins_rules
                            WHERE content_key = '$keyTitle'
                              AND language = '$currentLang'
                            LIMIT 1
                        ");

                        $rowTitle = mysqli_fetch_assoc($resTitle);

                        $title = !empty($rowTitle['content'])
                            ? htmlspecialchars($rowTitle['content'])
                            : "Rule " . $ruleID;

                        $deleteUrl = 'admincenter.php?site=admin_rules'
                            . '&delete=true'
                            . '&id=' . $ruleID
                            . '&captcha_hash=' . rawurlencode($hash);

                        $deleteUrlAttr = htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8');
                    ?>

                    <tr data-id="<?= $ruleID ?>">

                        <!-- DRAG HANDLE -->
                        <td class="text-muted cursor-move">
                            <i class="bi bi-list"></i>
                        </td>

                        <!-- TITLE -->
                        <td>
                            <strong><?= $title ?></strong>
                        </td>

                        <!-- DATE -->
                        <td>
                            <?= date('d.m.Y H:i', strtotime($rule['updated_at'])) ?>
                        </td>

                        <!-- ACTIVE -->
                        <td>
                            <span class="badge <?= $rule['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $languageService->get($rule['is_active'] ? 'yes' : 'no') ?>
                            </span>
                        </td>

                        <!-- ACTIONS -->
                        <td class="text-end">

                            <a href="admincenter.php?site=admin_rules&action=edit&edit=<?= $ruleID ?>"
                               class="btn btn-primary">
                                <i class="bi bi-pencil-square"></i>
                                <?= $languageService->get('edit') ?>
                            </a>

                            <button type="button"
                                    class="btn btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#confirmDeleteModal"
                                    data-delete-url="<?= $deleteUrlAttr ?>">
                                <i class="bi bi-trash3"></i>
                                <?= $languageService->get('delete') ?>
                            </button>

                        </td>

                    </tr>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <?= $languageService->get('no_rules_found') ?>
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>
</div>        
<?php } ?>
<script>
        document.addEventListener('DOMContentLoaded', () => {

            const tbody = document.getElementById('rules-sortable');

            if (!tbody || typeof Sortable === 'undefined') {
                return;
            }

            Sortable.create(tbody, {
                handle: '.cursor-move',
                animation: 150,
                onEnd: () => {

                    const order = [];
                    tbody.querySelectorAll('tr').forEach((tr, i) => {
                        order.push({
                            id: tr.dataset.id,
                            sort_order: i + 1
                        });
                    });

                    fetch('admincenter.php?site=admin_rules&action=save_sort', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(order)
                    });
                }
            });
        });
        </script>

