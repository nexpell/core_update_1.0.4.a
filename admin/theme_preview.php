<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

\nexpell\AccessControl::checkAdminAccess('ac_theme_preview');

require_once __DIR__ . '/../system/config.inc.php';
require_once __DIR__ . '/../system/classes/ThemeManager.php';

$db = $GLOBALS['_database'] ?? null;
if (!$db instanceof mysqli) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
        die('DB-Fehler: ' . htmlspecialchars($db->connect_error, ENT_QUOTES, 'UTF-8'));
    }
    $db->set_charset('utf8mb4');
}

$themeManager = $GLOBALS['nx_theme_manager'] ?? new \nexpell\ThemeManager($db, dirname(__DIR__) . '/includes/themes', '/includes/themes');
$themeManager->ensureSchema();

$themeSlug = preg_replace('/[^a-z0-9_-]/', '', strtolower((string)($_GET['theme'] ?? $themeManager->getActiveThemeSlug())));
$theme = $themeManager->getThemeBySlug($themeSlug);

if ($theme === null) {
    echo '<div class="alert alert-danger">Theme nicht gefunden.</div>';
    return;
}

$manifest = [];
$themeDir = dirname(__DIR__) . '/includes/themes/' . $themeSlug;
$manifestFile = $themeDir . '/theme.json';
if (file_exists($manifestFile)) {
    $decoded = json_decode((string)file_get_contents($manifestFile), true);
    if (is_array($decoded)) {
        $manifest = $decoded;
    }
}

$preview = trim((string)($theme['preview'] ?? ''));
if ($preview !== '' && !preg_match('#^(https?:)?//#i', $preview) && !str_starts_with($preview, '/')) {
    $preview = rtrim((string)$theme['web_path'], '/') . '/' . ltrim($preview, '/');
}

$cssAssets = [];
foreach ((array)($manifest['assets']['css'] ?? []) as $asset) {
    $asset = trim((string)$asset);
    if ($asset === '') {
        continue;
    }
    $cssAssets[] = str_starts_with($asset, '/') ? $asset : rtrim((string)$theme['web_path'], '/') . '/' . ltrim($asset, '/');
}

$jsAssets = [];
foreach ((array)($manifest['assets']['js'] ?? []) as $asset) {
    $asset = trim((string)$asset);
    if ($asset === '') {
        continue;
    }
    $jsAssets[] = str_starts_with($asset, '/') ? $asset : rtrim((string)$theme['web_path'], '/') . '/' . ltrim($asset, '/');
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Theme-Vorschau: <?= htmlspecialchars((string)($theme['name'] ?? $themeSlug), ENT_QUOTES, 'UTF-8') ?></title>
<?php foreach ($cssAssets as $asset): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($asset, ENT_QUOTES, 'UTF-8') ?>">
<?php endforeach; ?>
  <style>
    body { background:#f5f7fb; color:#212529; }
    .preview-shell { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem 4rem; }
    .preview-card { border: 0; border-radius: 1rem; overflow: hidden; box-shadow: 0 1rem 2rem rgba(15, 23, 42, 0.08); }
    .preview-hero { min-height: 280px; display: grid; place-items: center; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; padding: 2rem; text-align: center; }
    .preview-meta code { font-size: .875rem; }
  </style>
</head>
<body>
  <div class="preview-shell">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
      <div>
        <h1 class="h3 mb-1"><?= htmlspecialchars((string)($theme['name'] ?? $themeSlug), ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="text-muted preview-meta">
          <span class="me-3">Slug: <code><?= htmlspecialchars($themeSlug, ENT_QUOTES, 'UTF-8') ?></code></span>
          <span>Layout: <code><?= htmlspecialchars((string)($theme['layout']['file'] ?? 'index.php'), ENT_QUOTES, 'UTF-8') ?></code></span>
        </div>
      </div>
      <div class="d-flex gap-2">
        <a href="admincenter.php?site=theme_installer" class="btn btn-outline-secondary">Zurueck</a>
        <a href="admincenter.php?site=theme" class="btn btn-primary">Theme-Verwaltung</a>
      </div>
    </div>

    <?php if ($preview !== ''): ?>
      <div class="preview-card mb-4">
        <img src="<?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string)($theme['name'] ?? $themeSlug), ENT_QUOTES, 'UTF-8') ?>" style="display:block;width:100%;height:auto;">
      </div>
    <?php endif; ?>

    <div class="preview-card bg-white mb-4">
      <div class="preview-hero">
        <div>
          <div class="text-uppercase small mb-2">Nexpell Theme Preview</div>
          <h2 class="display-6 mb-3"><?= htmlspecialchars((string)($theme['name'] ?? $themeSlug), ENT_QUOTES, 'UTF-8') ?></h2>
          <p class="lead mb-0"><?= htmlspecialchars((string)($theme['description'] ?? 'Theme-Vorschau fuer Navigation, Karten und Buttons.'), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
      </div>

      <div class="p-4 p-lg-5">
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
              <div class="card-body">
                <h3 class="h5 mb-3">Inhaltstest</h3>
                <p>Diese Vorschau zeigt, ob eingebundene CSS- und JavaScript-Dateien eines Fremdtemplates korrekt geladen werden.</p>
                <div class="d-flex flex-wrap gap-2">
                  <button class="btn btn-primary">Primary</button>
                  <button class="btn btn-secondary">Secondary</button>
                  <button class="btn btn-outline-dark">Outline</button>
                </div>
                <hr>
                <div class="alert alert-info mb-0">Wenn Farben, Buttons und Abstaende hier sauber aussehen, ist das Theme-Manifest technisch korrekt angebunden.</div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
              <div class="card-body">
                <h3 class="h6">Geladene Assets</h3>
                <ul class="small mb-0">
                  <?php foreach ($cssAssets as $asset): ?>
                    <li><?= htmlspecialchars($asset, ENT_QUOTES, 'UTF-8') ?></li>
                  <?php endforeach; ?>
                  <?php foreach ($jsAssets as $asset): ?>
                    <li><?= htmlspecialchars($asset, ENT_QUOTES, 'UTF-8') ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php foreach ($jsAssets as $asset): ?>
  <script src="<?= htmlspecialchars($asset, ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endforeach; ?>
</body>
</html>
