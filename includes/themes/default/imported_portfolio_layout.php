<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once BASE_PATH . '/system/core/init.php';
require_once BASE_PATH . '/system/core/theme_builder_helper.php';
require_once BASE_PATH . '/system/core/builder_live.php';
require_once BASE_PATH . '/system/core/theme_options.php';

global $hp_title, $hp_description, $theme_web_path, $theme_favicons;

$importedThemeSlug = isset($importedThemeSlug) ? (string)$importedThemeSlug : 'default';
$pageSlug = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)($_GET['site'] ?? 'index'));
if ($pageSlug === '') {
    $pageSlug = 'index';
}

$siteTitle = trim((string)($hp_title ?? ucfirst($importedThemeSlug)));
$siteDescription = trim((string)($hp_description ?? 'Modernes CMS mit importiertem Template-Layout'));
$themePath = rtrim((string)($theme_web_path ?? ('/includes/themes/' . $importedThemeSlug)), '/');
$faviconIco = (string)($theme_favicons['ico'] ?? ($themePath . '/assets/img/favicon.png'));
$faviconPng = (string)($theme_favicons['png32'] ?? ($themePath . '/assets/img/favicon.png'));
$appleTouch = (string)($theme_favicons['apple180'] ?? ($themePath . '/assets/img/apple-touch-icon.png'));
$displayName = !empty($_SESSION['username']) ? (string)$_SESSION['username'] : $siteTitle;
$mainContent = (string)get_mainContent();
$contentTitle = !empty($_GET['site']) ? ucfirst(str_replace('_', ' ', (string)$_GET['site'])) : 'Content';
$isEasyFolio = $importedThemeSlug === 'easyfolio';
$templateColorMode = $isEasyFolio ? 'light' : 'dark';

$themeManager = $GLOBALS['nx_theme_manager'] ?? null;
$themeRuntime = $themeManager instanceof \nexpell\ThemeManager
    ? nx_theme_builder_runtime_settings($themeManager, $importedThemeSlug)
    : nx_theme_builder_generator_defaults(['name' => $siteTitle], $siteTitle);
$runtimeColors = is_array($themeRuntime['colors'] ?? null) ? $themeRuntime['colors'] : [];
$themeBg = function_exists('nx_get_theme_option') ? nx_get_theme_option('bg_color', '') : '';
$themeText = function_exists('nx_get_theme_option') ? nx_get_theme_option('text_color', '') : '';
$themePrimary = function_exists('nx_get_theme_option') ? nx_get_theme_option('primary', '') : '';
$themeSecondary = function_exists('nx_get_theme_option') ? nx_get_theme_option('secondary', '') : '';
$themeLink = function_exists('nx_get_theme_option') ? nx_get_theme_option('link_color', '') : '';
$themeHover = function_exists('nx_get_theme_option') ? nx_get_theme_option('link_hover_color', '') : '';
if ($themeBg !== '') {
    $runtimeColors['page_bg'] = $themeBg;
}
if ($themeText !== '') {
    $runtimeColors['text'] = $themeText;
}
if ($themePrimary !== '') {
    $runtimeColors['accent'] = $themePrimary;
}
if ($themeSecondary !== '') {
    $runtimeColors['surface'] = $themeSecondary;
}
$cardBackground = (string)($themeRuntime['card_background'] ?? ($runtimeColors['surface'] ?? '#ffffff'));
$cardBorderWidth = (string)($themeRuntime['card_border_width'] ?? '1');
$cardBorderColor = (string)($themeRuntime['card_border_color'] ?? '#d7dee8');
$navBackground = (string)($themeRuntime['nav_background'] ?? '#ffffff');
$navLink = (string)($themeLink !== '' ? $themeLink : ($themeRuntime['nav_link'] ?? ($runtimeColors['text'] ?? '#d9d9d9')));
$navHover = (string)($themeHover !== '' ? $themeHover : ($themeRuntime['nav_hover'] ?? ($runtimeColors['accent'] ?? '#ff4d4f')));
$navDropdownBackground = (string)($themeRuntime['nav_dropdown_background'] ?? ($runtimeColors['surface'] ?? '#1c1c1c'));

$widgetsByPosition = function_exists('nxb_prepare_builder') ? nxb_prepare_builder($pageSlug) : [];
$builderOverlayHtml = (string)($GLOBALS['nxb_live_overlay_html'] ?? '');
$isBuilder = isset($_GET['builder']) && $_GET['builder'] === '1';
$showZone = static function (string $zone) use ($widgetsByPosition, $isBuilder): bool {
    return $isBuilder || !empty($widgetsByPosition[$zone]);
};
$renderZone = static function (string $zone) use ($widgetsByPosition, $showZone): string {
    if (!$showZone($zone)) {
        return '';
    }
    return '<div class="nx-imported-zone nx-live-zone nx-zone" data-nx-zone="' . htmlspecialchars($zone, ENT_QUOTES, 'UTF-8') . '">' . implode('', $widgetsByPosition[$zone] ?? []) . '</div>';
};

$hasLeft = $showZone('left');
$hasRight = $showZone('right');
$mainColClass = ($hasLeft && $hasRight) ? 'col-lg-6' : (($hasLeft || $hasRight) ? 'col-lg-9' : 'col-12');
$sideColClass = 'col-lg-3';

if ($isEasyFolio) {
    $heroImage = $themePath . '/assets/img/profile/profile-1.webp';
} else {
    $heroImage = $themePath . '/assets/img/profile/profile-bg-5.webp';
}
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="<?= htmlspecialchars($templateColorMode, ENT_QUOTES, 'UTF-8') ?>">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8') ?>">
  <link rel="icon" href="<?= htmlspecialchars($faviconIco, ENT_QUOTES, 'UTF-8') ?>">
  <link rel="icon" type="image/png" href="<?= htmlspecialchars($faviconPng, ENT_QUOTES, 'UTF-8') ?>">
  <link rel="apple-touch-icon" href="<?= htmlspecialchars($appleTouch, ENT_QUOTES, 'UTF-8') ?>">

  <?php if ($isEasyFolio): ?>
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Noto+Sans:wght@300;400;500;600;700;800&family=Questrial&display=swap" rel="stylesheet">
  <?php else: ?>
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Raleway:wght@400;500;600;700;800&family=Mulish:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <?php endif; ?>

  <?= $components_css ?? '' ?>
  <?= $plugin_css ?? '' ?>
  <?= $theme_css ?? '' ?>
  <?php
  if (function_exists('nx_render_theme_options_css')) {
      echo nx_render_theme_options_css();
  }
  ?>

  <style>
    :root {
      --background-color: var(--bs-body-bg, <?= htmlspecialchars((string)($runtimeColors['page_bg'] ?? ($isEasyFolio ? '#ffffff' : '#141414')), ENT_QUOTES, 'UTF-8') ?>);
      --default-color: var(--bs-body-color, <?= htmlspecialchars((string)($runtimeColors['text'] ?? ($isEasyFolio ? '#111827' : '#d9d9d9')), ENT_QUOTES, 'UTF-8') ?>);
      --heading-color: var(--bs-body-color, <?= htmlspecialchars((string)($runtimeColors['text'] ?? ($isEasyFolio ? '#111827' : '#ededed')), ENT_QUOTES, 'UTF-8') ?>);
      --accent-color: var(--bs-primary, <?= htmlspecialchars((string)($runtimeColors['accent'] ?? '#ff4d4f'), ENT_QUOTES, 'UTF-8') ?>);
      --surface-color: <?= htmlspecialchars((string)($runtimeColors['surface'] ?? ($isEasyFolio ? '#ffffff' : '#1c1c1c')), ENT_QUOTES, 'UTF-8') ?>;
      --nav-color: var(--bs-link-color, <?= htmlspecialchars($navLink, ENT_QUOTES, 'UTF-8') ?>);
      --nav-hover-color: var(--bs-link-hover-color, <?= htmlspecialchars($navHover, ENT_QUOTES, 'UTF-8') ?>);
      --nav-dropdown-background-color: <?= htmlspecialchars($navDropdownBackground, ENT_QUOTES, 'UTF-8') ?>;
      --nx-theme-bg: var(--background-color);
      --nx-theme-surface: var(--surface-color);
      --nx-theme-text: var(--default-color);
      --nx-theme-heading: var(--heading-color);
      --nx-theme-accent: var(--accent-color);
      --nx-theme-accent-soft: color-mix(in srgb, var(--nx-theme-accent), white 18%);
      --nx-theme-nav-link: var(--nav-color, var(--nx-theme-text));
      --nx-theme-nav-hover: var(--nav-hover-color, var(--nx-theme-accent));
      --nx-theme-nav-dropdown-bg: var(--nav-dropdown-background-color, var(--nx-theme-surface));
      --nx-theme-border: color-mix(in srgb, var(--nx-theme-text), transparent 90%);
      --nx-theme-border-strong: color-mix(in srgb, var(--nx-theme-text), transparent 76%);
      --nx-theme-hover-surface: color-mix(in srgb, var(--nx-theme-text), transparent 94%);
      --nx-theme-focus-ring: color-mix(in srgb, var(--nx-theme-accent), transparent 82%);
      --nx-theme-muted: color-mix(in srgb, var(--nx-theme-text), transparent 30%);
      --nx-theme-card-radius: 16px;
    }

    html,
    body.index-page {
      background: var(--nx-theme-bg);
      color: var(--nx-theme-text);
    }

    #mainNavbar {
      transition: background-color .25s ease, border-color .25s ease, box-shadow .25s ease;
      background: var(--bs-body-bg, <?= htmlspecialchars($navBackground, ENT_QUOTES, 'UTF-8') ?>) !important;
      backdrop-filter: blur(10px);
      box-shadow: none !important;
    }

    <?php if ($isEasyFolio): ?>
    #mainNavbar {
      border-bottom: 1px solid rgba(17, 24, 39, 0.08);
    }
    #mainNavbar .nav-link,
    #mainNavbar .navbar-brand,
    #mainNavbar .dropdown-item {
      color: var(--nav-color) !important;
    }
    <?php else: ?>
    #mainNavbar {
      border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    #mainNavbar .nav-link,
    #mainNavbar .navbar-brand,
    #mainNavbar .dropdown-item,
    #mainNavbar .navbar-toggler {
      color: var(--nx-theme-nav-link) !important;
    }
    #mainNavbar .navbar-toggler {
      border-color: rgba(255,255,255,0.2) !important;
    }
    #mainNavbar .dropdown-menu {
      background: var(--nx-theme-nav-dropdown-bg) !important;
      border-color: var(--nx-theme-border) !important;
    }
    #mainNavbar .dropdown-item:hover,
    #mainNavbar .dropdown-item:focus,
    #mainNavbar .nav-link:hover,
    #mainNavbar .nav-link:focus {
      color: var(--nx-theme-nav-hover) !important;
    }
    <?php endif; ?>

    #mainNavbar .nav-link.active,
    #mainNavbar .nav-link.show,
    #mainNavbar .dropdown-item.active {
      color: var(--nx-theme-nav-hover) !important;
      background: transparent !important;
    }

    #mainNavbar .badge.bg-danger {
      background: var(--nx-theme-accent) !important;
      border-color: var(--nx-theme-accent) !important;
    }

    .nx-imported-zone {
      margin-bottom: 1.5rem;
    }

    .nx-imported-content .card,
    .nx-imported-content .panel,
    .nx-imported-content .box,
    .nx-imported-content .card-body,
    .nx-imported-content .panel-body,
    .nx-imported-content .box-body,
    .nx-imported-content .card-content,
    .nx-imported-content .content-box {
      border-radius: var(--nx-theme-card-radius);
      box-shadow: none;
    }

    <?php if ($isEasyFolio): ?>
    .nx-imported-content .card,
    .nx-imported-content .panel,
    .nx-imported-content .box {
      background: <?= htmlspecialchars($cardBackground, ENT_QUOTES, 'UTF-8') ?>;
      border: <?= htmlspecialchars($cardBorderWidth, ENT_QUOTES, 'UTF-8') ?>px solid <?= htmlspecialchars($cardBorderColor, ENT_QUOTES, 'UTF-8') ?>;
    }
    .nx-imported-content .form-control,
    .nx-imported-content .form-select,
    .nx-imported-content textarea {
      background: #fff;
      border-color: rgba(17, 24, 39, 0.12);
      color: #111827;
    }
    <?php else: ?>
    .nx-imported-content .card,
    .nx-imported-content .panel,
    .nx-imported-content .box,
    .nx-imported-content .card-body,
    .nx-imported-content .panel-body,
    .nx-imported-content .box-body,
    .nx-imported-content .card-content,
    .nx-imported-content .content-box {
      background: <?= htmlspecialchars($cardBackground, ENT_QUOTES, 'UTF-8') ?>;
      border: <?= htmlspecialchars($cardBorderWidth, ENT_QUOTES, 'UTF-8') ?>px solid <?= htmlspecialchars($cardBorderColor, ENT_QUOTES, 'UTF-8') ?>;
    }
    .nx-imported-content .form-control,
    .nx-imported-content .form-select,
    .nx-imported-content textarea {
      background: var(--nx-theme-bg);
      border-color: var(--nx-theme-border);
      color: var(--nx-theme-text);
    }
    .nx-imported-content .table {
      color: var(--nx-theme-text);
    }
    .nx-imported-content,
    .nx-imported-content p,
    .nx-imported-content li,
    .nx-imported-content span,
    .nx-imported-content div,
    .nx-imported-content small {
      color: var(--nx-theme-text);
    }
    .nx-imported-content h1,
    .nx-imported-content h2,
    .nx-imported-content h3,
    .nx-imported-content h4,
    .nx-imported-content h5,
    .nx-imported-content h6,
    main .section-title h2,
    main .hero h2 {
      color: var(--nx-theme-heading);
    }
    <?php endif; ?>

    .nx-imported-content .btn-primary,
    .nx-imported-content button.btn-primary,
    .nx-imported-content input.btn-primary,
    .nx-imported-content .page-item.active .page-link,
    .nx-imported-content .page-link:hover,
    .nx-imported-content .badge.bg-primary {
      background: var(--nx-theme-accent) !important;
      border-color: var(--nx-theme-accent) !important;
      color: #fff !important;
      box-shadow: none !important;
    }

    .nx-imported-content .btn-primary:hover,
    .nx-imported-content button.btn-primary:hover,
    .nx-imported-content input.btn-primary:hover,
    .nx-imported-content .page-item.active .page-link:hover {
      background: var(--nx-theme-accent-soft) !important;
      border-color: var(--nx-theme-accent-soft) !important;
    }

    .nx-imported-content .btn-outline-primary,
    .nx-imported-content .page-link,
    .nx-imported-content .nav-link,
    .nx-imported-content a {
      color: var(--nx-theme-accent);
      border-color: var(--nx-theme-border-strong);
    }

    .nx-imported-content .nav-link:hover,
    .nx-imported-content a:hover {
      color: var(--nx-theme-accent-soft);
    }

    .nx-imported-content .btn-secondary,
    .nx-imported-content .btn-light,
    .nx-imported-content .btn-outline-secondary {
      background: var(--nx-theme-hover-surface) !important;
      border-color: var(--nx-theme-border-strong) !important;
      color: var(--nx-theme-text) !important;
      box-shadow: none !important;
    }

    .nx-imported-content .form-control:focus,
    .nx-imported-content .form-select:focus,
    .nx-imported-content textarea:focus {
      border-color: var(--nx-theme-accent) !important;
      box-shadow: 0 0 0 .2rem var(--nx-theme-focus-ring) !important;
    }

    .nx-imported-content .card-header,
    .nx-imported-content .panel-heading {
      background: transparent;
      border-bottom-color: var(--nx-theme-border);
    }

    footer,
    footer.footer-easy,
    footer.footer-template-standard,
    footer.footer-template-modern,
    footer.footer-template-agency,
    footer.footer-template-simple-min {
      background: var(--nx-theme-bg) !important;
      color: var(--nx-theme-text) !important;
      border-top-color: var(--nx-theme-border) !important;
    }

    footer .text-white-50,
    footer .small.text-white-50,
    footer small,
    footer .footer-text-muted {
      color: var(--nx-theme-muted) !important;
    }

    footer hr {
      border-top-color: var(--nx-theme-border) !important;
      opacity: 1 !important;
    }

    footer a,
    footer .footer-link,
    footer .footer-list a {
      color: var(--nx-theme-text) !important;
    }

    footer a:hover,
    footer .footer-link:hover,
    footer .footer-list a:hover {
      color: var(--nx-theme-accent) !important;
    }

    footer #back-to-top,
    footer #cookie-settings-icon,
    footer .footer-social-chip {
      background: transparent !important;
      color: var(--nx-theme-text) !important;
      border-color: var(--nx-theme-border-strong) !important;
      box-shadow: none !important;
    }

    footer #back-to-top:hover,
    footer #cookie-settings-icon:hover,
    footer .footer-social-chip:hover {
      background: rgba(255,255,255,0.06) !important;
      color: var(--nx-theme-accent) !important;
      border-color: color-mix(in srgb, var(--nx-theme-accent), transparent 45%) !important;
    }
  </style>
</head>
<body class="index-page theme-<?= htmlspecialchars($importedThemeSlug, ENT_QUOTES, 'UTF-8') ?>">
<?= $builderOverlayHtml ?>

<?php if ($isEasyFolio): ?>
  <?php require BASE_PATH . '/includes/themes/easyfolio/partials/navigation.php'; ?>
<?php else: ?>
  <?php $pluginManager->getNavigationModule(); ?>
<?php endif; ?>

<?php if ($isEasyFolio): ?>
  <main class="main">
    <section id="hero" class="hero section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row align-items-center content">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <h2>Crafting Digital Experiences with <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="lead">Das importierte EasyFolio-Layout laeuft als eigenes Theme-Layout und bleibt dabei editierbar.</p>
            <div class="cta-buttons" data-aos="fade-up" data-aos-delay="300">
              <a href="#content" class="btn btn-primary">Zum Inhalt</a>
              <a href="#footer" class="btn btn-outline">Footer</a>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="hero-image">
              <img src="<?= htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8') ?>" alt="" class="img-fluid" data-aos="zoom-out" data-aos-delay="300">
              <div class="shape-1"></div>
              <div class="shape-2"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php if ($showZone('top')): ?>
      <section class="section pb-0"><div class="container"><?= $renderZone('top') ?></div></section>
    <?php endif; ?>
    <?php if ($showZone('undertop')): ?>
      <section class="section pt-0 pb-0"><div class="container"><?= $renderZone('undertop') ?></div></section>
    <?php endif; ?>

    <section id="content" class="services section">
      <div class="container section-title" data-aos="fade-up">
        <h2><?= htmlspecialchars($contentTitle, ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="title-shape"><svg viewBox="0 0 200 20" xmlns="http://www.w3.org/2000/svg"><path d="M 0,10 C 40,0 60,20 100,10 C 140,0 160,20 200,10" fill="none" stroke="currentColor" stroke-width="2"></path></svg></div>
        <p>Hier erscheint ausschliesslich der in nexpell eingestellte Seiteninhalt.</p>
      </div>
      <div class="container">
        <div class="row g-4">
          <?php if ($hasLeft): ?><div class="<?= $sideColClass ?>"><?= $renderZone('left') ?></div><?php endif; ?>
          <div class="<?= $mainColClass ?>">
            <?= $renderZone('maintop') ?>
            <div class="nx-imported-content"><?= $mainContent ?></div>
            <?= $renderZone('mainbottom') ?>
          </div>
          <?php if ($hasRight): ?><div class="<?= $sideColClass ?>"><?= $renderZone('right') ?></div><?php endif; ?>
        </div>
      </div>
    </section>

    <?php if ($showZone('bottom')): ?>
      <section class="section pt-0"><div class="container"><?= $renderZone('bottom') ?></div></section>
    <?php endif; ?>
  </main>
<?php else: ?>
  <main class="main">
    <section id="hero" class="hero section dark-background">
      <img src="<?= htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8') ?>" alt="" data-aos="fade-in">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-center">
          <div class="col-lg-8 text-center">
            <h2>Hi, I'm <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></h2>
            <p>Importiertes Portfolio-Theme mit eigenem Layout.</p>
          </div>
        </div>
      </div>
    </section>

    <?php if ($showZone('top')): ?>
      <section class="section pb-0"><div class="container"><?= $renderZone('top') ?></div></section>
    <?php endif; ?>
    <?php if ($showZone('undertop')): ?>
      <section class="section pt-0 pb-0"><div class="container"><?= $renderZone('undertop') ?></div></section>
    <?php endif; ?>

    <section id="content" class="services section">
      <div class="container section-title" data-aos="fade-up">
        <span class="subtitle">Content</span>
        <h2><?= htmlspecialchars($contentTitle, ENT_QUOTES, 'UTF-8') ?></h2>
        <p>Hier erscheint ausschliesslich der in nexpell eingestellte Seiteninhalt.</p>
      </div>
      <div class="container">
        <div class="row g-4">
          <?php if ($hasLeft): ?><div class="<?= $sideColClass ?>"><?= $renderZone('left') ?></div><?php endif; ?>
          <div class="<?= $mainColClass ?>">
            <?= $renderZone('maintop') ?>
            <div class="service-item nx-imported-content"><?= $mainContent ?></div>
            <?= $renderZone('mainbottom') ?>
          </div>
          <?php if ($hasRight): ?><div class="<?= $sideColClass ?>"><?= $renderZone('right') ?></div><?php endif; ?>
        </div>
      </div>
    </section>

    <?php if ($showZone('bottom')): ?>
      <section class="section pt-0"><div class="container"><?= $renderZone('bottom') ?></div></section>
    <?php endif; ?>
  </main>
<?php endif; ?>

  <?php $pluginManager->getFooterModule(); ?>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"></div>
  <div id="cookie-overlay" style="display:none;"></div>
  <?php require_once BASE_PATH . '/components/cookie/cookie-consent.php'; ?>
  <?= $components_js ?? '' ?>
  <?= $theme_js ?? '' ?>
  <?= $plugin_js ?? '' ?>
</body>
</html>
