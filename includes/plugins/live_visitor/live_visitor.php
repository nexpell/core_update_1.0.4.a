<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
global $languageService, $_database;

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

// Header-Daten
$data_array = [
    'class'    => $class,
    'title'    => $languageService->get('title'),
    'subtitle' => $languageService->get('list_live_visitors')
];

echo $tpl->loadTemplate("live_visitor", "head", $data_array, 'plugin');

function getVisitorCounter(mysqli $_database): array {
    $today_date    = date('Y-m-d');
    $yesterday     = date('Y-m-d', strtotime('-1 day'));
    $month_start   = date('Y-m-01');
    $online_window_ago = time() - (defined('VISITOR_ONLINE_WINDOW_SECONDS') ? VISITOR_ONLINE_WINDOW_SECONDS : 600);

    // Heute (Hits aus daily_counter)
    $today_hits = (int)$_database->query("
        SELECT SUM(hits) AS hits
        FROM visitor_daily_counter
        WHERE DATE(date) = '$today_date'
    ")->fetch_assoc()['hits'];

    // Gestern
    $yesterday_hits = (int)$_database->query("
        SELECT SUM(hits) AS hits
        FROM visitor_daily_counter
        WHERE DATE(date) = '$yesterday'
    ")->fetch_assoc()['hits'];

    // Monat
    $month_hits = (int)$_database->query("
        SELECT SUM(hits) AS hits
        FROM visitor_daily_counter
        WHERE date >= '$month_start'
    ")->fetch_assoc()['hits'];

    // Gesamt
    $total_hits = (int)$_database->query("
        SELECT SUM(hits) AS hits
        FROM visitor_daily_counter
    ")->fetch_assoc()['hits'];

    // Online (gleiches Zeitfenster wie Tracking)
    $online_visitors = (int)$_database->query("
        SELECT COUNT(*) AS cnt
        FROM visitors_live
        WHERE time >= $online_window_ago
    ")->fetch_assoc()['cnt'];

    // MaxOnline (aus daily_counter)
    $max_online = (int)$_database->query("
        SELECT MAX(maxonline) AS maxcnt
        FROM visitor_daily_counter
    ")->fetch_assoc()['maxcnt'];

    return [
        'today'     => $today_hits,
        'yesterday' => $yesterday_hits,
        'month'     => $month_hits,
        'total'     => $total_hits,
        'online'    => $online_visitors,
        'maxonline' => $max_online
    ];
}

// --- Zeitpunkt für letzte 10 Minuten ---

// --- Bot-Bedingung ---

$online_window_ago = time() - (defined('VISITOR_ONLINE_WINDOW_SECONDS') ? VISITOR_ONLINE_WINDOW_SECONDS : 600);

$res_online = $_database->query("
    SELECT vs.*, u.username, up.avatar
    FROM visitors_live vs
    LEFT JOIN users u ON u.userID = vs.userID AND u.is_active = 1
    LEFT JOIN user_profiles up ON up.userID = vs.userID
    WHERE vs.time >= $online_window_ago
    ORDER BY vs.time DESC
");

$online_count = $res_online->num_rows;


$history_window_ago = time() - (defined('VISITOR_HISTORY_WINDOW_SECONDS') ? VISITOR_HISTORY_WINDOW_SECONDS : 86400);

$sql = "
SELECT vh.*, u.username, up.avatar
FROM visitors_live_history vh
INNER JOIN (
    SELECT
        userID,
        MAX(time) AS last_time
    FROM visitors_live_history
    WHERE time >= $history_window_ago
      AND userID IS NOT NULL
      AND userID > 0
    GROUP BY userID
) latest
    ON latest.userID = vh.userID
   AND latest.last_time = vh.time
INNER JOIN users u
    ON u.userID = vh.userID
   AND u.is_active = 1
LEFT JOIN user_profiles up ON up.userID = vh.userID
WHERE vh.time >= $history_window_ago
  AND vh.userID IS NOT NULL
  AND vh.userID > 0
ORDER BY vh.time DESC
";

$res_history = $_database->query($sql);
$history_count = $res_history->num_rows;

$counter = getVisitorCounter($_database);

function normalize_country_code(?string $country_code): string {
    $country_code = strtolower(trim((string)$country_code));
    $country_code = str_replace(['_', '.'], '-', $country_code);
    $country_code = preg_replace('/\s+/', ' ', $country_code);

    if ($country_code === '' || $country_code === 'unknown') {
        return 'unknown';
    }

    if (preg_match('/^[a-z]{2}-[a-z0-9]+$/', $country_code)) {
        $country_code = substr($country_code, 0, 2);
    }

    $country_aliases = [
        'de' => 'de',
        'deu' => 'de',
        'ger' => 'de',
        'germany' => 'de',
        'deutschland' => 'de',
        'at' => 'at',
        'aut' => 'at',
        'austria' => 'at',
        'oesterreich' => 'at',
        'ch' => 'ch',
        'che' => 'ch',
        'switzerland' => 'ch',
        'schweiz' => 'ch',
        'fr' => 'fr',
        'fra' => 'fr',
        'france' => 'fr',
        'it' => 'it',
        'ita' => 'it',
        'italy' => 'it',
        'italien' => 'it',
        'es' => 'es',
        'esp' => 'es',
        'spain' => 'es',
        'spanien' => 'es',
        'gb' => 'gb',
        'gbr' => 'gb',
        'uk' => 'gb',
        'united kingdom' => 'gb',
        'great britain' => 'gb',
        'england' => 'gb',
        'us' => 'us',
        'usa' => 'us',
        'united states' => 'us',
        'united states of america' => 'us',
        'vereinigte staaten' => 'us',
        'nl' => 'nl',
        'nld' => 'nl',
        'netherlands' => 'nl',
        'niederlande' => 'nl',
        'be' => 'be',
        'bel' => 'be',
        'belgium' => 'be',
        'belgien' => 'be',
        'pl' => 'pl',
        'pol' => 'pl',
        'poland' => 'pl',
        'polen' => 'pl',
        'tr' => 'tr',
        'tur' => 'tr',
        'turkey' => 'tr',
        'tuerkei' => 'tr',
        'ru' => 'ru',
        'rus' => 'ru',
        'russia' => 'ru',
        'russland' => 'ru',
    ];

    if (isset($country_aliases[$country_code])) {
        return $country_aliases[$country_code];
    }

    $country_code_compact = preg_replace('/[^a-z]/', '', $country_code);

    if (isset($country_aliases[$country_code_compact])) {
        return $country_aliases[$country_code_compact];
    }

    if (preg_match('/^[a-z]{2}$/', $country_code)) {
        return $country_code;
    }

    return 'unknown';
}

function get_country_flag_file(?string $country_code): string {
    $country_code = normalize_country_code($country_code);
    $flag_web_path = "/admin/images/flags/{$country_code}.svg";

    $base_path = defined('BASE_PATH') ? rtrim((string)BASE_PATH, '/\\') : dirname(__DIR__, 3);
    $flag_fs_path = $base_path . str_replace('/', DIRECTORY_SEPARATOR, $flag_web_path);

    if (is_file($flag_fs_path)) {
        return $flag_web_path;
    }

    $fallback_web_path = "/admin/images/flags/unknown.svg";
    $fallback_fs_path = $base_path . str_replace('/', DIRECTORY_SEPARATOR, $fallback_web_path);

    if (is_file($fallback_fs_path)) {
        return $fallback_web_path;
    }

    return $flag_web_path;
}
?>

<div class="pb-4">

    <div class="row">
        <div class="col-md-3 mb-3"><div class="card text-center"><div class="card-body"><h6><?= $languageService->get('visits_today') ?></h6><span class="badge bg-primary"><?= $counter['today'] ?></span></div></div></div>
        <div class="col-md-3 mb-3"><div class="card text-center"><div class="card-body"><h6><?= $languageService->get('visits_this_month') ?></h6><span class="badge bg-success"><?= $counter['month'] ?></span></div></div></div>
        <div class="col-md-3 mb-3"><div class="card text-center"><div class="card-body"><h6><?= $languageService->get('visits_total') ?></h6><span class="badge bg-secondary"><?= $counter['total'] ?></span></div></div></div>
        <div class="col-md-3 mb-3"><div class="card text-center"><div class="card-body"><h6><?= $languageService->get('list_live_visitors') ?></h6><span class="badge bg-warning text-dark"><?= $counter['online'] ?></span></div></div></div>
</div>

<div class="card p-3 mb-3">
<?php


function render_visitors_card(mysqli_result $res, string $type = 'online') {
    global $languageService;

    $lang = $languageService->detectLanguage();
    $languageService->readPluginModule('live_visitor');

    echo '<div class="card p-3 mb-3">';
    echo '<h5 class="mb-3">';
    echo ($type === 'online'
        ? $languageService->get('online_visitors')
        : $languageService->get('historical_visitors'));
    echo '</h5>';

    echo '<ul class="list-group list-group-flush">';

    while ($row = mysqli_fetch_assoc($res)) {
        if ($type === 'history' && empty($row['userID'])) {
            continue;
        }

        $username = $row['username'] ?? $languageService->get('visitor_guest');

        if (!empty($row['userID'])) {
            $avatar = getavatar($row['userID']);
        } else {
            // Fallback für Gäste → direkt Initialen "G"
            $avatar = '/images/avatars/svg-avatar.php?name=Gast';
        }
        
        $page_url = $row['site'] ?? '#';

        // --- Seite bestimmen ---
        $page_key = 'start'; // Default
        $parsed = parse_url($page_url);
        $path   = $parsed['path'] ?? '/';
        if (!empty($parsed['query']) && preg_match('/site=([^&]+)/', $parsed['query'], $m)) {
            $page_key = $m[1];
        } elseif (preg_match('#^/(de|en|it)/([^/]+)#', $path, $m)) {
            $page_key = $m[2];
        }

        $page_key = strtolower(preg_replace('/[^a-z0-9_]/i', '', $page_key));

        // Mehrsprachige Anzeige
        // ---------- Mehrsprachige Liste ----------
        $array_watching = [
            'about' => [
                'de' => 'die About Us Seite',
                'en' => 'the About Us page',
                'it' => 'la pagina Chi siamo'
            ],
            'blog' => [
                'de' => 'den Blog',
                'en' => 'the blog',
                'it' => 'il blog'
            ],
            'forum' => [
                'de' => 'das Forum',
                'en' => 'the forum',
                'it' => 'il forum'
            ],
            'gallery' => [
                'de' => 'die Galerie',
                'en' => 'the gallery',
                'it' => 'la galleria'
            ],
            'counter' => [
                'de' => 'den Counter',
                'en' => 'the counter',
                'it' => 'il contatore'
            ],
            'live_visitor' => [
                'de' => 'den Live-Besucher',
                'en' => 'the Live Visitors',
                'it' => 'i visitatori in tempo reale'
            ],
            'shoutbox' => [
                'de' => 'die Shoutbox',
                'en' => 'the shoutbox',
                'it' => 'la shoutbox'
            ],
            'leistung' => [
                'de' => 'die Leistung',
                'en' => 'the service',
                'it' => 'il servizio'
            ],
            'info' => [
                'de' => 'die Info-Seite',
                'en' => 'the info page',
                'it' => 'la pagina info'
            ],
            'resume' => [
                'de' => 'der Lebenslauf',
                'en' => 'the resume',
                'it' => 'il curriculum'
            ],
            'todo' => [
                'de' => 'die ToDo-Liste',
                'en' => 'the todo list',
                'it' => 'la lista delle cose da fare'
            ],
            'articles' => [
                'de' => 'die Artikel',
                'en' => 'the articles',
                'it' => 'gli articoli'
            ],
            'achievements' => [
                'de' => 'die Erfolge',
                'en' => 'the achievements',
                'it' => 'i successi'
            ],
            'userlist' => [
                'de' => 'die Benutzerliste',
                'en' => 'the user list',
                'it' => 'la lista utenti'
            ],
            'downloads' => [
                'de' => 'die Downloads',
                'en' => 'the downloads',
                'it' => 'i download'
            ],
            'partners' => [
                'de' => 'die Partner',
                'en' => 'the partners',
                'it' => 'i partner'
            ],
            'wiki' => [
                'de' => 'das Wiki',
                'en' => 'the wiki',
                'it' => 'il wiki'
            ],
            'search' => [
                'de' => 'die Suche',
                'en' => 'the search',
                'it' => 'la ricerca'
            ],
            'contact' => [
                'de' => 'die Kontaktseite',
                'en' => 'the contact page',
                'it' => 'la pagina contatti'
            ],
            'gametracker' => [
                'de' => 'der GameTracker',
                'en' => 'the gametracker',
                'it' => 'il gametracker'
            ],
            'discord' => [
                'de' => 'der Discord-Server',
                'en' => 'the Discord server',
                'it' => 'il server Discord'
            ],
            'twitch' => [
                'de' => 'der Twitch-Kanal',
                'en' => 'the Twitch channel',
                'it' => 'il canale Twitch'
            ],
            'youtube' => [
                'de' => 'der YouTube-Kanal',
                'en' => 'the YouTube channel',
                'it' => 'il canale YouTube'
            ],
            'imprint' => [
                'de' => 'das Impressum',
                'en' => 'the imprint',
                'it' => 'l\'impressum'
            ],
            'privacy_policy' => [
                'de' => 'die Datenschutzrichtlinie',
                'en' => 'the privacy policy',
                'it' => 'la politica sulla privacy'
            ],
            'links' => [
                'de' => 'die Links',
                'en' => 'the links',
                'it' => 'i link'
            ],
            'pricing' => [
                'de' => 'die Preisübersicht',
                'en' => 'the pricing',
                'it' => 'i prezzi'
            ],
            'rules' => [
                'de' => 'die Regeln',
                'en' => 'the rules',
                'it' => 'le regole'
            ],
            'messenger' => [
                'de' => 'der Messenger',
                'en' => 'the messenger',
                'it' => 'il messenger'
            ],
                'start' => [
                'de' => 'Startseite',
                'en' => 'Homepage',
                'it' => 'Pagina iniziale'
            ],
            'login' => [
                'de' => 'Login',
                'en' => 'Login',
                'it' => 'Login'
            ],
            'register' => [
                'de' => 'Registrierung',
                'en' => 'Register',
                'it' => 'Registrazione'
            ],
            'lostpassword' => [
                'de' => 'Passwort vergessen',
                'en' => 'Lost password',
                'it' => 'Password dimenticata'
            ],
            'profile' => [
                'de' => 'Profil',
                'en' => 'Profile',
                'it' => 'Profilo'
            ],
            'edit_profile' => [
                'de' => 'Profil bearbeiten',
                'en' => 'Edit profile',
                'it' => 'Modifica profilo'
            ],
            '#' => [
                'de' => 'eine unbekannte Seite',
                'en' => 'an unknown page',
                'it' => 'una pagina sconosciuta'
            ]
        ];

        $page_display = $array_watching[$page_key][$lang] 
            ?? ($array_watching[$page_key]['en'] ?? $page_key);

        // --- Flagge ---
        $country_code = normalize_country_code($row['country_code'] ?? 'unknown');
        $flag_file = get_country_flag_file($country_code);

        // --- Zeit ---
        $timestamp = $row['time'] ?? time(); // Unix-Timestamp
        $time = date("d.m.Y H:i", $timestamp); // Direkt formatieren, nicht nochmal strtotime

        // --- Ausgabe ---
        echo '<li class="list-group-item visitor-item d-flex justify-content-between align-items-center">';
        echo '<div class="visitor-left d-flex align-items-center">';
        if ($avatar) {
            echo '<img src="' . htmlspecialchars($avatar) . '" class="avatar me-2" alt="Avatar">';
        }
        echo '<strong>' . htmlspecialchars($username) . '</strong>';
        echo '</div>';

        echo '<div class="visitor-middle text-start flex-grow-1 ms-3">';
        if ($type === 'online') {
            echo ($lang === 'de' ? 'schaut an ' : ($lang === 'en' ? 'is watching ' : 'sta guardando '));
        } else {
            echo ($lang === 'de' ? 'schaute an ' : ($lang === 'en' ? 'watched ' : 'ha guardato '));
        }
        echo '<a href="' . htmlspecialchars($page_url) . '">' . htmlspecialchars($page_display) . '</a>';
        echo '</div>';

        echo '<div class="visitor-right d-flex align-items-center justify-content-end gap-2 ms-auto text-end">';
        echo '<small class="text-muted mb-0">' . htmlspecialchars($time) . '</small>';
        echo '<span class="visitor-flag" title="' . htmlspecialchars(strtoupper($country_code)) . '" style="background-image:url(\''
            . htmlspecialchars($flag_file) . '\');"></span>';
        echo '</div>';

        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
}

// Aufruf bleibt unverändert
render_visitors_card($res_online, 'online');
render_visitors_card($res_history, 'history');

