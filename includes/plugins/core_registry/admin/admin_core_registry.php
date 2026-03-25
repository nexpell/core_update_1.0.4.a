<?php
declare(strict_types=1);

use nexpell\LanguageService;
use nexpell\AccessControl;

global $_database, $languageService;

// SECURITY
#AccessControl::checkAdminAccess('core_registry');

// CONFIG
$coreJsonUrl = 'https://www.update.nexpell.de/updates/update_info_v2.json';
$apiEndpoint = 'https://www.update.nexpell.de/api/core_registry.php';

// LOAD CORE JSON
$raw     = json_decode(@file_get_contents($coreJsonUrl), true) ?: [];
$updates = $raw['updates'] ?? [];

#$raw = json_decode(@file_get_contents($coreJsonUrl), true) ?: [];
#$updates = is_array($raw) ? $raw : [];

// ACTION
$action = $_GET['action'] ?? '';

// DELETE
if ($action === 'delete' && !empty($_GET['version'])) {

    $version = $_GET['version'];

    $updates = array_values(array_filter(
        $updates,
        fn($u) => ($u['version'] ?? '') !== $version
    ));

    pushCore($entry, $originalVersion, $apiEndpoint);

    nx_audit_delete('admin_core_registry', (string)$version, (string)$version, 'admincenter.php?site=admin_core_registry');
    nx_redirect('admincenter.php?site=admin_core_registry', 'success', 'alert_deleted', false);
}

// SAVE (ADD / EDIT)
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $originalVersion = $_POST['original_version'] ?? null;

    $version  = trim($_POST['version']);
    $build    = max(1, (int)($_POST['build'] ?? 1));

    $channel = strtolower($_POST['channel'] ?? 'stable');
    $channel = in_array($channel, ['stable','beta','dev'], true)
        ? $channel
        : 'stable';

    $requiresNewUpdater = !empty($_POST['requires_new_updater']);

    /* VISIBLE */
    $mode = $_POST['visible_mode'] ?? 'ALL';
    $visibleFor = ($mode === 'CUSTOM')
        ? array_values(array_unique(array_filter(
            array_map(
                fn($m) => strtolower(trim($m)),
                preg_split('/\R/', $_POST['visible_for'] ?? '')
            )
        )))
        : [];

    /* DELETE FILES */
    $deleteFilesRaw = (string)($_POST['delete_files'] ?? '');
    $deleteFiles = array_values(array_unique(array_filter(
        array_map(
            static fn(string $l): string => '/' . ltrim(trim($l), '/'),
            preg_split('/\R/', $deleteFilesRaw)
        )
    )));

    $entry = [
        'version'              => $version,
        'build'                => $build,
        'channel'              => $channel,
        'requires_new_updater' => $requiresNewUpdater,
        'visible_for'          => $visibleFor,
        'zip_url'              => trim($_POST['zip_url']),
        'changelog'            => trim($_POST['changelog'] ?? ''),
        'notes'                => trim($_POST['notes'] ?? ''),
        'release_date'         => $_POST['release_date'] ?? date('Y-m-d'),
        'delete_files'         => $deleteFiles
    ];

    pushCore($entry, $originalVersion, $apiEndpoint);

    if ($originalVersion) nx_audit_update('admin_core_registry', (string)$originalVersion, true, (string)$version, 'admincenter.php?site=admin_core_registry');
    else nx_audit_create('admin_core_registry', (string)$version, (string)$version, 'admincenter.php?site=admin_core_registry');

    nx_redirect('admincenter.php?site=admin_core_registry', 'success', 'alert_saved', false);
}

// ADD / EDIT FORM
if ($action === 'add' || $action === 'edit') {

    $edit = null;

    if ($action === 'edit' && !empty($_GET['version'])) {
        foreach ($updates as $u) {
            if (($u['version'] ?? '') === $_GET['version']) {
                $edit = $u;
                break;
            }
        }
    }
    ?>
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <div class="card-title">
                <i class="bi bi-gear-wide-connected fs-5"></i> <?= $action === 'edit' ? 'Core-Update bearbeiten' : 'Neues Core-Update' ?>
                <small class="text-muted"><?= $action === 'edit' ? 'Bearbeiten' : 'Hinzufügen' ?></small>
            </div>
        </div>

        <div class="card-body">
            <form method="post" action="?site=admin_core_registry&action=save" class="needs-validation" novalidate>

            <input type="hidden" name="original_version"
                    value="<?= htmlspecialchars($edit['version'] ?? '') ?>">

            <!-- Basis -->
            <div class="row g-3">
                <div class="col-md-4">
                <label class="form-label">Version</label>
                <input class="form-control"
                        name="version"
                        value="<?= htmlspecialchars($edit['version'] ?? '') ?>"
                        placeholder="z. B. 1.0.2.2"
                        <?= $action === 'edit' ? 'readonly' : '' ?>
                        required>
                </div>

                <div class="col-md-4">
                <label class="form-label">Build</label>
                <input class="form-control"
                        type="number"
                        min="1"
                        name="build"
                        value="<?= (int)($edit['build'] ?? 1) ?>">
                <div class="form-text">
                    <strong>Build</strong> ist eine interne Revisionsnummer innerhalb derselben Version.
                    Erhöhen, wenn ein Update neu gebaut/korrigiert wird ohne Versionssprung.
                </div>
                </div>

                <div class="col-md-4">
                <label class="form-label">Update-Kanal</label>
                <select class="form-select" name="channel">
                    <?php foreach (['stable','beta','dev'] as $c): ?>
                    <option value="<?= $c ?>"
                        <?= (($edit['channel'] ?? 'stable') === $c) ? 'selected' : '' ?>>
                        <?= strtoupper($c) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>

            <hr class="my-4">

            <!-- Updater-Anforderung -->
            <div class="mb-3">
                <label class="form-label">Updater</label>

                <div class="form-check">
                    <input class="form-check-input"
                        type="checkbox"
                        id="requires_new_updater"
                        name="requires_new_updater"
                        value="1"
                        <?= !empty($edit['requires_new_updater']) ? 'checked' : '' ?>>

                    <label class="form-check-label" for="requires_new_updater">
                        Neuer Updater erforderlich
                    </label>
                </div>

                <div class="form-text">
                    Aktivieren, wenn dieses Update den bestehenden Updater ersetzt.
                    Der Update-Vorgang wird danach automatisch beendet.
                </div>
            </div>

            <!-- Quellen / Release -->
            <div class="row g-3">
                <div class="col-md-8">
                <label class="form-label">Update-ZIP-URL</label>
                <input class="form-control"
                        name="zip_url"
                        value="<?= htmlspecialchars($edit['zip_url'] ?? '') ?>"
                        placeholder="https://update.nexpell.de/updates/core_update_x.y.z.zip"
                        required>
                </div>

                <?php
                $releaseDate = $edit['release_date'] ?? null;
                if ($releaseDate && preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $releaseDate)) {
                    $dt = DateTime::createFromFormat('d.m.Y', $releaseDate);
                    $releaseDate = $dt ? $dt->format('Y-m-d') : date('Y-m-d');
                }
                $releaseDate = $releaseDate ?: date('Y-m-d');
                ?>

                <div class="col-md-4">
                <label class="form-label">Release-Datum</label>
                <input class="form-control"
                        type="date"
                        name="release_date"
                        value="<?= htmlspecialchars($releaseDate) ?>">
                </div>
            </div>

            <hr class="my-4">

            <!-- Texte -->
            <div class="row g-3">
                <div class="col-md-6">
                <label class="form-label">Changelog</label>
                <textarea class="form-control"
                            name="changelog"
                            rows="6"
                            placeholder="Änderungen für Benutzer"><?= htmlspecialchars($edit['changelog'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6">
                <label class="form-label">Interne Notizen</label>
                <textarea class="form-control"
                            name="notes"
                            rows="6"
                            placeholder="Kurzinfo für Update-Historie"><?= htmlspecialchars($edit['notes'] ?? '') ?></textarea>
                <div class="form-text">
                    Kurze interne Beschreibung für die Update-Historie.
                    Sichtbar für Administratoren, nicht für Endbenutzer.
                </div>
                </div>
            </div>

            <!-- Delete Files -->
            <div class="mb-3">
                <label class="form-label">Zu löschende Dateien</label>
                <textarea class="form-control"
                        name="delete_files"
                        rows="5"
                        placeholder="Eine Datei pro Zeile"><?= htmlspecialchars(
                implode("\n", $edit['delete_files'] ?? [])
                ) ?></textarea>
                <div class="form-text">
                Eine Datei <strong>pro Zeile</strong>. Pfade müssen mit <code>/</code> beginnen (relativ zum Root).<br>
                Beispiel: <code>/system/classes/OldUpdater.php</code>
                </div>
            </div>

            <!-- Sichtbarkeit -->
            <?php
                $visibleList = $edit['visible_for'] ?? [];
                $isCustom    = !empty($visibleList);
            ?>

            <div class="row g-3 align-items-start">
                <div class="col-md-4">
                <label class="form-label">Sichtbarkeit</label>
                <select class="form-select" name="visible_mode" id="visible_mode">
                    <option value="ALL" <?= !$isCustom ? 'selected' : '' ?>>ALL – für alle sichtbar</option>
                    <option value="CUSTOM" <?=  $isCustom ? 'selected' : '' ?>>CUSTOM – eingeschränkt</option>
                </select>
                </div>

                <div class="col-md-8" id="visible_for_box"
                    style="display: <?= $isCustom ? 'block' : 'none' ?>;">
                <label class="form-label">Sichtbar für (E-Mail-Adressen)</label>
                <textarea class="form-control"
                            name="visible_for"
                            rows="4"
                            placeholder="Eine E-Mail pro Zeile"><?= htmlspecialchars(
                    implode("\n", $visibleList)
                ) ?></textarea>
                <div class="form-text">
                    Eine E-Mail pro Zeile. Nur diese Adressen sehen das Update.
                    Leer lassen = für alle sichtbar.
                </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-start gap-2 mt-4 pt-3">
                <button class="btn btn-primary">Speichern</button>
            </div>
        </form>
        </div>
    </div>
    <script>
        (function () {
            const mode = document.getElementById('visible_mode');
            const box  = document.getElementById('visible_for_box');

            if (!mode || !box) return;

            function toggle() {
                box.style.display = (mode.value === 'CUSTOM') ? 'block' : 'none';
            }

            mode.addEventListener('change', toggle);
            toggle(); // Initialzustand
        })();
    </script>
    <?php

    return;
}

// LIST
?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-gear-wide-connected"></i> <span>Core Registry</span>
            <small class="text-muted">Übersicht</small>
        </div>
    </div>
<div class="card-body">

<a href="?site=admin_core_registry&action=add"
   class="btn btn-secondary mb-3">Neues Core-Update</a>

<table class="table align-middle">
<thead>
<tr>
    <th>Version</th>
    <th>Build</th>
    <th>Kanal</th>
    <th>Release</th>
    <th>Updater</th>
    <th>Sichtbar</th>
    <th>Aktion</th>
</tr>
</thead>
<tbody>

<?php foreach ($updates as $u): ?>
<tr>
<td class="fw-semibold"><?= htmlspecialchars($u['version']) ?></td>
<td><?= (int)($u['build'] ?? 1) ?></td>

<td>
<?php
$ch = $u['channel'] ?? 'stable';
$badge = match ($ch) {
    'beta' => 'warning',
    'dev'  => 'danger',
    default => 'success'
};
echo '<span class="badge bg-'.$badge.'">'.strtoupper($ch).'</span>';
?>
</td>

<td><?= htmlspecialchars($u['release_date'] ?? '-') ?></td>

<td>
<?= !empty($u['requires_new_updater'])
    ? '<span class="badge bg-danger">JA</span>'
    : '<span class="badge bg-secondary">NEIN</span>' ?>
</td>

<td>
<?php
$vis = $u['visible_for'] ?? [];
if (!empty($vis)) {
    echo '<span class="badge bg-info">CUSTOM</span><br>';
    echo '<small class="text-muted">'.htmlspecialchars(implode(', ', $vis)).'</small>';
} else {
    echo '<span class="badge bg-success">ALL</span>';
}
?>
</td>

<td>
<a class="btn btn-warning d-inline-flex align-items-center gap-1 w-auto"
   href="?site=admin_core_registry&action=edit&version=<?= urlencode($u['version']) ?>"><i class="bi bi-pencil-square"></i> Edit</a>

<a class="btn btn-danger d-inline-flex align-items-center gap-1 w-auto"
   onclick="return confirm('Core-Update wirklich löschen?')"
   href="?site=admin_core_registry&action=delete&version=<?= urlencode($u['version']) ?>"><i class="bi bi-trash3"></i> Löschen</a>
</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>
</div>

<?php

// PUSH CORE JSON
function pushCore(array $entry, ?string $originalVersion, string $endpoint): void
{
    $payload = [
        'updates' => [$entry],
        'original_version' => $originalVersion
    ];

    $json = json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    if ($json === false) {
        throw new RuntimeException('JSON encode failed');
    }

    $ch = curl_init($endpoint);

    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'X-Nexpell-Key: NEXPELL_SECRET_KEY_2025'
        ],
        CURLOPT_POSTFIELDS     => $json
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        throw new RuntimeException('cURL error: ' . curl_error($ch));
    }

    curl_close($ch);
}