<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
global $_database, $languageService, $tpl;

// Styleklasse laden
$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

// Header-Daten
$data_array = [
    'class' => $class,
    'title' => $languageService->get('title'),
    'subtitle' => 'Teamspeak'
];
echo $tpl->loadTemplate("teamspeak", "head", $data_array, "plugin");

require_once __DIR__ . '/system/TeamSpeakService.php';

// AKTIVE SERVER LADEN
$res = safe_query("
    SELECT *
    FROM plugins_teamspeak
    WHERE enabled = 1
    ORDER BY sort_order ASC, id ASC
");

$servers = [];

// SERVER DURCHGEHEN
while ($srv = mysqli_fetch_assoc($res)) {

    $service = new TeamSpeakService($srv);
    $data    = $service->getServerData();

$realUsers = 0;

foreach ($data['clients'] ?? [] as $cl) {
    if ((int)($cl['client_type'] ?? 1) === 0) {
        $realUsers++;
    }
}

$servers[] = [
    'title'  => $srv['title'],
    'online' => !empty($data['online']),
    'used'   => $realUsers,
    'max'    => (int)($data['server']['max'] ?? 0),
];

}

// FALLBACK: KEIN SERVER
if (empty($servers)) {
    echo '<div class="alert alert-warning mb-0">' . $languageService->get('info_no_server') . '</div>';
    return;
}

// WIDGET RENDERN
echo $tpl->loadTemplate(
    'teamspeak',
    'widget',
    [
        'servers' => $servers
    ],
    'plugin'
);