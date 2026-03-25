<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
global $_database,$languageService;

if (!function_exists('sponsors_frontend_content')) {
    function sponsors_frontend_content(mysqli $db, string $key, string $lang, string $fallback): string
    {
        $contentKey = mysqli_real_escape_string($db, $key);
        $fallbacks = [strtolower($lang), 'de', 'en', 'it'];

        foreach ($fallbacks as $iso) {
            $isoEsc = mysqli_real_escape_string($db, $iso);
            $res = mysqli_query($db, "SELECT content FROM settings_plugins_lang WHERE content_key = '{$contentKey}' AND language = '{$isoEsc}' LIMIT 1");
            if ($res && ($row = mysqli_fetch_assoc($res)) && trim((string)($row['content'] ?? '')) !== '') {
                return (string)$row['content'];
            }
        }

        return $fallback;
    }
}

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

// Header-Daten
$data_array = [
    'class'    => $class,
    'title' => $languageService->get('title'),
    'subtitle' => 'Sponsors'
];
    
echo $tpl->loadTemplate("sponsors", "head", $data_array, 'plugin');

// Sponsoren-Daten abrufen
$result = safe_query("SELECT * FROM plugins_sponsors WHERE is_active = 1 ORDER BY sort_order ASC");

// Sponsoren-Daten abrufen und Array aufbauen
$sponsors = [];
$levelColors = [
    'platin_sponsor'   => '#00bcd4',
    'gold_sponsor'     => '#ffc107',
    'silber_sponsor'   => '#adb5bd',
    'bronze_sponsor'   => '#cd7f32',
    'partner'          => '#6c757d',
    'unterstuetzer'    => '#999'
];

$imagePath = '/includes/plugins/sponsors/images/';
if ($result && $result->num_rows > 0) {
    while ($ds = mysqli_fetch_array($result)) {
        $levelKey = strtolower(str_replace([' ', 'ü'], ['_', 'ue'], $ds['level']));

        $urlRaw = trim((string)($row['slug'] ?? ''));
            if ($urlRaw) {
                $urlCandidate = (stripos($urlRaw, 'http') === 0) ? $urlRaw : 'http://' . $urlRaw;
                $row['valid_url'] = filter_var($urlCandidate, FILTER_VALIDATE_URL) ? $urlCandidate : '';
            } else {
                $row['valid_url'] = '';
            }
            
            $slug[] = $row;

        $sponsors[] = [
            'id'    => (int)$ds['id'],
            'name'  => htmlspecialchars($ds['name']),
            'logo'  => $imagePath . htmlspecialchars($ds['logo']),
            'level' => $languageService->get($levelKey),
            'color' => $levelColors[$levelKey] ?? '#ccc',
            'slug'  => $slug
        ];
    }

    // Daten in $data_array zusammenfassen
    $currentLang = (string)($languageService->detectLanguage() ?: ($_SESSION['language'] ?? 'de'));
    $data_array = [
        'headline' => sponsors_frontend_content($_database, 'sponsors_headline', $currentLang, $languageService->get('info_title')),
        'text'     => sponsors_frontend_content($_database, 'sponsors_intro', $currentLang, $languageService->get('info_text')),
        'sponsors' => $sponsors
    ];

    echo $tpl->loadTemplate("sponsors", "main", $data_array, "plugin");
} else {
    // Keine Partner vorhanden → Hinweis anzeigen
    echo '<div class="alert alert-info">' . $languageService->get('no_sponsors_found') . '</div>';
}
