<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\AccessControl;
use nexpell\ThemeManager;

AccessControl::checkAdminAccess('ac_theme');

if (!class_exists('nexpell\\ThemeManager')) {
    require_once __DIR__ . '/../system/classes/ThemeManager.php';
}

$db = $GLOBALS['_database'] ?? null;
if (!$db instanceof mysqli) {
    echo '<div class="alert alert-danger">Keine Datenbankverbindung verfuegbar.</div>';
    return;
}

$themeManager = $GLOBALS['nx_theme_manager'] ?? new ThemeManager($db, dirname(__DIR__) . '/includes/themes', '/includes/themes');
$themeManager->ensureSchema();

$themes = $themeManager->getAllThemes();
$activeTheme = $themeManager->getActiveThemeRow();
$activeManifest = $themeManager->getActiveManifest();
$activeThemePath = $themeManager->getActiveThemePath();

$bootswatchVariants = [];
if (!empty($activeManifest['settings']['supports_bootswatch_variant'])) {
    $distDir = $activeThemePath . '/css/dist';
    if (is_dir($distDir)) {
        $entries = scandir($distDir);
        foreach ($entries ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (is_dir($distDir . '/' . $entry) && file_exists($distDir . '/' . $entry . '/bootstrap.min.css')) {
                $bootswatchVariants[] = $entry;
            }
        }
        sort($bootswatchVariants, SORT_NATURAL | SORT_FLAG_CASE);
    }
}

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
        'assets/img/profile/profile-1.webp',
        'assets/img/profile/profile-bg-5.webp',
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

$activePreview = $previewForTheme([
    'preview' => $activeManifest['preview'] ?? '',
    'web_path' => $themeManager->getActiveThemeWebPath(),
]);

$navbarClass = (string)($activeTheme['navbar_class'] ?? 'bg-dark');
$navbarTheme = (string)($activeTheme['navbar_theme'] ?? 'dark');
$themeVariant = (string)($activeTheme['themename'] ?? 'yeti');
$logoPic = (string)($activeTheme['logo_pic'] ?? 'default_logo.png');
$regPic = (string)($activeTheme['reg_pic'] ?? 'default_login_bg.jpg');
$headlines = (string)($activeTheme['headlines'] ?? 'headlines_03.css');

echo '<div class="card shadow-sm border-0 mb-4 mt-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-palette"></i>
            <span>Theme-Verwaltung</span>
            <small class="small-muted">Manifest-basierte Themes mit lokalen Assets und Fremdtemplate-Support</small>
        </div>
    </div>
    <div class="card-body">';

echo '<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="admincenter.php?site=theme_designer" class="btn btn-primary">Theme Designer</a>
    <a href="admincenter.php?site=theme_installer" class="btn btn-outline-secondary">Theme-Dateien</a>
</div>';

echo '<div class="alert alert-info mb-4">
    Aktives Theme: <strong>' . htmlspecialchars((string)($activeManifest['name'] ?? $activeTheme['name'] ?? 'Default'), ENT_QUOTES, 'UTF-8') . '</strong>
    <br>Slug: <code>' . htmlspecialchars($themeManager->getActiveThemeSlug(), ENT_QUOTES, 'UTF-8') . '</code>
    <br>Layout: <code>' . htmlspecialchars($themeManager->getLayoutFile(), ENT_QUOTES, 'UTF-8') . '</code>
    <br>Template-Verzeichnis: <code>' . htmlspecialchars($themeManager->getTemplateDirectory(), ENT_QUOTES, 'UTF-8') . '</code>
</div>';

echo '<div class="row g-4">';
echo '<div class="col-xl-8">';
echo '<div class="row g-4">';

foreach ($themes as $theme) {
    $preview = $previewForTheme($theme);
    $isActive = !empty($theme['active']);
    $cardClass = $isActive ? 'border-primary shadow' : 'border-0 shadow-sm';
    $button = $isActive
        ? '<button type="button" class="btn btn-outline-secondary" disabled>Aktiv</button>'
        : '<button type="button" class="btn btn-primary js-theme-activate" data-theme-slug="' . htmlspecialchars((string)$theme['slug'], ENT_QUOTES, 'UTF-8') . '">Aktivieren</button>';

    echo '<div class="col-md-6">
        <div class="card h-100 ' . $cardClass . '">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">' . htmlspecialchars((string)$theme['name'], ENT_QUOTES, 'UTF-8') . '</h5>
                        <div class="text-muted small"><code>' . htmlspecialchars((string)$theme['slug'], ENT_QUOTES, 'UTF-8') . '</code></div>
                    </div>
                    ' . ($isActive ? '<span class="badge text-bg-primary">Aktiv</span>' : '') . '
                </div>';

    if ($preview !== '') {
        echo '<div class="ratio ratio-16x9 bg-light border rounded overflow-hidden mb-3">
                <img src="' . htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars((string)$theme['name'], ENT_QUOTES, 'UTF-8') . '" style="object-fit:cover;width:100%;height:100%;">
              </div>';
    } else {
        echo '<div class="ratio ratio-16x9 border rounded d-flex flex-column align-items-center justify-content-center text-muted mb-3" style="background:linear-gradient(135deg,#f7f7f8 0%,#eceef2 100%);">
                <strong class="mb-1">' . htmlspecialchars((string)$theme['name'], ENT_QUOTES, 'UTF-8') . '</strong>
                <span class="small">Keine Vorschau gefunden</span>
              </div>';
    }

    echo '<p class="small text-muted flex-grow-1">' . htmlspecialchars((string)($theme['description'] ?? 'Keine Beschreibung vorhanden.'), ENT_QUOTES, 'UTF-8') . '</p>
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <small class="text-muted">Layout: <code>' . htmlspecialchars((string)($theme['layout']['file'] ?? 'index.php'), ENT_QUOTES, 'UTF-8') . '</code></small>
                    ' . $button . '
                </div>
            </div>
        </div>
    </div>';
}

echo '</div>';
echo '</div>';

echo '<div class="col-xl-4">';
echo '<div class="card border-0 shadow-sm mb-4">
    <div class="card-header">
        <div class="card-title">
            <i class="bi bi-sliders"></i>
            <span>Theme-Einstellungen</span>
            <small class="small-muted">Aktives Theme konfigurieren</small>
        </div>
    </div>
    <div class="card-body">';

if ($activePreview !== '') {
    echo '<div class="ratio ratio-16x9 bg-light border rounded overflow-hidden mb-3">
            <img src="' . htmlspecialchars($activePreview, ENT_QUOTES, 'UTF-8') . '" alt="Aktives Theme" style="object-fit:cover;width:100%;height:100%;">
          </div>';
} else {
    echo '<div class="ratio ratio-16x9 border rounded d-flex flex-column align-items-center justify-content-center text-muted mb-3" style="background:linear-gradient(135deg,#f7f7f8 0%,#eceef2 100%);">
            <strong class="mb-1">' . htmlspecialchars((string)($activeManifest['name'] ?? 'Theme'), ENT_QUOTES, 'UTF-8') . '</strong>
            <span class="small">Keine Vorschau gefunden</span>
          </div>';
}

echo '<form id="theme-settings-form">
        <input type="hidden" name="theme_slug" value="' . htmlspecialchars($themeManager->getActiveThemeSlug(), ENT_QUOTES, 'UTF-8') . '">';

if (!empty($bootswatchVariants)) {
    echo '<div class="mb-3">
            <label class="form-label">Bootstrap-Variante</label>
            <select class="form-select" name="bootswatch_variant">';
    foreach ($bootswatchVariants as $variant) {
        $selected = $variant === $themeVariant ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($variant, ENT_QUOTES, 'UTF-8') . '"' . $selected . '>' . htmlspecialchars(ucfirst($variant), ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo '</select>
        </div>';
}

echo '<div class="mb-3">
        <label class="form-label">Navbar-Stil</label>
        <select class="form-select" name="navbar_class">
            <option value="bg-dark"' . ($navbarClass === 'bg-dark' ? ' selected' : '') . '>Dunkel</option>
            <option value="bg-light"' . ($navbarClass === 'bg-light' ? ' selected' : '') . '>Hell</option>
            <option value="bg-primary"' . ($navbarClass === 'bg-primary' ? ' selected' : '') . '>Primary</option>
            <option value="bg-body-tertiary"' . ($navbarClass === 'bg-body-tertiary' ? ' selected' : '') . '>Body tertiary</option>
            <option value="shadow-sm"' . ($navbarClass === 'shadow-sm' ? ' selected' : '') . '>Shadow</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Navbar-Theme</label>
        <select class="form-select" name="navbar_theme">
            <option value="light"' . ($navbarTheme === 'light' ? ' selected' : '') . '>Light</option>
            <option value="dark"' . ($navbarTheme === 'dark' ? ' selected' : '') . '>Dark</option>
            <option value="auto"' . ($navbarTheme === 'auto' ? ' selected' : '') . '>Auto</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Logo-Datei</label>
        <input class="form-control" type="text" name="logo_pic" value="' . htmlspecialchars($logoPic, ENT_QUOTES, 'UTF-8') . '">
    </div>

    <div class="mb-3">
        <label class="form-label">Login-Hintergrund</label>
        <input class="form-control" type="text" name="reg_pic" value="' . htmlspecialchars($regPic, ENT_QUOTES, 'UTF-8') . '">
    </div>

    <div class="mb-3">
        <label class="form-label">Headline-Datei</label>
        <input class="form-control" type="text" name="headlines" value="' . htmlspecialchars($headlines, ENT_QUOTES, 'UTF-8') . '">
    </div>

    <button type="submit" class="btn btn-primary w-100">Einstellungen speichern</button>
    <div id="theme-save-status" class="small mt-3 text-muted"></div>
    </form>';

echo '</div></div></div></div></div></div>';

echo <<<HTML
<script>
(function () {
  const statusEl = document.getElementById('theme-save-status');

  async function postThemeData(formData) {
    const response = await fetch('theme_save.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    });

    const text = await response.text();
    if (!response.ok || text.trim() !== 'OK') {
      throw new Error(text || 'Speichern fehlgeschlagen.');
    }
  }

  document.querySelectorAll('.js-theme-activate').forEach((button) => {
    button.addEventListener('click', async () => {
      const themeSlug = button.getAttribute('data-theme-slug');
      const formData = new FormData();
      formData.append('theme_slug', themeSlug);

      button.disabled = true;
      const oldText = button.textContent;
      button.textContent = 'Aktiviere...';

      try {
        await postThemeData(formData);
        window.location.reload();
      } catch (error) {
        alert(error.message);
        button.disabled = false;
        button.textContent = oldText;
      }
    });
  });

  const settingsForm = document.getElementById('theme-settings-form');
  if (settingsForm) {
    settingsForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      statusEl.textContent = 'Speichere...';

      try {
        await postThemeData(new FormData(settingsForm));
        statusEl.textContent = 'Gespeichert. Bitte Seite neu laden.';
      } catch (error) {
        statusEl.textContent = error.message;
      }
    });
  }
})();
</script>
HTML;
