<?php

declare(strict_types=1);

use nexpell\LanguageService;
use nexpell\AccessControl;

AccessControl::checkAdminAccess('ac_plugin_installer');
global $_database, $languageService;

// Admin-Rechte prüfen
AccessControl::checkAdminAccess('plugin_registry');

// CONFIG
$pluginJsonUrl = 'https://www.update.nexpell.de/plugins/plugins_v2.json';
$apiEndpoint  = 'https://www.update.nexpell.de/api/plugins_registry.php';

// LOAD JSON → AS ASSOCIATIVE ARRAY (modulname)
$raw = json_decode(@file_get_contents($pluginJsonUrl), true) ?: [];
$plugins = $raw['plugins'] ?? [];

#echo '<pre>';
#var_dump(count($plugins), $plugins[0] ?? 'KEIN PLUGIN');
#exit;
// ACTION
$action = $_GET['action'] ?? '';

function normalizeEditorContent(string $content): string
{
    // 1) Entferne doppelt escapte Newlines
    $content = str_replace(
        ['\\r\\n', '\\n', '\\r'],
        ["\n", "\n", "\n"],
        $content
    );

    // 2) Windows → Unix Newlines
    $content = str_replace("\r\n", "\n", $content);

    // 3) Falls CKEditor HTML liefert → nichts weiter tun
    if ($content !== strip_tags($content)) {
        return $content;
    }

    // 4) Plaintext → HTML (einmalig!)
    return nl2br($content);
}

// DELETE
if (
    $action === 'delete'
    && !empty($_GET['modulname'])
    && !empty($_GET['version'])
) {

    $plugin = [
        'modulname' => trim($_GET['modulname']),
        'version'   => trim($_GET['version']),
        '__delete'  => true
    ];

    pushPlugin($plugin, null, $apiEndpoint);

    nx_audit_delete('admin_plugin_registry', $plugin['modulname'].'@'.$plugin['version'], $plugin['modulname'].'@'.$plugin['version'], 'admincenter.php?site=admin_plugin_registry');
    nx_redirect('admincenter.php?site=admin_plugin_registry', 'success', 'alert_deleted', false);
}

// SAVE (ADD + EDIT)
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $desc_de = normalizeEditorContent($_POST['desc_de'] ?? '');
    $desc_en = normalizeEditorContent($_POST['desc_en'] ?? '');
    $desc_it = normalizeEditorContent($_POST['desc_it'] ?? '');

    $plugin = [
        'name'        => trim($_POST['name']),
        'modulname'   => trim($_POST['modulname']),
        'description' =>
            '[[lang:de]]' . $desc_de .
            '[[lang:en]]' . $desc_en .
            '[[lang:it]]' . $desc_it,

        'version'     => trim($_POST['version']),
        'core' => [
            'min' => trim($_POST['core_min']),
            'max' => ($_POST['core_max'] ?? '') ?: null
        ],
        'author'   => trim($_POST['author']),
        'url'      => trim($_POST['url']),
        'lang'     => trim($_POST['lang']),
        'image'    => trim($_POST['image']),
        'download' => trim($_POST['download'])
    ];

    // VISIBLE LOGIC
    $visibleRaw = strtoupper(trim($_POST['visible_for'] ?? 'ALL'));

    if (in_array($visibleRaw, ['ALL', 'HIDDEN', 'DISABLED'], true)) {

        $plugin['visible_for'] = $visibleRaw;

    } elseif ($visibleRaw === 'CUSTOM') {

        $plugin['visible_for'] = 'CUSTOM';

        $emails = preg_split('/[\n,]+/', $_POST['visible_emails'] ?? '');

        $plugin['visible_emails'] = array_values(array_unique(array_filter(array_map(
            static fn($m) => strtolower(trim($m)),
            $emails
        ))));

    } else {
        $plugin['visible_for'] = 'ALL';
    }

    pushPlugin($plugin, null, $apiEndpoint);

    nx_audit_create('admin_plugin_registry', $plugin['modulname'].'@'.$plugin['version'], $plugin['name'] ?? ($plugin['modulname'].'@'.$plugin['version']), 'admincenter.php?site=admin_plugin_registry');
    nx_redirect('admincenter.php?site=admin_plugin_registry', 'success', 'alert_saved', false);
}

// ADD / EDIT FORM
if ($action === 'add' || $action === 'edit') {

    $editPlugin = null;

    if ($action === 'edit'
        && !empty($_GET['modulname'])
        && !empty($_GET['version'])
    ) {
        foreach ($plugins as $pl) {
            if (
                $pl['modulname'] === $_GET['modulname'] &&
                $pl['version']   === $_GET['version']
            ) {
                $editPlugin = $pl;
                break;
            }
        }
    }

    /* description zerlegen */
    $desc = ['de'=>'','en'=>'','it'=>''];
    if (!empty($editPlugin['description'])) {
        preg_match_all(
            '/\[\[lang:(de|en|it)\]\](.*?)(?=\[\[lang:|$)/s',
            $editPlugin['description'],
            $m,
            PREG_SET_ORDER
        );
        foreach ($m as $row) {
            $desc[$row[1]] = trim($row[2]);
        }
    }

    ?>
    <style>
    .lang-flag {
    cursor: pointer;
    border: 2px solid transparent;
    border-radius: 6px;
    padding: 4px;
    transition: all .15s ease;
    }

    .lang-flag img {
        height: 24px;
        width: auto;
        display: block;
    }

    .lang-flag.active {
        border-color: #fe821d;
        background: rgba(253, 149, 13, 0.1);
    }

    .lang-flag input {
        display: none;
    }
    </style>
   <div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
        <i class="bi bi-plugin"></i> <span><?= $action === 'edit' ? 'Plugin bearbeiten' : 'Neues Plugin' ?></span>
            <small class="text-muted">
            <?= $action === 'edit' ? 'Bearbeiten' : 'Hinzufügen' ?>
            </small>
        </div>
    </div>

    <div class="card-body">
        <form method="post" action="?site=admin_plugin_registry&action=save" class="needs-validation" novalidate>

        <input type="hidden" name="original_version"
                value="<?= htmlspecialchars($editPlugin['version'] ?? '') ?>">

        <!-- Basisdaten -->
        <div class="row g-3">
            <div class="col-md-6">
            <label class="form-label">Modulname</label>
            <input class="form-control"
                    name="modulname"
                    value="<?= htmlspecialchars($editPlugin['modulname'] ?? '') ?>"
                    <?= $action === 'edit' ? 'readonly' : '' ?>
                    required>
            <div class="form-text">Einzigartiger Modulname (im Edit-Modus gesperrt).</div>
            </div>

            <div class="col-md-6">
            <label class="form-label">Name</label>
            <input class="form-control"
                    name="name"
                    value="<?= htmlspecialchars($editPlugin['name'] ?? '') ?>"
                    required>
            </div>

            <div class="col-md-4">
            <label class="form-label">Version</label>
            <input class="form-control"
                    name="version"
                    value="<?= htmlspecialchars($editPlugin['version'] ?? '') ?>"
                    required>
            </div>

            <div class="col-md-4">
            <label class="form-label">Core min</label>
            <input class="form-control"
                    name="core_min"
                    value="<?= htmlspecialchars($editPlugin['core']['min'] ?? '') ?>"
                    required>
            </div>

            <div class="col-md-4">
            <label class="form-label">Core max</label>
            <input class="form-control"
                    name="core_max"
                    value="<?= htmlspecialchars($editPlugin['core']['max'] ?? '') ?>">
            </div>
        </div>

        <!-- Beschreibung (Tabs) -->
        <label class="form-label mb-2">Beschreibung</label>

        <ul class="nav nav-tabs" id="descTabs" role="tablist">
            <li class="nav-item" role="presentation">
            <button class="nav-link active" id="desc-de-tab" data-bs-toggle="tab"
                    data-bs-target="#desc-de" type="button" role="tab">DE</button>
            </li>
            <li class="nav-item" role="presentation">
            <button class="nav-link" id="desc-en-tab" data-bs-toggle="tab"
                    data-bs-target="#desc-en" type="button" role="tab">EN</button>
            </li>
            <li class="nav-item" role="presentation">
            <button class="nav-link" id="desc-it-tab" data-bs-toggle="tab"
                    data-bs-target="#desc-it" type="button" role="tab">IT</button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 rounded-bottom p-3 mb-3" id="descTabsContent">
            <div class="tab-pane fade show active" id="desc-de" role="tabpanel">
            <textarea class="form-control" data-editor="nx_editor" name="desc_de" rows="6"><?= htmlspecialchars_decode($desc['de'], ENT_QUOTES) ?></textarea>
            </div>
            <div class="tab-pane fade" id="desc-en" role="tabpanel">
            <textarea class="form-control" data-editor="nx_editor" name="desc_en" rows="6"><?= htmlspecialchars_decode($desc['en'], ENT_QUOTES) ?></textarea>
            </div>
            <div class="tab-pane fade" id="desc-it" role="tabpanel">
            <textarea class="form-control" data-editor="nx_editor" name="desc_it" rows="6"><?= htmlspecialchars_decode($desc['it'], ENT_QUOTES) ?></textarea>
            </div>
        </div>

        <!-- Meta -->
        <div class="row g-3">
            <div class="col-md-6">
            <label class="form-label">Autor</label>
            <input class="form-control" name="author"
                    value="<?= htmlspecialchars($editPlugin['author'] ?? 'nexpell Team') ?>">
            </div>

            <div class="col-md-6">
            <label class="form-label">URL</label>
            <input class="form-control" name="url"
                    value="<?= htmlspecialchars($editPlugin['url'] ?? 'https://www.nexpell.de') ?>">
            </div>

            <div class="col-md-6">
            <label class="form-label">Sichtbarkeit</label>
            <select class="form-select" name="visible_for" id="visible_for">
                <?php foreach (['ALL','HIDDEN','DISABLED','CUSTOM'] as $v): ?>
                <option value="<?= $v ?>"
                    <?= (($editPlugin['visible_for'] ?? 'ALL') === $v) ? 'selected' : '' ?>>
                    <?= $v ?>
                </option>
                <?php endforeach; ?>
            </select>
            </div>

            <div class="col-md-6" id="visible_emails_box" style="display:none;">
            <label class="form-label">Sichtbar für E-Mails (eine pro Zeile)</label>
            <textarea class="form-control" name="visible_emails" rows="4"><?= htmlspecialchars(
                implode("\n", $editPlugin['visible_emails'] ?? [])
            ) ?></textarea>
            </div>
        </div>

        <!-- Sprachen -->
        <div class="mb-3">
            <label class="form-label">Sprachen</label>

            <div class="d-flex flex-wrap gap-2" id="lang-flags">
            <?php
                $selectedLangs = array_map('trim', explode(',', $editPlugin['lang'] ?? 'de,gb,it'));
                $langs = ['de' => 'Deutsch', 'gb' => 'English', 'it' => 'Italiano'];

                foreach ($langs as $code => $label):
                $checked = in_array($code, $selectedLangs, true);
            ?>
                <label class="lang-flag <?= $checked ? 'active' : '' ?>">
                <input type="checkbox" value="<?= $code ?>" <?= $checked ? 'checked' : '' ?>>
                <img class="rounded" src="images/flags/<?= $code ?>.svg"
                    alt="<?= $label ?>" title="<?= $label ?>">
                </label>
            <?php endforeach; ?>
            </div>
        </div>

        <input type="hidden" name="lang" id="lang"
                value="<?= htmlspecialchars($editPlugin['lang'] ?? 'de,gb,it') ?>">

        <!-- Assets -->
        <div class="row g-3">
            <div class="col-md-6">
            <label class="form-label">Bild</label>
            <input class="form-control" name="image"
                    value="<?= htmlspecialchars($editPlugin['image'] ?? '') ?>">
            </div>

            <div class="col-md-6">
            <label class="form-label">Download</label>
            <input class="form-control" name="download"
                    value="<?= htmlspecialchars($editPlugin['download'] ?? '') ?>">
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="d-flex justify-content-start gap-2 mt-4 pt-3">
            <button class="btn btn-primary">
            Speichern
            </button>
        </div>

    </form>
  </div>
</div>

<script>
(function(){
  const sel = document.getElementById('visible_for');
  const box = document.getElementById('visible_emails_box');
  if(!sel || !box) return;
  const t = () => box.style.display = (sel.value === 'CUSTOM') ? 'block' : 'none';
  sel.addEventListener('change', t);
  t();
})();
</script>

<script>
(function () {
  const container = document.getElementById('lang-flags');
  const hidden    = document.getElementById('lang');
  if(!container || !hidden) return;

  function update() {
    const selected = [];
    container.querySelectorAll('input:checked').forEach(cb => {
      selected.push(cb.value);
      cb.closest('.lang-flag')?.classList.add('active');
    });
    container.querySelectorAll('input:not(:checked)').forEach(cb => {
      cb.closest('.lang-flag')?.classList.remove('active');
    });
    hidden.value = selected.join(',');
  }

  container.addEventListener('change', update);
  update();
})();
</script>
    <?php
    return;
}

// LIST
if (!empty($plugins)) {
    usort($plugins, fn($a, $b) =>
        strcasecmp($a['modulname'] ?? '', $b['modulname'] ?? '')
    );
}
?>
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-plugin"></i> <span>Plugin Registry</span>
            <small class="text-muted">Übersicht</small>
        </div>
    </div>
<div class="card-body">

<a href="?site=admin_plugin_registry&action=add"
   class="btn btn-secondary mb-3">Neues Plugin</a>

<table class="table">
<thead>
<tr>
    <th>Name</th>
    <th>Modul</th>
    <th>Version</th>
    <th>Core</th>
    <th>Visible</th>
    <th>Aktion</th>
</tr>
</thead>
<tbody>

<?php foreach ($plugins as $p): ?>
<tr>
<td><?= htmlspecialchars($p['name']) ?></td>
<td><?= htmlspecialchars($p['modulname']) ?></td>
<td><?= htmlspecialchars($p['version']) ?></td>
<td><?= htmlspecialchars($p['core']['min'] ?? '-') ?> → <?= htmlspecialchars($p['core']['max'] ?? '∞') ?></td>
<td>
<?php
$visibleRaw = $p['visible_for'] ?? 'ALL';

/* visible_for sicher normalisieren */
if (is_string($visibleRaw)) {
    $vf = strtoupper($visibleRaw);
} elseif (is_array($visibleRaw)) {
    $vf = 'CUSTOM';
} else {
    $vf = 'ALL';
}

echo htmlspecialchars($vf);

/* CUSTOM → E-Mails anzeigen */
if ($vf === 'CUSTOM') {

    $emails = [];

    if (!empty($p['visible_emails'])) {
        if (is_array($p['visible_emails'])) {
            $emails = $p['visible_emails'];
        } elseif (is_string($p['visible_emails'])) {
            // Fallback für kaputte/alte JSONs
            $emails = array_map('trim', explode(',', $p['visible_emails']));
        }
    }

    if (!empty($emails)) {
        echo '<br><small class="text-muted">'
           . htmlspecialchars(implode(', ', $emails))
           . '</small>';
    }
}
?>

</td>
<td>
<a class="btn btn-warning d-inline-flex align-items-center gap-1 w-auto"
   href="?site=admin_plugin_registry&action=edit&modulname=<?= urlencode($p['modulname']) ?>&version=<?= urlencode($p['version']) ?>">
   <i class="bi bi-pencil-square"></i> Bearbeiten
</a>
<a class="btn btn-danger d-inline-flex align-items-center gap-1 w-auto"
   onclick="return confirm('Wirklich löschen?')"
   href="?site=admin_plugin_registry&action=delete&modulname=<?= urlencode($p['modulname']) ?>&version=<?= urlencode($p['version']) ?>">
   <i class="bi bi-trash3"></i> Löschen
</a>
</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>
</div>
<?php

function pushPlugin(array $plugin, ?string $originalVersion, string $endpoint): void
{
    $payload = [
        'plugins' => [$plugin]
    ];

    if ($originalVersion !== null && $originalVersion !== '') {
        $payload['original_version'] = $originalVersion;
    }

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
        CURLOPT_POSTFIELDS     => $json,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_FOLLOWLOCATION => true
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        throw new RuntimeException(
            'Registry API error: ' . curl_error($ch)
        );
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300) {
        throw new RuntimeException(
            'Registry API HTTP ' . $httpCode . ': ' . $response
        );
    }
}
