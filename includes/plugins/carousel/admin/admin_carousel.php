<?php

// Session absichern
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;

// LanguageService initialisieren
global $_database,$languageService;

use nexpell\AccessControl;
// Den Admin-Zugriff für das Modul überprüfen
AccessControl::checkAdminAccess('carousel');

// Aktionen
$action = $_GET['action'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

$plugin_path = __DIR__ . '/../images/';

if (!is_dir($plugin_path)) {
    mkdir($plugin_path, 0777, true);
}

$types = ['sticky', 'parallax', 'agency', 'carousel'];
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4', 'video/webm'];

safe_query("CREATE TABLE IF NOT EXISTS plugins_carousel_lang (
  id INT(11) NOT NULL AUTO_INCREMENT,
  content_key VARCHAR(80) NOT NULL,
  language CHAR(2) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_content_lang (content_key, language),
  KEY idx_content_key (content_key),
  KEY idx_language (language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$languages = carousel_get_active_languages();
$currentLanguage = strtolower((string)$languageService->detectLanguage());
if (!isset($languages[$currentLanguage])) {
    $currentLanguage = array_key_first($languages);
}

function carousel_get_active_languages(): array
{
    $languages = [];
    $langRes = safe_query("SELECT iso_639_1, name_de FROM settings_languages WHERE active = 1 ORDER BY id ASC");
    if ($langRes && mysqli_num_rows($langRes) > 0) {
        while ($langRow = mysqli_fetch_assoc($langRes)) {
            $code = strtolower(trim((string)($langRow['iso_639_1'] ?? '')));
            if ($code !== '') {
                $languages[$code] = (string)($langRow['name_de'] ?? strtoupper($code));
            }
        }
    }
    if (empty($languages)) {
        $languages = ['de' => 'Deutsch', 'en' => 'English', 'it' => 'Italiano'];
    }
    return $languages;
}

function carousel_lang_key(int $slideID, string $field): string
{
    return 'carousel_' . $slideID . '_' . $field;
}

function carousel_get_text(int $slideID, string $field, string $lang, string $fallbackLang = 'de'): string
{
    $allowedFields = ['title', 'subtitle', 'description'];
    if (!in_array($field, $allowedFields, true)) {
        return '';
    }

    $key = escape(carousel_lang_key($slideID, $field));
    $langEsc = escape(strtolower($lang));
    $fallbackEsc = escape(strtolower($fallbackLang));

    $res = safe_query("SELECT content FROM plugins_carousel_lang WHERE content_key = '{$key}' AND language = '{$langEsc}' LIMIT 1");
    if ($res && ($row = mysqli_fetch_assoc($res)) && ($row['content'] ?? '') !== '') {
        return (string)$row['content'];
    }

    $resFallback = safe_query("SELECT content FROM plugins_carousel_lang WHERE content_key = '{$key}' AND language = '{$fallbackEsc}' LIMIT 1");
    if ($resFallback && ($rowFallback = mysqli_fetch_assoc($resFallback)) && ($rowFallback['content'] ?? '') !== '') {
        return (string)$rowFallback['content'];
    }

    // Legacy/mismatch fallback:
    // if content_key doesn't match current slide id, use first available key for this field.
    $resAny = safe_query("
        SELECT content
        FROM plugins_carousel_lang
        WHERE content_key REGEXP '^carousel_[0-9]+_{$field}$'
          AND language = '{$langEsc}'
        ORDER BY CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(content_key, '_', 2), '_', -1) AS UNSIGNED) ASC
        LIMIT 1
    ");
    if ($resAny && ($rowAny = mysqli_fetch_assoc($resAny)) && ($rowAny['content'] ?? '') !== '') {
        return (string)$rowAny['content'];
    }

    $resAnyFallback = safe_query("
        SELECT content
        FROM plugins_carousel_lang
        WHERE content_key REGEXP '^carousel_[0-9]+_{$field}$'
          AND language = '{$fallbackEsc}'
        ORDER BY CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(content_key, '_', 2), '_', -1) AS UNSIGNED) ASC
        LIMIT 1
    ");
    if ($resAnyFallback && ($rowAnyFallback = mysqli_fetch_assoc($resAnyFallback)) && ($rowAnyFallback['content'] ?? '') !== '') {
        return (string)$rowAnyFallback['content'];
    }

    return '';
}

function carousel_get_text_map(int $slideID, string $field, array $languages): array
{
    $map = [];
    foreach (array_keys($languages) as $langCode) {
        $map[$langCode] = carousel_get_text($slideID, $field, $langCode);
    }
    return $map;
}

function carousel_save_texts(int $slideID, string $field, array $values, array $languages): void
{
    global $_database;

    $key = mysqli_real_escape_string($_database, carousel_lang_key($slideID, $field));
    foreach (array_keys($languages) as $langCode) {
        $langEsc = mysqli_real_escape_string($_database, (string)$langCode);
        $content = mysqli_real_escape_string($_database, trim((string)($values[$langCode] ?? '')));
        safe_query("
            INSERT INTO plugins_carousel_lang (content_key, language, content, updated_at)
            VALUES ('{$key}', '{$langEsc}', '{$content}', NOW())
            ON DUPLICATE KEY UPDATE
              content = VALUES(content),
              updated_at = NOW()
        ");
    }
}

function carousel_delete_texts(int $slideID): void
{
    $prefix = escape('carousel_' . $slideID . '_');
    safe_query("DELETE FROM plugins_carousel_lang WHERE content_key LIKE '{$prefix}%'");
}

// Speichern 
if (isset($_POST['save_block'])) {
    $id = (int)($_POST['id'] ?? 0);
    $type = $_POST['type'];
    #$title = $_POST['title'];
    #$subtitle = $_POST['subtitle'];
    #$description = $_POST['description'];
    $link = escape($_POST['link']);
    $visible = isset($_POST['visible']) ? 1 : 0;
    $isEdit = $id > 0;
    $filename = '';
    $media_type = '';

    $nameArray1 = $_POST['title'] ?? [];
    $nameArray2 = $_POST['subtitle'] ?? [];
    $nameArray3 = $_POST['description'] ?? [];

    if (isset($_FILES['media_file']) && $_FILES['media_file']['size'] > 0) {
        $mime = mime_content_type($_FILES['media_file']['tmp_name']);
        if (in_array($mime, $allowedMimeTypes, true)) {
            $ext = pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('block_') . '.' . $ext;
            $media_type = str_starts_with($mime, 'video/') ? 'video' : 'image';

            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $plugin_path . $filename)) {
                if ($isEdit) {
                    $result = safe_query("SELECT media_file FROM plugins_carousel WHERE id = " . (int)$id);
                    if (mysqli_num_rows($result)) {
                        $old = mysqli_fetch_assoc($result);
                        if ($old && $old['media_file'] !== $filename && file_exists($plugin_path . $old['media_file'])) {
                            @unlink($plugin_path . $old['media_file']);
                        }
                    }
                }
            } else {
                nx_alert('danger', 'alert_upload_failed', false);
            }
        } else {
            nx_alert('danger', 'alert_upload_error', false);
        }
    }

    if ($isEdit) {
        safe_query("UPDATE plugins_carousel SET 
            type = '$type',
            link = '$link', visible = $visible
            " . ($filename ? ", media_file = '$filename', media_type = '$media_type'" : '') . "
            WHERE id = $id");
        carousel_save_texts($id, 'title', $nameArray1, $languages);
        carousel_save_texts($id, 'subtitle', $nameArray2, $languages);
        carousel_save_texts($id, 'description', $nameArray3, $languages);
        $auditTitle = carousel_get_text($id, 'title', $currentLanguage);
        nx_audit_update('admin_carousel', (string)$id, true, $auditTitle, 'admincenter.php?site=admin_carousel');
    } else {
        safe_query("INSERT INTO plugins_carousel 
            (type, link, visible, media_type, media_file)
            VALUES ('$type', '$link', $visible, '$media_type', '$filename')");
        $newId = (int)($_database->insert_id ?? 0);
        carousel_save_texts($newId, 'title', $nameArray1, $languages);
        carousel_save_texts($newId, 'subtitle', $nameArray2, $languages);
        carousel_save_texts($newId, 'description', $nameArray3, $languages);
        $auditTitle = carousel_get_text($newId, 'title', $currentLanguage);
        nx_audit_create('admin_carousel', (string)$newId, $auditTitle, 'admincenter.php?site=admin_carousel');
    }

    nx_redirect('admincenter.php?site=admin_carousel', 'success', 'alert_saved', false);
}

// Löschen
if (isset($_GET['delete'])) {
    $delID = (int)$_GET['delete'];
    $block = mysqli_fetch_assoc(safe_query("SELECT * FROM plugins_carousel WHERE id = $delID"));

    if ($block) {
        $auditTitle = carousel_get_text($delID, 'title', $currentLanguage);
        $imageFilename = $block['media_file'] ?? '';
        if (!empty($imageFilename) && file_exists($plugin_path . $imageFilename)) {
            @unlink($plugin_path . $imageFilename);
        }

        safe_query("DELETE FROM plugins_carousel WHERE id = $delID");
        carousel_delete_texts($delID);

        nx_audit_delete('admin_carousel', (string)$delID, $auditTitle !== '' ? $auditTitle : (string)$delID, 'admincenter.php?site=admin_carousel');
        nx_redirect('admincenter.php?site=admin_carousel', 'success', 'alert_deleted', false);
    }

    nx_redirect('admincenter.php?site=admin_carousel', 'danger', 'alert_not_found', false);
}

// Formular: add/edit
if ($action === 'add' || $action === 'edit') {
    $edit = null;
    $editTitleMap = [];
    $editSubtitleMap = [];
    $editDescriptionMap = [];
    if ($action === 'edit' && isset($_GET['id'])) {
        $edit = mysqli_fetch_assoc(safe_query("SELECT * FROM plugins_carousel WHERE id = " . (int)$_GET['id']));
        if ($edit) {
            $editId = (int)$edit['id'];
            $editTitleMap = carousel_get_text_map($editId, 'title', $languages);
            $editSubtitleMap = carousel_get_text_map($editId, 'subtitle', $languages);
            $editDescriptionMap = carousel_get_text_map($editId, 'description', $languages);
        }
    }

    ?>
  <div class="card shadow-sm mt-4">
  <div class="card-header">
    <div class="card-title">
      <i class="bi bi-image"></i> <span> Carousel <?= $action ? $languageService->get('edit') : $languageService->get('add')?></span>
    </div>
  </div>

  <div class="card-body">
    <div class="row g-4">
      <!-- LEFT: Form -->
      <div class="col-lg-8">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">

          <div class="row g-3">
            <!-- Typ -->
            <div class="col-md-2">
              <label class="form-label"><?= $languageService->get('type') ?></label>
              <select name="type" class="form-select" required>
                <?php foreach ($types as $t): ?>
                  <option value="<?= $t ?>" <?= ($edit['type'] ?? '') === $t ? 'selected' : '' ?>>
                    <?= ucfirst($t) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text"><?= $languageService->get('info_type') ?></div>
            </div>

            <!-- Maintitle -->
            <div class="col-12">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="small text-muted"><?= $languageService->get('choose_language') ?: 'Sprache wählen' ?></div>
                <div class="btn-group btn-group-sm" id="langSwitcher" role="group" aria-label="Language Switcher">
                  <?php foreach ($languages as $code => $label): ?>
                    <button type="button"
                            class="btn <?= $code === $currentLanguage ? 'btn-primary active' : 'btn-secondary' ?> lang-switch-btn"
                            data-lang="<?= htmlspecialchars($code) ?>">
                        <?= strtoupper($code) ?>
                    </button>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="alert alert-info mb-0" role="alert">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <h5 class="mb-0"><?= $languageService->get('maintitle') ?></h5>
                </div>

                <?php foreach ($languages as $code => $label): ?>
                  <div class="mb-3 row lang-row" data-lang="<?= htmlspecialchars($code) ?>">
                    <label class="col-sm-2 col-form-label"><?= $label ?>:</label>
                    <div class="col-sm-10">
                      <textarea class="form-control lang-field" rows="2" name="title[<?= $code ?>]"><?= htmlspecialchars($editTitleMap[$code] ?? '') ?></textarea>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Subtitle -->
            <div class="col-12">
              <div class="alert alert-info mb-0" role="alert">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <h5 class="mb-0"><?= $languageService->get('subtitle') ?></h5>
                </div>

                <?php foreach ($languages as $code => $label): ?>
                  <div class="mb-3 row lang-row" data-lang="<?= htmlspecialchars($code) ?>">
                    <label class="col-sm-2 col-form-label"><?= $label ?>:</label>
                    <div class="col-sm-10">
                      <textarea class="form-control lang-field" rows="2" name="subtitle[<?= $code ?>]"><?= htmlspecialchars($editSubtitleMap[$code] ?? '') ?></textarea>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Description -->
            <div class="col-12">
              <div class="alert alert-info mb-0" role="alert">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <h5 class="mb-0"><?= $languageService->get('description') ?></h5>
                </div>

                <?php foreach ($languages as $code => $label): ?>
                  <div class="mb-3 row lang-row" data-lang="<?= htmlspecialchars($code) ?>">
                    <label class="col-sm-2 col-form-label"><?= $label ?>:</label>
                    <div class="col-sm-10">
                      <textarea class="form-control lang-field" rows="3" name="description[<?= $code ?>]"><?= htmlspecialchars($editDescriptionMap[$code] ?? '') ?></textarea>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Link + Upload (ohne Preview!) -->
            <div class="col-12">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Link</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                    <input class="form-control"
                           name="link"
                           value="<?= htmlspecialchars($edit['link'] ?? '') ?>"
                           placeholder="https://example.com">
                  </div>
                  <div class="form-text"><?= $languageService->get('info_link') ?></div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">
                    <?= !empty($edit['media_file']) ? $languageService->get('media_file_optional') : $languageService->get('media_file_upload') ?>
                  </label>
                  <input type="file"
                         name="media_file"
                         class="form-control"
                         accept="image/*,video/*"
                         <?= empty($edit['media_file']) ? 'required' : '' ?>>
                  <div class="form-text">
                    <?= !empty($edit['media_file']) ? $languageService->get('media_file_keep_current') : $languageService->get('media_file_allowed') ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Aktiv/Sichtbar -->
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check form-switch">
                <input class="form-check-input"
                       type="checkbox"
                       role="switch"
                       id="visibleSwitch"
                       name="visible"
                       value="1"
                       <?= ($edit['visible'] ?? 0) ? 'checked' : '' ?>>
                <label class="form-check-label" for="visibleSwitch"><?= $languageService->get('active') ?></label>
              </div>
            </div>

            <!-- Actions -->
            <div class="col-12 pt-2">
              <div class="d-flex gap-2 justify-content-start">
                <button class="btn btn-primary" name="save_block">
                  <?= $languageService->get('save') ?>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- RIGHT: Sticky Preview -->
      <div class="col-lg-4">
        <?php if (!empty($edit['media_file'])): ?>
          <?php
            $plugin_url = '../includes/plugins/carousel/images/';
            $mediaPath = $plugin_url . $edit['media_file'];
            $extension = strtolower(pathinfo($edit['media_file'], PATHINFO_EXTENSION));
          ?>

          <div class="position-sticky" style="top: 1rem;">
            <div>
              <h6 class="fw-semibold"><?= $languageService->get('media_file_current') ?></h6>
              <div class="text-muted small text-truncate" title="<?= htmlspecialchars($edit['media_file']) ?>">
                <span class="badge bg-secondary mb-2 mt-1"><?= htmlspecialchars($edit['media_file']) ?></span>
              </div>
            </div>

            <div>
              <div class="ratio ratio-16x9 border rounded-3 overflow-hidden">
                <?php if (in_array($extension, ['jpg','jpeg','png','gif','webp'])): ?>
                  <img src="<?= htmlspecialchars($mediaPath) ?>"
                       alt="Vorschau"
                       style="width:100%; height:100%; object-fit:cover;">
                <?php elseif (in_array($extension, ['mp4','webm','ogg'])): ?>
                  <video controls style="width:100%; height:100%; object-fit:cover;">
                    <source src="<?= htmlspecialchars($mediaPath) ?>" type="video/<?= $extension ?>">
                        <?= $languageService->get('info_video_cant_load') ?>
                  </video>
                <?php else: ?>
                  <div class="d-flex align-items-center justify-content-center text-muted small">
                    <?= $languageService->get('info_no_preview') ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="card border-0 bg-light">
            <div class="card-body text-muted small">
                <?= $languageService->get('info_preview_current_file') ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script>
  (function () {
    const buttons = document.querySelectorAll('.lang-switch-btn');
    const rows = document.querySelectorAll('.lang-row');
    if (!buttons.length || !rows.length) return;

    function setLang(langCode) {
      rows.forEach(function (row) {
        row.style.display = (row.getAttribute('data-lang') === langCode) ? '' : 'none';
      });
      buttons.forEach(function (btn) {
        const isActive = btn.getAttribute('data-lang') === langCode;
        btn.classList.toggle('active', isActive);
        btn.classList.toggle('btn-primary', isActive);
        btn.classList.toggle('btn-secondary', !isActive);
      });
    }

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        setLang(btn.getAttribute('data-lang'));
      });
    });

    const active = document.querySelector('.lang-switch-btn.active');
    setLang(active ? active.getAttribute('data-lang') : buttons[0].getAttribute('data-lang'));
  })();
</script>
<?php
} elseif ($action == "settings") {

    // Speichern?
    if (isset($_POST['save_settings'])) {
        $carousel_height = escape($_POST['carousel_height']);
        $parallax_height = escape($_POST['parallax_height']);
        $sticky_height = escape($_POST['sticky_height']);
        $agency_height = escape($_POST['agency_height']);

        safe_query("UPDATE plugins_carousel_settings SET 
            carousel_height = '$carousel_height',
            parallax_height = '$parallax_height',
            sticky_height = '$sticky_height',
            agency_height = '$agency_height'
        WHERE carouselID = 1");

        nx_audit_update('admin_carousel', null, true, null, 'admincenter.php?site=admin_carousel');
        nx_alert('success', 'alert_saved', false);
    }

    // Aktuelle Einstellungen laden
    $settings = mysqli_fetch_assoc(safe_query("SELECT * FROM plugins_carousel_settings WHERE carouselID = 1"));
    ?>
    <style>
    .vh-preview {
        height:320px;
        border:1px solid rgba(0,0,0,.12);
        border-radius:.75rem;
        overflow:hidden;
        position:relative;
        background:#f8f9fa;
    }

    .vh-topbar {
        height:28px;
        background:#e9ecef;
        display:flex;
        align-items:center;
        padding:0 .5rem;
        z-index:5;
        position:relative;
    }

    .vh-pill {
        font-size:.75rem;
        color:#495057;
        background:rgba(255,255,255,.85);
        border:1px solid rgba(0,0,0,.08);
        border-radius:999px;
        padding:.1rem .45rem;
    }

    .vh-header {
        position:absolute;
        top:28px;
        left:0;
        width:100%;
        height:0;
        background-size:cover;
        background-position:center;
        background-repeat:no-repeat;
        background-color:#dee2e6;
        transition:height .15s ease;
        z-index:2;
    }

    .vh-endline {
        position:absolute;
        left:0;
        right:0;
        height:1px;
        background:rgba(0,0,0,.25);
        top:28px;
        z-index:4;
        pointer-events:none;
    }

    .vh-content {
        position:absolute;
        top:28px;
        left:0;
        right:0;
        bottom:0;
        padding:12px 14px;
        z-index:1;
    }
    .vh-content-line {
        height:10px;
        background:rgba(0,0,0,.06);
        border-radius:6px;
        margin-top:10px;
    }

    .vh-bottomlabel {
        position:absolute;
        bottom:.5rem;
        left:.5rem;
        z-index:5;
    }
    </style>
    <form method="post">
        <div class="row g-4">
            <!-- LEFT: Inputs Card -->
            <div class="col-lg-7">
                <div class="card shadow-sm mt-3">
                    <div class="card-header py-2">
                        <div class="card-title">
                            <i class="bi bi-gear"></i> <span><?= $languageService->get('title_settings_height') ?></span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><?= $languageService->get('label_carousel_height') ?></label>
                                    <input class="form-control"
                                        name="carousel_height"
                                        value="<?= htmlspecialchars($settings['carousel_height']) ?>"
                                        placeholder="75vh">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><?= $languageService->get('label_parallax_height') ?></label>
                                    <input class="form-control"
                                        name="parallax_height"
                                        value="<?= htmlspecialchars($settings['parallax_height']) ?>"
                                        placeholder="60vh">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><?= $languageService->get('label_sticky_height') ?></label>
                                    <input class="form-control"
                                        name="sticky_height"
                                        value="<?= htmlspecialchars($settings['sticky_height']) ?>"
                                        placeholder="120px">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><?= $languageService->get('label_sticky_height') ?></label>
                                    <input class="form-control"
                                        name="agency_height"
                                        value="<?= htmlspecialchars($settings['agency_height']) ?>"
                                        placeholder="70vh">
                                </div>
                            </div>
                        </div>

                        <div class="text-muted small">
                            <?= $languageService->get('info_height') ?>
                        </div>
                        <button type="submit" name="save_settings" class="btn btn-primary mt-4"><?= $languageService->get('save') ?></button>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Live Preview Card -->
            <?php
                $previewImageUrl = '';
                $imgDirFs  = __DIR__ . '/../images/';
                $imgDirUrl = '../includes/plugins/carousel/images/';

                $allowed = ['jpg','jpeg','png','webp','gif'];

                if (is_dir($imgDirFs)) {
                    $files = array_values(array_filter(scandir($imgDirFs), function($f) use ($imgDirFs, $allowed) {
                        if ($f === '.' || $f === '..') return false;
                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                        if (!in_array($ext, $allowed, true)) return false;
                        return is_file($imgDirFs . $f);
                    }));

                    if (!empty($files)) {
                        $previewImageUrl = $imgDirUrl . $files[0];
                    }
                }
            ?>
            <div class="col-lg-5">
                <div class="card h-100 shadow-sm mt-3">
                    <div class="card-header py-2">
                        <div class="card-title">
                            <i class="bi bi-aspect-ratio"></i> <span><?= $languageService->get('title_live_preview') ?></span>
                        </div>
                        <div class="alert alert-secondary mb-3 mt-3 small">
                            <div class="fw-semibold mb-1"><?= $languageService->get('title_infobox') ?></div>
                            <div>
                                <?= $languageService->get('info_infobox') ?>
                            </div>
                            <div class="text-muted mt-1">
                                <?= $languageService->get('info_infobox_example') ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="vhPreview" class="vh-preview">
                        <!-- “Browser/Website” Topbar -->
                        <div class="vh-topbar">
                            <span class="vh-pill"><?= $languageService->get('label_sitestart') ?></span>
                        </div>

                        <!-- Header Bereich -->
                        <div
                            id="vhBar"
                            class="vh-header"
                            style="<?php if (!empty($previewImageUrl)) : ?>
                            background-image:url('<?= htmlspecialchars($previewImageUrl) ?>');
                            <?php endif; ?>"
                        ></div>

                        <!-- Header-End-Linie -->
                        <div id="vhEndLine" class="vh-endline" aria-hidden="true"></div>

                        <!-- “Content” unterhalb des Headers -->
                        <div class="vh-content">
                            <div class="vh-content-line"></div>
                            <div class="vh-content-line w-75"></div>
                            <div class="vh-content-line w-50"></div>
                        </div>

                        <div class="vh-bottomlabel">
                            <span class="vh-pill"><?= $languageService->get('label_siteend') ?></span>
                        </div>
                        </div>

                        <div class="mt-3 d-flex align-items-center gap-2">
                        <span class="badge bg-primary" id="vhType">—</span>
                        <span class="text-muted small"><?= $languageService->get('info_click_field') ?></span>
                        </div>

                        <?php if (empty($previewImageUrl)) : ?>
                        <div class="alert alert-light border small mt-3 mb-0">
                            <?= $languageService->get('info_no_pic_yet') ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
        function parseHeightToVhPercent(value, viewportPx) {
            if (!value) return null;
            const v = value.trim().toLowerCase();
            const m = v.match(/^(\d+(?:[.,]\d+)?)\s*(vh|px)?$/);
            if (!m) return null;

            const num = parseFloat(m[1].replace(',', '.'));
            const unit = m[2] || 'px';
            if (unit === 'vh') return num;
            if (unit === 'px') return (num / viewportPx) * 100;
            return null;
        }

        function clamp(n, min, max){ return Math.max(min, Math.min(max, n)); }

        const fields = ['carousel_height','parallax_height','sticky_height','agency_height'];
        const fieldLabels = {
            carousel_height: 'Carousel',
            parallax_height: 'Parallax',
            sticky_height: 'Sticky',
            agency_height: 'Agency'
        };

        let activeField = fields[0];

        function updatePreview() {
            const viewportPx = window.innerHeight || 900;

            const preview = document.getElementById('vhPreview');
            const bar = document.getElementById('vhBar');
            const line = document.getElementById('vhEndLine');
            const badge = document.getElementById('vhType');
            if (!preview || !bar || !line || !badge) return;

            const previewRect = preview.getBoundingClientRect();
            const topbarHeight = 28;
            const usablePx = previewRect.height - topbarHeight;

            const input = document.querySelector(`[name="${activeField}"]`);
            const raw = input ? input.value : '';

            badge.textContent = fieldLabels[activeField] || '—';

            const vhPct = parseHeightToVhPercent(raw, viewportPx);
            if (vhPct === null || Number.isNaN(vhPct)) {
            bar.style.height = '0px';
            line.style.top = topbarHeight + 'px';
            return;
            }

            const pct = clamp(vhPct, 0, 200);
            const pxInBox = (pct / 100) * usablePx;

            bar.style.height = pxInBox + 'px';
            line.style.top = (topbarHeight + pxInBox) + 'px';
        }

        document.addEventListener('focusin', (e) => {
            const name = e.target?.getAttribute?.('name');
            if (!fields.includes(name)) return;
            activeField = name;
            updatePreview();
        });

        document.addEventListener('input', (e) => {
            if (e.target?.getAttribute?.('name') === activeField) updatePreview();
        });

        window.addEventListener('resize', updatePreview);
        document.addEventListener('DOMContentLoaded', updatePreview);
        </script>
    <?php
}

// Listenansicht
else {
    $result = safe_query("SELECT * FROM plugins_carousel ORDER BY sort ASC, created_at DESC");
    ?>
<div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="card-title">
            <i class="bi bi-image"></i> <span><?= $languageService->get('title_header') ?></span>
            <small class="text-muted"><?= $languageService->get('overview') ?></small>
        </div>
        <div class="d-flex gap-2 mt-2 mb-2">
            <a href="admincenter.php?site=admin_carousel&action=add" class="btn btn-secondary me-1">
                <?= $languageService->get('add') ?>
            </a>
            <a href="admincenter.php?site=admin_carousel&action=settings" class="btn btn-secondary">
                <?= $languageService->get('settings') ?>
            </a>
        </div>
    </div>

    <style>
        .thumb {
            width: 160px;
            height: 90px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid rgba(0,0,0,.08);
        }
        .table thead th {
            font-size: .825rem;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: #6c757d;
            border-bottom: 1px solid rgba(0,0,0,.08);
            white-space: nowrap;
        }
        .truncate {
            max-width: 520px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:140px;"><?= $languageService->get('th_preview') ?></th>
                        <th style="width:160px;"><?= $languageService->get('type') ?></th>
                        <th><?= $languageService->get('name') ?></th>
                        <th style="width:140px;"><?= $languageService->get('th_visible') ?></th>
                        <th class="text-end pe-3" style="width:300px;"><?= $languageService->get('actions') ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $hasRows = false;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $hasRows = true;

                        $fileUrl = htmlspecialchars('../includes/plugins/carousel/images/' . $row['media_file']);
                        $extension = strtolower(pathinfo($row['media_file'], PATHINFO_EXTENSION));

                        $title = carousel_get_text((int)$row['id'], 'title', $currentLanguage);
                        ?>
                        <tr>
                            <td class="ps-3">
                                <?php if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="<?= $fileUrl ?>" class="thumb" alt="Vorschau">
                                <?php elseif (in_array($extension, ['mp4', 'webm', 'ogg'])): ?>
                                    <video class="thumb" muted playsinline>
                                        <source src="<?= $fileUrl ?>" type="video/<?= $extension ?>">
                                        <?= $languageService->get('info_video_not_found') ?>
                                    </video>
                                <?php else: ?>
                                    <span class="text-muted"><?= $languageService->get('info_datatype') ?></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php
                                // Typ-Badge Mapping
                                $type = strtolower($row['type']);
                                $typeClass = match ($type) {
                                    'parallax' => 'text-bg-warning',
                                    'agency'   => 'text-bg-primary',
                                    'carousel' => 'text-bg-info text-white',
                                    default    => 'text-bg-secondary',
                                };
                                ?>
                                <span class="badge <?= $typeClass ?>"><?= ucfirst($row['type']) ?></span>
                            </td>

                            <td>
                                <div class="fw-semibold truncate" title="<?= htmlspecialchars($title) ?>">
                                    <?= htmlspecialchars($title) ?>
                                </div>
                            </td>

                            <td>
                                <?php if (!empty($row['visible'])): ?>
                                    <span class="badge text-bg-success"><?= $languageService->get('yes') ?></span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary"><?= $languageService->get('no') ?></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="admincenter.php?site=admin_carousel&action=edit&id=<?= (int)$row['id'] ?>"
                                    class="btn btn-warning d-inline-flex align-items-center gap-1 w-auto">
                                    <i class="bi bi-pencil-square"></i> <?= $languageService->get('edit') ?>
                                </a>

                                <!-- Delete Modal -->
                                <button type="button"
                                        class="btn btn-danger d-inline-flex align-items-center gap-1 w-auto"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal"
                                        data-delete-url="admincenter.php?site=admin_carousel&delete=<?= (int)$row['id'] ?>">
                                    <i class="bi bi-trash3"></i> <?= $languageService->get('delete') ?>
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}





