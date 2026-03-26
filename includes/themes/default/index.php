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
$globalWidgetsByPosition = $widgetsByPosition;
$modulePath = BASE_PATH . '/includes/modules/' . $pageSlug . '.php';
$isModulePage = ($pageSlug !== 'index' && is_file($modulePath));
if ($pageSlug !== 'index') {
    $indexWidgetsByPosition = nxb_build_widgets_html('index');
    if ($isModulePage) {
        $globalWidgetsByPosition['navbar'] = $indexWidgetsByPosition['navbar'] ?? [];
        $globalWidgetsByPosition['footer'] = $indexWidgetsByPosition['footer'] ?? [];
    } else {
        if (empty($globalWidgetsByPosition['navbar']) && !empty($indexWidgetsByPosition['navbar'])) {
            $globalWidgetsByPosition['navbar'] = $indexWidgetsByPosition['navbar'];
        }
        if (empty($globalWidgetsByPosition['footer']) && !empty($indexWidgetsByPosition['footer'])) {
            $globalWidgetsByPosition['footer'] = $indexWidgetsByPosition['footer'];
        }
    }
}
$widgetsByPosition = $globalWidgetsByPosition;
$GLOBALS['nxb_widgets_by_position'] = $widgetsByPosition;

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
body.builder-active [data-nx-zone="navbar"] .builder-placeholder,
body.builder-active [data-nx-zone="footer"] .builder-placeholder {
  min-height: 3.25rem;
  padding: .75rem 1rem;
  font-size: 0.8rem;
}
body.builder-active .nx-live-zone:not(:has(.nx-live-item)) .builder-placeholder {
  min-height: 8rem;
  /*border: 1px solid rgba(226,232,240,.9);*/
  background: rgba(255,255,255,.6);
}
body.builder-active [data-nx-zone="navbar"]:not(:has(.nx-live-item)) .builder-placeholder,
body.builder-active [data-nx-zone="footer"]:not(:has(.nx-live-item)) .builder-placeholder {
  min-height: 3.25rem;
}
/* Zonen-Bezeichnungen (data-nx-zone) ausgeblendet – stören im Builder, werden intern nur für Speichern/Laden genutzt */
body.builder-active .nx-zone::before {
  display: none;
}
</style>
