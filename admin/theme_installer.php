<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

\nexpell\AccessControl::checkAdminAccess('ac_theme_installer');

if (!class_exists('nexpell\\ThemeManager')) {
    require_once __DIR__ . '/../system/classes/ThemeManager.php';
}
if (!class_exists('nexpell\\ThemeUninstaller')) {
    require_once __DIR__ . '/../system/classes/ThemeUninstaller.php';
}

$db = $GLOBALS['_database'] ?? null;
if (!$db instanceof mysqli) {
    echo '<div class="alert alert-danger">Keine Datenbankverbindung verfuegbar.</div>';
    return;
}

$themeManager = $GLOBALS['nx_theme_manager'] ?? new \nexpell\ThemeManager($db, dirname(__DIR__) . '/includes/themes', '/includes/themes');
$themeManager->ensureSchema();

$action = (string)($_GET['action'] ?? '');

if ($action === 'upload') {
    define('THEME_INSTALLER_CONTEXT', true);
    include __DIR__ . '/theme_installer_upload.php';
    return;
}

if (isset($_GET['uninstall'])) {
    $themeSlug = strtolower(trim((string)$_GET['uninstall']));
    $themeSlug = preg_replace('/[^a-z0-9_-]/', '', $themeSlug);

    $CAPCLASS = new \nexpell\Captcha;
    $captchaHash = $_GET['captcha_hash'] ?? '';
    if (!$CAPCLASS->checkCaptcha(0, $captchaHash)) {
        nx_redirect('admincenter.php?site=theme_installer', 'danger', 'transaction_invalid', false);
    }

    if ($themeSlug === '' || $themeSlug === 'default') {
        nx_redirect('admincenter.php?site=theme_installer', 'danger', 'Standard-Theme kann nicht deinstalliert werden.', true, true);
    }

    if ($themeManager->getActiveThemeSlug() === $themeSlug) {
        nx_redirect('admincenter.php?site=theme_installer', 'danger', 'Aktives Theme kann nicht deinstalliert werden. Bitte zuerst ein anderes Theme aktivieren.', true, true);
    }

    $uninstaller = new \nexpell\ThemeUninstaller();
    $uninstaller->uninstall($themeSlug);

    nx_audit_action(
        'theme_installer',
        'audit_action_theme_uninstalled',
        $themeSlug,
        null,
        'admincenter.php?site=theme_installer',
        ['theme' => $themeSlug]
    );

    nx_redirect('admincenter.php?site=theme_installer', 'success', 'Theme wurde entfernt.', true, true);
}

$themes = $themeManager->getAllThemes();
$activeSlug = $themeManager->getActiveThemeSlug();

$previewForTheme = static function (array $theme): string {
    $preview = trim((string)($theme['preview'] ?? ''));
    $themePath = rtrim((string)($theme['path'] ?? ''), '/\\');
    $webPath = rtrim((string)($theme['web_path'] ?? ''), '/');

    $resolve = static function (string $candidate) use ($themePath, $webPath): string {
        if ($candidate === '') {
            return '';
        }
        if (preg_match('#^(https?:)?//#i', $candidate) || str_starts_with($candidate, '/')) {
            return $candidate;
        }
        if ($themePath !== '' && file_exists($themePath . '/' . ltrim($candidate, '/\\'))) {
            return $webPath . '/' . ltrim(str_replace('\\', '/', $candidate), '/');
        }
        return '';
    };

    $resolved = $resolve($preview);
    if ($resolved !== '') {
        return $resolved;
    }

    $fallbacks = [
        'screenshot.png',
        'screenshot.jpg',
        'screenshot.jpeg',
        'preview.png',
        'preview.jpg',
        'preview.jpeg',
        'images/preview.png',
        'images/preview.jpg',
        'images/preview.jpeg',
        'assets/img/preview.png',
        'assets/img/preview.jpg',
        'assets/img/preview.jpeg',
        'assets/img/hero-bg.jpg',
        'assets/img/hero-bg.jpeg',
        'assets/img/hero-bg.png',
        'assets/img/hero.jpg',
        'assets/img/hero.png',
        'assets/img/about/about-18.webp',
        'assets/img/about/about-portrait-7.webp',
        'assets/img/profile/profile-1.webp',
        'assets/img/profile/profile-bg-5.webp',
        'assets/img/logo.webp',
        'images/default_logo.png',
    ];

    foreach ($fallbacks as $candidate) {
        $resolved = $resolve($candidate);
        if ($resolved !== '') {
            return $resolved;
        }
    }

    return '';
};

echo '<div class="card shadow-sm border-0 mb-4 mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-box-seam"></i>
            <span>Theme-Import und Theme-Dateien</span>
            <small class="small-muted">ZIP-Import fuer lokale und externe Templates</small>
        </div>
    </div>
    <div class="card-body">';

echo '<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
        <div class="text-muted">
            Erwartete Struktur: <code>includes/themes/&lt;slug&gt;/theme.json</code> mit Assets unter <code>assets/</code>, <code>css/</code>, <code>js/</code> oder <code>vendor/</code>.
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="admincenter.php?site=theme_designer" class="btn btn-outline-primary">Theme Designer</a>
            <a href="admincenter.php?site=theme" class="btn btn-outline-secondary">Theme-Verwaltung</a>
            <a href="admincenter.php?site=theme_installer&action=upload&mode=generate" class="btn btn-outline-primary">Eigenes Theme erzeugen</a>
            <a href="admincenter.php?site=theme_installer&action=upload" class="btn btn-primary">Theme importieren</a>
        </div>
      </div>';

if (empty($themes)) {
    echo '<div class="alert alert-info mb-0">Es wurden keine Theme-Ordner gefunden.</div>';
    echo '</div></div>';
    return;
}

echo '<div class="row g-4">';

$CAPCLASS = new \nexpell\Captcha;
$CAPCLASS->createTransaction();
$hash = $CAPCLASS->getHash();

foreach ($themes as $theme) {
    $slug = (string)($theme['slug'] ?? '');
    $name = (string)($theme['name'] ?? $slug);
    $preview = $previewForTheme($theme);
    $description = trim((string)($theme['description'] ?? ''));
    $description = $description !== '' ? $description : 'Keine Beschreibung vorhanden.';
    $layoutFile = (string)($theme['layout']['file'] ?? 'index.php');
    $isActive = $slug === $activeSlug;
    $canUninstall = !$isActive && $slug !== 'default';
    $themePath = (string)($theme['path'] ?? '');

    echo '<div class="col-xl-4 col-md-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</h5>
                        <div class="small text-muted"><code>' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '</code></div>
                    </div>
                    ' . ($isActive ? '<span class="badge text-bg-primary">Aktiv</span>' : '<span class="badge text-bg-light">Installiert</span>') . '
                </div>';

    if ($preview !== '') {
        echo '<div class="ratio ratio-16x9 bg-light border rounded overflow-hidden mb-3">
                <img src="' . htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" style="object-fit:cover;width:100%;height:100%;">
              </div>';
    } else {
        echo '<div class="ratio ratio-16x9 bg-light border rounded d-flex align-items-center justify-content-center text-muted mb-3">
                Keine Vorschau
              </div>';
    }

    echo '<div class="small text-muted mb-3">' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '</div>
          <div class="small mb-1"><strong>Layout:</strong> <code>' . htmlspecialchars($layoutFile, ENT_QUOTES, 'UTF-8') . '</code></div>
          <div class="small mb-3"><strong>Ordner:</strong> <code>' . htmlspecialchars($themePath, ENT_QUOTES, 'UTF-8') . '</code></div>
          <div class="mt-auto d-flex flex-wrap gap-2">
            <a href="admincenter.php?site=theme_preview&theme=' . rawurlencode($slug) . '" class="btn btn-outline-secondary btn-sm">Vorschau</a>
            <a href="admincenter.php?site=theme_installer&action=upload&edit=' . rawurlencode($slug) . '" class="btn btn-outline-primary btn-sm">Bearbeiten</a>';

    if ($canUninstall) {
        $deleteUrl = 'admincenter.php?site=theme_installer&uninstall=' . rawurlencode($slug) . '&captcha_hash=' . rawurlencode($hash);
        echo '<button type="button"
                    class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmDeleteModal"
                    data-delete-url="' . htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') . '">Deinstallieren</button>';
    } else {
        echo '<button type="button" class="btn btn-outline-secondary btn-sm" disabled>Geschuetzt</button>';
    }

    echo '  </div>
          </div>
        </div>
    </div>';
}

echo '</div></div></div>';

echo '<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Theme deinstallieren</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schliessen"></button>
            </div>
            <div class="modal-body">
                Das Theme wird aus <code>includes/themes/&lt;slug&gt;</code> und aus den Theme-Tabellen entfernt. Fortfahren?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <a href="#" class="btn btn-danger" id="confirmDeleteThemeLink">Jetzt deinstallieren</a>
            </div>
        </div>
    </div>
</div>';

echo <<<HTML
<script>
(function () {
  const modal = document.getElementById('confirmDeleteModal');
  const confirmLink = document.getElementById('confirmDeleteThemeLink');

  if (!modal || !confirmLink) {
    return;
  }

  modal.addEventListener('show.bs.modal', function (event) {
    const trigger = event.relatedTarget;
    if (!trigger) {
      confirmLink.setAttribute('href', '#');
      return;
    }

    const deleteUrl = trigger.getAttribute('data-delete-url') || '#';
    confirmLink.setAttribute('href', deleteUrl);
  });
})();
</script>
HTML;
