<?php
/**********************************************************************
 * NEXPELL — ADMIN FORUM PERMISSIONS
 **********************************************************************/
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once BASE_PATH . '/system/config.inc.php';
require_once BASE_PATH . '/system/core/init.php';

global $_database;

if ($_database->error) {
    echo json_encode([
        'saved' => false,
        'error' => $_database->error
    ]);
    exit;
}

/* ==========================================================
   CSRF
========================================================== */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$CSRF = $_SESSION['csrf_token'];

/* ==========================================================
   PARAMETER
========================================================== */
$type      = $_GET['type'] ?? 'forum';   // forum | category | thread
$currentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* ==========================================================
   ROLLEN
========================================================== */
$groups = [];
$res = safe_query("
    SELECT roleID AS id, role_name AS label
    FROM user_roles
    WHERE is_active = 1
    ORDER BY roleID
");
while ($row = mysqli_fetch_assoc($res)) {
    $groups[] = [
        'id'    => (int)$row['id'],
        'label' => $row['label']
    ];
}

/* ==========================================================
   LISTEN
========================================================== */
$forums = $categories = $threads = [];

$res = safe_query("SELECT boardID AS id, title FROM plugins_forum_boards ORDER BY title");
while ($r = mysqli_fetch_assoc($res)) $forums[] = $r;

$res = safe_query("SELECT catID AS id, title FROM plugins_forum_categories ORDER BY position");
while ($r = mysqli_fetch_assoc($res)) $categories[] = $r;

$res = safe_query("SELECT threadID AS id, title FROM plugins_forum_threads ORDER BY updated_at DESC");
while ($r = mysqli_fetch_assoc($res)) $threads[] = $r;

/* ==========================================================
   TABELLE / ID-SPALTE
========================================================== */
switch ($type) {

    case 'forum':
        $table = 'plugins_forum_permissions_board';
        $idCol = 'boardID';
        $list  = $forums;
        break;

    case 'category':
        $table = 'plugins_forum_permissions_categories';
        $idCol = 'catID';
        $list  = $categories;
        break;

    case 'thread':
        $table = 'plugins_forum_permissions_threads';
        $idCol = 'threadID';
        $list  = $threads;
        break;

    default:
        $type  = 'forum';
        $table = 'plugins_forum_permissions_board';
        $idCol = 'boardID';
        $list  = $forums;
}

/* ==========================================================
   ACL LADEN  ✅ FIX: $idCol statt $field
========================================================== */
$acl = [];

if ($currentId > 0 && $table && $idCol) {
    $res = safe_query("
        SELECT *
        FROM {$table}
        WHERE {$idCol} = {$currentId}
    ");

    while ($row = mysqli_fetch_assoc($res)) {
        $acl[(int)$row['role_id']] = $row;
    }
}

/* ==========================================================
   USER / ROLE KONTEXT
========================================================== */
$userID = (int)($_SESSION['userID'] ?? 0);
$roleID = (int)($_SESSION['roleID'] ?? 0);

// Gast-Fallback
if ($userID === 0) {
    $roleID = 11;
}

/* ==========================================================
   ACL DEBUG STATUS
========================================================== */
$res = safe_query("SELECT forum_acl_debug FROM settings LIMIT 1");
$cfg = mysqli_fetch_assoc($res);
$ACL_DEBUG = ((int)($cfg['forum_acl_debug'] ?? 0) === 1);

// Debug Toggle (nur Admin)
if (isset($_GET['toggle_acl_debug']) && $roleID === 1) {
    safe_query("
        UPDATE settings
        SET forum_acl_debug = IF(forum_acl_debug = 1, 0, 1)
        LIMIT 1
    ");
    header("Location: admincenter.php?site=admin_forum_permissions");
    exit;
}

/* ==========================================================
   ROLE NAME
========================================================== */
$aclRoleName = 'unbekannt';
$res = safe_query("
    SELECT role_name
    FROM user_roles
    WHERE roleID = {$roleID}
    LIMIT 1
");
if ($r = mysqli_fetch_assoc($res)) {
    $aclRoleName = $r['role_name'];
}
?>


<div class="card">
<div class="card-header">
<i class="bi bi-journal-text"></i> Forum Rechteverwaltung
</div>
<div class="card-body">












<div class="accordion mb-4" id="forumAclHelp">

  <!-- =======================================================
       REITER 1: Hinweise zur Rechtevergabe
  ======================================================== -->
  <div class="accordion-item border-info">
    <h2 class="accordion-header" id="aclHelpHeading1">
      <button class="accordion-button collapsed bg-info text-white"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#aclHelpCollapse1"
              aria-expanded="false"
              aria-controls="aclHelpCollapse1">
        <i class="bi bi-info-circle me-2"></i>
        Hinweise zur Rechtevergabe
      </button>
    </h2>

    <div id="aclHelpCollapse1"
         class="accordion-collapse collapse"
         aria-labelledby="aclHelpHeading1"
         data-bs-parent="#forumAclHelp">

      <div class="accordion-body">

        <p>
          In diesem Bereich legst du fest, welche <strong>Benutzerrollen</strong> in welchen
          <strong>Foren, Kategorien oder einzelnen Threads</strong> welche Aktionen ausführen dürfen.
          Die Rechte werden rollenbasiert vergeben und beim Zugriff dynamisch ausgewertet.
        </p>

        <ul>
          <li>
            <strong>Sehen</strong> – steuert, ob der Bereich grundsätzlich sichtbar ist.
            Ist dieses Recht deaktiviert, erscheint das Forum, die Kategorie oder der Thread für diese Rolle nicht.
          </li>

          <li>
            <strong>Lesen</strong> – erlaubt das Öffnen von Kategorien, Themen und Beiträgen.
            Ohne Leserecht können Inhalte nicht angezeigt werden, auch wenn sie sichtbar sind.
          </li>

          <li>
            <strong>Neues Thema</strong> – ermöglicht das Erstellen neuer Threads innerhalb eines Forums oder einer Kategorie.
          </li>

          <li>
            <strong>Antworten</strong> – erlaubt das Antworten auf bestehende Themen.
            Dieses Recht greift nur, wenn der Thread nicht gesperrt ist oder die Rolle über Moderationsrechte verfügt.
          </li>

          <li>
            <strong>Bearbeiten</strong> – erlaubt das Bearbeiten <em>fremder Beiträge</em>.
            Eigene Beiträge dürfen unabhängig davon immer bearbeitet werden.
            Dieses Recht eignet sich für vertrauenswürdige Benutzer ohne vollständige Moderationsrechte.
          </li>

          <li>
            <strong>Löschen</strong> – erlaubt das Entfernen von fremden Beiträgen.
          </li>

          <li>
            <strong>Moderator</strong> – gewährt erweiterte Moderationsrechte, z. B.:
            <ul class="mb-0">
              <li>Themen sperren oder öffnen</li>
              <li>Beiträge bearbeiten, verschieben oder moderieren</li>
              <li>Antworten auch in gesperrten Threads</li>
              <li>Fremde Beiträge löschen</li>
            </ul>
          </li>
        </ul>

        <p class="text-muted small mb-0">
          <strong>Rechte-Hierarchie:</strong><br>
          Kategorie-Rechte bilden die Grundlage für alle enthaltenen Foren.<br>
          Foren-Rechte können Kategorie-Rechte überschreiben.<br>
          <strong>Thread-Rechte haben die höchste Priorität</strong> und gelten ausschließlich für den jeweiligen Thread.
        </p>

      </div>
    </div>
  </div>

  <!-- =======================================================
       REITER 2: Erklärung der Rechte – verständlich erklärt
  ======================================================== -->
  <div class="accordion-item border-success">
    <h2 class="accordion-header" id="aclHelpHeading2">
      <button class="accordion-button collapsed bg-success text-white"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#aclHelpCollapse2"
              aria-expanded="false"
              aria-controls="aclHelpCollapse2">
        <i class="bi bi-info-circle me-2"></i>
        Erklärung der Rechte – verständlich erklärt
      </button>
    </h2>

    <div id="aclHelpCollapse2"
         class="accordion-collapse collapse"
         aria-labelledby="aclHelpHeading2"
         data-bs-parent="#forumAclHelp">

      <div class="accordion-body">

        <p>
          Diese Übersicht zeigt, <strong>welche Aktionen im Forum sichtbar sind</strong>,
          abhängig von Rolle, Zustand und gesetzten Rechten.
        </p>

        <div class="table-responsive">
  <table class="table table-bordered align-middle text-center small">
    <thead class="table-light">
      <tr>
        <th class="text-start">Rolle / Zustand</th>
        <th>Sehen</th>
        <th>Lesen</th>
        <th>Neues Thema</th>
        <th>Zitat</th>
        <th>Bearbeiten</th>
        <th>Löschen</th>
      </tr>
    </thead>
    <tbody>

      <tr class="table-secondary">
        <td class="text-start">Kein Zugriff</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
      </tr>

      <tr>
        <td class="text-start">Nur sehen (gesperrter Bereich)</td>
        <td>✅</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
      </tr>

      <tr>
        <td class="text-start">Sehen &amp; lesen (nur anschauen)</td>
        <td>✅</td>
        <td>✅</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
      </tr>

      <tr>
        <td class="text-start">Antworten nicht erlaubt</td>
        <td>✅</td>
        <td>✅</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
      </tr>

      <tr>
        <td class="text-start">Antworten nicht erlaubt, eigener Beitrag</td>
        <td>✅</td>
        <td>✅</td>
        <td>❌</td>
        <td>❌</td>
        <td>✅</td>
        <td>❌</td>
      </tr>

      <tr class="table-success">
        <td class="text-start">Antworten erlaubt, eigener Beitrag</td>
        <td>✅</td>
        <td>✅</td>
        <td>❌</td>
        <td>✅</td>
        <td>✅</td>
        <td>✅</td>
      </tr>

      <tr>
        <td class="text-start">Neues Thema erlaubt</td>
        <td>✅</td>
        <td>✅</td>
        <td>✅</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
      </tr>

      <tr>
        <td class="text-start">Bearbeiten global erlaubt</td>
        <td>✅</td>
        <td>✅</td>
        <td>❌</td>
        <td>❌</td>
        <td>✅</td>
        <td>❌</td>
      </tr>

      <tr>
        <td class="text-start">Löschen global erlaubt</td>
        <td>✅</td>
        <td>✅</td>
        <td>❌</td>
        <td>❌</td>
        <td>❌</td>
        <td>✅</td>
      </tr>

      <tr>
        <td class="text-start">Bearbeiten + Löschen global</td>
        <td>✅</td>
        <td>✅</td>
        <td>❌</td>
        <td>❌</td>
        <td>✅</td>
        <td>✅</td>
      </tr>

      <tr class="table-warning fw-bold">
        <td class="text-start">Moderator</td>
        <td>✅</td>
        <td>✅</td>
        <td>✅</td>
        <td>✅</td>
        <td>✅</td>
        <td>✅</td>
      </tr>

    </tbody>
  </table>
</div>



        <hr>

        <h6 class="fw-bold">🧭 Wichtige Regeln</h6>
        <ul class="small">
          <li>
            <strong>Zitat</strong> erscheint nur, wenn Antworten erlaubt sind
          </li>
          <li>
            <strong>Eigene Beiträge</strong> können bearbeitet werden, wenn das erlaubt ist – auch ohne Antwortrecht
          </li>
          <li>
            <strong>Globale Bearbeiten/Löschen-Rechte</strong> gelten für <em>alle</em> Beiträge
          </li>
          <li>
            <strong>Moderatoren</strong> sehen immer alle Aktionen
          </li>
        </ul>

        <p class="text-muted small mb-0">
          Tipp: Wenn etwas fehlt, prüfe zuerst den Thread – dort gesetzte Regeln
          überschreiben alle anderen.
        </p>

      </div>
    </div>
  </div>

  <!-- =======================================================
     REITER 3: ACL-Debug – Wofür & wie verwenden
======================================================== -->
<div class="accordion-item border-warning">
  <h2 class="accordion-header" id="aclHelpHeading3">
    <button class="accordion-button collapsed bg-warning text-dark"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#aclHelpCollapse3"
            aria-expanded="false"
            aria-controls="aclHelpCollapse3">
      <i class="bi bi-bug-fill me-2"></i>
      ACL-Debug – Hilfe bei unerwartetem Verhalten
    </button>
  </h2>

  <div id="aclHelpCollapse3"
       class="accordion-collapse collapse"
       aria-labelledby="aclHelpHeading3"
       data-bs-parent="#forumAclHelp">

    <div class="accordion-body">

      <p>
        Das <strong>ACL-Debug</strong> hilft dir zu verstehen,
        <strong>warum bestimmte Aktionen im Forum erlaubt oder verboten sind</strong>,
        auch wenn es auf den ersten Blick unlogisch erscheint.
      </p>

      <hr>

      <h6 class="fw-bold">🔍 Wann brauchst du das ACL-Debug?</h6>
      <ul>
        <li>Ein Button fehlt plötzlich (Zitat, Bearbeiten, Löschen)</li>
        <li>Ein Benutzer darf etwas, obwohl er es eigentlich nicht sollte</li>
        <li>Eine Rolle reagiert anders als erwartet</li>
        <li>Nach einer Rechteänderung stimmt das Verhalten nicht</li>
      </ul>

      <hr>

      <h6 class="fw-bold">🧭 Was zeigt dir das Debug an?</h6>
      <p>
        Das Debug erklärt Schritt für Schritt,
        <strong>welche Regel tatsächlich gegriffen hat</strong>.
        Dabei wird immer geprüft:
      </p>

      <ol>
        <li><strong>Gibt es eine spezielle Regel für diesen Thread?</strong></li>
        <li><strong>Falls nicht: Gibt es eine Regel in der Kategorie?</strong></li>
        <li><strong>Falls nicht: Gilt eine Board-Regel?</strong></li>
        <li><strong>Falls gar nichts gesetzt ist: Standardverhalten</strong></li>
      </ol>

      <div class="alert alert-warning small">
        <strong>Wichtig:</strong><br>
        Sobald irgendwo eine Regel existiert, gilt:
        <br>
        <strong>Alles, was nicht ausdrücklich erlaubt ist, ist verboten.</strong>
      </div>

      <hr>

      <h6 class="fw-bold">🧪 So testest du Rechte korrekt</h6>
        <ol>
          <li>Erstelle oder nutze einen <strong>Test-Account</strong></li>
          <li>Weise diesem Account die <strong>zu prüfende Rolle</strong> zu</li>
          <li>Melde dich mit diesem Account im Forum an</li>
          <li>Öffne den gewünschten Thread</li>
          <li>Aktiviere das ACL-Debug</li>
        </ol>

        <div class="alert alert-warning small">
          <strong>Wichtig:</strong><br>
          Das Debug zeigt <strong>nicht Admin-Rechte</strong>,
          sondern exakt das,
          <strong>was dieser Benutzer mit dieser Rolle darf</strong>.
        </div>

      <h6 class="fw-bold">💡 Typische Aha-Momente</h6>
      <ul class="small">
        <li>
          „Antworten ist verboten, weil auf Kategorie-Ebene eine Regel existiert“
        </li>
        <li>
          „Der Thread überschreibt die Board-Rechte“
        </li>
        <li>
          „Der Benutzer sieht mehr, weil er Moderator ist“
        </li>
      </ul>

      <p class="text-muted small mb-0">
        Tipp: Das Debug ist kein Fehleranzeiger,
        sondern ein <strong>Erklärwerkzeug</strong>.
        Es zeigt dir nicht, <em>dass</em> etwas falsch ist –
        sondern <em>warum</em> es so entschieden wurde.
      </p>

    </div>
  </div>
</div>


</div>





<?php if ((int)$roleID === 1): ?>
<div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
    <div>
        <i class="bi bi-bug-fill me-1"></i>
        <strong>Forum ACL Debug</strong>
        <span class="text-muted">
            Status:
            <?= $ACL_DEBUG
                ? '<span class="text-success">aktiv</span>'
                : '<span class="text-danger">inaktiv</span>' ?>
        </span>
    </div>

    <a href="admincenter.php?site=admin_forum_permissions&toggle_acl_debug=1"
       class="btn btn-sm btn-outline-dark">
        <?= $ACL_DEBUG ? 'Debug ausschalten' : 'Debug einschalten' ?>
    </a>
</div>
<?php endif; ?>


<form method="get" action="admincenter.php" class="row g-2 mb-3">
<input type="hidden" name="site" value="admin_forum_permissions">

<div class="col-md-3">
<label class="form-label">Rechte-Ebene</label>
<select name="type" class="form-select" onchange="this.form.submit()">
<option value="forum"    <?= $type==='forum'?'selected':'' ?>>Forum</option>
<option value="category" <?= $type==='category'?'selected':'' ?>>Kategorie</option>
<option value="thread"   <?= $type==='thread'?'selected':'' ?>>Thread</option>
</select>
</div>

<div class="col-md-6">
<label class="form-label">Ziel auswählen</label>
<select name="id" class="form-select" onchange="this.form.submit()">
<option value="0">– auswählen –</option>
<?php foreach ($list as $i): ?>
<option value="<?= (int)$i['id'] ?>" <?= $currentId===(int)$i['id']?'selected':'' ?>>
<?= htmlspecialchars($i['title']) ?>
</option>
<?php endforeach; ?>
</select>
</div>
</form>

<?php if ($currentId > 0): ?>
<table class="table table-bordered">
<thead>
<tr>
<th>Gruppe</th>
<th>Sehen</th>
<th>Lesen</th>
<th>Neues Thema</th>
<th>Antworten</th>
<th>Bearbeiten</th>
<th>Löschen</th>
<th>Moderator</th>
</tr>
</thead>
<tbody>

<?php
$fields = [
    'view'   => 'can_view',
    'read'   => 'can_read',
    'post'   => 'can_post',
    'reply'  => 'can_reply',
    'edit'   => 'can_edit',
    'delete' => 'can_delete',
    'mod'    => 'is_mod'
];
?>

<?php foreach ($groups as $g):
$role = $g['id'];
$row  = $acl[$role] ?? [];
?>
<tr>
<td><?= htmlspecialchars($g['label']) ?></td>

<?php foreach ($fields as $ui => $db):

    $val = $row[$db] ?? null;

    // 🔑 ACL-Logik:
    // - 1   = erlaubt
    // - NULL = erben
    $state   = ((int)$val === 1) ? '1' : 'null';
    $checked = ($state === '1') ? 'checked' : '';

?>
<td class="text-center">
    <input
        type="checkbox"
        class="perm-toggle"
        data-state="<?= $state ?>"
        data-type="<?= $type ?>"
        data-id="<?= $currentId ?>"
        data-role="<?= $role ?>"
        data-field="<?= $ui ?>"
        <?= $checked ?>
    >
</td>
<?php endforeach; ?>

</tr>
<?php endforeach; ?>

</tbody>
</table>
<?php endif; ?>

</div>
</div>


<script>
const NX_CSRF = "<?= $CSRF ?>";

document.querySelectorAll('.perm-toggle').forEach(el => {

  el.addEventListener('click', e => {

    // Shift + Klick = NULL
    if (e.shiftKey) {
      e.preventDefault();           // ⬅ wichtig
      el.checked = false;
      el.indeterminate = true;
    } else {
      el.indeterminate = false;
    }

    const value =
      el.indeterminate ? null :
      el.checked ? 1 : null;

    fetch('admincenter.php?site=admin_forum_permissions_ajax', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        csrf: NX_CSRF,
        type: el.dataset.type,
        id: el.dataset.id,
        role_id: el.dataset.role,
        field: el.dataset.field,
        value: value
      })
    })
    .then(r => r.json())
    .then(d => {
      if (!d.saved) {
        alert(d.error || 'Speichern fehlgeschlagen');
      }
    });

  });

});


</script>
