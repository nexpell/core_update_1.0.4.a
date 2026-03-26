<?php
// === /includes/builder_core.php ===
// Gemeinsame Core-Funktionen für Live- und Preview-Builder
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

/* ===========================================================
   SECURITY / INIT
   =========================================================== */
if (!defined('BASE_PATH')) {
  define('BASE_PATH', dirname(__DIR__, 2)); // zwei Ebenen hoch: /system/core → /
}
require_once BASE_PATH . '/system/config.inc.php';
require_once BASE_PATH . '/system/core/theme_builder_helper.php';

// --- DB Connection ---
global $_database;
/** @var mysqli|null $_database */
$_database = $_database ?? new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($_database->connect_errno) {
  http_response_code(500);
  die('DB connection failed: ' . $_database->connect_error);
}
$_database->set_charset('utf8mb4');

// --- CSRF Token ---
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$CSRF = $_SESSION['csrf_token'];

/* ===========================================================
   KONSTANTEN / GRUNDWERTE
   =========================================================== */

// Definiert die gültigen Widget-Zonen
if (!defined('NX_ZONES')) {
  define('NX_ZONES', ['top','undertop','left','maintop','mainbottom','right','bottom']);
}

/* ===========================================================
   HELPER-FUNKTIONEN
   =========================================================== */

/**
 * Sicherheit: SQL-Escape Wrapper
 */
function nx_escape(string $value): string {
  global $_database;
  return $_database->real_escape_string($value);
}

/**
 * Lädt alle registrierten Widgets aus der Tabelle settings_widgets.
 * Gibt ein Array mit widget_key, title, plugin, allowed_zones usw. zurück.
 */
function nx_load_available_widgets(): array {
  global $_database;
  $out = [];
  if (!$_database) return $out;

  $res = $_database->query("
    SELECT widget_key, COALESCE(NULLIF(title,''), widget_key) AS title,
           plugin, allowed_zones
    FROM settings_widgets
    ORDER BY title ASC
  ");
  if ($res) {
    while ($row = $res->fetch_assoc()) {
      $out[] = [
        'widget_key'   => $row['widget_key'],
        'title'        => $row['title'],
        'plugin'       => $row['plugin'],
        'allowed_zones'=> $row['allowed_zones'] ?? '',
      ];
    }
    $res->free();
  }
  return $out;
}

/**
 * Lädt alle gespeicherten Widget-Instanzen einer Seite.
 * Gibt ein Array [position => [widgets...]] zurück.
 */

function nx_load_widgets_for_page(string $page): array {
    global $_database;

    $out = [];
    if (!$_database || $_database->connect_errno) {
        echo "<div class='text-danger'>❌ DB-Verbindung ungültig</div>";
        return $out;
    }

    $stmt = $_database->prepare("
        SELECT 
            p.position,
            p.widget_key,
            p.instance_id,
            COALESCE(p.settings, '{}') AS settings,
            COALESCE(w.title, p.widget_key) AS title,
            COALESCE(w.allowed_zones, '') AS allowed_zones
        FROM settings_widgets_positions AS p
        LEFT JOIN settings_widgets AS w ON w.widget_key = p.widget_key
        WHERE p.page = ?
        ORDER BY p.position ASC, p.sort_order ASC
    ");

    if (!$stmt) {
        echo "<div class='text-danger'>❌ Prepare fehlgeschlagen: " . htmlspecialchars($_database->error) . "</div>";
        return $out;
    }

    $stmt->bind_param('s', $page);
    if (!$stmt->execute()) {
        echo "<div class='text-danger'>❌ Execute fehlgeschlagen: " . htmlspecialchars($stmt->error) . "</div>";
        $stmt->close();
        return $out;
    }

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $pos = $row['position'] ?: 'unknown';
        if (!isset($out[$pos])) $out[$pos] = [];

        // Robust gegen ungültiges JSON
        $settings = [];
        $json = trim($row['settings'] ?? '');
        if ($json !== '') {
            $decoded = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $settings = $decoded;
            } else {
                // Fehlerhafte JSONs sicher reparieren
                $settings = [];
            }
        }

        $out[$pos][] = [
            'widget_key'    => (string)$row['widget_key'],
            'instance_id'   => (string)$row['instance_id'],
            'title'         => (string)$row['title'],
            'settings'      => $settings,
            'allowed_zones' => (string)($row['allowed_zones'] ?? '')
        ];
    }

    $stmt->close();

    // 🧪 Debug optional
    if (isset($_GET['debug_builder'])) {
        echo "<pre class='small text-muted bg-light border p-2'><b>🧩 Debug nx_load_widgets_for_page({$page}):</b>\n";
        echo htmlspecialchars(print_r($out, true));
        echo "</pre>";
    }

    return $out;
}



/**
 * Gibt ein Widget-HTML aus (optional für spätere Integration)
 */
function nx_render_widget(array $widget): string {
  $title = htmlspecialchars($widget['title'] ?? 'Untitled');
  return "<div class='nx-live-item border p-2 mb-1 rounded bg-light'>{$title}</div>";
}

/**
 * Einfacher Log-Helper (optional)
 */
function nx_log(string $msg): void {
  // file_put_contents(BASE_PATH.'/logs/builder.log', '['.date('c').'] '.$msg.PHP_EOL, FILE_APPEND);
}

function nx_default_builder_zone_definitions(): array {
  return [
    ['key' => 'top', 'label' => 'Top', 'enabled' => true],
    ['key' => 'undertop', 'label' => 'Unter Top', 'enabled' => true],
    ['key' => 'left', 'label' => 'Linke Sidebar', 'enabled' => true],
    ['key' => 'main', 'label' => 'Hauptinhalt', 'enabled' => true],
    ['key' => 'maintop', 'label' => 'Main Top', 'enabled' => true],
    ['key' => 'mainbottom', 'label' => 'Main Bottom', 'enabled' => true],
    ['key' => 'right', 'label' => 'Rechte Sidebar', 'enabled' => true],
    ['key' => 'bottom', 'label' => 'Bottom', 'enabled' => true],
  ];
}

function nx_normalize_builder_zones(?array $zones): array {
  $normalized = [];

  foreach ($zones ?? [] as $zone) {
    if (!is_array($zone)) {
      continue;
    }

    $key = strtolower(trim((string)($zone['key'] ?? '')));
    $key = preg_replace('/[^a-z0-9_-]/', '', $key);
    if ($key === '') {
      continue;
    }

    $normalized[$key] = [
      'key' => $key,
      'label' => trim((string)($zone['label'] ?? ucfirst(str_replace('_', ' ', $key)))) ?: ucfirst($key),
      'enabled' => array_key_exists('enabled', $zone) ? (bool)$zone['enabled'] : true,
    ];
  }

  return array_values($normalized);
}

function nx_get_active_theme_builder_zones(bool $enabledOnly = false): array {
  static $cache = null;

  if ($cache === null) {
    $zones = [];
    $themeManager = $GLOBALS['nx_theme_manager'] ?? null;

    if ($themeManager instanceof \nexpell\ThemeManager) {
      $runtime = nx_theme_builder_runtime_settings($themeManager);
      $zones = nx_theme_builder_zones((string)($runtime['layout_preset'] ?? 'right-sidebar'));
    }

    if ($zones === []) {
      $zones = nx_default_builder_zone_definitions();
    }

    $cache = $zones;
  }

  if (!$enabledOnly) {
    return $cache;
  }

  $enabledZones = [];
  foreach ($cache as $zone) {
    $key = (string)($zone['key'] ?? '');
    if ($key === '') {
      continue;
    }
    if (!array_key_exists('enabled', $zone) || (bool)$zone['enabled']) {
      $enabledZones[$key] = $zone;
    }
  }

  $currentPage = preg_replace('/[^A-Za-z0-9_\/-]/', '', (string)($_GET['site'] ?? 'index'));
  if ($currentPage === '') {
    $currentPage = 'index';
  }

  if (isset($GLOBALS['_database']) && $GLOBALS['_database'] instanceof mysqli) {
    $stmt = $GLOBALS['_database']->prepare("
      SELECT DISTINCT position
      FROM settings_widgets_positions
      WHERE page = ?
    ");
    if ($stmt) {
      $stmt->bind_param('s', $currentPage);
      if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
          $position = strtolower(trim((string)($row['position'] ?? '')));
          if ($position === '') {
            continue;
          }
          foreach ($cache as $zone) {
            if ((string)($zone['key'] ?? '') === $position) {
              $enabledZones[$position] = array_merge($zone, ['enabled' => true]);
              break;
            }
          }
        }
      }
      $stmt->close();
    }
  }

  return array_values($enabledZones);
}

function nx_get_active_theme_zone_keys(bool $enabledOnly = true): array {
  return array_values(array_map(static function (array $zone): string {
    return (string)$zone['key'];
  }, nx_get_active_theme_builder_zones($enabledOnly)));
}
