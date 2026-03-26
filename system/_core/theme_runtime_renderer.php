<?php
declare(strict_types=1);

require_once BASE_PATH . '/system/core/init.php';
require_once BASE_PATH . '/system/core/builder_live.php';
require_once BASE_PATH . '/system/core/theme_builder_helper.php';

$pageSlug = $_GET['site'] ?? 'index';
$widgetsByPosition = function_exists('nxb_prepare_builder') ? nxb_prepare_builder($pageSlug) : [];
$builderOverlayHtml = (string)($GLOBALS['nxb_live_overlay_html'] ?? '');
$isBuilder = isset($_GET['builder']) && $_GET['builder'] === '1';
$themeManager = $GLOBALS['nx_theme_manager'] ?? null;
$themeRuntime = $themeManager instanceof \nexpell\ThemeManager
    ? nx_theme_builder_runtime_settings($themeManager)
    : nx_theme_builder_generator_defaults([], 'Theme');

$hexToRgb = static function (string $hex, string $fallback = '255, 255, 255'): string {
    $value = ltrim(trim($hex), '#');
    if (strlen($value) === 3) {
        $value = $value[0] . $value[0] . $value[1] . $value[1] . $value[2] . $value[2];
    }
    if (!preg_match('/^[0-9a-fA-F]{6}$/', $value)) {
        return $fallback;
    }
    return implode(', ', [
        (string)hexdec(substr($value, 0, 2)),
        (string)hexdec(substr($value, 2, 2)),
        (string)hexdec(substr($value, 4, 2)),
    ]);
};

$layoutPreset = (string)($themeRuntime['layout_preset'] ?? 'right-sidebar');
$pageWidth = (string)($themeRuntime['page_width'] ?? 'wide');
$contentWidth = (string)($themeRuntime['content_width'] ?? 'normal');
$columnRatio = (string)($themeRuntime['column_ratio'] ?? '75-25');
$twoSidebarRatio = (string)($themeRuntime['two_sidebar_ratio'] ?? '3-6-3');
$sectionSpacing = (string)($themeRuntime['section_spacing'] ?? 'normal');
$cardStyle = (string)($themeRuntime['card_style'] ?? 'elevated');
$cardRadius = (string)($themeRuntime['card_radius'] ?? '');
$cardBackground = (string)($themeRuntime['card_background'] ?? ($colors['surface'] ?? '#ffffff'));
$cardBorderWidth = (string)($themeRuntime['card_border_width'] ?? '1');
$cardBorderColor = (string)($themeRuntime['card_border_color'] ?? '#d7dee8');
$buttonRadius = (string)($themeRuntime['button_radius'] ?? '999px');
$inputRadius = (string)($themeRuntime['input_radius'] ?? '.75rem');
$headingWeight = (string)($themeRuntime['heading_weight'] ?? '700');
$heroRadius = (string)($themeRuntime['hero_radius'] ?? '1.75rem');
$heroStyle = (string)($themeRuntime['hero_style'] ?? 'standard');
$navStyle = (string)($themeRuntime['nav_style'] ?? 'solid');
$navVariant = (string)($themeRuntime['nav_variant'] ?? 'light');
$navLinkWeight = (string)($themeRuntime['nav_link_weight'] ?? '');
$navFontSize = (string)($themeRuntime['nav_font_size'] ?? '1rem');
$navTextTransform = (string)($themeRuntime['nav_text_transform'] ?? 'none');
$navBorderWidth = (string)($themeRuntime['nav_border_width'] ?? '1');
$navWidth = (string)($themeRuntime['nav_width'] ?? 'full');
$navRadius = (string)($themeRuntime['nav_radius'] ?? '0');
$navTopSpacing = (string)($themeRuntime['nav_top_spacing'] ?? '0');
$navBackground = (string)($themeRuntime['nav_background'] ?? '#ffffff');
$navLink = (string)($themeRuntime['nav_link'] ?? '#212529');
$navHover = (string)($themeRuntime['nav_hover'] ?? '#1f6feb');
$navActive = (string)($themeRuntime['nav_active'] ?? '#1f6feb');
$navDropdownBackground = (string)($themeRuntime['nav_dropdown_background'] ?? '#ffffff');
$paginationRadius = (string)($themeRuntime['pagination_radius'] ?? '999px');
$paginationBorderWidth = (string)($themeRuntime['pagination_border_width'] ?? '1');
$paginationGap = (string)($themeRuntime['pagination_gap'] ?? '.25rem');
$paginationFontWeight = (string)($themeRuntime['pagination_font_weight'] ?? '600');
$paginationColor = (string)($themeRuntime['pagination_color'] ?? '#1f6feb');
$paginationBackground = (string)($themeRuntime['pagination_background'] ?? $cardBackground);
$paginationBorderColor = (string)($themeRuntime['pagination_border_color'] ?? '#d7dee8');
$paginationHoverColor = (string)($themeRuntime['pagination_hover_color'] ?? '#ffffff');
$paginationHoverBackground = (string)($themeRuntime['pagination_hover_background'] ?? '#1f6feb');
$paginationHoverBorderColor = (string)($themeRuntime['pagination_hover_border_color'] ?? '#1f6feb');
$paginationActiveColor = (string)($themeRuntime['pagination_active_color'] ?? '#ffffff');
$paginationActiveBackground = (string)($themeRuntime['pagination_active_background'] ?? '#1f6feb');
$paginationActiveBorderColor = (string)($themeRuntime['pagination_active_border_color'] ?? '#1f6feb');
$showHero = !empty($themeRuntime['show_hero']);
$mainContent = function_exists('get_mainContent') ? (string)get_mainContent() : '';
$activeModule = (string)($GLOBALS['nx_active_module'] ?? $pageSlug);
$isWideContentPage = in_array($activeModule, ['pricing'], true);
$colors = is_array($themeRuntime['colors'] ?? null) ? $themeRuntime['colors'] : [];
$accentColor = (string)($colors['accent'] ?? '#1f6feb');
$pageTopColor = (string)($colors['page_top'] ?? '#1a2230');
$pageBgColor = (string)($colors['page_bg'] ?? '#11151b');
$surfaceColor = (string)($colors['surface'] ?? '#ffffff');
$textColor = (string)($colors['text'] ?? '#0f172a');
$effectiveCardBackground = $cardBackground !== 'transparent' ? $cardBackground : $surfaceColor;
$accentRgb = $hexToRgb($accentColor, '31, 111, 235');
$pageTopRgb = $hexToRgb($pageTopColor, '26, 34, 48');
$pageBgRgb = $hexToRgb($pageBgColor, '17, 21, 27');
$surfaceRgb = $hexToRgb($surfaceColor, '255, 255, 255');
$cardBgRgb = $hexToRgb($effectiveCardBackground, $surfaceRgb);
$textRgb = $hexToRgb($textColor, '15, 23, 42');
$borderRgb = $hexToRgb($cardBorderColor !== '' ? $cardBorderColor : '#d7dee8', '215, 222, 232');
$navBgRgb = $hexToRgb($navBackground, $surfaceRgb);
$navLinkRgb = $hexToRgb($navLink, $textRgb);

$showZone = static function (string $zone) use ($widgetsByPosition, $isBuilder): bool {
    return $isBuilder || !empty($widgetsByPosition[$zone]);
};

$allowLeft = in_array($layoutPreset, ['two-sidebars'], true) || $showZone('left');
$allowRight = in_array($layoutPreset, ['right-sidebar', 'two-sidebars'], true) || $showZone('right');
$hasLeft = $allowLeft && $showZone('left');
$hasRight = $allowRight && $showZone('right');
$ratioMap = [
    '70-30' => ['main' => 'col-xl-8 col-lg-8', 'side' => 'col-xl-4 col-lg-4'],
    '75-25' => ['main' => 'col-xl-9 col-lg-8', 'side' => 'col-xl-3 col-lg-4'],
    '80-20' => ['main' => 'col-xl-9 col-lg-8', 'side' => 'col-xl-3 col-lg-4'],
];
$twoSidebarRatioMap = [
    '3-6-3' => ['left' => 'col-xl-3 col-lg-3', 'main' => 'col-xl-6 col-lg-6', 'right' => 'col-xl-3 col-lg-3'],
    '4-4-4' => ['left' => 'col-xl-4 col-lg-4', 'main' => 'col-xl-4 col-lg-4', 'right' => 'col-xl-4 col-lg-4'],
    '3-5-4' => ['left' => 'col-xl-3 col-lg-3', 'main' => 'col-xl-5 col-lg-5', 'right' => 'col-xl-4 col-lg-4'],
    '4-5-3' => ['left' => 'col-xl-4 col-lg-4', 'main' => 'col-xl-5 col-lg-5', 'right' => 'col-xl-3 col-lg-3'],
];
$ratio = $ratioMap[$columnRatio] ?? $ratioMap['75-25'];
$twoSidebar = $twoSidebarRatioMap[$twoSidebarRatio] ?? $twoSidebarRatioMap['3-6-3'];
$mainClass = $hasLeft && $hasRight ? $twoSidebar['main'] : (($hasLeft || $hasRight) ? $ratio['main'] : 'col-12');
$leftSideClass = $hasLeft && $hasRight ? $twoSidebar['left'] : $ratio['side'];
$rightSideClass = $hasLeft && $hasRight ? $twoSidebar['right'] : $ratio['side'];
$mainColumnStyle = '';
$sideColumnStyle = '';
$widthClassMap = [
    'boxed' => 'nx-theme-container nx-theme-container--boxed',
    'wide' => 'nx-theme-container nx-theme-container--wide',
    'full' => 'nx-theme-container nx-theme-container--full',
];
$containerClass = $widthClassMap[$pageWidth] ?? $widthClassMap['wide'];

$contentMaxMap = [
    'narrow' => '69rem',
    'normal' => '88rem',
    'wide' => '100%',
];
$pageMaxMap = [
    'boxed' => '72rem',
    'wide' => '96rem',
    'full' => '100%',
];
$navContentShellMax = $isWideContentPage
    ? ($pageMaxMap[$pageWidth] ?? '96rem')
    : 'var(--nx-theme-content-shell-max)';
$mainContentShellMax = $isWideContentPage
    ? '100%'
    : 'var(--nx-theme-content-shell-max)';
$spacingMap = [
    'compact' => ['section' => '1rem', 'surface' => '1rem', 'shell' => '1rem'],
    'normal' => ['section' => '1.5rem', 'surface' => '1.25rem', 'shell' => '1.5rem'],
    'relaxed' => ['section' => '2.25rem', 'surface' => '1.75rem', 'shell' => '2rem'],
];
$cardStyleMap = [
    'flat' => ['radius' => '.9rem', 'border' => 'transparent', 'shadow' => 'none'],
    'outlined' => ['radius' => '1rem', 'border' => 'var(--nx-theme-divider)', 'shadow' => 'none'],
    'elevated' => ['radius' => '1.25rem', 'border' => 'var(--nx-theme-divider)', 'shadow' => '0 18px 44px rgba(15,23,42,.10)'],
    'soft' => ['radius' => '1.5rem', 'border' => 'transparent', 'shadow' => '0 12px 30px rgba(15,23,42,.06)'],
];
$heroStyleMap = [
    'compact' => ['padding' => 'clamp(1.15rem, 2.2vw, 1.75rem)', 'minHeight' => '0', 'title' => 'clamp(2rem, 3vw, 3.4rem)'],
    'standard' => ['padding' => 'clamp(1.5rem, 3vw, 2.5rem)', 'minHeight' => '0', 'title' => 'clamp(2.4rem, 4vw, 4.6rem)'],
    'immersive' => ['padding' => 'clamp(2rem, 4vw, 3.5rem)', 'minHeight' => '24rem', 'title' => 'clamp(2.8rem, 5vw, 5.4rem)'],
];
$spacing = $spacingMap[$sectionSpacing] ?? $spacingMap['normal'];
$cardVars = $cardStyleMap[$cardStyle] ?? $cardStyleMap['elevated'];
$resolvedCardRadius = $cardRadius !== '' ? $cardRadius : $cardVars['radius'];
$heroVars = $heroStyleMap[$heroStyle] ?? $heroStyleMap['standard'];
$navStyleMap = [
    'solid' => ['border' => 'transparent', 'shadow' => '0 10px 28px rgba(15,23,42,.10)', 'backdrop' => 'none'],
    'outlined' => ['border' => 'color-mix(in srgb, ' . $navLink . ', transparent 82%)', 'shadow' => 'none', 'backdrop' => 'none'],
    'glass' => ['border' => 'color-mix(in srgb, ' . $navLink . ', transparent 76%)', 'shadow' => '0 14px 34px rgba(15,23,42,.14)', 'backdrop' => 'blur(22px) saturate(1.18)'],
];
$navVariantMap = [
    'primary' => [
        'innerPadding' => '.9rem 1.35rem',
        'itemGap' => '.25rem',
        'linkPad' => '.45rem .8rem',
        'linkRadius' => '.85rem',
        'linkWeight' => '500',
        'linkSpacing' => '0',
        'hoverBg' => 'color-mix(in srgb, ' . $accentColor . ', transparent 90%)',
        'activeBg' => 'color-mix(in srgb, ' . $accentColor . ', transparent 84%)',
    ],
    'dark' => [
        'innerPadding' => '.65rem 1rem',
        'itemGap' => '.15rem',
        'linkPad' => '.42rem .72rem',
        'linkRadius' => '.4rem',
        'linkWeight' => '600',
        'linkSpacing' => '.01em',
        'hoverBg' => 'color-mix(in srgb, ' . $accentColor . ', transparent 84%)',
        'activeBg' => 'color-mix(in srgb, ' . $accentColor . ', transparent 76%)',
    ],
    'light' => [
        'innerPadding' => '1rem 1.5rem',
        'itemGap' => '.35rem',
        'linkPad' => '.35rem .7rem',
        'linkRadius' => '.2rem',
        'linkWeight' => '500',
        'linkSpacing' => '.02em',
        'hoverBg' => 'transparent',
        'activeBg' => 'transparent',
    ],
    'soft' => [
        'innerPadding' => '.8rem 1.2rem',
        'itemGap' => '.2rem',
        'linkPad' => '.45rem .85rem',
        'linkRadius' => '999px',
        'linkWeight' => '500',
        'linkSpacing' => '0',
        'hoverBg' => 'color-mix(in srgb, ' . $accentColor . ', transparent 92%)',
        'activeBg' => 'color-mix(in srgb, ' . $accentColor . ', transparent 84%)',
    ],
];
$navVars = $navStyleMap[$navStyle] ?? $navStyleMap['solid'];
$navVariantVars = $navVariantMap[$navVariant] ?? $navVariantMap['light'];
$resolvedNavLinkWeight = in_array($navLinkWeight, ['300', '400', '500', '600', '700'], true)
    ? $navLinkWeight
    : $navVariantVars['linkWeight'];
$navBackgroundComputed = $navStyle === 'glass'
    ? 'color-mix(in srgb, ' . $navBackground . ', transparent 28%)'
    : $navBackground;
$navDropdownComputed = $navStyle === 'glass'
    ? 'color-mix(in srgb, ' . $navDropdownBackground . ', transparent 12%)'
    : $navDropdownBackground;

require_once __DIR__ . '/../../includes/themes/' . ($theme_name ?? 'default') . '/header.php';
?>
<?= $builderOverlayHtml ?>
<style>
:root {
  --nx-theme-accent: <?= htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') ?>;
  --primary: <?= htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') ?>;
  --accent-color: <?= htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-accent-strong: color-mix(in srgb, <?= htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') ?>, black 20%);
  --nx-theme-page-top: <?= htmlspecialchars($pageTopColor, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-page-bg: <?= htmlspecialchars($pageBgColor, ENT_QUOTES, 'UTF-8') ?>;
  --background-color: <?= htmlspecialchars($pageBgColor, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-surface: <?= htmlspecialchars($surfaceColor, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-surface-2: <?= htmlspecialchars($cardBackground, ENT_QUOTES, 'UTF-8') ?>;
  --surface-color: <?= htmlspecialchars($effectiveCardBackground, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-text: <?= htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8') ?>;
  --default-color: <?= htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8') ?>;
  --heading-color: <?= htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8') ?>;
  --contrast-color: #ffffff;
  --nx-theme-divider: color-mix(in srgb, <?= htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8') ?>, transparent 88%);
  --nx-theme-muted: color-mix(in srgb, <?= htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8') ?>, white 35%);
  --nx-theme-text-inverse: #f8fbff;
  --bs-primary: <?= htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-primary-rgb: <?= htmlspecialchars($accentRgb, ENT_QUOTES, 'UTF-8') ?>;
  --bs-body-color: <?= htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-body-color-rgb: <?= htmlspecialchars($textRgb, ENT_QUOTES, 'UTF-8') ?>;
  --bs-body-bg: <?= htmlspecialchars($pageBgColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-body-bg-rgb: <?= htmlspecialchars($pageBgRgb, ENT_QUOTES, 'UTF-8') ?>;
  --bs-emphasis-color: <?= htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-emphasis-color-rgb: <?= htmlspecialchars($textRgb, ENT_QUOTES, 'UTF-8') ?>;
  --bs-secondary-color: <?= htmlspecialchars('color-mix(in srgb, ' . $textColor . ', white 35%)', ENT_QUOTES, 'UTF-8') ?>;
  --bs-secondary-color-rgb: <?= htmlspecialchars($textRgb, ENT_QUOTES, 'UTF-8') ?>;
  --bs-secondary-bg: <?= htmlspecialchars($surfaceColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-secondary-bg-rgb: <?= htmlspecialchars($surfaceRgb, ENT_QUOTES, 'UTF-8') ?>;
  --bs-tertiary-bg: <?= htmlspecialchars($effectiveCardBackground, ENT_QUOTES, 'UTF-8') ?>;
  --bs-tertiary-bg-rgb: <?= htmlspecialchars($cardBgRgb, ENT_QUOTES, 'UTF-8') ?>;
  --bs-card-bg: <?= htmlspecialchars($effectiveCardBackground, ENT_QUOTES, 'UTF-8') ?>;
  --bs-card-cap-bg: <?= htmlspecialchars($surfaceColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-card-border-color: <?= htmlspecialchars($cardBorderColor !== '' ? $cardBorderColor : 'color-mix(in srgb, ' . $textColor . ', transparent 88%)', ENT_QUOTES, 'UTF-8') ?>;
  --bs-border-color: <?= htmlspecialchars($cardBorderColor !== '' ? $cardBorderColor : 'color-mix(in srgb, ' . $textColor . ', transparent 88%)', ENT_QUOTES, 'UTF-8') ?>;
  --bs-border-color-rgb: <?= htmlspecialchars($borderRgb, ENT_QUOTES, 'UTF-8') ?>;
  --bs-border-width: <?= htmlspecialchars($cardBorderWidth, ENT_QUOTES, 'UTF-8') ?>px;
  --bs-border-radius: <?= htmlspecialchars($resolvedCardRadius, ENT_QUOTES, 'UTF-8') ?>;
  --bs-link-color: <?= htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-link-color-rgb: <?= htmlspecialchars($accentRgb, ENT_QUOTES, 'UTF-8') ?>;
  --bs-link-hover-color: color-mix(in srgb, <?= htmlspecialchars($accentColor, ENT_QUOTES, 'UTF-8') ?>, black 20%);
  --bs-dropdown-bg: <?= htmlspecialchars($navDropdownComputed, ENT_QUOTES, 'UTF-8') ?>;
  --bs-dropdown-link-color: <?= htmlspecialchars($navLink, ENT_QUOTES, 'UTF-8') ?>;
  --bs-dropdown-link-hover-color: <?= htmlspecialchars($navHover, ENT_QUOTES, 'UTF-8') ?>;
  --bs-dropdown-border-color: <?= htmlspecialchars($cardBorderColor !== '' ? $cardBorderColor : 'color-mix(in srgb, ' . $textColor . ', transparent 88%)', ENT_QUOTES, 'UTF-8') ?>;
  --bs-list-group-bg: <?= htmlspecialchars($effectiveCardBackground, ENT_QUOTES, 'UTF-8') ?>;
  --bs-list-group-color: <?= htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-list-group-border-color: <?= htmlspecialchars($cardBorderColor !== '' ? $cardBorderColor : 'color-mix(in srgb, ' . $textColor . ', transparent 88%)', ENT_QUOTES, 'UTF-8') ?>;
  --bs-table-bg: <?= htmlspecialchars($effectiveCardBackground, ENT_QUOTES, 'UTF-8') ?>;
  --bs-table-color: <?= htmlspecialchars($textColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-table-border-color: <?= htmlspecialchars($cardBorderColor !== '' ? $cardBorderColor : 'color-mix(in srgb, ' . $textColor . ', transparent 88%)', ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-color: <?= htmlspecialchars($paginationColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-bg: <?= htmlspecialchars($paginationBackground !== 'transparent' ? $paginationBackground : 'transparent', ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-border-color: <?= htmlspecialchars($paginationBorderColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-hover-color: <?= htmlspecialchars($paginationHoverColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-hover-bg: <?= htmlspecialchars($paginationHoverBackground !== 'transparent' ? $paginationHoverBackground : 'transparent', ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-hover-border-color: <?= htmlspecialchars($paginationHoverBorderColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-focus-color: <?= htmlspecialchars($paginationHoverColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-focus-bg: <?= htmlspecialchars($paginationHoverBackground !== 'transparent' ? $paginationHoverBackground : 'transparent', ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-focus-border-color: <?= htmlspecialchars($paginationHoverBorderColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-active-color: <?= htmlspecialchars($paginationActiveColor, ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-active-bg: <?= htmlspecialchars($paginationActiveBackground !== 'transparent' ? $paginationActiveBackground : 'transparent', ENT_QUOTES, 'UTF-8') ?>;
  --bs-pagination-active-border-color: <?= htmlspecialchars($paginationActiveBorderColor, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-content-max: <?= htmlspecialchars($contentMaxMap[$contentWidth] ?? '68rem', ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-page-max: <?= htmlspecialchars($pageMaxMap[$pageWidth] ?? '96rem', ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-content-shell-max: min(var(--nx-theme-content-max), var(--nx-theme-page-max));
  --nx-theme-main-shell-max: <?= htmlspecialchars($mainContentShellMax, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-section-gap: <?= htmlspecialchars($spacing['section'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-card-gap: .9rem;
  --nx-theme-surface-padding: <?= htmlspecialchars($spacing['surface'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-shell-padding: <?= htmlspecialchars($spacing['shell'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-card-radius: <?= htmlspecialchars($resolvedCardRadius, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-card-border: <?= htmlspecialchars($cardBorderColor !== '' ? $cardBorderColor : $cardVars['border'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-card-border-width: <?= htmlspecialchars($cardBorderWidth, ENT_QUOTES, 'UTF-8') ?>px;
  --nx-theme-card-shadow: <?= htmlspecialchars($cardVars['shadow'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-button-radius: <?= htmlspecialchars($buttonRadius, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-input-radius: <?= htmlspecialchars($inputRadius, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-heading-weight: <?= htmlspecialchars($headingWeight, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-hero-radius: <?= htmlspecialchars($heroRadius, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-hero-padding: <?= htmlspecialchars($heroVars['padding'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-hero-min-height: <?= htmlspecialchars($heroVars['minHeight'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-hero-title-size: <?= htmlspecialchars($heroVars['title'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-bg: <?= htmlspecialchars($navBackgroundComputed, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-shell-width: <?= htmlspecialchars(
      $navWidth === 'content'
          ? $navContentShellMax
          : 'none',
      ENT_QUOTES,
      'UTF-8'
  ) ?>;
  --nx-theme-nav-shell-width-value: <?= htmlspecialchars(
      $navWidth === 'content'
          ? 'min(calc(100% - 2rem), var(--nx-theme-nav-shell-width))'
          : '100vw',
      ENT_QUOTES,
      'UTF-8'
  ) ?>;
  --nx-theme-nav-shell-margin-inline: <?= htmlspecialchars(
      ($navWidth !== 'content') ? '0' : 'auto',
      ENT_QUOTES,
      'UTF-8'
  ) ?>;
  --nx-theme-nav-radius: <?= htmlspecialchars($navRadius, ENT_QUOTES, 'UTF-8') ?>px;
  --nx-theme-nav-top-spacing: <?= htmlspecialchars($navTopSpacing, ENT_QUOTES, 'UTF-8') ?>px;
  --nx-theme-nav-link: <?= htmlspecialchars($navLink, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-hover: <?= htmlspecialchars($navHover, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-active: <?= htmlspecialchars($navActive, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-dropdown-bg: <?= htmlspecialchars($navDropdownComputed, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-inner-padding: <?= htmlspecialchars($navVariantVars['innerPadding'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-item-gap: <?= htmlspecialchars($navVariantVars['itemGap'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-link-padding: <?= htmlspecialchars($navVariantVars['linkPad'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-link-radius: <?= htmlspecialchars($navVariantVars['linkRadius'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-link-weight: <?= htmlspecialchars($resolvedNavLinkWeight, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-font-size: <?= htmlspecialchars($navFontSize, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-text-transform: <?= htmlspecialchars($navTextTransform, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-border-width: <?= htmlspecialchars($navBorderWidth, ENT_QUOTES, 'UTF-8') ?>px;
  --nx-theme-nav-link-spacing: <?= htmlspecialchars($navVariantVars['linkSpacing'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-hover-bg: <?= htmlspecialchars($navVariantVars['hoverBg'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-active-bg: <?= htmlspecialchars($navVariantVars['activeBg'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-pagination-radius: <?= htmlspecialchars($paginationRadius, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-pagination-border-width: <?= htmlspecialchars($paginationBorderWidth, ENT_QUOTES, 'UTF-8') ?>px;
  --nx-theme-pagination-gap: <?= htmlspecialchars($paginationGap, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-pagination-font-weight: <?= htmlspecialchars($paginationFontWeight, ENT_QUOTES, 'UTF-8') ?>;
  --nav-color: <?= htmlspecialchars($navLink, ENT_QUOTES, 'UTF-8') ?>;
  --nav-hover-color: <?= htmlspecialchars($navHover, ENT_QUOTES, 'UTF-8') ?>;
  --nav-dropdown-background-color: <?= htmlspecialchars($navDropdownComputed, ENT_QUOTES, 'UTF-8') ?>;
  --nav-dropdown-color: <?= htmlspecialchars($navLink, ENT_QUOTES, 'UTF-8') ?>;
  --nav-dropdown-hover-color: <?= htmlspecialchars($navHover, ENT_QUOTES, 'UTF-8') ?>;
  --nav-mobile-background-color: <?= htmlspecialchars($navDropdownComputed, ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-border: <?= htmlspecialchars($navVars['border'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-shadow: <?= htmlspecialchars($navVars['shadow'], ENT_QUOTES, 'UTF-8') ?>;
  --nx-theme-nav-backdrop: <?= htmlspecialchars($navVars['backdrop'], ENT_QUOTES, 'UTF-8') ?>;
}
</style>
<style>
html,
body,
.sticky-footer-wrapper {
  background-color: var(--nx-theme-page-bg) !important;
  color: var(--nx-theme-text) !important;
}

.nx-theme-shell,
.nx-theme-content,
.nx-theme-zone {
  color: var(--nx-theme-text) !important;
}

#mainNavbar {
  background: var(--nx-theme-nav-bg) !important;
  border-bottom: var(--nx-theme-nav-border-width) solid var(--nx-theme-nav-border) !important;
  box-shadow: var(--nx-theme-nav-shadow) !important;
  backdrop-filter: var(--nx-theme-nav-backdrop);
  -webkit-backdrop-filter: var(--nx-theme-nav-backdrop);
  border-radius: var(--nx-theme-nav-radius) !important;
  margin-top: var(--nx-theme-nav-top-spacing) !important;
  overflow: visible;
  max-width: var(--nx-theme-nav-shell-width);
  width: var(--nx-theme-nav-shell-width-value);
  margin-left: var(--nx-theme-nav-shell-margin-inline) !important;
  margin-right: var(--nx-theme-nav-shell-margin-inline) !important;
}

#mainNavbar > .container {
  max-width: <?= $navWidth === 'content'
      ? 'var(--nx-theme-nav-shell-width)'
      : $navContentShellMax ?> !important;
  width: 100% !important;
  margin-left: auto !important;
  margin-right: auto !important;
  padding: var(--nx-theme-nav-inner-padding) !important;
}

<?php if ($navWidth !== 'content'): ?>
#mainNavbar {
  max-width: none !important;
  width: 100% !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
}
<?php endif; ?>

#mainNavbar .navbar-nav,
#mainNavbar .navbar-nav .dropdown-menu {
  gap: var(--nx-theme-nav-item-gap);
}

#mainNavbar .nav-link,
#mainNavbar .navbar-brand,
#mainNavbar .navbar-toggler,
#mainNavbar .navbar-toggler-icon,
#mainNavbar .dropdown-toggle,
#mainNavbar .btn {
  color: var(--nx-theme-nav-link) !important;
}

#mainNavbar .nav-link,
#mainNavbar .dropdown-toggle {
  padding: var(--nx-theme-nav-link-padding) !important;
  border-radius: var(--nx-theme-nav-link-radius) !important;
  font-weight: var(--nx-theme-nav-link-weight) !important;
  font-size: var(--nx-theme-nav-font-size) !important;
  text-transform: var(--nx-theme-nav-text-transform) !important;
  letter-spacing: var(--nx-theme-nav-link-spacing) !important;
}

#mainNavbar .nav-link:hover,
#mainNavbar .nav-link:focus,
#mainNavbar .dropdown-toggle:hover,
#mainNavbar .dropdown-toggle:focus {
  color: var(--nx-theme-nav-hover) !important;
  background: var(--nx-theme-nav-hover-bg) !important;
}

#mainNavbar .nav-link.active,
#mainNavbar .nav-link.show,
#mainNavbar .dropdown.show > .nav-link,
#mainNavbar .dropdown.show > .dropdown-toggle {
  color: var(--nx-theme-nav-active) !important;
  background: var(--nx-theme-nav-active-bg) !important;
}

#mainNavbar .dropdown-menu {
  background: var(--nx-theme-nav-dropdown-bg) !important;
  border-color: var(--nx-theme-nav-border) !important;
}

<?php if ($navVariant === 'light'): ?>
#mainNavbar .nav-link,
#mainNavbar .dropdown-toggle {
  border-bottom: 2px solid transparent !important;
}

#mainNavbar .nav-link:hover,
#mainNavbar .nav-link:focus,
#mainNavbar .dropdown-toggle:hover,
#mainNavbar .dropdown-toggle:focus,
#mainNavbar .nav-link.active,
#mainNavbar .nav-link.show,
#mainNavbar .dropdown.show > .nav-link,
#mainNavbar .dropdown.show > .dropdown-toggle {
  background: transparent !important;
  border-bottom-color: var(--nx-theme-nav-active) !important;
}
<?php endif; ?>

#mainNavbar .dropdown-item {
  color: var(--nx-theme-nav-link) !important;
}

#mainNavbar .dropdown-item:hover,
#mainNavbar .dropdown-item:focus,
#mainNavbar .dropdown-item.active {
  color: var(--nx-theme-nav-hover) !important;
  background: color-mix(in srgb, var(--nx-theme-nav-hover), transparent 92%) !important;
}

.btn,
.form-control,
.form-select,
.input-group-text {
  border-radius: var(--nx-theme-input-radius) !important;
}

.btn {
  border-radius: var(--nx-theme-button-radius) !important;
}

.nx-theme-main-surface,
.nx-theme-surface,
.nx-theme-content .card,
.nx-theme-content .widget,
.nx-theme-content .module,
.nx-theme-content .forumlastposts,
.nx-theme-content .panel {
  background: var(--nx-theme-surface-2) !important;
  color: var(--nx-theme-text) !important;
  border-color: var(--nx-theme-card-border) !important;
  border-radius: var(--nx-theme-card-radius) !important;
  box-shadow: var(--nx-theme-card-shadow) !important;
  border-width: var(--nx-theme-card-border-width) !important;
  border-style: solid !important;
  overflow: hidden !important;
}

.nx-theme-content .card-body,
.nx-theme-content .card-header,
.nx-theme-content .panel-heading,
.nx-theme-content .table-responsive,
.nx-theme-content .table,
.nx-theme-content .table > :not(caption) > * > * {
  border-radius: 0 !important;
}

.nx-theme-content .card-header,
.nx-theme-content .panel-heading {
  border-top-left-radius: var(--nx-theme-card-radius) !important;
  border-top-right-radius: var(--nx-theme-card-radius) !important;
}

.nx-theme-main-surface .container,
.nx-theme-main-surface .container-sm,
.nx-theme-main-surface .container-md,
.nx-theme-main-surface .container-lg,
.nx-theme-main-surface .container-xl,
.nx-theme-main-surface .container-xxl,
.nx-theme-main-surface .container-fluid {
  max-width: none !important;
  width: 100% !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
}

.nx-theme-main-surface .row {
  --bs-gutter-x: var(--nx-theme-card-gap);
  --bs-gutter-y: var(--nx-theme-card-gap);
  margin-left: calc(var(--nx-theme-card-gap) / -2) !important;
  margin-right: calc(var(--nx-theme-card-gap) / -2) !important;
}

.nx-theme-main-surface .row > [class*="col-"] {
  padding-left: calc(var(--nx-theme-card-gap) / 2) !important;
  padding-right: calc(var(--nx-theme-card-gap) / 2) !important;
}

.nx-theme-content,
.nx-theme-content p,
.nx-theme-content li,
.nx-theme-content span,
.nx-theme-content small,
.nx-theme-content strong,
.nx-theme-content em,
.nx-theme-content label,
.nx-theme-content td,
.nx-theme-content th,
.nx-theme-content h1,
.nx-theme-content h2,
.nx-theme-content h3,
.nx-theme-content h4,
.nx-theme-content h5,
.nx-theme-content h6,
.nx-theme-content .card-title,
.nx-theme-content .card-text,
.nx-theme-content .widget-title,
.nx-theme-content .module-title,
.nx-theme-content .text-body,
.nx-theme-content .text-dark,
.nx-theme-content .text-muted,
.nx-theme-content .small,
.nx-theme-content .lead {
  color: var(--nx-theme-text) !important;
}

.nx-theme-content h1,
.nx-theme-content h2,
.nx-theme-content h3,
.nx-theme-content h4,
.nx-theme-content h5,
.nx-theme-content h6,
.nx-theme-hero h1 {
  font-weight: var(--nx-theme-heading-weight) !important;
}

.nx-theme-content .head-boxes,
.nx-theme-content .head-boxes span,
.nx-theme-content .head-boxes p,
.nx-theme-content .head-boxes h1,
.nx-theme-content .head-boxes h2,
.nx-theme-content .head-boxes h3,
.nx-theme-content .head-boxes h4,
.nx-theme-content .head-boxes h5,
.nx-theme-content .head-boxes h6 {
  color: revert !important;
}

.nx-theme-content .head-boxes {
  background: transparent !important;
  border: 0 !important;
  box-shadow: none !important;
  height: 4.5rem !important;
  display: block !important;
  overflow: hidden !important;
  margin-bottom: 0.5rem !important;
}

.nx-theme-content [data-nx-zone="left"] .head-boxes {
  height: 4.5rem !important;
  margin: 0 0 0.5rem 0 !important;
}

.nx-theme-content [data-nx-zone="left"] .head-boxes-1 {
  margin-left: 0 !important;
  margin-bottom: 0.5rem !important;
}

.nx-theme-content .head-boxes-1 .head-boxes-head,
.nx-theme-content .head-boxes-8 .head-boxes-head,
.nx-theme-content .head-boxes-1 .head-boxes-foot,
.nx-theme-content .head-boxes-8 .head-boxes-foot {
  display: none !important;
}

.nx-theme-content .head-boxes-3 .head-boxes-head,
.nx-theme-content .head-boxes-4 .head-boxes-head,
.nx-theme-content .head-boxes-5 .head-boxes-head,
.nx-theme-content .head-boxes-6 .head-boxes-head,
.nx-theme-content .head-boxes-7 .head-boxes-head,
.nx-theme-content .head-boxes-9 .head-boxes-head,
.nx-theme-content .head-boxes-10 .head-boxes-head {
  color: transparent !important;
  visibility: hidden !important;
}

.nx-theme-content .head-boxes-2 h2,
.nx-theme-content .head-boxes-2 h2::after,
.nx-theme-content .head-boxes-3 p.head-boxes-foot,
.nx-theme-content .head-boxes-4 h2,
.nx-theme-content .head-boxes-5 h2,
.nx-theme-content .head-boxes-8 h2,
.nx-theme-content .head-boxes-8 h2::after {
  color: var(--nx-theme-accent) !important;
}

.nx-theme-content .head-boxes-3 h2,
.nx-theme-content .head-boxes-10 p.head-boxes-foot {
  color: var(--nx-theme-text) !important;
}

.nx-theme-content a,
.nx-theme-shell a:not(.btn) {
  color: var(--nx-theme-accent) !important;
}

.nx-theme-content input,
.nx-theme-content textarea,
.nx-theme-content select,
.nx-theme-content .form-control {
  background-color: var(--nx-theme-surface) !important;
  color: var(--nx-theme-text) !important;
  border-color: var(--nx-theme-divider) !important;
}

.nx-theme-shell {
  padding-top: var(--nx-theme-shell-padding);
  padding-bottom: calc(var(--nx-theme-shell-padding) * 2);
}

.nx-theme-container {
  width: min(100% - 2rem, var(--nx-theme-page-max));
  margin-inline: auto;
}

.nx-theme-container.nx-theme-container--full {
  width: calc(100% - 2rem);
  max-width: none;
}

.nx-theme-zone,
.nx-theme-content,
.nx-theme-hero {
  margin-bottom: var(--nx-theme-section-gap) !important;
}

.nx-theme-main-surface {
  padding: 0 !important;
  max-width: var(--nx-theme-main-shell-max);
  width: min(100%, var(--nx-theme-main-shell-max));
  margin-inline: auto;
  border: 0 !important;
  box-shadow: none !important;
  background: transparent !important;
}

.nx-theme-main-surface.nx-theme-main-surface--wide {
  max-width: none !important;
  width: 100% !important;
}

.nx-theme-content[data-nx-module="pricing"] .nx-theme-main-surface {
  max-width: none !important;
  width: 100% !important;
}

.nx-theme-content:not([data-nx-module="pricing"]) .nx-theme-column[data-nx-zone="main"] > .nx-theme-zone,
.nx-theme-content:not([data-nx-module="pricing"]) .nx-theme-column[data-nx-zone="main"] > .nx-theme-main-surface {
  width: min(100%, var(--nx-theme-main-shell-max));
  max-width: var(--nx-theme-main-shell-max);
  margin-inline: auto;
}

.nx-theme-content .row {
  --bs-gutter-y: var(--nx-theme-section-gap);
}

.nx-theme-main-row > .nx-theme-column {
  display: flex;
  flex-direction: column;
}

@media (max-width: 991.98px) {
  .nx-theme-main-row > .nx-theme-column {
    flex: 0 0 100% !important;
    max-width: 100% !important;
  }
}

.nx-theme-content .nx-theme-main-row {
  width: min(100%, var(--nx-theme-content-shell-max));
  margin-left: auto !important;
  margin-right: auto !important;
}

.nx-theme-hero-panel {
  padding: var(--nx-theme-hero-padding) !important;
  min-height: var(--nx-theme-hero-min-height);
  max-width: var(--nx-theme-content-shell-max);
  width: min(100%, var(--nx-theme-content-shell-max));
  margin-inline: auto;
}

.nx-theme-hero-copy {
  width: 100%;
}

.nx-theme-hero h1 {
  font-size: var(--nx-theme-hero-title-size) !important;
}
</style>

<main class="nx-theme-shell" data-layout-preset="<?= htmlspecialchars($layoutPreset, ENT_QUOTES, 'UTF-8') ?>">
  <?php if ($showHero): ?>
    <section class="nx-theme-hero">
      <div class="<?= htmlspecialchars($containerClass, ENT_QUOTES, 'UTF-8') ?>">
        <div class="nx-theme-hero-panel">
          <div class="nx-theme-hero-copy">
            <span class="nx-theme-kicker">Eigenes Template</span>
            <h1><?= htmlspecialchars((string)($GLOBALS['hp_title'] ?? ($themeRuntime['hero_title'] ?? 'Theme')), ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="lead mb-0"><?= htmlspecialchars((string)($GLOBALS['hp_description'] ?? ($themeRuntime['hero_text'] ?? 'Eigenes Nexpell Theme als Startpunkt.')), ENT_QUOTES, 'UTF-8') ?></p>
            <div class="nx-theme-actions">
              <a class="btn btn-primary" href="#nx-main-content"><?= htmlspecialchars((string)($themeRuntime['cta_label'] ?? 'Mehr erfahren'), ENT_QUOTES, 'UTF-8') ?></a>
              <a class="btn btn-outline-light" href="admincenter.php?site=theme_designer">Theme Designer</a>
            </div>
            <div class="nx-theme-status">
              <strong>Runtime Theme</strong>
              <span>Preset: <?= htmlspecialchars($layoutPreset, ENT_QUOTES, 'UTF-8') ?></span>
              <span>Slug: <code><?= htmlspecialchars((string)($theme_name ?? 'default'), ENT_QUOTES, 'UTF-8') ?></code></span>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($showZone('undertop')): ?>
    <section class="<?= htmlspecialchars($containerClass, ENT_QUOTES, 'UTF-8') ?> nx-theme-zone nx-live-zone nx-zone" data-nx-zone="undertop">
      <?php foreach ($widgetsByPosition['undertop'] ?? [] as $widget) echo $widget; ?>
    </section>
  <?php endif; ?>

  <section id="nx-main-content" class="<?= htmlspecialchars($containerClass, ENT_QUOTES, 'UTF-8') ?> nx-theme-content" data-nx-module="<?= htmlspecialchars($activeModule, ENT_QUOTES, 'UTF-8') ?>">
    <div class="row g-4 align-items-start nx-theme-main-row">
      <?php if ($hasLeft): ?>
        <aside class="<?= htmlspecialchars($leftSideClass, ENT_QUOTES, 'UTF-8') ?> nx-theme-column nx-live-zone nx-zone" data-nx-zone="left"<?= $sideColumnStyle !== '' ? ' style="' . htmlspecialchars($sideColumnStyle, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
          <?php foreach ($widgetsByPosition['left'] ?? [] as $widget) echo $widget; ?>
        </aside>
      <?php endif; ?>

      <div class="<?= $mainClass ?> nx-theme-column nx-live-zone nx-zone" data-nx-zone="main"<?= $mainColumnStyle !== '' ? ' style="' . htmlspecialchars($mainColumnStyle, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
        <?php if ($showZone('maintop')): ?>
          <div class="nx-theme-zone mb-3 nx-live-zone nx-zone" data-nx-zone="maintop">
            <?php foreach ($widgetsByPosition['maintop'] ?? [] as $widget) echo $widget; ?>
          </div>
        <?php endif; ?>

        <div class="nx-theme-surface nx-theme-main-surface<?= $isWideContentPage ? ' nx-theme-main-surface--wide' : '' ?>">
          <?= $mainContent ?>
        </div>

        <?php if ($showZone('mainbottom')): ?>
          <div class="nx-theme-zone mt-1 nx-live-zone nx-zone" data-nx-zone="mainbottom">
            <?php foreach ($widgetsByPosition['mainbottom'] ?? [] as $widget) echo $widget; ?>
          </div>
        <?php endif; ?>
      </div>

      <?php if ($hasRight): ?>
        <aside class="<?= htmlspecialchars($rightSideClass, ENT_QUOTES, 'UTF-8') ?> nx-theme-column nx-live-zone nx-zone" data-nx-zone="right"<?= $sideColumnStyle !== '' ? ' style="' . htmlspecialchars($sideColumnStyle, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
          <?php foreach ($widgetsByPosition['right'] ?? [] as $widget) echo $widget; ?>
        </aside>
      <?php endif; ?>
    </div>
  </section>

  <?php if ($showZone('bottom')): ?>
    <section class="<?= htmlspecialchars($containerClass, ENT_QUOTES, 'UTF-8') ?> nx-theme-zone nx-live-zone nx-zone" data-nx-zone="bottom">
      <div class="row g-3">
        <?php foreach ($widgetsByPosition['bottom'] ?? [] as $widget) echo $widget; ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../../includes/themes/' . ($theme_name ?? 'default') . '/footer.php'; ?>
