<?php
if (session_status() === PHP_SESSION_NONE) session_start();

use nexpell\AccessControl;
use nexpell\PluginManager;
use nexpell\SeoUrlHandler;

global $_database, $theme_name, $languageService;

$tpl = new Template();

// SETTINGS LADEN
$settings = [];

$res = $_database->query("
    SELECT setting_key, setting_value
    FROM navigation_website_settings
");

while ($row = $res->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$theme_engine = (int)($settings["theme_engine_enabled"] ?? 1);

// Bootstrap shadow class for dropdown menus (e.g. "shadow", "shadow-sm")
$allowedShadows = ["", "shadow-none", "shadow-sm", "shadow", "shadow-lg"];
$dropdown_shadow_class = trim((string)($settings["dropdown_shadow"] ?? ""));
if (!in_array($dropdown_shadow_class, $allowedShadows, true)) {
    $dropdown_shadow_class = "";
}

// Chevron anzeigen?
$showChevron = ((string)($settings["chevron_show"] ?? "1") === "1");
$chevronHtml = $showChevron ? "<i class='bi bi-chevron-down ms-1'></i>" : "";

// MULTILANG HELPER
function nav_lang(?string $txt): string
{
    global $languageService;

    if ($txt === null) {
        return '';
    }

    $txt = trim((string)$txt);
    if ($txt === '') {
        return '';
    }

    if (strpos($txt, '[[lang:') === false) {
        return $txt;
    }

    try {
        $parsed = (string)$languageService->parseMultilang($txt);
        return $parsed !== '' ? $parsed : $txt;
    } catch (\Throwable $e) {
        return $txt;
    }
}

function nav_table_has_column(string $table, string $column): bool
{
    global $_database;
    static $cache = [];

    $key = $table . '.' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    $tableEsc = $_database->real_escape_string($table);
    $colEsc   = $_database->real_escape_string($column);
    $res = $_database->query("SHOW COLUMNS FROM `{$tableEsc}` LIKE '{$colEsc}'");
    $cache[$key] = ($res instanceof mysqli_result) && ($res->num_rows > 0);
    if ($res instanceof mysqli_result) {
        $res->free();
    }
    return $cache[$key];
}

function nav_resolve_url(string $url, string $currentLang): string
{
    $url = trim($url);
    if ($url === '') {
        return '/';
    }

    // Placeholder aus DB-Links ersetzen
    $url = str_replace(
        ['{current_lang}', '%7Bcurrent_lang%7D', '%7bcurrent_lang%7d'],
        rawurlencode($currentLang),
        $url
    );

    // Query-Links immer über zentralen SEO-Handler normalisieren
    if (str_starts_with($url, 'index.php')) {
        return SeoUrlHandler::convertToSeoUrl($url);
    }

    return $url;
}

// MAIN NAVIGATION
$mainnav_html = "";
$currentNavLang = strtolower((string)($languageService->currentLanguage ?? 'de'));
$currentNavLang = preg_replace('/[^a-z]/', '', $currentNavLang) ?: 'de';
$mainNameExpr = nav_table_has_column('navigation_website_main', 'name') ? "m.name" : "''";
$subNameExpr  = nav_table_has_column('navigation_website_sub', 'name') ? "s.name" : "''";

$main = $_database->query("
    SELECT
        m.*,
        COALESCE(NULLIF(l.content, ''), NULLIF({$mainNameExpr}, ''), m.modulname) AS display_name
    FROM navigation_website_main m
    LEFT JOIN navigation_website_lang l
        ON l.content_key = CONCAT('nav_main_', m.mnavID)
       AND l.language = '{$currentNavLang}'
    ORDER BY m.sort ASC
");

while ($m = $main->fetch_assoc()) {

    $name = nav_lang($m["display_name"] ?? $m["name"] ?? $m["modulname"] ?? '');
    $icon = "";
    $url  = nav_resolve_url((string)($m["url"] ?? ''), $currentNavLang);
    $mnavID = (int)$m["mnavID"];

    $subres = $_database->query("
        SELECT
            s.*,
            COALESCE(NULLIF(l.content, ''), NULLIF({$subNameExpr}, ''), s.modulname) AS display_name
        FROM navigation_website_sub s
        LEFT JOIN navigation_website_lang l
            ON l.content_key = CONCAT('nav_sub_', s.snavID)
           AND l.language = '{$currentNavLang}'
        WHERE s.mnavID = {$mnavID}
        ORDER BY s.sort ASC
    ");

    // DROPDOWN
    if ($m["isdropdown"] == 1) {

        if ($subres->num_rows == 0) continue;
        $shadowClass = $dropdown_shadow_class !== "" ? " ".$dropdown_shadow_class : "";
        $mainnav_html .= "
        <li class='nav-item dropdown'>
            <a class='nav-link d-flex align-items-center gap-1' href='#' data-bs-toggle='dropdown' data-bs-display='static'>
                {$name}
                {$chevronHtml}
            </a>
            <ul class='dropdown-menu{$shadowClass}'>";

        while ($s = $subres->fetch_assoc()) {
            $sname = nav_lang($s["display_name"] ?? $s["name"] ?? $s["modulname"] ?? '');
            $mainnav_html .= "
                <li>
                    <a class='dropdown-item' href='" . nav_resolve_url((string)($s["url"] ?? ''), $currentNavLang) . "'>
                        {$sname}
                    </a>
                </li>
            ";
        }

        $mainnav_html .= "</ul></li>";

    } else {
        // EINZEL-LINK
        $mainnav_html .= "
        <li class='nav-item'>
            <a class='nav-link' href='{$url}'>{$name}</a>
        </li>";
    }
}

// USER NAV
function nav_user(): string
{
    if (empty($_SESSION['userID'])) {
        return "
        <li class='nav-item'>
            <a class='nav-link' href='" . SeoUrlHandler::convertToSeoUrl('index.php?site=login') . "'>
                <i class='bi bi-box-arrow-in-right me-1'></i> Login
            </a>
        </li>";
    }

    $uid = (int)$_SESSION['userID'];
    global $_database;

    // EINHEITLICHE ADMIN-PRÜFUNG
    $canAdmin = AccessControl::canAccessAdmin($_database, $uid);

    return "
    <li class='nav-item dropdown'>
        <a class='nav-link d-flex align-items-center gap-1' href='#' data-bs-toggle='dropdown' data-bs-display='static'>
            <img src='" . htmlspecialchars(getavatar($uid), ENT_QUOTES, 'UTF-8') . "'
                 class='navbar-avatar'
                 style='width:22px;height:22px;border-radius:4px;'>
            " . htmlspecialchars(getusername($uid), ENT_QUOTES, 'UTF-8') . "
            <i class='bi bi-chevron-down ms-1'></i>
        </a>

        <ul class='dropdown-menu dropdown-menu-end'>

            <li>
                <a class='dropdown-item'
                   href='" . SeoUrlHandler::convertToSeoUrl("index.php?site=profile&userID={$uid}") . "'>
                    <i class='bi bi-person me-2'></i> Profil
                </a>
            </li>

            " . ($canAdmin ? "
            <li>
                <a class='dropdown-item' href='/admin/admincenter.php' target='_blank'>
                    <i class='bi bi-speedometer2 me-2'></i> Admincenter
                </a>
            </li>
            " : "") . "

            <li><hr class='dropdown-divider'></li>

            <li>
                <a class='dropdown-item' href='" . SeoUrlHandler::convertToSeoUrl('index.php?site=logout') . "'>
                    <i class='bi bi-box-arrow-right me-2'></i> Logout
                </a>
            </li>

        </ul>
    </li>";
}
// LANGUAGE SELECTOR (SEO FINAL)
$languages    = $languageService->getActiveLanguages();
$current_lang = $languageService->currentLanguage;
$current_flag = "";
$lang_html    = "";
$home_url     = \nexpell\SeoUrlHandler::convertToSeoUrl(
    'index.php?site=index&lang=' . rawurlencode((string)$current_lang)
);
if (!is_string($home_url) || $home_url === '') {
    $home_url = '/';
}

// aktuelle Parameter sichern
$currentQuery = $_GET;

foreach ($languages as $l) {

    $flag = $l['flag'];
    $iso  = $l['iso_639_1'];
    $name = $l['name_native'];

    $active = ($iso === $current_lang);
    if ($active) {
        $current_flag = $flag;
    }

    // Query kopieren & Sprache ersetzen
    $query = $currentQuery;
    $query['lang'] = $iso;

    // SEO-URL erzeugen
    $url = \nexpell\SeoUrlHandler::convertToSeoUrl(
        'index.php?' . http_build_query($query)
    );

    $lang_html .= "
        <li>
            <a class='dropdown-item d-flex align-items-center " . ($active ? "active-language" : "") . "'
               href='{$url}'>
               
                <img src='{$flag}' class='me-2'
                     style='width:20px;height:20px;border-radius:4px;'>
                <span>{$name}</span>

                " . ($active ? "<i class='bi bi-check2 ms-auto text-success'></i>" : "") . "
            </a>
        </li>";
}

// MESSENGER BADGE
function nav_messenger_badge(): string
{
    // Plugin aktiv & User eingeloggt?
    if (!PluginManager::isActive('messenger')) {
        return '';
    }

    if (empty($_SESSION['userID'])) {
        return '';
    }

    global $_database;

    // Tabelle vorhanden?
    $check = $_database->query("
        SHOW TABLES LIKE 'plugins_messages'
    ");

    if (!$check || $check->num_rows === 0) {
        return '';
    }

    $uid = (int)$_SESSION['userID'];

    // Ungelesene Nachrichten zählen
    $row = mysqli_fetch_assoc(safe_query("
        SELECT COUNT(*) AS unread
        FROM plugins_messages
        WHERE receiver_id = {$uid}
          AND is_read = 0
    "));

    $unread = (int)($row['unread'] ?? 0);

    // Badge nur wenn > 0
    $badgeHtml = '';
    if ($unread > 0) {
        $badge = ($unread > 99) ? '99+' : $unread;
        $badgeHtml = "<span class='badge rounded-pill bg-danger'>{$badge}</span>";
    }

    $messengerUrl = SeoUrlHandler::convertToSeoUrl(
        'index.php?site=messenger'
    );

    // ICON IMMER ANZEIGEN
    return "
    <li class='nav-item'>
        <a class='nav-link nav-icon-badge' href='$messengerUrl'>
            <span class='icon-wrapper'>
                <i class='bi bi-envelope fs-5'></i>
                {$badgeHtml}
            </span>
        </a>
    </li>";
}
// FORUM BADGE
function nav_forum_badge(): string
{
    // Plugin aktiv & User eingeloggt?
    if (!PluginManager::isActive('forum')) {
        return '';
    }

    if (empty($_SESSION['userID'])) {
        return '';
    }

    global $_database;

    // Tabelle prüfen
    $check = $_database->query("SHOW TABLES LIKE 'plugins_forum_read'");
    if (!$check || $check->num_rows === 0) {
        return '';
    }

    $uid = (int)$_SESSION['userID'];

    // Neue Beiträge zählen
    $row = mysqli_fetch_assoc(safe_query("
        SELECT COUNT(*) AS new_posts
        FROM plugins_forum_posts p
        INNER JOIN plugins_forum_threads t
            ON t.threadID = p.threadID
           AND t.is_deleted = 0
        LEFT JOIN plugins_forum_read r
            ON r.userID = {$uid}
           AND r.threadID = p.threadID
        WHERE p.is_deleted = 0
          AND p.created_at > IFNULL(r.last_read_at, '1970-01-01')
    "));

    $count = (int)($row['new_posts'] ?? 0);

    // Badge nur wenn > 0
    $badgeHtml = '';
    if ($count > 0) {
        $badge = ($count > 99) ? '99+' : $count;
        $badgeHtml = "<span class='badge rounded-pill bg-danger'>{$badge}</span>";
    }

    $forumUrl = SeoUrlHandler::convertToSeoUrl(
        "index.php?site=forum"
    );

    // 💬 ICON IMMER ANZEIGEN
    return "
    <li class='nav-item'>
        <a class='nav-link nav-icon-badge' href='$forumUrl'>
            <span class='icon-wrapper'>
                <i class='bi bi-chat-dots fs-5'></i>
                {$badgeHtml}
            </span>
        </a>
    </li>";
}

// DROPDOWN ANIMATION (nur wenn Engine aktiv = 1)
if ($theme_engine === 1) {

    // Wert aus DB normalisieren (verhindert Fallback wegen Groß-/Kleinschreibung, Leerzeichen, Trennzeichen)
    $rawDropdown = (string)($settings["dropdown_animation"] ?? "slidefade");
    $dropdown = strtolower(trim($rawDropdown));

    // Aliase tolerieren
    $dropdown = str_replace([" ", "_"], ["", ""], $dropdown);
    $dropdown = str_replace("-", "", $dropdown);

    $allowedAnimations = [
        'fade'      => 'fade',
        'fadeup'    => 'fadeup',
        'slide'     => 'slide',
        'slidefade' => 'slidefade',
        'zoom'      => 'zoom',
        'scalefade' => 'scalefade',
        'slideblur' => 'slideblur',
        'tilt'      => 'tilt',
    ];

    $animation = $allowedAnimations[$dropdown] ?? 'slidefade';
    $dropdown_animation = 'data-animation="' . $animation . '"';

} else {
    $dropdown_animation = ""; // deaktiviert
}

// NAVIGATION STYLE (3 MODI)
$navbar_class = "";
$navbar_shadow = "";
$theme_toggle = "";
$html_theme = "";
$navbar_theme = "";
$nav_data_attrs = "";

$nav_height_value = trim((string)($settings["nav_height"] ?? ""));
if ($nav_height_value === '') {
    $nav_height_value = "80px";
}
$nav_height_style = 'style="--nav-height: '.$nav_height_value.';"';

$logo_style = '';
$dynamic_logo_attr = '';

// MODE 0 → Custom CSS Modus
if ($theme_engine === 0) {

    $navbar_class = "";
    $navbar_shadow = "";
    $nav_height_style = "";
    $theme_toggle = "";
    $dropdown_animation = "";
    $html_theme = "";
    $nav_data_attrs = "";

    $logo_style = 'style="max-height: calc('.$nav_height_value.' - 15px);"';
    $dynamic_logo_attr = '';

    $navbar_modus = $settings["navbar_modus"] ?? "auto";
}

// MODE 1 → Theme Engine aktiv
elseif ($theme_engine === 1) {

    $nav_height_value = trim((string)($settings["nav_height"] ?? ""));
    if ($nav_height_value === '') {
        $nav_height_value = "80px";
    }
    $nav_height_style = 'style="--nav-height: '.$nav_height_value.';"';

    $navbar_shadow = $settings["navbar_shadow"] ?? "";
    $navbar_modus  = $settings["navbar_modus"] ?? "auto";

    $logo_style = 'style="max-height: calc('.$nav_height_value.' - 10px);"';
    $dynamic_logo_attr = '';

    $navbar_theme = "";
    $html_theme = "";
    $navbar_class = "";

    // ================================
    // EXTRA NAV SETTINGS (Mode 1 only)
    // ================================
    // Navbar density
    $density = $settings["navbar_density"] ?? "normal";
    $allowedDensity = ["compact", "normal", "loose"];
    $density = in_array($density, $allowedDensity, true) ? $density : "normal";
    $navbar_class .= " nx-density-" . $density;

    // Dropdown style
    $ddStyle = $settings["dropdown_style"] ?? "auto"; // auto|solid|glass
    $allowedDdStyle = ["auto","solid","glass"];
    $ddStyle = in_array($ddStyle, $allowedDdStyle, true) ? $ddStyle : "auto";
    $navbar_class .= " nx-dd-style-" . $ddStyle;

    // Item hover style
    $itemHover = $settings["dropdown_item_hover"] ?? "surface"; // surface|underline|slide|none
    $allowedItemHover = ["surface","underline","slide","none"];
    $itemHover = in_array($itemHover, $allowedItemHover, true) ? $itemHover : "surface";
    $navbar_class .= " nx-itemhover-" . $itemHover;

    // Chevron rotate toggle
    $chevronRotate = (string)($settings["chevron_rotate"] ?? "1"); // 0|1
    if ($chevronRotate === "1") {
        $navbar_class .= " nx-chevron-rotate";
    }

    // Trigger + hover delay as data-attrs for JS
    $trigger = $settings["dropdown_trigger"] ?? "hover"; // hover|click
    $allowedTrigger = ["hover","click"];
    $trigger = in_array($trigger, $allowedTrigger, true) ? $trigger : "hover";

    $hoverDelay = (int)($settings["dropdown_hover_delay"] ?? 120);
    if ($hoverDelay < 0) $hoverDelay = 0;
    if ($hoverDelay > 600) $hoverDelay = 600;

    $nav_data_attrs = 'data-dropdown-trigger="' . htmlspecialchars($trigger, ENT_QUOTES) . '" '
                    . 'data-hover-delay="' . $hoverDelay . '"';

    // Merge CSS vars into the existing nav height style
    $styleVars = [];
    $styleVars[] = '--nav-height: ' . $nav_height_value . ';';

    $nav_height_style = 'style="' . implode(' ', $styleVars) . '"';

}

elseif ($theme_engine === 2) {

    // Theme bestimmt ALLES
    $navbar_shadow = $settings["navbar_shadow"] ?? "";
    $html_theme = '';

    // KEIN nav_height_style → Theme bestimmt Höhe
    $nav_height_style = 'style="--nav-height: '.$nav_height_value.';"';
    $dropdown_animation = "";
    $nav_data_attrs = "";

    // NEU: JS berechnet die Höhe dynamisch
    $logo_style = 'style="max-height: calc('.$nav_height_value.' - 10px);"';
    $dynamic_logo_attr = 'data-dynamic-logo="1"';

    $navbar_modus = $settings["navbar_modus"] ?? "auto";
    $navbar_class = "";

    $dropdown_animation = "";
}

// TEMPLATE DATEN ÜBERGEBEN
$data_array = [

    "html_theme"         => $html_theme,
    "navbar_class"       => $navbar_class,
    "navbar_shadow"      => $navbar_shadow,
    "navbar_theme"       => $navbar_theme,
    "nav_height_style"   => $nav_height_style,
    "nav_data_attrs"     => $nav_data_attrs,
    "logo_height"        => $logo_style,
    "dynamic_logo_attr"  => $dynamic_logo_attr,
    "dropdown_animation" => $dropdown_animation,

    // Logo Position
    "logo_center"       => ($settings["logo_center"] == "1") ? "logo-center" : "",
    "left_side_pos"     => ($settings["logo_center"] == "1") ? "left-fixed" : "right-of-logo",

    // Logos
    "logo_light"        => "/includes/plugins/navigation/images/{$settings["logo_light"]}",
    "logo_dark"         => "/includes/plugins/navigation/images/{$settings["logo_dark"]}",
    "home_url"          => $home_url,

    // Navigation Inhalt
    "mainnav"           => $mainnav_html,
    "usernav"           => nav_user(),
    "messenger_badge"   => nav_messenger_badge(),
    "forum_badge"       => nav_forum_badge(),

    // Languages
    "current_flag"      => $current_flag,
    "language_list"     => $lang_html,

    "theme_toggle"      => $theme_toggle,
];

// TEMPLATE LADEN
echo $tpl->loadTemplate("navigation", "main", $data_array, "plugin");
