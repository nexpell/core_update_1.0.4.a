<?php
declare(strict_types=1);

// Session absichern
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once BASE_PATH . '/system/core/init.php';
require_once BASE_PATH . '/system/core/builder_live.php';

use nexpell\AccessControl;
#AccessControl::checkAdminAccess('ac_plugin_widgets_setting');


// --- Builder-Modus aktiv? ---
$isBuilder = (isset($_GET['builder']) && $_GET['builder'] === '1');

if ($isBuilder) {
    // Die Methode ruft selbst exit() auf, wenn der Zugriff fehlt.
    // Wenn der Benutzer Rechte hat, läuft der Code einfach weiter.
    AccessControl::checkAdminAccess('ac_plugin_widgets_setting');
}

// Seite bestimmen
$pageSlug = $_GET['site'] ?? 'index';
$widgetsByPosition = nxb_build_widgets_html($pageSlug);

// Nur für Startseite: konfiguriertes Startseiten-Modul ausblenden (Layout nur über Content-Zone)
$isStartpageView = false;
if ($pageSlug === 'index' || $pageSlug === '') {
    $res = safe_query("SELECT startpage FROM `settings` LIMIT 1");
    $row = ($res && mysqli_num_rows($res)) ? mysqli_fetch_assoc($res) : null;
    $isStartpageView = ($row && isset($row['startpage']) && (string)$row['startpage'] === 'startpage');
}

// Builder-Modus aktiv?
$isBuilder = (isset($_GET['builder']) && $_GET['builder'] === '1');

// Header laden
require_once 'header.php';
?>

<?php if ($isBuilder): ?>
<!-- === Builder-Kennzeichnung (ohne Layout-Änderung) === -->
<script>
// === Zonen-Restriktions-Logik / Builder-Flag START ===
// Klasse für CSS-Markierungen sicher setzen (falls Header <body>-Klassen anders setzt)
(function(){
  const add = cls => { try { document.documentElement.classList.add(cls); } catch(e){} try { document.body && document.body.classList.add(cls); } catch(e){} };
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => add('builder-active'));
  } else {
    add('builder-active');
  }
})();
</script>
<!-- === Zonen-Restriktions-Logik / Builder-Flag ENDE === -->
<?php endif; ?>

<?php /*if (!empty($_SESSION['userID'])): ?>
<div class="card mt-3 border-warning">
    <div class="card-header bg-warning text-dark fw-bold">
        🔐 Deine Login- & Rollenrechte
    </div>
    <div class="card-body small">
        <ul class="mb-0">
            <li><strong>UserID:</strong> <?= (int)$_SESSION['userID'] ?></li>
            <li><strong>Username:</strong> <?= htmlspecialchars($_SESSION['username'] ?? '') ?></li>
            <li><strong>roleID (höchste):</strong> <?= $_SESSION['roleID'] ?? '–' ?></li>
            <li><strong>userrole (Text):</strong> <?= $_SESSION['userrole'] ?? '–' ?></li>

            <hr class="my-2">

            <li><strong>Alle Rollen (IDs):</strong>
                <?= !empty($_SESSION['roles']) ? implode(', ', $_SESSION['roles']) : 'keine' ?>
            </li>

            <li><strong>Alle Rollen (Namen):</strong>
                <?= !empty($_SESSION['role_names']) ? implode(', ', $_SESSION['role_names']) : 'keine' ?>
            </li>

            <hr class="my-2">

            <?php
            $flags = [
                'is_admin','is_coadmin','is_leader','is_coleader','is_squadleader',
                'is_warorg','is_moderator','is_editor','is_member','is_trial',
                'is_guest','is_registered','is_honor','is_streamer',
                'is_designer','is_technician'
            ];
            foreach ($flags as $flag):
                if (!empty($_SESSION[$flag])):
            ?>
                <li class="text-success">✅ <?= $flag ?></li>
            <?php endif; endforeach; ?>
        </ul>
    </div>
</div>
<?php endif;*/ ?>


  <!-- Eine Zone: nur Content (Blöcke hier ablegen, Reihenfolge frei); ohne umschließenden Container, damit Container-Fluid volle Breite nutzen kann -->
  <main class="flex-fill">
    <?php if (!$isStartpageView): ?>
    <div class="container">
      <?php echo get_mainContent(); ?>
    </div>
    <?php endif; ?>
    <?php if ($isBuilder || !empty($widgetsByPosition['content'])): ?>
    <div class="nx-live-zone nx-zone" data-nx-zone="content" style="margin:0;padding:0;border:none;">
      <?php if (!empty($widgetsByPosition['content'])): ?>
        <?php foreach ($widgetsByPosition['content'] as $w) echo $w; ?>
      <?php elseif ($isBuilder): ?>
        <div class="builder-placeholder">Hier Blöcke ablegen – Reihenfolge frei wählbar</div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </main>

  <?php
  if ($isBuilder) {
    nxb_inject_live_overlay_with_palette($pageSlug);
  }
  ?>
  <?php require_once 'footer.php'; ?>


<!-- === BUILDER STYLES (eine Zone) === -->
<style>
.builder-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 8rem;
  padding: 1.5rem;
  margin: 0;
  text-align: center;
  font-size: 0.875rem;
  color: #94a3b8;
  background: transparent;
  border: none;
}
body.builder-active .nx-live-zone:not(:has(.nx-live-item)) .builder-placeholder {
  min-height: 8rem;
  /*border: 1px solid rgba(226,232,240,.9);*/
  background: rgba(255,255,255,.6);
}
/* Zonen-Bezeichnungen (data-nx-zone) ausgeblendet – stören im Builder, werden intern nur für Speichern/Laden genutzt */
body.builder-active .nx-zone::before {
  display: none;
}
</style>
