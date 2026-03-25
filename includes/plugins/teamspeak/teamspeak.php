<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
global $_database, $languageService, $tpl;

$languageService->readPluginModule('teamspeak');

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
require_once __DIR__ . '/system/TeamSpeakTreeBuilder.php';
require_once __DIR__ . '/system/TeamSpeakHtmlRenderer.php';
require_once __DIR__ . '/system/ServerGeoIp.php';

// Server laden
$res = safe_query("
    SELECT *
    FROM plugins_teamspeak
    WHERE enabled = 1
    ORDER BY sort_order ASC, id ASC
");

$serversHtml = '';

while ($srv = mysqli_fetch_assoc($res)) {

    // TS SERVICE (PRO SERVER!)
    $service = new TeamSpeakService($srv);
    $data    = $service->getServerData();


$serverID = $srv['host'] ?? '';

if (
    empty($srv['server_country'])
    && $serverID !== ''
) {
    $country = ServerGeoIp::getCountry($serverID);

    if ($country !== '') {
        safe_query("
            UPDATE plugins_teamspeak
            SET server_country = '" . escape(strtoupper($country)) . "'
            WHERE id = " . (int)$srv['id']
        );

        $srv['server_country'] = strtoupper($country);
    }
}

    // Baum bauen
    $tree = TeamSpeakTreeBuilder::build(
        $data['channels'] ?? [],
        $data['clients']  ?? []
    );

    // HTML rendern
    $renderer = new TeamSpeakHtmlRenderer($srv);

    $treeHtml = $data['online']
        ? $renderer->render($tree)
        : '<div class="alert alert-warning mb-0">' . $languageService->get('info_server_offline') . '</div>';

    // User zählen (nur echte User)
    $userCount = 0;
    foreach ($data['clients'] ?? [] as $cl) {
        if ((int)($cl['client_type'] ?? 1) === 0) {
            $userCount++;
        }
    }

    // Server-Template
    $serverCountry = strtoupper($srv['server_country'] ?? '');

    $flagPath = '';

    if ($serverCountry !== '') {
        $resFlag = safe_query("
            SELECT flag
            FROM settings_languages
            WHERE iso_639_1 = '" . escape($serverCountry) . "'
            LIMIT 1
        ");

        if ($resFlag && mysqli_num_rows($resFlag) === 1) {
            $rowFlag = mysqli_fetch_assoc($resFlag);
            $flagPath = $rowFlag['flag'] ?? '';
        }
    }

    $flagHtml = '';

    if ($flagPath !== '') {
        $flagHtml = '<img src="' . htmlspecialchars($flagPath) . '" 
            alt="' . strtoupper($serverCountry) . '" 
            class="ts-server-flag">';
    }

    $serversHtml .= $tpl->loadTemplate(
        'teamspeak',
        'server_item',
        [
            'serverID'   => (int)$srv['id'],
            'server_name' => ($data['server']['name'] ?: $srv['title'])
                           . ($flagHtml !== '' ? ' ' . $flagHtml : ''),

            'cache_time'  => max(5, (int)$srv['cache_time']),
            'server_used' => $userCount,
            'server_max'  => $data['server']['max'],
            'tree_html'   => $treeHtml
        ],
        'plugin'
    );
}

// MAIN TEMPLATE
echo $tpl->loadTemplate(
    'teamspeak',
    'main',
    ['servers' => $serversHtml],
    'plugin'
);
