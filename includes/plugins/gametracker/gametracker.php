<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

global $_database, $languageService, $tpl;

// Stilklasse laden
$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

// Header
$data_array = [
    'class' => $class,
    'title' => $languageService->get('server_preview'),
    'subtitle' => 'Server Preview'
];
echo $tpl->loadTemplate("gametracker", "head", $data_array, 'plugin');

use xPaw\SourceQuery\SourceQuery;

require __DIR__ . '/GameQ/Autoloader.php';
use GameQ\GameQ;

function stripColorCodes(?string $text): string {
    // $text kann jetzt null sein, wird mit '' versehen
    return preg_replace('/\^\d/', '', $text ?? '');
}

function colorizePlayerName($name) {
    // Optional: zuerst HTML escapen, falls nötig
    $name = htmlspecialchars($name, ENT_QUOTES);

    // Entferne weißen CoD-Farbcode (^7) und ersetze ihn mit neutraler Farbe
    $name = preg_replace('/\^7/', '<span style="color:inherit">', $name);

    // Andere Farbcodes (z. B. ^1–^6, ^0) wie gewohnt behandeln
    $colors = [
        '0' => '#000000', // Schwarz
        '1' => '#FF0000', // Rot
        '2' => '#00FF00', // Grün
        '3' => '#FFFF00', // Gelb
        '4' => '#0000FF', // Blau
        '5' => '#00FFFF', // Cyan
        '6' => '#FF00FF', // Magenta
        // '7' => '#FFFFFF', // Weiß -> entfernen
        '8' => '#FFA500', // Orange
        '9' => '#A52A2A', // Braun
    ];

    foreach ($colors as $code => $hex) {
        $name = str_replace("^$code", "<span style=\"color:$hex\">", $name);
    }

    // Schließe offene <span> automatisch (optional)
    $openSpans = substr_count($name, '<span');
    $closeSpans = substr_count($name, '</span>');
    $name .= str_repeat('</span>', $openSpans - $closeSpans);

    return $name;
}

function generateJoinLink(string $ip, int $port, string $game): ?string {
    $game = strtolower($game);

    $steamGames = [
        'csgo', 'css', 'cs16', 'czero', 'dod', 'dod_source', 'tf2', 'tf',
        'hl', 'hl2', 'hl2mp', 'gmod', 'garrysmod', 'left4dead2',
        'dayofdefeat', 'ins', 'insurgency'
    ];

    $codGames = [
        'cod', 'coduo', 'cod2', 'cod4', 'codmw2', 'codmw3',
        'codwaw', 'codbo', 'codbo2'
    ];

    switch (true) {
        case in_array($game, $steamGames, true):
            return "steam://connect/{$ip}:{$port}";

        case $game === 'fivem':
            return "fivem://connect/{$ip}:{$port}";

        case in_array($game, ['ts3', 'teamspeak'], true):
            return "ts3server://{$ip}:{$port}";

        case $game === 'minecraft':
            return ($port == 25565) ? $ip : "{$ip}:{$port}";

        case in_array($game, ['samp', 'mta'], true):
            return "{$game}://{$ip}:{$port}";

        case in_array($game, $codGames, true):
            return "{$ip}:{$port}"; // kein direkter Link – manuell beitreten

        case in_array($game, [
            'unturned', 'rust', 'valheim', 'conanexiles', '7daystodie', 'hurtworld',
            'arkse', 'dayz', 'terraria', 'arma', 'arma2', 'arma3', 'squad'
        ], true):
            return "{$ip}:{$port}"; // nur IP/Port anzeigen

        default:
            return null;
    }
}
    // Mapping für lesbare Namen (wie bei GameTracker)
    $modFriendlyNames = [
    'cs16'           => 'Counter-Strike 1.6',
    'cs16'           => 'Counter-Strike: Condition Zero',  // 'czero' → 'cs16'
    'css'            => 'Counter-Strike: Source',
    'css'            => 'Counter-Strike: Source',          // 'cs_source' → 'css'
    'csgo'           => 'Counter-Strike: Global Offensive',
    'cod'            => 'Call of Duty',
    'coduo'          => 'Call of Duty: United Offensive',
    'cod2'           => 'Call of Duty 2',
    'cod4'           => 'Call of Duty 4: Modern Warfare',
    'codmw2'         => 'Call of Duty: Modern Warfare 2',
    'codmw3'         => 'Call of Duty: Modern Warfare 3',
    'codwaw'         => 'Call of Duty: World at War',
    'codbo'          => 'Call of Duty: Black Ops',
    'codbo2'         => 'Call of Duty: Black Ops II',
    'bf1942'         => 'Battlefield 1942',
    'bfv'            => 'Battlefield Vietnam',
    'bf2'            => 'Battlefield 2',
    'bf2142'         => 'Battlefield 2142',
    'bf3'            => 'Battlefield 3',
    'bf4'            => 'Battlefield 4',
    'bfbc2'          => 'Battlefield: Bad Company 2',
    'hl'             => 'Half-Life',
    'hl2'            => 'Half-Life 2',
    'hl2mp'          => 'Half-Life 2 Deathmatch',
    'dod'            => 'Day of Defeat',
    'dod_source'     => 'Day of Defeat: Source',
    'tf'             => 'Team Fortress Classic',
    'tf2'            => 'Team Fortress 2',
    'gmod'           => 'Garry\'s Mod',
    'gmod'           => 'Garry\'s Mod',                    // 'garrysmod' → 'gmod'
    'ut'             => 'Unreal Tournament',
    'ut2003'         => 'Unreal Tournament 2003',
    'ut2004'         => 'Unreal Tournament 2004',
    'ut3'            => 'Unreal Tournament 3',
    'q3a'            => 'Quake III Arena',
    'q4'             => 'Quake 4',
    'samp'           => 'San Andreas Multiplayer',
    'mta'            => 'Multi Theft Auto',
    'mohaa'          => 'Medal of Honor: Allied Assault',
    'moh'            => 'Medal of Honor',
    'mohwf'          => 'Medal of Honor: Warfighter',
    'rtcw'           => 'Return to Castle Wolfenstein',
    'et'             => 'Wolfenstein: Enemy Territory',
    'sof2'           => 'Soldier of Fortune II',
    'ravenshield'    => 'Rainbow Six 3: Raven Shield',
    'minecraft'      => 'Minecraft',
    'unturned'       => 'Unturned',
    'rust'           => 'Rust',
    'valheim'        => 'Valheim',
    'conanexiles'    => 'Conan Exiles',
    '7daystodie'     => '7 Days to Die',
    'hurtworld'      => 'Hurtworld',
    'arkse'          => 'ARK: Survival Evolved',
    'dayz'           => 'DayZ',
    'terraria'       => 'Terraria',
    'arma'           => 'ARMA: Cold War Assault',
    'arma2'          => 'ARMA 2',
    'arma3'          => 'ARMA 3',
    'insurgency'     => 'Insurgency',
    'insurgency'     => 'Insurgency',                      // 'ins' → 'insurgency'
    'squad'          => 'Squad',
    'fivem'          => 'FiveM',
    'factorio'       => 'Factorio',
    ];


if (isset($_GET['action']) && $_GET['action'] === 'serverdetails' && isset($_GET['id'])) {
    $serverId = (int)$_GET['id'];
    $server = safe_query("SELECT * FROM plugins_gametracker_servers WHERE id = $serverId");

    if (mysqli_num_rows($server)) {
        $ds = mysqli_fetch_array($server);

        $queryList = [[
            'id'   => 'server_' . $ds['id'],
            'type' => strtolower($ds['game']),
            'host' => $ds['ip'] . ':' . $ds['port'],
        ]];

        $gq = new GameQ();
        $gq->addServers($queryList);
        $results = $gq->process();

        $info = $results['server_' . $ds['id']];

        echo '<div class="card">';
        echo '<div class="card-header">';
        echo '<span class="fw-semibold">' . htmlspecialchars(stripColorCodes($info['gq_hostname'] ?? $ds['name'])) . '</span>';
        echo '</div><div class="card-body">';

        if (!empty($info['gq_online'])) {
            echo '<div class="row">';

            echo '<div class="col-md-6 mb-2">';

            $gameQType = strtolower($ds['game']); // bleibt "source"
            $realGame = isset($ds['mod']) && !empty($ds['mod']) ? strtolower($ds['mod']) : strtolower($ds['game']);

            $gqMod = strtolower($info['gq_mod'] ?? '');
            $gameRaw = strtolower($ds['game'] ?? '');

            // Ermittlung des Namens
            if (isset($modFriendlyNames[$gqMod])) {
                $detectedGame = $modFriendlyNames[$gqMod];
            } elseif (isset($modFriendlyNames[$gameRaw])) {
                $detectedGame = $modFriendlyNames[$gameRaw];
            } else {
                $detectedGame = ucfirst($gqMod ?: $gameRaw ?: 'Unbekannt');
            }

            // Ausgabe
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_game') . ':</strong> ' . htmlspecialchars($detectedGame) . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_gametype') . ':</strong> ' . htmlspecialchars($info['g_gametype'] ?? '-') . '</div>';

            // Spieleranzeige (immer)
            $players = (int)($info['gq_numplayers'] ?? 0);
            $maxPlayers = (int)($info['gq_maxplayers'] ?? 0);

            // Farbe der Badge je nach Spieleranzahl
            $badgeClass = ($players > 0) ? 'text-bg-success' : 'text-bg-secondary';
            ?>
            <style>.badge.custom-size {
                font-size: var(--bs-body-font-size);
            }
            </style>
            <?php
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_players') . ':</strong> <span class="badge ' . $badgeClass . ' custom-size">' . $players . ' / ' . $maxPlayers . '</span></div>';

            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_transport') . ':</strong> ' . htmlspecialchars($info['gq_transport'] ?? '-') . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_clients') . ':</strong> ' . (int)($info['sv_privateClients'] ?? 0) . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_location') . ':</strong> ' . htmlspecialchars($info['.Location'] ?? '-') . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_protocol') . ':</strong> ' . htmlspecialchars($info['gq_protocol'] ?? '-') . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_punkbuster') . ':</strong> ' . ((isset($info['sv_punkbuster']) && $info['sv_punkbuster']) ? '<span class="badge bg-success">' . htmlspecialchars($languageService->get('mode_active'), ENT_QUOTES, 'UTF-8'). '</span>' : '<span class="badge bg-secondary">' . htmlspecialchars($languageService->get('mode_deactivated'), ENT_QUOTES, 'UTF-8') . '</span>') . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_mod') . ':</strong> ' . htmlspecialchars($info['gq_mod'] ?? '-') . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_version') . ':</strong> ' . htmlspecialchars($info['shortversion'] ?? '-') . '</div>';            

            $ip   = $ds['ip'] ?? '';
            $port = (int)($ds['port'] ?? 0);
            $game = $ds['game'] ?? '';

            $joinLink = generateJoinLink($ip, $port, $game);

            if ($joinLink) {
                $copyValue = $joinLink; // echter Wert zum Kopieren
                $displayText = $languageService->get('action_join_server');
                $isLink = true;

                // Je nach Protokoll anzeigen
                if (str_starts_with($joinLink, 'steam://')) {
                    $displayText = $languageService->get('action_join_steam');
                } elseif (str_starts_with($joinLink, 'fivem://')) {
                    $displayText = $languageService->get('action_join_fivem');
                } elseif (str_starts_with($joinLink, 'ts3server://')) {
                    $displayText = $languageService->get('action_join_teamspeak');
                } elseif (!str_contains($joinLink, '://')) {
                    // Nur IP → kein Link!
                $displayText = $languageService->get('action_copy_ip') . ': ' . htmlspecialchars($joinLink, ENT_QUOTES, 'UTF-8');
                    $isLink = false;
                }

                // Ausgabe
                echo '<p><strong>Beitreten:</strong> ';
                
                if ($isLink) {
                    echo '<a href="' . htmlspecialchars($joinLink) . '" target="_blank" rel="noopener">' . htmlspecialchars($displayText) . '</a>';
                } else {
                    echo '<span id="server-ip-' . $ds['id'] . '">' . htmlspecialchars($joinLink) . '</span>';
                    echo '<button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard(\'server-ip-' . $ds['id'] . '\')">' . $languageService->get('btn_copy') . '</button>';
                }

                echo '</p>';
            } else {
                echo '<p><strong>' . $languageService->get('info_join') . ':</strong> ' . $languageService->get('info_cant_join') . '</p>';
            }

            ?>
            <script>
            window.translations = window.translations || {};
            Object.assign(window.translations, {
                copyIpSuccess: <?= json_encode($languageService->get('info_copy_ip_success')) ?>,
                copyError: <?= json_encode($languageService->get('info_copy_error')) ?>
            });

            function formatWithValue(template, value) {
                // Wenn %s vorhanden ist -> ersetzen, sonst hinten anhängen
                return (template && template.includes('%s'))
                ? template.replace('%s', value)
                : `${template} ${value}`;
            }

            function copyToClipboard(id) {
                const el = document.getElementById(id);
                if (!el) return;

                const text = (el.innerText || el.textContent || '').trim();

                navigator.clipboard.writeText(text).then(
                function () {
                    alert(formatWithValue(window.translations.copyIpSuccess, text));
                },
                function (err) {
                    alert(formatWithValue(window.translations.copyError, String(err)));
                }
                );
            }
            </script>
            <?php

            echo '</div><div class="col-md-6 mb-2">';

            // Map-Name anzeigen
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_map') . ':</strong> ' . htmlspecialchars($info['mapname'] ?? $info['map'] ?? '-') . '</div>';
            $gameRaw = $ds['game'] ?? '';
            $modRaw  = htmlspecialchars($info['gq_mod'] ?? '-');

            // Bestimme echten Game-Namen für GameTracker (z. B. bei "source" durch den Mod ersetzen)
            if (strtolower($gameRaw) === 'source' && !empty($modRaw)) {
                $gtGame = strtolower($modRaw);
            } else {
                $gtGame = strtolower($gameRaw);
            }

            // Mapnamen vorbereiten
            $mapname = strtolower($info['mapname'] ?? $info['map'] ?? '');
            $mapImagePath = "https://image.gametracker.com/images/maps/160x120/" . $ds['game_pic'] . "/" . $mapname . ".jpg";

            // Mapanzeige
            echo '<div class="col-md-12 mb-2">';
            echo '<img src="' . $mapImagePath . '" alt="' . htmlspecialchars($mapname) . '" class="img-fluid rounded shadow-sm" style="max-height:250px;" onerror="this.onerror=null;this.src=\'includes/plugins/gametracker/images/map_no_image.jpg\';">';
            echo '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_ip') . '/' . $languageService->get('label_port') . ':</strong> ' . htmlspecialchars($ds['ip'] . ':' . $ds['port']) . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_admin') . ':</strong> ' . htmlspecialchars($info['.Admin'] ?? '-') . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_email') . ':</strong> ' . htmlspecialchars($info['.Email'] ?? '-') . '</div>';
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_website') . ':</strong> <a href="' . htmlspecialchars($info['.Website'] ?? '#') . '" target="_blank">' . htmlspecialchars($info['.Website'] ?? '-') . '</a></div>';
                        
            $ip   = $ds['ip'] ?? '';
            $port = $ds['port'] ?? '';

            if (!empty($ip) && !empty($port)) {
                $gtUrl  = "https://www.gametracker.com/server_info/{$ip}:{$port}/";
                $gtImg  = "https://cache.gametracker.com/server_info/{$ip}:{$port}/b_560_95_1.png";

                echo '<div class="col-md-12 mb-3">';
                echo '<a href="' . htmlspecialchars($gtUrl) . '" target="_blank">';
                echo '<img src="' . htmlspecialchars($gtImg) . '" border="0" width="560" height="95" alt="GameTracker Banner">';
                echo '</a>';
                echo '</div>';
            } else {
                echo '<div class="text-muted small">' . $languageService->get('info_no_banner') . '</div>';
            }            

            echo '</div>'; // end row

            // Spielerinformationen anzeigen
            if (isset($info['players']) && is_array($info['players']) && count($info['players']) > 0) {
    usort($info['players'], function ($a, $b) {
        return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
    });

    echo '<hr><h5>' . $languageService->get('title_players') . '</h5>';
    echo '<div class="table-responsive">';
    echo '<table class="table">';

    // Liste erlaubter Felder (nur diese sollen angezeigt werden)
    $allowedKeys = ['id', 'name', 'score', 'ping', 'time', 'frags'];

    // Liste ausgeschlossener Felder (diese sollen niemals angezeigt werden)
    $excludedKeys = ['gq_name'];

    // Erstes Spielerobjekt holen
    $firstPlayer = $info['players'][0] ?? [];

    // Spieler-Keys filtern nach erlaubten Feldern und ausgeschlossenen Feldern
    $playerKeys = array_filter(array_keys($firstPlayer), function ($key) use ($allowedKeys, $excludedKeys) {
        return in_array(strtolower($key), $allowedKeys) && !in_array(strtolower($key), $excludedKeys);
    });

    // Fallback: wenn keine erlaubten Felder gefunden wurden, nimm alle Keys außer ausgeschlossene
    if (empty($playerKeys)) {
        $playerKeys = array_filter(array_keys($firstPlayer), function ($key) use ($excludedKeys) {
            return !in_array(strtolower($key), $excludedKeys);
        });
    }

    echo '<thead><tr>';
    foreach ($playerKeys as $key) {
        echo '<th>' . ucfirst($key) . '</th>';
    }
    echo '</tr></thead><tbody>';

    foreach ($info['players'] as $player) {
        echo '<tr>';
        foreach ($playerKeys as $key) {
            $value = $player[$key] ?? '-';

            if (strtolower($key) === 'name') {
                // Farben nur bei "name"
                echo '<td>' . colorizePlayerName($value) . '</td>';
            } elseif (strtolower($key) === 'time') {
                // Zeit als hh:mm:ss formatieren
                $timeSeconds = (int)$value;
                $hours = floor($timeSeconds / 3600);
                $minutes = floor(($timeSeconds % 3600) / 60);
                $seconds = $timeSeconds % 60;
                $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                echo '<td>' . htmlspecialchars($formattedTime) . '</td>';
            } else {
                echo '<td>' . htmlspecialchars($value) . '</td>';
            }
        }
        echo '</tr>';
    }

    echo '</tbody></table></div>';
} else {
    echo '<p><em>' . $languageService->get('info_no_playerinfos') . '</em></p>';
}

        } else {
            echo '<div class="alert alert-danger mb-0">' . $languageService->get('alert_server_disconnected') . '</div>';
        }

        echo '</div>';
        echo '<div class="text-end">';
        echo '<a href="' . SeoUrlHandler::convertToSeoUrl('index.php?site=gametracker') . '" 
           class="btn btn-secondary">' . $languageService->get('btn_back') . '</a>';
        echo '</div></div>';

    } else {
        echo '<div class="alert alert-warning">' . $languageService->get('alert_server_not_found') . '</div>';
    }

    return;
}

$servers = safe_query("SELECT * FROM plugins_gametracker_servers WHERE active = 1 ORDER BY sort_order");

if (mysqli_num_rows($servers)) {
    $queryList = [];
    while ($ds = mysqli_fetch_array($servers)) {
        $queryList[] = [
            'id'   => 'server_' . (int)$ds['id'],
            'type' => strtolower($ds['game']),
            'host' => $ds['ip'] . ':' . $ds['port'],
            'game' => strtolower($ds['game']),
            'game_pic' => strtolower($ds['game_pic'])
        ];
    }
    $gq = new GameQ();
    $gq->addServers($queryList);
    $results = $gq->process();

    echo '<div class="row g-4">';

    foreach ($queryList as $server) {
        $id = $server['id'];
        $info = $results[$id] ?? null;

        echo '<div class="col-md-6 col-lg-4">';
        echo '<div class="card h-100">';
        echo '<div class="card-header">';
        $hostname = (string)($info['gq_hostname'] ?? $server['name'] ?? 'Unbekannter Server');
        echo '<strong>' . htmlspecialchars(stripColorCodes($hostname)) . '</strong>';
        echo '</div><div class="card-body">';

        if (!empty($info['gq_online'])) {
            $gameQType = $server['game'];
            $realGame = !empty($server['mod']) ? $server['mod'] : $server['game'];
            $gameRaw = $server['game'];

            $gqMod = strtolower($info['gq_mod'] ?? '');

            if (isset($modFriendlyNames[$gqMod])) {
                $detectedGame = $modFriendlyNames[$gqMod];
            } elseif (isset($modFriendlyNames[$gameRaw])) {
                $detectedGame = $modFriendlyNames[$gameRaw];
            } else {
                $detectedGame = ucfirst($gqMod ?: $gameRaw ?: 'Unbekannt');
            }
            echo '<div class="col-md-12 mb-2"><strong>' . $languageService->get('label_game') . ':</strong> ' . htmlspecialchars($detectedGame) . '</div>';

            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<p><strong>' . $languageService->get('label_map') . ':</strong> ' . htmlspecialchars($info['mapname'] ?? $info['map'] ?? '-') . '</p>';
            echo '<p><strong>' . $languageService->get('label_mod') . ':</strong> ' . htmlspecialchars($info['gq_mod'] ?? '-') . '</p>';
            
            // Spieleranzeige (immer)
            $players = (int)($info['gq_numplayers'] ?? 0);
            $maxPlayers = (int)($info['gq_maxplayers'] ?? 0);

            // Farbe der Badge je nach Spieleranzahl
            $badgeClass = ($players > 0) ? 'text-bg-success' : 'text-bg-secondary';
            ?>
            <style>.badge.custom-size {
                font-size: var(--bs-body-font-size);
            }
            </style>
            <?php
            echo '<p><strong>' . $languageService->get('label_players') . ':</strong> <span class="badge ' . $badgeClass . ' custom-size">' . $players . ' / ' . $maxPlayers . '</span></p>';
            echo '<p><strong>' . $languageService->get('label_version') . ':</strong> ' . htmlspecialchars($info['shortversion'] ?? $info['version'] ?? '-') . '</p>';
            echo '</div><div class="col-md-6">';

            $mapname = strtolower($info['mapname'] ?? $info['map'] ?? '');
            

            $mapImagePath = "https://image.gametracker.com/images/maps/160x120/" . $server['game_pic'] . "/" . $mapname . ".jpg";

            echo '<div class="col-md-12 mb-2 text-center">';
            echo '<img src="' . $mapImagePath . '" alt="' . htmlspecialchars($mapname) . '" class="img-fluid rounded shadow-sm" style="max-height:250px;" onerror="this.onerror=null;this.src=\'includes/plugins/gametracker/images/map_no_image.jpg\';">';
            echo '</div>';

            echo '</div></div>';
        } else {
            echo '<div class="alert alert-danger mb-0">' . $languageService->get('alert_server_disconnected') . '</div>';
        }

        echo '<a href="' . SeoUrlHandler::convertToSeoUrl(
            'index.php?site=gametracker&action=serverdetails&id=' . (int)str_replace('server_', '', $id)
        ) . '" class="btn btn-outline-primary mt-2 w-100">' . $languageService->get('btn_details') . '</a>';

        echo '</div>';
        echo '</div></div>';
    }

    echo '</div>';
} else {
    echo '<div class="alert alert-info">' . $languageService->get('alert_no_servers') . '</div>';
}