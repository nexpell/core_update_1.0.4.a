<?php
declare(strict_types=1);

if (!defined('BASE_PATH')) {
  define('BASE_PATH', dirname(__DIR__, 2)); // zwei Ebenen hoch: /system/core → /
}
require_once BASE_PATH . '/system/config.inc.php';
require_once BASE_PATH . '/system/core/builder_widgets_core.php';
require_once BASE_PATH . '/system/core/builder_core.php';
require_once BASE_PATH . '/system/core/theme_options.php';

$page = $_GET['site'] ?? 'index';
$available = nx_load_available_widgets();
$assigned  = nx_load_widgets_for_page($page);

if (session_status() === PHP_SESSION_NONE) session_start();

use nexpell\SeoUrlHandler;

// DB
global $_database;
$_database = $_database ?? new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($_database->connect_errno) {
  throw new RuntimeException('DB connect error: ' . $_database->connect_error);
}
$_database->set_charset('utf8mb4');

// CSRF
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Konfig
const NXB_ZONE_SELECTORS = ['.nx-live-zone', '.nx-zone'];
// Builder-Zonen
const NXB_POSITIONS      = ['navbar', 'content', 'footer'];
const NXB_LEGACY_MERGE   = ['top', 'undertop', 'left', 'maintop', 'mainbottom', 'right', 'bottom', 'content'];

function nxb_is_builder(): bool { return isset($_GET['builder']) && $_GET['builder'] === '1'; }
function nxb_h(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// Daten laden
function nxb_db_fetch_palette(): array {
  global $_database;

  $out = [];
  $activePlugins = [];
  $humanizeLabel = static function (string $value): string {
    $value = trim($value);
    if ($value === '') {
      return 'Widgets';
    }
    $value = str_replace(['_', '-'], ' ', $value);
    $value = preg_replace('/\s+/', ' ', $value) ?? $value;
    return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
  };
  if ($resActive = $_database->query("
    SELECT sp.modulname
    FROM settings_plugins sp
    INNER JOIN settings_plugins_installed spi
      ON spi.modulname = sp.modulname
    WHERE sp.activate = 1
  ")) {
    while ($rowActive = $resActive->fetch_assoc()) {
      $slug = trim((string)($rowActive['modulname'] ?? ''));
      if ($slug !== '') {
        $activePlugins[strtolower($slug)] = true;
      }
    }
    $resActive->close();
  }
  // 1) Core-Widgets (immer verfügbar, mit Kategorie)
  if (function_exists('nxb_core_widgets_list')) {
    foreach (nxb_core_widgets_list() as $core) {
      $out[] = [
        'widget_key'    => (string)$core['widget_key'],
        'title'         => (string)($core['title'] ?? $core['widget_key']),
        'category'      => (string)($core['category'] ?? 'Inhalt'),
        'allowed_zones' => '',
        'is_core'       => true,
      ];
    }
  }

  // 2) Plugin-/DB-Widgets gesammelt unter "Widgets"
  if ($res = $_database->query("SELECT widget_key, COALESCE(NULLIF(title,''), widget_key) AS title, allowed_zones, plugin, modulname FROM settings_widgets ORDER BY plugin ASC, modulname ASC, title ASC")) {
    while ($row = $res->fetch_assoc()) {
      $pluginGroup = trim((string)($row['plugin'] ?? ''));
      $moduleGroup = trim((string)($row['modulname'] ?? ''));
      $pluginSlug = strtolower($pluginGroup !== '' ? $pluginGroup : $moduleGroup);
      if ($pluginSlug !== '' && empty($activePlugins[$pluginSlug])) {
        continue;
      }
      $pluginLabel = $pluginGroup !== '' ? $humanizeLabel($pluginGroup) : $humanizeLabel($moduleGroup);
      $out[] = [
        'widget_key'    => (string)$row['widget_key'],
        'title'         => (string)$row['title'],
        'category'      => 'Widgets',
        'plugin_group'  => $pluginLabel,
        'allowed_zones' => (string)($row['allowed_zones'] ?? ''),
        'is_core'       => false,
      ];
    }
    $res->close();
  }

  return $out;
}
function nxb_db_fetch_widgets(string $page): array {
  global $_database;
  $out = [];
  $hasWidgetAnywhere = static function (array $rows, array $widgetKeys): bool {
    foreach ($rows as $items) {
      if (!is_array($items)) {
        continue;
      }
      foreach ($items as $item) {
        $widgetKey = (string)($item['widget_key'] ?? '');
        if (in_array($widgetKey, $widgetKeys, true)) {
          return true;
        }
      }
    }
    return false;
  };
  $sql = "SELECT position, widget_key, instance_id, settings, title, modulname
          FROM settings_widgets_positions
          WHERE page = ?
          ORDER BY position ASC, sort_order ASC, id ASC";
  if (!$st = $_database->prepare($sql)) return $out;
  $st->bind_param('s', $page);
  if (!$st->execute()) { $st->close(); return $out; }
  $st->bind_result($pos, $wkey, $iid, $settings, $title, $modulname);
  while ($st->fetch()) {
    $cfg = [];
    if ($settings) { $tmp = json_decode($settings, true); if (is_array($tmp)) $cfg = $tmp; }
    $out[$pos][] = [
      'position'=>(string)$pos,
      'widget_key'=>(string)$wkey,
      'instance_id'=>(string)($iid ?? ''),
      'settings'=>$cfg,
      'title'=>(string)($title ?: $wkey),
      'modulname'=>(string)($modulname ?? '')
    ];
  }
  $st->close();

  $modulePath = BASE_PATH . '/includes/modules/' . $page . '.php';
  $isModulePage = ($page !== 'index' && is_file($modulePath));
  if ($isModulePage) {
    $indexOut = [];
    $indexSql = "SELECT position, widget_key, instance_id, settings, title, modulname
                 FROM settings_widgets_positions
                 WHERE page = 'index'
                 ORDER BY position ASC, sort_order ASC, id ASC";
    if ($res = $_database->query($indexSql)) {
      while ($row = $res->fetch_assoc()) {
        $cfg = [];
        if (!empty($row['settings'])) {
          $tmp = json_decode((string)$row['settings'], true);
          if (is_array($tmp)) {
            $cfg = $tmp;
          }
        }
        $pos = (string)($row['position'] ?? '');
        if ($pos === '') {
          continue;
        }
        $indexOut[$pos][] = [
          'position'    => $pos,
          'widget_key'  => (string)($row['widget_key'] ?? ''),
          'instance_id' => (string)($row['instance_id'] ?? ''),
          'settings'    => $cfg,
          'title'       => (string)(($row['title'] ?? '') ?: ($row['widget_key'] ?? '')),
          'modulname'   => (string)($row['modulname'] ?? ''),
        ];
      }
      $res->close();
    }
    $pageHasAnyNavbar = $hasWidgetAnywhere($out, ['core_nav_demo']);
    $pageHasAnyFooter = $hasWidgetAnywhere($out, ['core_footer_simple', 'core_footer_3col', 'core_footer_2col', 'core_footer_centered']);

    $indexNavbar = $indexOut['navbar'] ?? [];
    $indexFooter = $indexOut['footer'] ?? [];

    if (empty($indexNavbar)) {
      foreach (NXB_LEGACY_MERGE as $legacyPos) {
        foreach (($indexOut[$legacyPos] ?? []) as $legacyWidget) {
          if (($legacyWidget['widget_key'] ?? '') === 'core_nav_demo') {
            $indexNavbar[] = $legacyWidget;
          }
        }
      }
    }
    if (empty($indexFooter)) {
      foreach (NXB_LEGACY_MERGE as $legacyPos) {
        foreach (($indexOut[$legacyPos] ?? []) as $legacyWidget) {
          $widgetKey = (string)($legacyWidget['widget_key'] ?? '');
          if (in_array($widgetKey, ['core_footer_simple', 'core_footer_3col', 'core_footer_2col', 'core_footer_centered'], true)) {
            $indexFooter[] = $legacyWidget;
          }
        }
      }
    }

    if (!$pageHasAnyNavbar && !empty($indexNavbar)) {
      $out['navbar'] = $indexNavbar;
    }
    if (!$pageHasAnyFooter && !empty($indexFooter)) {
      $out['footer'] = $indexFooter;
    }
  }

  return $out;
}

// Globale Ablage aller Widgets (für verschachtelte Layout-Widgets wie Sektionen/Spalten)
$GLOBALS['__NX_ALL_WIDGET_ROWS'] = $GLOBALS['__NX_ALL_WIDGET_ROWS'] ?? [];

/* === Zonen-Restriktions-Logik START (serverseitige Map laden) ========= */
function nxb_db_fetch_allowed_zones_map(): array {
  global $_database;
  $map = [];
  if ($res = $_database->query("SELECT widget_key, allowed_zones FROM settings_widgets")) {
    while ($row = $res->fetch_assoc()) {
      $zones = array_filter(array_map('trim', explode(',', (string)($row['allowed_zones'] ?? ''))));
      // Vereinbarung: leeres Array = überall erlaubt
      $map[(string)$row['widget_key']] = array_values($zones);
    }
    $res->close();
  }
  return $map;
}
$__NX_ALLOWED_ZONES_MAP = nxb_db_fetch_allowed_zones_map();
/* === Zonen-Restriktions-Logik ENDE ==================================== */

// Serverseitiges Rendering (initial) über PluginManager
function nxb_render_widget_content(string $widget_key, string $instance_id, array $settings, string $title): string {
  if (!nxb_is_builder() && function_exists('nxb_render_frontend_widget_html')) {
    return nxb_render_frontend_widget_html($widget_key, $instance_id, $settings, $title);
  }

  // Core-Widgets direkt im Core rendern
  if (function_exists('nxb_render_core_widget_html') && strpos($widget_key, 'core_') === 0) {
    // Sektionen/Container brauchen stabile ID für Zonen-Namen (sec_<id>_c1), damit Speichern/Laden passt
    if (in_array($widget_key, ['core_section_full', 'core_section_two_col', 'core_section_three_col', 'core_container', 'core_row', 'core_col'], true)
        && (empty($settings['id']) || !is_string($settings['id']))) {
      $settings['id'] = $instance_id;
    }
    return nxb_render_core_widget_html($widget_key, $settings, $title);
  }

  // PluginManager aus init.php steht i.d.R. via Autoloader bereit:
  require_once BASE_PATH . '/system/core/init.php';
  $pm = new \nexpell\PluginManager($GLOBALS['_database']);
  return $pm->renderWidget($widget_key, [
    'instanceId' => $instance_id,
    'settings'   => $settings,
    'title'      => $title,
    'ctx'        => ['builder'=>nxb_is_builder(), 'widget_key'=>$widget_key, 'instance_id'=>$instance_id, 'title'=>$title]
  ]);
}

// Wrapper für Live-Controls. $extraClasses z. B. für Cols in einer Row (col-12 col-md-6).
// $extraData: z. B. ['data-nx-col-span' => '6'] für Builder-Grid-Layout.
function nxb_live_wrap(array $w, string $innerHtml, string $extraClasses = '', array $extraData = []): string {
  if (!nxb_is_builder()) return $innerHtml;
  $cls = 'nx-live-item' . ($extraClasses !== '' ? ' ' . trim($extraClasses) : '');
  $dataParts = [];
  foreach ($extraData as $k => $v) {
    if ($k !== '' && (is_string($v) || is_int($v))) {
      $dataParts[] = nxb_h($k) . '="' . nxb_h((string)$v) . '"';
    }
  }
  $dataStr = empty($dataParts) ? '' : ' ' . implode(' ', $dataParts);
  $attrs = sprintf(
    'class="%s" data-nx-iid="%s" data-nx-key="%s" data-nx-title="%s" data-nx-settings="%s"%s',
    nxb_h($cls),
    nxb_h($w['instance_id']),
    nxb_h($w['widget_key']),
    nxb_h($w['title']),
    nxb_h(json_encode($w['settings'], JSON_UNESCAPED_UNICODE)),
    $dataStr
  );
  return '<div '.$attrs.'>
    <div class="nx-drag-handle" title="Ziehen">⋮⋮</div>
    <div class="nx-live-controls btn-group btn-group-sm" role="group">
      <button type="button" class="btn btn-light btn-settings" title="Einstellungen"><i class="bi bi-sliders"></i></button>
      <button type="button" class="btn btn-outline-secondary btn-duplicate" title="Duplizieren"><i class="bi bi-files"></i></button>
      <button type="button" class="btn btn-outline-danger btn-remove" title="Entfernen"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="nx-live-content">'.$innerHtml.'</div>
  </div>';
}

function nxb_debug(string $msg): void {
  $GLOBALS['__NX_DEBUG'] = $GLOBALS['__NX_DEBUG'] ?? [];
  $GLOBALS['__NX_DEBUG'][] = [ date('H:i:s.v'), $msg ];
}

function nxb_output_debug_panel(): void {
  // Debug-Panel unsichtbar halten, aber window.nxDebug im JS verfügbar machen
  // (schreibt die Meldungen einfach in die Konsole).
  echo '<script>
    window.nxDebug = function(m) {
      try {
        console.log("[NXB]", m);
      } catch (e) {}
    };
  </script>';
}

function nxb_build_widgets_html(string $page): array {
  nxb_debug('nxb_build_widgets_html START page=' . $page);
  $rows = nxb_db_fetch_widgets($page);
  nxb_debug('nxb_db_fetch_widgets done, positions: ' . implode(',', array_keys($rows)));
  $GLOBALS['__NX_ALL_WIDGET_ROWS'] = $rows;
  $out  = [];
  foreach (NXB_POSITIONS as $pos) {
    $out[$pos] = [];
    if ($pos === 'content') {
      $items = [];
      foreach (NXB_LEGACY_MERGE as $leg) {
        foreach ($rows[$leg] ?? [] as $w) {
          $items[] = $w;
        }
      }
    } else {
      $items = $rows[$pos] ?? [];
    }
    nxb_debug('position ' . $pos . ' items=' . count($items));
    foreach ($items as $i => $w) {
      nxb_debug('render pos=' . $pos . ' i=' . $i . ' key=' . ($w['widget_key'] ?? ''));
      $content = nxb_render_widget_content($w['widget_key'], $w['instance_id'], $w['settings'], $w['title']);
      nxb_debug('render done ' . $w['widget_key']);
      $out[$pos][] = nxb_live_wrap($w, $content);
    }
  }
  nxb_debug('nxb_build_widgets_html END');
  return $out;
}

function nxb_inject_live_overlay_with_palette(string $page): void {
  if (!nxb_is_builder()) return;

  global $__NX_ALLOWED_ZONES_MAP;

  $rows = nxb_db_fetch_widgets($page);
  $hasWidgets = false;
  foreach (NXB_LEGACY_MERGE as $p) {
    if (!empty($rows[$p])) {
      $hasWidgets = true;
      break;
    }
  }
  $debugMode = !empty($_GET['debug']);
  if ($hasWidgets && !$debugMode) {
    nxb_debug('Builder wird trotz vorhandener Widgets geladen');
  /*
    $hrefNoBuilder = (string)(isset($_SERVER['REQUEST_URI']) ? preg_replace('#[?&]builder=1&?|([?&])builder=1#', '$1', $_SERVER['REQUEST_URI']) : '/');
    $hrefNoBuilder = trim($hrefNoBuilder, '&?');
    if ($hrefNoBuilder === '') $hrefNoBuilder = '/';
    $hrefDebug = (strpos($_SERVER['REQUEST_URI'] ?? '', '?') !== false ? '&' : '?') . 'debug=1';
    echo '<div class="nx-live-toolbar" style="position:fixed;top:0;left:0;right:0;background:#fff;z-index:2147480001;border-bottom:1px solid #e9ecef;padding:.5rem 1rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">';
    echo '<span class="badge bg-warning text-dark">Builder pausiert (Endlosschleife-Schutz)</span>';
    echo '<span class="small text-muted">Seite hat Widgets. <a href="'.htmlspecialchars(($_SERVER['REQUEST_URI'] ?? '').$hrefDebug, ENT_QUOTES, 'UTF-8').'"><strong>Debug einschalten (debug=1)</strong></a> – dann siehst du die Fehlerursache unten.</span>';
    echo '<a href="'.htmlspecialchars($hrefNoBuilder, ENT_QUOTES, 'UTF-8').'" class="btn btn-sm btn-outline-secondary">Seite ohne Builder</a>';
    echo '</div>';
    echo '<div style="height:3.5rem;"></div>';
    return;
    */
  }
  if ($hasWidgets && $debugMode) {
    nxb_debug('DEBUG-Modus: Builder-Script wird geladen trotz Widgets');
  }

  $lang    = $_SESSION['language'] ?? 'de';
  $csrf    = $_SESSION['csrf_token'];
  $palette = nxb_db_fetch_palette();

  echo '<style>
    .nx-live-zone, .nx-zone{ position:relative; margin:0; }
    body.builder-active [data-nx-zone="content"]{ box-sizing:border-box; }
    body.builder-active .nx-live-zone, body.builder-active .nx-zone{
      min-height:4rem;
      background:rgba(0, 0, 0, 0.15);
      border:1px dashed rgba(226,232,240,.7);
      border-radius:.25rem;
    }
    /* Layout-Container mit innenliegender Zone: mehr vertikaler „Drop-Space“ für große Widgets */
    body.builder-active .nx-container .nx-live-zone{
      padding-top:1.5rem;
      padding-bottom:1.5rem;
      min-height:5rem;
    }
    .nx-live-item{ position:relative; outline:none; padding:0; margin:0; background:transparent; border:none; border-radius:0; box-shadow:none; list-style:none; }
    .nx-live-item:hover{ box-shadow:none; outline:none; }
    .nx-live-item:hover::after{
      content:""; position:absolute; inset:2px; border:2px solid rgba(254, 130, 29, 1); border-radius:4px;
      pointer-events:none; box-sizing:border-box; z-index:1;
    }
    .nx-live-item.nx-live-active{ outline:none; background:rgba(13,110,253,.03); }
    .nx-live-item.nx-live-active::before{
      content:""; position:absolute; inset:2px; border:2px dashed rgba(254, 130, 29, 1); border-radius:4px;
      pointer-events:none; box-sizing:border-box; z-index:2;
    }
    .nx-live-item.nx-live-active:hover::after{
      border-width:0;
    }
    .nx-drag-handle{ display:none; }
    .nx-live-content{ cursor:grab; min-height:1.5rem; padding:0; outline:none; }
    /* WICHTIG: Kein overflow:hidden, damit Schatten/Overlays von Widgets (z. B. Navigation) im Builder sichtbar bleiben */
    body.builder-active .nx-live-content{ overflow:visible; max-width:100%; }
    body.builder-active .nx-live-content,
    body.builder-active .nx-live-content .text-body,
    body.builder-active .nx-live-content .text-body-secondary{
      color: var(--bs-body-color, #212529) !important;
    }
    body.builder-active .nx-live-content .text-muted,
    body.builder-active .nx-live-content .nx-inline-placeholder{
      color: var(--bs-body-color, #212529) !important;
      opacity: .68;
    }
    /* Bilder im Builder: Breite begrenzen, aber ansonsten wie im Frontend rendern */
    body.builder-active .nx-live-content img{ max-width:100%; height:auto; display:block; }
    /* Globale Navbar-Schatten im Builder deaktivieren – werden pro Widget gesteuert (z. B. core_nav_demo) */
    body.builder-active .nx-live-content nav.navbar{ box-shadow:none; }
    body.builder-active .nx-live-content .nx-header-image{ max-width:100%; }
    body.builder-active .nx-live-content .nx-header-image img{ height:100% !important; object-fit:cover !important; max-height:none !important; }
    body.builder-active .nx-header > img[data-nx-inline="image"]{ max-width:100% !important; max-height:200px !important; width:100% !important; height:200px !important; object-fit:cover !important; display:block !important; }
    body.builder-active .nx-live-content *:not([data-nx-inline]){ outline:none; }
    .nx-live-item.sortable-dragging .nx-live-content{ cursor:grabbing; }
    .nx-live-controls{ position:absolute; top:.5rem; right:.5rem; display:flex; gap:.25rem; z-index:2147480000; pointer-events:auto; opacity:0; transition:opacity .12s }
    .nx-live-item:hover .nx-live-controls{ opacity:1 }
    .nx-drop-hint{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none; opacity:.4; font-size:.9rem; color:#6c757d; text-transform:uppercase; letter-spacing:.04em; padding:.5rem .75rem; box-sizing:border-box; min-width:0; overflow:hidden; text-align:center; white-space:normal; overflow-wrap:break-word; word-break:break-word; }
    .nx-live-toolbar{ position:fixed; top:0; left:0; right:0; background:#fff; z-index:2147480001; border-bottom:1px solid #e9ecef; padding:.5rem 1rem; display:flex; gap:.75rem; align-items:center; color:#111827; }
    .nx-live-toolbar,
    .nx-live-toolbar *{
      color:inherit;
    }
    .nx-live-toolbar .text-muted,
    .nx-live-toolbar .small{
      color:#6b7280 !important;
    }
    .nx-live-toolbar code{
      color:#111827 !important;
      background:#f3f4f6;
      border:1px solid #e5e7eb;
      padding:.15rem .35rem;
      border-radius:.35rem;
    }
    .nx-live-toolbar .btn-outline-secondary{
      color:#374151 !important;
      border-color:#cbd5e1 !important;
      background:#ffffff !important;
    }
    .nx-live-toolbar .btn-outline-secondary:hover,
    .nx-live-toolbar .btn-outline-secondary:focus{
      color:#111827 !important;
      border-color:#94a3b8 !important;
      background:#f8fafc !important;
    }
    .nx-live-toolbar .badge.text-bg-light,
    .nx-live-toolbar .badge.bg-light{
      color:#111827 !important;
      background:#f3f4f6 !important;
      border:1px solid #e5e7eb;
    }
    body.builder-active{ background:var(--bs-body-bg, #f1f3f5) !important; }
    /* Live-Builder: volle Breite, kein Container dazwischen (aber spezielle Demo-Container nicht aufziehen) */
    body.builder-active main,
    body.builder-active main .container:not(.nx-keep-container){ max-width:none !important; width:100% !important; box-sizing:border-box; }
    body.builder-active main .container:not(.nx-keep-container){ padding-left:0; padding-right:0; }
    body.builder-active [data-nx-zone="content"]{ overflow-x:hidden; max-width:100%; box-sizing:border-box; }
    body.builder-active [data-nx-zone="content"] .nx-live-item{ max-width:100%; box-sizing:border-box; margin-top:0; margin-bottom:0; }
    body.builder-active [data-nx-zone="content"] > .nx-live-item[data-nx-item-width],
    body.builder-active .nx-live-zone > .nx-live-item[data-nx-item-width]{ width:100%; }
    body.builder-active section.nx-section{ max-width:100%; box-sizing:border-box; }
    body.builder-active section.nx-section .container,
    body.builder-active section.nx-section .container-fluid{ width:100% !important; max-width:100% !important; box-sizing:border-box; padding-left:calc(var(--bs-gutter-x, 1.5rem) / 2); padding-right:calc(var(--bs-gutter-x, 1.5rem) / 2); }
    body.builder-active section.nx-section .row.nx-section-cols{ max-width:100%; }
    body.builder-active .nx-container{ width:100%; max-width:100%; box-sizing:border-box; }
    body.builder-active .nx-container .container,
    body.builder-active .nx-container .container-fluid{ width:100% !important; max-width:100% !important; box-sizing:border-box; padding-left:calc(var(--bs-gutter-x, 1.5rem) / 2); padding-right:calc(var(--bs-gutter-x, 1.5rem) / 2); }
    .nx-badge{ font-size:.75rem }

    /* Content-Zone bis zum Footer: Flex-Kette + min-height-Fallback */
    body.builder-active html, body.builder-active body{ height:100%; }
    body.builder-active .sticky-footer-wrapper{ display:flex !important; flex-direction:column !important; min-height:100vh !important; flex:1 1 auto !important; }
    body.builder-active .sticky-footer-wrapper > main.flex-fill{ flex:1 1 auto !important; min-height:0 !important; display:flex !important; flex-direction:column !important; }
    body.builder-active .sticky-footer-wrapper > main.flex-fill > .container{ display:flex !important; flex-direction:column !important; flex:1 1 auto !important; min-height:0 !important; }
    body.builder-active [data-nx-zone="content"]{
      flex:1 1 auto !important;
      min-height:calc(100vh - 16rem) !important;
      /* Mehr „Drop-Fläche“ für den Haupt-Content: Magnetismus greift früher */
      padding-top:2.5rem;
      padding-bottom:2.5rem;
      box-sizing:border-box;
    }

    /* Linke Sidebar: Palette | Inhaltsbereich dazwischen | Rechte Sidebar: Einstellungen */
    body.builder-active .nx-live-toolbar{ margin-left:280px; margin-right:280px; padding-left:1.5rem; padding-right:1.5rem; }
    body.builder-active .sticky-footer-wrapper{ padding-top:3.25rem; margin-left:280px; margin-right:280px; padding-left:1.5rem; padding-right:1.5rem; max-width:100%; box-sizing:border-box; }
    body.builder-active.nx-palette-hidden .nx-live-toolbar{ margin-left:0; }
    body.builder-active.nx-palette-hidden .sticky-footer-wrapper{ margin-left:0; }
    #nx-palette{
      position:fixed; top:0; left:0; width:280px; height:100vh;
      overflow:hidden; background:#f8f9fa; border-right:1px solid #dee2e6;
      z-index:2147480002; display:flex; flex-direction:column;
      transition: transform .18s ease, opacity .18s ease;
      color:#111827;
    }
    #nx-pal-head{ padding:.75rem 1rem; background:linear-gradient(90deg,#f1f3f5,#e9ecef); border-bottom:1px solid #dee2e6; flex-shrink:0; display:flex; align-items:center; gap:.5rem; justify-content:space-between; }
    #nx-palette strong,
    #nx-palette .small:not(.text-muted),
    #nx-palette .fw-semibold{
      color:#111827 !important;
    }
    #nx-palette .text-muted{
      color:#6b7280 !important;
    }
    #nx-palette .input-group-text{
      color:#4b5563 !important;
      background:#ffffff !important;
      border-color:#cbd5e1 !important;
    }
    #nx-palette .form-control,
    #nx-palette .form-select,
    #nx-palette input[type="search"]{
      background:#ffffff !important;
      color:#111827 !important;
      border-color:#cbd5e1 !important;
    }
    #nx-palette .form-control::placeholder,
    #nx-palette input::placeholder{
      color:#9ca3af !important;
      opacity:1;
    }
    #nx-palette .form-control:focus,
    #nx-palette .form-select:focus,
    #nx-palette input:focus{
      color:#111827 !important;
      background:#ffffff !important;
      border-color:#60a5fa !important;
      box-shadow:0 0 0 .2rem rgba(96,165,250,.18) !important;
    }
    #nx-pal-body{ padding:0; overflow-y:auto; flex:1; min-height:0; }
    .nx-pal-categories{ display:flex; flex-direction:column; }
    .nx-pal-category{ background:#fff; }
    .nx-pal-category-head{ cursor:pointer; font-size:.875rem; color:#111827; transition:background .15s ease, color .15s ease; }
    .nx-pal-category-head:hover{ background:rgba(148,163,184,.14); }
    .nx-pal-category-head.active{
      background-color: #eaeaea !important;
      color:#2a2a2a !important;
    }
    .nx-pal-category-head[aria-expanded="false"] .nx-pal-caret{ transform:rotate(-90deg); }
    .nx-pal-caret{ transition:transform .2s ease; flex-shrink:0; }
    .nx-pal-list{ list-style:none; padding:.5rem .5rem .75rem; margin:0; display:flex; flex-direction:column; gap:.35rem; }
    .nx-pal-item{ position:relative; padding:.5rem .6rem .5rem 1.6rem; background:#f8f9fa; border:1px solid #dee2e6; border-radius:.4rem; user-select:none; cursor:grab; display:flex; align-items:center; justify-content:space-between; gap:.5rem; font-size:.8125rem; transition:box-shadow .12s ease,border-color .12s ease,transform .08s ease; color:#111827; }
    .nx-pal-item:hover{ border-color:#0d6efd; box-shadow:0 2px 8px rgba(13,110,253,.15); transform:translateY(-1px); }
    .nx-pal-item-variant{ font-size:.75rem; background:#fdfdfd; border-style:dashed; }
    .nx-pal-item-variant .nx-pal-handle{ top:12px; }
    .nx-pal-plugin{ display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:.55rem .75rem; background:#f8fafc; border:1px solid #dee2e6; border-radius:.4rem; cursor:pointer; color:#111827; font-size:.8125rem; transition:box-shadow .12s ease,border-color .12s ease,transform .08s ease; }
    .nx-pal-plugin:hover,
    .nx-pal-plugin.active{ border-color:#0d6efd; box-shadow:0 2px 8px rgba(13,110,253,.15); transform:translateY(-1px); }
    .nx-pal-item .text-muted,
    .nx-pal-item small{
      color:#6b7280 !important;
    }
    .nx-pal-preview{ border-radius:.35rem; padding:.35rem .45rem; background:linear-gradient(135deg,#f8fafc,#e2e8f0); border:1px solid rgba(148,163,184,.7); overflow:hidden; }
    .nx-pal-preview-header-text{ }
    .nx-pal-preview-header-text::before{ content:""; display:block; width:26px; height:3px; border-radius:999px; background:#0d6efd; margin-bottom:4px; }
    .nx-pal-preview-header-text span{ display:block; background:transparent; border-radius:3px; margin-bottom:3px; }
    .nx-pal-preview-header-text span:nth-child(1){ height:7px; width:80%; background:#0f172a; }
    .nx-pal-preview-header-text span:nth-child(2){ height:6px; width:55%; background:#64748b; }
    .nx-pal-preview-header-text span:nth-child(3){ height:4px; width:35%; background:#cbd5f5; }
    .nx-pal-preview-header-eyebrow{ position:relative; padding-top:2px; }
    .nx-pal-preview-header-eyebrow::before{ content:"EYEBROW"; display:inline-block; font-size:8px; letter-spacing:.12em; text-transform:uppercase; color:#0d6efd; margin-bottom:3px; }
    .nx-pal-preview-header-eyebrow span{ display:block; border-radius:3px; }
    .nx-pal-preview-header-eyebrow span:nth-child(1){ height:7px; width:78%; background:#0f172a; margin-bottom:3px; }
    .nx-pal-preview-header-eyebrow span:nth-child(2){ height:1px; width:50%; background:rgba(148,163,184,.9); }
    .nx-pal-preview-header-overlay{ position:relative; background:radial-gradient(circle at top,#1e293b,#020617); color:#e5e7eb; }
    .nx-pal-preview-header-overlay span{ display:block; border-radius:3px; }
    .nx-pal-preview-header-overlay span:nth-child(1){ height:7px; width:70%; background:rgba(248,250,252,.95); margin-bottom:3px; }
    .nx-pal-preview-header-overlay span:nth-child(2){ height:5px; width:55%; background:rgba(148,163,184,.95); }
    .nx-pal-preview-hero-dark{ background:radial-gradient(circle at top left,#1e293b,#020617); color:#e5e7eb; }
    .nx-pal-preview-hero-dark span{ display:block; border-radius:3px; margin-bottom:3px; }
    .nx-pal-preview-hero-dark span:nth-child(1){ height:7px; width:80%; background:#e5e7eb; }
    .nx-pal-preview-hero-dark span:nth-child(2){ height:5px; width:95%; background:rgba(148,163,184,.95); }
    .nx-pal-preview-hero-dark span:nth-child(3){ height:18px; width:60%; background:transparent; border-radius:999px; border:1px solid rgba(59,130,246,.95); box-shadow:0 0 0 1px rgba(37,99,235,.5) inset; }
    .nx-pal-preview-hero-light span{ display:block; border-radius:3px; margin-bottom:3px; }
    .nx-pal-preview-hero-light span:nth-child(1){ height:7px; width:85%; background:#0f172a; }
    .nx-pal-preview-hero-light span:nth-child(2){ height:5px; width:70%; background:#64748b; }
    .nx-pal-preview-hero-light span:nth-child(3){ height:16px; width:55%; background:linear-gradient(90deg,#0d6efd,#4f46e5); border-radius:999px; }
    .nx-pal-preview-testimonials-grid{ background:linear-gradient(135deg,#eff6ff,#e0f2fe); }
    .nx-pal-preview-testimonials-grid span{ display:inline-block; vertical-align:top; width:30%; height:18px; margin-right:4px; border-radius:4px; background:#fff; box-shadow:0 1px 3px rgba(15,23,42,.15); }
    .nx-pal-preview-testimonials-single{ background:linear-gradient(135deg,#f9fafb,#e5e7eb); position:relative; }
    .nx-pal-preview-testimonials-single span{ display:block; border-radius:3px; margin-bottom:3px; }
    .nx-pal-preview-testimonials-single span:nth-child(1){ height:7px; width:85%; background:#0f172a; }
    .nx-pal-preview-testimonials-single span:nth-child(2){ height:5px; width:65%; background:#6b7280; }
    .nx-pal-preview-testimonials-single span:nth-child(3){ height:4px; width:40%; background:#9ca3af; }
    .nx-pal-preview-faq-compact span{ display:block; border-radius:3px; margin-bottom:3px; }
    .nx-pal-preview-faq-compact span:nth-child(1),
    .nx-pal-preview-faq-compact span:nth-child(3),
    .nx-pal-preview-faq-compact span:nth-child(5){ height:6px; width:80%; background:#0f172a; }
    .nx-pal-preview-faq-compact span:nth-child(2),
    .nx-pal-preview-faq-compact span:nth-child(4),
    .nx-pal-preview-faq-compact span:nth-child(6){ height:4px; width:60%; background:#9ca3af; }
    .nx-pal-preview-faq-intro span{ display:block; border-radius:3px; margin-bottom:3px; }
    .nx-pal-preview-faq-intro span:nth-child(1){ height:6px; width:70%; background:#0f172a; }
    .nx-pal-preview-faq-intro span:nth-child(2){ height:4px; width:90%; background:#6b7280; }
    .nx-pal-preview-faq-intro span:nth-child(3),
    .nx-pal-preview-faq-intro span:nth-child(5),
    .nx-pal-preview-faq-intro span:nth-child(7){ height:5px; width:80%; background:#111827; }
    .nx-pal-preview-faq-intro span:nth-child(4),
    .nx-pal-preview-faq-intro span:nth-child(6),
    .nx-pal-preview-faq-intro span:nth-child(8){ height:3px; width:55%; background:#9ca3af; }
    /* Navigation-Previews */
    .nx-pal-preview-nav-simple span,
    .nx-pal-preview-nav-dropdown span,
    .nx-pal-preview-nav-centered span{ display:block; border-radius:3px; margin-bottom:3px; }
    .nx-pal-preview-nav-simple span:nth-child(1),
    .nx-pal-preview-nav-dropdown span:nth-child(1),
    .nx-pal-preview-nav-centered span:nth-child(1){ height:6px; width:30%; background:#0f172a; }
    .nx-pal-preview-nav-simple span:nth-child(2),
    .nx-pal-preview-nav-dropdown span:nth-child(2),
    .nx-pal-preview-nav-centered span:nth-child(2){ height:4px; width:80%; background:#6b7280; }
    .nx-pal-preview-nav-simple span:nth-child(3),
    .nx-pal-preview-nav-dropdown span:nth-child(3),
    .nx-pal-preview-nav-centered span:nth-child(3){ height:3px; width:60%; background:#9ca3af; }
    .nx-pal-preview-generic{
      background:linear-gradient(135deg,#f8fafc,#e2e8f0);
      border:1px solid rgba(148,163,184,.65);
    }
    .nx-pal-preview-generic span{ display:block; border-radius:3px; margin-bottom:3px; }
    .nx-pal-preview-generic span:nth-child(1){ height:7px; width:76%; background:#0f172a; }
    .nx-pal-preview-generic span:nth-child(2){ height:5px; width:52%; background:#64748b; }
    .nx-pal-preview-generic span:nth-child(3){ height:4px; width:34%; background:#cbd5e1; }
    .nx-pal-handle{ position:absolute; left:.5rem; top:50%; transform:translateY(-50%); opacity:.6; cursor:grab; font-size:.7rem; }
    /* Basis-Design Button – wie eine helle Karte */
    .nx-pal-global{
      border-radius:.75rem;
      border:1px solid rgba(148,163,184,.25);
      background:#ffffff;
      color:#0f172a;
      font-size:.82rem;
      padding:.55rem .9rem;
      display:flex;
      align-items:center;
      gap:.45rem;
      box-shadow:0 1px 3px rgba(15,23,42,.06);
      transition:background .12s ease,border-color .12s ease,box-shadow .12s ease,transform .08s ease,color .12s ease;
    }
    .nx-pal-global i{
      font-size:.95rem;
      color:#64748b;
    }
    .nx-pal-global:hover{
      background:#f8fafc;
      border-color:#cbd5f5;
      box-shadow:0 3px 8px rgba(15,23,42,.10);
      transform:translateY(-1px);
      color:#0f172a;
    }
    .nx-pal-global.nx-global-active{
      background:#eef2ff;
      border-color:#4f46e5;
      color:#111827;
      box-shadow:0 3px 10px rgba(79,70,229,.25);
    }
    .nx-pal-global.nx-global-active i{
      color:#4f46e5;
    }

    /* === Colorpicker (Basis-Design & Farb-Felder) ====================== */
    .nx-cp-overlay{
      position:fixed;
      inset:0;
      display:none;
      align-items:center;
      justify-content:center;
      background:rgba(15,23,42,.32);
      z-index:2147483646;
      padding:1.5rem;
      box-sizing:border-box;
    }
    .nx-cp-popup{
      position:relative;
      background:#ffffff;
      border-radius:1.25rem;
      box-shadow:0 22px 60px rgba(15,23,42,.32);
      padding:1rem 1rem 0.9rem;
      width:min(360px, 100%);
      display:flex;
      flex-direction:column;
      gap:.6rem;
      box-sizing:border-box;
    }
    .nx-cp-header{
      display:flex;
      align-items:center;
      gap:.5rem;
      margin-bottom:.3rem;
    }
    .nx-cp-hex-wrap{
      flex:1;
    }
    .nx-cp-hex-input{
      width:100%;
      border-radius:999px;
      border:1px solid #e2e8f0;
      padding:.35rem .85rem;
      font-size:.82rem;
      outline:none;
      background:#f8fafc;
    }
    .nx-cp-hex-input:focus{
      border-color:#3b82f6;
      box-shadow:0 0 0 1px rgba(59,130,246,.35);
      background:#ffffff;
    }
    .nx-cp-btn{
      border:none;
      border-radius:.9rem;
      width:2rem;
      height:2rem;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      font-size:.9rem;
      cursor:pointer;
      box-shadow:0 1px 3px rgba(15,23,42,.18);
      background:#fee2e2;
      color:#b91c1c;
      padding:0;
    }
    .nx-cp-btn-apply{
      background:#dbeafe;
      color:#1d4ed8;
    }
    .nx-cp-btn:hover{
      transform:translateY(-1px);
      box-shadow:0 3px 8px rgba(15,23,42,.28);
    }

    .nx-cp-sv-wrap{
      position:relative;
      margin-top:.25rem;
      border-radius:0;
      overflow:hidden;
      height:180px;
      background:#000;
      cursor:crosshair;
    }
    .nx-cp-sv-inner{
      position:absolute;
      inset:0;
      background:conic-gradient(from 180deg at 50% 0,#fff,var(--nx-cp-h,210) 0deg,#000);
      background:
        linear-gradient(to top, #000, transparent),
        linear-gradient(to right, #fff, hsl(var(--nx-cp-h,210),100%,50%));
    }
    .nx-cp-sv-cursor{
      position:absolute;
      width:14px;
      height:14px;
      border-radius:999px;
      border:2px solid #ffffff;
      box-shadow:0 0 0 1px rgba(15,23,42,.45);
      transform:translate(-50%,-50%);
      pointer-events:none;
      background:transparent;
    }

    .nx-cp-strip-wrap{
      position:relative;
      margin-top:.5rem;
      height:18px;
      border-radius:999px;
      overflow:hidden;
      background:#e5e7eb;
      cursor:pointer;
    }
    .nx-cp-hue{
      /* Reihenfolge an HSV-Kreis angepasst: Rot → Gelb → Grün → Cyan → Blau → Magenta → Rot */
      background:linear-gradient(90deg,
        #ff0000, /* 0°  red    */
        #ffff00, /* 60° yellow */
        #00ff00, /* 120° green */
        #00ffff, /* 180° cyan  */
        #0000ff, /* 240° blue  */
        #ff00ff, /* 300° magenta */
        #ff0000  /* 360° red   */
      );
    }
    .nx-cp-alpha-wrap{
      position:relative;
    }
    .nx-cp-alpha-wrap::before{
      content:"";
      position:absolute;
      inset:0;
      background-image:linear-gradient(45deg,#e5e7eb 25%,transparent 25%,transparent 75%,#e5e7eb 75%,#e5e7eb),
                       linear-gradient(45deg,#e5e7eb 25%,transparent 25%,transparent 75%,#e5e7eb 75%,#e5e7eb);
      background-size:10px 10px;
      background-position:0 0,5px 5px;
      opacity:.9;
    }
    .nx-cp-alpha-inner{
      position:absolute;
      inset:0;
      background:linear-gradient(90deg,rgba(15,23,42,0), var(--nx-cp-alpha-color,#3b82f6));
    }
    .nx-cp-strip-cursor{
      position:absolute;
      top:50%;
      width:14px;
      height:14px;
      border-radius:999px;
      border:2px solid #ffffff;
      box-shadow:0 0 0 1px rgba(15,23,42,.45);
      transform:translate(-50%,-50%);
      pointer-events:none;
      background:transparent;
    }

    /* Hero-Höhen (nur im Builder & Frontend, angelehnt an moderne Landingpages) */
    .nx-hero-h-40{ min-height:40vh; display:flex; align-items:center; }
    .nx-hero-h-50{ min-height:50vh; display:flex; align-items:center; }
    .nx-hero-h-60{ min-height:60vh; display:flex; align-items:center; }
    .nx-hero-h-80{ min-height:80vh; display:flex; align-items:center; }
    .nx-hero-h-100{ min-height:100vh; display:flex; align-items:center; }
    .ghost{ opacity:.5 }
    .nx-dragging #nx-palette{ pointer-events:none; }

    /* Zusätzliche Beispiele-Sidebar rechts von der Palette */
    #nx-examples-panel{
      position:fixed; top:0; left:280px; width:280px; height:100vh;
      background:#ffffff; border-right:1px solid #dee2e6;
      z-index:2147480001; display:flex; flex-direction:column;
      box-shadow:2px 0 12px rgba(15,23,42,.08);
      transform:translateX(-100%);
      opacity:0;
      pointer-events:none;
      transition:transform .18s ease, opacity .18s ease;
      color:#111827;
    }
    #nx-examples-panel.nx-examples-visible{
      transform:translateX(0);
      opacity:1;
      pointer-events:auto;
    }
    #nx-examples-panel .nx-examples-head{
      padding:.75rem 1rem; border-bottom:1px solid #dee2e6;
      display:flex; align-items:center; justify-content:space-between; gap:.5rem;
      color:#111827;
      background:#ffffff;
    }
    #nx-examples-panel .nx-examples-body{
      padding:.5rem 1rem .75rem; overflow-y:auto; flex:1; min-height:0;
      background:#f8fafc;
    }
    #nx-examples-empty{ color:#6c757d; font-size:.8125rem; }
    #nx-examples-panel .text-muted,
    #nx-examples-panel .small{
      color:#6b7280 !important;
    }
    #nx-examples-panel .fw-semibold,
    #nx-examples-title,
    #nx-examples-panel .nx-example-title{
      color:#111827 !important;
    }
    .nx-example-item{
      position:relative;
      display:flex; gap:.5rem;
      padding:.5rem .5rem .5rem 1.4rem;
      margin-bottom:.4rem;
      border-radius:.5rem;
      background:#ffffff;
      border:1px dashed #cbd5f5;
      cursor:grab;
      transition:border-color .12s ease, box-shadow .12s ease, transform .08s ease, background .12s ease;
    }
    .nx-example-item:hover{
      border-color:#0d6efd;
      box-shadow:0 2px 10px rgba(15,23,42,.12);
      background:#f9fafb;
      transform:translateY(-1px);
    }
    .nx-example-group{
      display:flex; gap:.5rem;
      padding:.7rem .8rem;
      margin-bottom:.4rem;
      border-radius:.5rem;
      background:#ffffff;
      border:1px solid #dbe3ee;
      cursor:pointer;
      transition:border-color .12s ease, box-shadow .12s ease, transform .08s ease, background .12s ease;
    }
    .nx-example-group:hover{
      border-color:#0d6efd;
      box-shadow:0 2px 10px rgba(15,23,42,.12);
      background:#f9fafb;
      transform:translateY(-1px);
    }
    .nx-example-handle{
      position:absolute; left:.45rem; top:50%; transform:translateY(-50%);
      font-size:.7rem; opacity:.6; cursor:grab;
    }
    .nx-example-inner{ flex:1; min-width:0; }
    .nx-example-title{ white-space:nowrap; text-overflow:ellipsis; overflow:hidden; }
    .nx-example-desc{ font-size:.75rem; color:#6b7280 !important; line-height:1.4; }

    /* Demo-Navigationen (für core_html-Templates) */
    .nx-demo-nav{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:1.5rem;
      padding:.75rem 1.25rem;
      border-radius:.75rem;
      background:rgba(15,23,42,.96);
      color:#e5e7eb;
    }
    .nx-demo-nav .nx-nav-brand{
      font-weight:700;
      letter-spacing:.05em;
      text-transform:uppercase;
      font-size:.8rem;
    }
    .nx-demo-nav .nx-nav-links{
      display:flex;
      align-items:center;
      gap:1.25rem;
      list-style:none;
      padding:0;
      margin:0;
    }
    .nx-demo-nav a{
      color:inherit;
      text-decoration:none;
      font-size:.82rem;
    }
    .nx-demo-nav a:hover{
      color:#38bdf8;
    }
    .nx-demo-nav .nx-nav-cta{
      display:flex;
      align-items:center;
      gap:.75rem;
    }
    .nx-demo-nav .nx-nav-btn{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:.4rem 1rem;
      border-radius:999px;
      background:#22c55e;
      color:#052e16;
      font-size:.8rem;
      font-weight:600;
    }
    .nx-demo-nav .nx-nav-btn:hover{
      background:#16a34a;
      color:#022c22;
    }
    /* Dropdown-Hover */
    .nx-demo-nav .nx-nav-dropdown{
      position:relative;
    }
    .nx-demo-nav .nx-nav-dropdown-menu{
      position:absolute;
      top:100%;
      left:0;
      margin-top:.35rem;
      min-width:180px;
      padding:.35rem 0;
      border-radius:.6rem;
      background:#0b1120;
      box-shadow:0 16px 40px rgba(15,23,42,.6);
      opacity:0;
      transform:translateY(4px);
      pointer-events:none;
      transition:opacity .14s ease, transform .14s ease;
      z-index:20;
    }
    .nx-demo-nav .nx-nav-dropdown-menu a{
      display:block;
      padding:.3rem .9rem;
      font-size:.8rem;
      color:#e5e7eb;
    }
    .nx-demo-nav .nx-nav-dropdown-menu a:hover{
      background:rgba(15,118,110,.38);
    }
    .nx-demo-nav .nx-nav-dropdown:hover .nx-nav-dropdown-menu{
      opacity:1;
      transform:translateY(0);
      pointer-events:auto;
    }
    .nx-demo-nav.nx-demo-nav-centered{
      justify-content:center;
      background:#f8fafc;
      color:#020617;
      border-radius:0;
      border-bottom:1px solid #e5e7eb;
    }
    .nx-demo-nav.nx-demo-nav-centered .nx-nav-inner{
      display:flex;
      align-items:center;
      gap:2rem;
    }
    .nx-demo-nav.nx-demo-nav-centered .nx-nav-links a:hover{
      color:#0d6efd;
    }

    /* Ein-/Ausblenden */
    #nx-palette.is-hidden{
      transform: translateX(-100%);
      opacity: 0;
      pointer-events: none;
    }
    body.builder-active .nx-palette-hidden .nx-builder-main-wrap{ margin-left:0; }
    #nx-toggle-palette[aria-expanded="false"] { opacity: .75; }

    /* Rechte Sidebar: Einstellungen (280px wie Palette, damit Content mittig liegt) */
    #nx-settings-sidebar{
      position:fixed; top:0; right:0; width:280px; height:100vh;
      background:#fff; border-left:1px solid #dee2e6; z-index:2147480001;
      display:flex; flex-direction:column; box-shadow:-4px 0 12px rgba(0,0,0,.06);
      color:#111827;
    }
    #nx-settings-sidebar .nx-settings-header{ padding:.75rem 1rem; border-bottom:1px solid #dee2e6; flex-shrink:0; }
    #nx-settings-sidebar .nx-settings-body{ flex:1; overflow-y:auto; padding:1rem; }
    #nx-settings-sidebar .nx-settings-placeholder{ color:#6c757d; font-size:.875rem; padding:1rem; }
    #nx-settings-sidebar label,
    #nx-settings-sidebar .form-label,
    #nx-settings-sidebar .form-text,
    #nx-settings-sidebar .small,
    #nx-settings-sidebar .text-muted,
    #nx-settings-sidebar .form-check-label{
      color:#374151 !important;
    }
    #nx-settings-sidebar .form-control,
    #nx-settings-sidebar .form-select,
    #nx-settings-sidebar textarea,
    #nx-settings-sidebar input[type="text"],
    #nx-settings-sidebar input[type="search"],
    #nx-settings-sidebar input[type="number"]{
      background:#ffffff !important;
      color:#111827 !important;
      border-color:#cbd5e1 !important;
    }
    #nx-settings-sidebar .form-control::placeholder,
    #nx-settings-sidebar textarea::placeholder,
    #nx-settings-sidebar input::placeholder{
      color:#9ca3af !important;
      opacity:1;
    }
    #nx-settings-sidebar .form-control:focus,
    #nx-settings-sidebar .form-select:focus,
    #nx-settings-sidebar textarea:focus,
    #nx-settings-sidebar input:focus{
      color:#111827 !important;
      background:#ffffff !important;
      border-color:#60a5fa !important;
      box-shadow:0 0 0 .2rem rgba(96,165,250,.18) !important;
    }
    #nx-settings-sidebar option{
      color:#111827;
      background:#ffffff;
    }
    #nx-settings-sidebar .accordion-item{
      border:1px solid #dbe3ee;
      border-radius:.65rem;
      overflow:hidden;
      margin-bottom:.5rem;
      background:#ffffff;
    }
    #nx-settings-sidebar .accordion-button{
      color:#111827 !important;
      background:#f8fafc !important;
      font-weight:600;
      box-shadow:none !important;
    }
    #nx-settings-sidebar .accordion-button:not(.collapsed){
      color:#111827 !important;
      background:#eef2ff !important;
    }
    #nx-settings-sidebar .accordion-body{
      color:#374151;
      background:#ffffff;
    }
    #nx-settings-footer{ position:sticky; bottom:0; padding-top:.5rem; margin-top:auto; background:linear-gradient(to top, #fff 70%, rgba(255,255,255,.85)); }
    #nx-settings-footer .btn{ white-space:nowrap; }

    /* Feste Bausteine (Navbar, Footer): Kennzeichnung im Builder, keine Drop-Zone */
    body.builder-active .nx-fixed-block{
      position:relative; margin:0;
      border:none; border-radius:0;
      padding:0; background:transparent;
    }
    body.builder-active .nx-fixed-block:not(:has(.nx-live-zone)){
      display:none !important;
    }
    body.builder-active .nx-fixed-block::before{
      display:none;
    }
    body.builder-active .nx-fixed-block::after{
      display:none;
    }

    /* === Zonen-Restriktions-Logik START (optische Marker) ============== */
    .nx-zone-allowed   { outline: 2px solid #0d6efd !important; box-shadow: inset 0 0 0 2px rgba(13,110,253,.4); background: rgba(13,110,253,.06) !important; transition: outline .12s ease, box-shadow .12s ease, background .12s ease; }
    .nx-zone-forbidden { outline: 2px solid #dc3545 !important; box-shadow: inset 0 0 0 2px rgba(220,53,69,.35); background: rgba(220,53,69,.06) !important; cursor: not-allowed !important; transition: outline .12s ease, box-shadow .12s ease, background .12s ease; }

    /* Zonen greifen den Drag zuverlässig */
    .nx-live-zone, .nx-zone { position: relative; pointer-events: auto; }
    .nx-drop-hint { pointer-events: none; }
    /* Section-Spalten: einheitlich abgerundet, mehr Abstand zwischen Cols (Gutter), Trennlinie, kein Überlauf */
    body.builder-active section.nx-section .nx-section-cols { --bs-gutter-x: 1.5rem; }
    body.builder-active section.nx-section .nx-section-cols > [data-nx-zone] { min-width: 0; overflow: hidden; border-radius: 0.25rem; }
    body.builder-active section.nx-section .nx-section-cols > [data-nx-zone] + [data-nx-zone] { border-left: 1px solid rgba(0,0,0,.12); }
    body.builder-active section.nx-row{ max-width:100%; box-sizing:border-box; }
    body.builder-active section.nx-row .container, body.builder-active section.nx-row .container-fluid{ width:100% !important; max-width:100% !important; box-sizing:border-box; padding-left:calc(var(--bs-gutter-x, 1.5rem) / 2); padding-right:calc(var(--bs-gutter-x, 1.5rem) / 2); }
    body.builder-active section.nx-row .nx-row-cols { --bs-gutter-x: 1.5rem; max-width:100%; display:flex !important; flex-wrap: wrap !important; margin-left: calc(var(--bs-gutter-x) * -0.5); margin-right: calc(var(--bs-gutter-x) * -0.5); }
    body.builder-active section.nx-row .nx-row-cols > .nx-live-item { min-width: 0; overflow: hidden; border-radius: 0.25rem; box-sizing: border-box; padding-left: calc(var(--bs-gutter-x) * 0.5); padding-right: calc(var(--bs-gutter-x) * 0.5); }
    body.builder-active section.nx-row .nx-row-cols > .nx-live-item + .nx-live-item { border-left: 1px solid rgba(0,0,0,.12); }
    /* Spaltenbreite im Builder per data-nx-col-span (12er-Grid), unabhängig von Bootstrap */
    body.builder-active section.nx-row .nx-row-cols > .nx-live-item[data-nx-col-span="12"] { flex: 0 0 100%; max-width: 100%; }
    body.builder-active section.nx-row .nx-row-cols > .nx-live-item[data-nx-col-span="6"] { flex: 0 0 50%; max-width: 50%; }
    body.builder-active section.nx-row .nx-row-cols > .nx-live-item[data-nx-col-span="4"] { flex: 0 0 33.333333%; max-width: 33.333333%; }
    body.builder-active section.nx-row .nx-row-cols > .nx-live-item[data-nx-col-span="3"] { flex: 0 0 25%; max-width: 25%; }
    body.builder-active section.nx-row .nx-row-cols > .nx-live-item[data-nx-col-span="8"] { flex: 0 0 66.666667%; max-width: 66.666667%; }
    body.builder-active section.nx-row .nx-row-cols > .nx-live-item[data-nx-col-span="9"] { flex: 0 0 75%; max-width: 75%; }
    body.builder-active .nx-inline-placeholder{ font-style:italic; opacity:.85; }
    /* === Zonen-Restriktions-Logik ENDE ================================= */
  </style>';

  function url_with_params(string $url, array $params): string {
    $parts = parse_url($url);
    $base  = ($parts['scheme'] ?? '') ? ($parts['scheme'].'://') : '';
    $base .= $parts['host']  ?? '';
    $base .= $parts['path']  ?? '';

    // bestehende Query lesen & mergen
    $qs = [];
    if (!empty($parts['query'])) parse_str($parts['query'], $qs);
    $qs = array_merge($qs, $params);

    return $base . (empty($qs) ? '' : ('?' . http_build_query($qs)));
}

function nxb_builder_available_pages(string $currentPage): array {
  global $_database;
  $pages = [];

  $addPage = static function (string $slug) use (&$pages): void {
    $slug = trim($slug);
    if ($slug === '') return;
    if (!isset($pages[$slug])) {
      $pages[$slug] = ['slug' => $slug];
    }
  };

  $addPage('index');
  $addPage($currentPage);

  if ($_database instanceof mysqli) {
    if ($res = $_database->query("SELECT DISTINCT page FROM settings_widgets_positions WHERE page <> '' ORDER BY page ASC")) {
      while ($row = $res->fetch_assoc()) {
        $addPage((string)($row['page'] ?? ''));
      }
      $res->free();
    }

    if ($res = $_database->query("SELECT modulname, url FROM navigation_website_sub ORDER BY modulname ASC")) {
      while ($row = $res->fetch_assoc()) {
        $slug = trim((string)($row['modulname'] ?? ''));
        $url  = trim((string)($row['url'] ?? ''));
        if ($slug !== '' && strtolower($slug) !== 'static') {
          $addPage($slug);
          continue;
        }
        if ($url !== '') {
          $query = (string)parse_url($url, PHP_URL_QUERY);
          if ($query !== '') {
            $parts = [];
            parse_str($query, $parts);
            $site = trim((string)($parts['site'] ?? ''));
            if ($site !== '') {
              $addPage($site);
            }
          }
        }
      }
      $res->free();
    }
  }

  ksort($pages, SORT_NATURAL | SORT_FLAG_CASE);
  return array_values($pages);
}

function nxb_builder_page_url(string $lang, string $page): string {
  $params = [
    'site' => ($page !== '' ? $page : 'index'),
    'lang' => $lang,
    'builder' => '1',
  ];
  if (!empty($_GET['debug']) && (string)$_GET['debug'] === '1') {
    $params['debug'] = '1';
  }
  return '/index.php?' . http_build_query($params);
}

$liveUrl = nxb_builder_page_url($lang, $page);

$href = $liveUrl;
$availablePages = nxb_builder_available_pages($page);

  echo '<div class="nx-live-toolbar container-fluid">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill text-bg-primary px-3 py-2">Live-Builder</span>
        <span class="text-muted small">Seite:</span>
        <select id="nx-page-switch" class="form-select form-select-sm" style="width:auto;min-width:180px;">';
  foreach ($availablePages as $pageOption) {
    $slug = (string)($pageOption['slug'] ?? '');
    if ($slug === '') continue;
    $pageHref = nxb_builder_page_url($lang, $slug);
    echo '<option value="' . nxb_h($pageHref) . '"' . ($slug === $page ? ' selected' : '') . '>' . nxb_h($slug) . '</option>';
  }
  echo '</select>
      </div>
      <span class="text-muted small" style="font-size:0.8rem;">Navbar &amp; Footer: feste Blöcke → im <strong>Admincenter</strong> bearbeiten (nicht hier).</span>
      <div class="d-flex align-items-center gap-2 ms-auto">
        <span class="badge text-bg-light nx-badge d-none d-md-inline">Lang: '.nxb_h($lang).'</span>
        <button id="nx-toggle-palette" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" type="button" aria-expanded="true">
          <i class="bi bi-grid-3x3-gap"></i><span class="d-none d-sm-inline">Widgets</span>
        </button>
        <a id="nx-live-save" class="btn btn-sm btn-primary d-flex align-items-center gap-1" type="button" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">
          <i class="bi bi-check2-circle"></i><span class="d-none d-md-inline">Speichern &amp; neu laden</span>
        </a>
      </div>
    </div>
  </div>';

  // Palette nach Kategorien gruppieren (Shuffle-ähnlich: Rubriken mit Aufklappbereichen)
  $byCategory = [];
  if ($palette) {
    foreach ($palette as $w) {
      $cat = (string)($w['category'] ?? 'Sonstige');
      if (!isset($byCategory[$cat])) {
        $byCategory[$cat] = [];
      }
      $byCategory[$cat][] = $w;
    }
  }
  $categoryOrder = ['Layout', 'Grundbausteine', 'Inhalt', 'Widgets', 'Sonstige'];
  $orderedCategories = array_unique(array_merge($categoryOrder, array_keys($byCategory)));

  echo '<aside id="nx-palette">
    <div id="nx-pal-head">
      <div class="d-flex flex-column gap-2 w-100">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-grid-3x3-gap text-secondary"></i>
          <div>
            <strong class="small d-block">Komponenten</strong>
            <span class="text-muted small">Suche, Filter &amp; Favoriten</span>
          </div>
        </div>
        <div class="input-group input-group-sm">
          <span class="input-group-text border-end-0 me-2"><i class="bi bi-search text-muted"></i></span>
          <input type="search" class="form-control border-start-0 px-2 py-2" id="nx-pal-search" placeholder="Widgets durchsuchen …" autocomplete="off">
        </div>
      </div>
    </div>
    <div id="nx-pal-body">
      <div class="px-3 pt-2 pb-1 border-bottom">
        <button type="button" class="btn btn-outline-secondary btn-sm w-100 text-start d-flex align-items-center gap-2 nx-pal-global">
          <i class="bi bi-sliders"></i>
          <span>Basis-Design</span>
        </button>
      </div>
      <div id="nx-pal-categories" class="nx-pal-categories">';
  $first = true;
  foreach ($orderedCategories as $cat) {
    $items = $byCategory[$cat] ?? [];
    if (empty($items)) {
      continue;
    }
    // Items innerhalb der Kategorie alphabetisch nach Titel sortieren
    usort($items, function ($a, $b) {
      return strcasecmp((string)($a['title'] ?? ''), (string)($b['title'] ?? ''));
    });
    $catId = 'nx-pal-cat-' . preg_replace('/[^a-z0-9]/', '-', strtolower($cat));
    $collapsed = $first ? ' show' : '';
    $first = false;
    echo '<div class="nx-pal-category border-bottom">
        <button type="button" class="nx-pal-category-head w-100 text-start d-flex align-items-center gap-2 py-2 px-3 border-0 bg-transparent" data-nx-cat="' . nxb_h($cat) . '" aria-expanded="false" aria-controls="' . nxb_h($catId) . '">
          <i class="bi bi-chevron-down nx-pal-caret small"></i>
          <span class="fw-semibold small">' . nxb_h($cat) . '</span>
          <span class="badge rounded-pill text-bg-light text-dark ms-auto">' . count($items) . '</span>
        </button>
        <div id="' . nxb_h($catId) . '" class="nx-pal-category-content d-none" hidden aria-hidden="true">
          <ul class="nx-pal-list list-unstyled mb-0 px-2 pb-2">';
    if ($cat === 'Widgets') {
      $plugins = [];
      foreach ($items as $w) {
        $plugin = trim((string)($w['plugin_group'] ?? 'Widgets'));
        if ($plugin === '') {
          $plugin = 'Widgets';
        }
        if (!isset($plugins[$plugin])) {
          $plugins[$plugin] = 0;
        }
        $plugins[$plugin]++;
      }
      ksort($plugins, SORT_NATURAL | SORT_FLAG_CASE);
      foreach ($plugins as $plugin => $pluginCount) {
        echo '<li class="nx-pal-plugin" data-plugin-group="' . nxb_h($plugin) . '">
                <span class="fw-medium">' . nxb_h($plugin) . '</span>
                <span class="badge rounded-pill text-bg-light text-dark">' . (int)$pluginCount . '</span>
              </li>';
      }
      foreach ($items as $w) {
        echo '<li class="nx-pal-item d-none"
                  data-pal-key="' . nxb_h($w['widget_key']) . '"
                  data-pal-title="' . nxb_h($w['title']) . '"
                  data-plugin-group="' . nxb_h((string)($w['plugin_group'] ?? '')) . '"
                  data-allowed="' . nxb_h($w['allowed_zones'] ?? '') . '">
                <span class="nx-pal-handle">⋮⋮</span> ' . nxb_h($w['title']) . '
              </li>';
      }
    } else {
      foreach ($items as $w) {
        $isCore = !empty($w['is_core']);
        echo '<li class="nx-pal-item"
                  data-pal-key="' . nxb_h($w['widget_key']) . '"
                  data-pal-title="' . nxb_h($w['title']) . '"
                  data-plugin-group="' . nxb_h((string)($w['plugin_group'] ?? '')) . '"
                  data-allowed="' . nxb_h($w['allowed_zones'] ?? '') . '">
                <span class="nx-pal-handle">⋮⋮</span> ' . nxb_h($w['title']);
        if ($isCore) {
          echo ' <span class="badge rounded-pill text-bg-primary ms-1 small">Core</span>';
        }
        echo '</li>';
      }
    }
    echo '</ul>
        </div>
      </div>';
  }
  echo '</div>
    </div>
  </aside>';

  // Beispiele-Sidebar rechts von der Palette (wird per JS befüllt)
  echo '<aside id="nx-examples-panel">
    <div class="nx-examples-head">
      <div>
        <div class="text-muted small">Vorlagen für</div>
        <div id="nx-examples-title" class="small fw-semibold">Widget</div>
      </div>
    </div>
    <div class="nx-examples-body" id="nx-examples-body">
      <div id="nx-examples-empty" class="text-muted small">Für dieses Widget sind aktuell keine Vorlagen hinterlegt.</div>
    </div>
  </aside>';

  // Rechte Sidebar: Einstellungen (immer sichtbar)
  echo '<aside id="nx-settings-sidebar">
    <div class="nx-settings-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0 small fw-semibold" id="nx-settings-label">Einstellungen</h5>
    </div>
    <div class="nx-settings-body d-flex flex-column">
      <div id="nx-settings-placeholder" class="nx-settings-placeholder">Widget in eine Zone ziehen oder auf <i class="bi bi-sliders"></i> klicken – hier erscheinen die Einstellungen.</div>
      <div id="nx-settings-content" class="d-none d-flex flex-column flex-grow-1">
        <form id="nx-settings-form" class="d-flex flex-column gap-2 flex-grow-1">
          <div id="nx-settings-fields" class="mb-2"></div>
          <div class="mb-2 flex-grow-1 d-flex flex-column min-h-0 d-none" aria-hidden="true">
            <label class="form-label small mb-1" for="nx-settings-json">JSON</label>
            <textarea id="nx-settings-json" class="form-control form-control-sm font-monospace flex-grow-1" rows="6"></textarea>
          </div>
          <div id="nx-settings-footer" class="d-flex justify-content-between align-items-center mt-2">
            <small id="nx-settings-error" class="text-danger"></small>
            <button type="submit" class="btn btn-primary btn-sm">Speichern</button>
          </div>
        </form>
      </div>
    </div>
  </aside>';

  $CSRF          = json_encode($csrf, JSON_UNESCAPED_UNICODE);
  $PAGE          = json_encode($page, JSON_UNESCAPED_UNICODE);
  $ZONE_SELECTORS = json_encode(NXB_ZONE_SELECTORS, JSON_UNESCAPED_UNICODE);
  $BASE_URL      = json_encode(rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/'));
  $THEME_OPTIONS = json_encode(nx_get_theme_options(), JSON_UNESCAPED_UNICODE);

  echo '<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>';

  /* === Zonen-Restriktions-Logik START (Map in JS bereitstellen) ======= */
  echo '<script>window.widgetRestrictions = '.json_encode($__NX_ALLOWED_ZONES_MAP, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).';</script>';

  /* Globale Variablen für ausgelagertes Builder-Script */
  echo '<script>
    window.NXB_BUILDER_VARS = {
      CSRF: '.$CSRF.',
      PAGE: '.$PAGE.',
      BASE_URL: '.$BASE_URL.',
      ZONE_SELECTORS: '.$ZONE_SELECTORS.',
      THEME_OPTIONS: '.$THEME_OPTIONS.'
    };
    document.addEventListener("DOMContentLoaded", function () {
      var pageSwitch = document.getElementById("nx-page-switch");
      if (pageSwitch) {
        pageSwitch.addEventListener("change", function () {
          if (pageSwitch.value) {
            window.location.href = pageSwitch.value;
          }
        });
      }
    });
  </script>';

  // Debug-Panel (PHP + JS) nur im Debug-Modus (debug=1) ausgeben
  if ($debugMode) {
    nxb_output_debug_panel();
  }

  /* Externes Builder-Script laden (mit Cache-Busting-Version) */
  echo '<script src="/public/js/builder_live.js?v=3"></script>';

}

function nxb_prepare_builder(string $page): array {
  return nxb_build_widgets_html($page);
}
