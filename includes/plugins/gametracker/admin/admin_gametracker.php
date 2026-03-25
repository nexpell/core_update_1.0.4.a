<?php

// Session absichern
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
global $languageService;

use nexpell\AccessControl;
// Den Admin-Zugriff für das Modul überprüfen
AccessControl::checkAdminAccess('gametracker');

use xPaw\SourceQuery\SourceQuery;
require __DIR__ . '/../GameQ/Autoloader.php';
use GameQ\GameQ;

function stripColorCodes(?string $text): string {
    return preg_replace('/\^\d/', '', $text ?? '');
}

// Server löschen
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    safe_query("DELETE FROM plugins_gametracker_servers WHERE id = " . $id);

    nx_audit_delete('admin_gametracker', (string)$id, (string)$id, 'admincenter.php?site=admin_gametracker');
    nx_redirect('admincenter.php?site=admin_gametracker', 'success', 'alert_deleted', false);
}

// Server hinzufügen oder bearbeiten
if (isset($_POST['save_server'])) {
    $id = (int)$_POST['id'];
    $ip = escape($_POST['ip']);
    $port = (int)$_POST['port'];
    $query_port = isset($_POST['query_port']) ? (int)$_POST['query_port'] : null;
    $game = escape($_POST['game']);
    $game_pic = $_POST['game_pic'];
    $sort_order = (int)$_POST['sort_order'];
    $active = isset($_POST['active']) ? 1 : 0;

    if ($id > 0) {
        safe_query("UPDATE plugins_gametracker_servers SET 
            ip='$ip',
            port=$port,
            query_port=" . ($query_port !== null ? $query_port : 'NULL') . ",
            game='$game',
            game_pic='$game_pic',
            sort_order=$sort_order,
            active=$active 
            WHERE id=$id");

        nx_audit_update('admin_gametracker', (string)$id, true, $ip . ':' . $port, 'admincenter.php?site=admin_gametracker');
    } else {
        safe_query("INSERT INTO plugins_gametracker_servers 
            (ip, port, query_port, game, game_pic, sort_order, active) 
            VALUES (
                '$ip',
                $port,
                " . ($query_port !== null ? $query_port : 'NULL') . ",
                '$game',
                '$game_pic',
                $sort_order,
                $active
            )");

        $newId = (int)($_database->insert_id ?? 0);
        nx_audit_create('admin_gametracker', (string)$newId, $ip . ':' . $port, 'admincenter.php?site=admin_gametracker');
    }

    nx_redirect('admincenter.php?site=admin_gametracker', 'success', 'alert_saved', false);
}

// Mapping Game-Typ zu GameTracker-Ordnernamen für Map-Bilder
    $imageFolderOverrides = [
        'coduo'       => 'uo',
        'cs16'        => 'cs',
        'css'         => 'cs_source',
        'dods'        => 'dod_source',
        'hl2mp'       => 'hl2dm',
        'tf'          => 'tfc',
        'ins'         => 'insurgency',
        'gmod'        => 'garrysmod',
        'l4d2'        => 'left4dead2',
        'l4d'         => 'left4dead',
        'arma'        => 'arma',
        'arma2'       => 'arma2',
        'arma3'       => 'arma3',
        'samp'        => 'samp',
        'mta'         => 'mta',
        'fivem'       => 'fivem',
        'ut'          => 'ut',
        'ut2003'      => 'ut2003',
        'ut2004'      => 'ut2004',
        'ut3'         => 'ut3',
        'quake3'      => 'q3a',
        'quake4'      => 'q4',
        'cod'         => 'cod',
        'cod2'        => 'cod2',
        'cod4'        => 'cod4',
        'codmw2'      => 'codmw2',
        'codmw3'      => 'codmw3',
        'codbo'       => 'codbo',
        'codbo2'      => 'codbo2',
        'codwaw'      => 'codwaw',
        'mohaa'       => 'mohaa',
        'moh'         => 'moh',
        'mohwf'       => 'mohwf',
        'bf1942'      => 'bf1942',
        'bfv'         => 'bfv',
        'bf2'         => 'bf2',
        'bf2142'      => 'bf2142',
        'bf3'         => 'bf3',
        'bf4'         => 'bf4',
        'bfbc2'       => 'bfbc2',
        'ravenshield' => 'ravenshield',
        'sof2'        => 'sof2',
        'et'          => 'et',
        'rtcw'        => 'rtcw',
        'minecraft'   => 'minecraft',
        'terraria'    => 'terraria',
        'rust'        => 'rust',
        'valheim'     => 'valheim',
        'hurtworld'   => 'hurtworld',
        '7dtd'        => '7daystodie',
        'factorio'    => 'factorio',
        'conan'       => 'conanexiles',
        'ark'         => 'arkse',
        'squad'       => 'squad',
        'unturned'    => 'unturned',
    ];
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

// Bearbeiten-Formular anzeigen
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $editId = (int)$_GET['id'];
    $res = safe_query("SELECT * FROM plugins_gametracker_servers WHERE id = $editId");
    $server = mysqli_fetch_array($res);

    // Aktuelles Spiel / game_pic
    $currentGame = $server['game'] ?? '';
    $gamePic     = $imageFolderOverrides[$currentGame] ?? $currentGame;

    echo '<div class="card shadow-sm mt-4">';
    echo '<div class="card-header d-flex align-items-center justify-content-between">';
    echo '<div class="card-title mb-0">';
    echo '<i class="bi bi-controller"></i> <span>' . htmlspecialchars($languageService->get('title_server')) . '</span> ';
    echo '<small class="text-muted">' . htmlspecialchars($languageService->get('edit')) . '</small>';
    echo '</div>';
    echo '</div>';

    echo '<div class="card-body">';
    echo '<form method="post" class="vstack gap-3">';
    echo '<input type="hidden" name="id" value="' . (int)($server['id'] ?? 0) . '">';

    // Erste Zeile: IP / Port / Query-Port
    echo '<div class="row g-3">';
    echo '<div class="col-md-6">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_ip')) . '</label>';
    echo '<input class="form-control" name="ip" value="' . htmlspecialchars((string)($server['ip'] ?? '')) . '" required>';
    echo '</div>';

    echo '<div class="col-md-3">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_port')) . '</label>';
    echo '<input class="form-control" name="port" value="' . htmlspecialchars((string)($server['port'] ?? '')) . '" required>';
    echo '</div>';

    echo '<div class="col-md-3">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_query')) . '</label>';
    echo '<input class="form-control" name="query_port" value="' . htmlspecialchars((string)($server['query_port'] ?? '')) . '" inputmode="numeric">';
    echo '</div>';
    echo '</div>';

    // Game Auswahl + (unsichtbar) game_pic
    echo '<div class="row g-3">';
    echo '<div class="col-md-8">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_game')) . '</label>';
    echo '<select class="form-select" name="game" id="gameSelect" required>';
    echo '<option value="" disabled' . ($currentGame ? '' : ' selected') . '>' . htmlspecialchars($languageService->get('select_choose')) . '</option>';

    foreach ($modFriendlyNames as $value => $label) {
        $selected = ($currentGame === $value) ? ' selected' : '';
        echo '            <option value="' . htmlspecialchars((string)$value) . '"' . $selected . '>'
           . htmlspecialchars((string)$label) . '</option>';
    }

    echo '</select>';
    echo '<input type="hidden" name="game_pic" id="gamePic" value="' . htmlspecialchars((string)$gamePic) . '">';
    echo '<div class="form-check form-switch mt-3">';
    echo '<input class="form-check-input" type="checkbox" name="active" id="activeSwitch"' . (!empty($server['active']) ? ' checked' : '') . '>';
    echo '<label class="form-check-label fw-semibold" for="activeSwitch">'
        . htmlspecialchars($languageService->get('active')) .
        '</label>';
    echo '</div>';
    echo '</div>';

    // Sortierung (rechts)
    echo '<div class="col-md-4">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_sort')) . '</label>';
    echo '<input class="form-control" name="sort_order" value="' . htmlspecialchars((string)($server['sort_order'] ?? '')) . '" inputmode="numeric">';
    echo '</div>';

    // Buttons
    echo '<div class="d-flex gap-2 pt-2">';
    echo '<button class="btn btn-primary" type="submit" name="save_server">' . htmlspecialchars($languageService->get('save')) . '</button>';
    echo '</div>';

    echo '</form>';
    echo '</div>';
    echo '</div>';

    // JS: game_pic dynamisch setzen
    $imageOverridesJson = json_encode($imageFolderOverrides, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    echo <<<EOT
<script>
  const imageFolderOverrides = $imageOverridesJson;

  const gameSelect = document.getElementById('gameSelect');
  const gamePicEl  = document.getElementById('gamePic');

  if (gameSelect && gamePicEl) {
    gameSelect.addEventListener('change', function() {
      const selectedGame = this.value;
      const override = imageFolderOverrides[selectedGame] || selectedGame;
      gamePicEl.value = override;
    });
  }
</script>
EOT;

    return;
}

// Neu-Formular anzeigen
if (isset($_GET['action']) && $_GET['action'] === 'add') {

    $gamePic = '';

    echo '<div class="card shadow-sm mt-4">';
    echo '<div class="card-header">';
    echo '<div class="card-title mb-0">';
    echo '<i class="bi bi-controller"></i> ';
    echo '<span>' . htmlspecialchars($languageService->get('title_server')) . '</span> ';
    echo '<small class="text-muted">' . htmlspecialchars($languageService->get('add')) . '</small>';
    echo '</div>';
    echo '</div>';

    echo '<div class="card-body">';
    echo '<form method="post" class="vstack gap-3">';
    echo '<input type="hidden" name="id" value="0">';

    // IP / Port / Query-Port
    echo '<div class="row g-3">';
    echo '<div class="col-md-6">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_ip')) . '</label>';
    echo '<input class="form-control" name="ip" required>';
    echo '</div>';

    echo '<div class="col-md-3">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_port')) . '</label>';
    echo '<input class="form-control" name="port" required>';
    echo '</div>';

    echo '<div class="col-md-3">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_query')) . '</label>';
    echo '<input class="form-control" name="query_port">';
    echo '</div>';
    echo '</div>';

    // Spiel + Aktiv
    echo '<div class="row g-3">';
    echo '<div class="col-md-8">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_game')) . '</label>';
    echo '<select class="form-select" name="game" id="gameSelect" required>';
    echo '<option value="" disabled selected>' . htmlspecialchars($languageService->get('select_choose')) . '</option>';

    foreach ($modFriendlyNames as $value => $label) {
        echo '            <option value="' . htmlspecialchars((string)$value) . '">'
           . htmlspecialchars((string)$label) . '</option>';
    }

    echo '</select>';
    echo '<input type="hidden" name="game_pic" id="gamePic" value="">';

    // Aktiv-Switch
    echo '<div class="form-check form-switch mt-3">';
    echo '<input class="form-check-input" type="checkbox" name="active" id="activeSwitch" checked>';
    echo '<label class="form-check-label fw-semibold" for="activeSwitch">'
         . htmlspecialchars($languageService->get('active')) .
         '</label>';
    echo '</div>';

    echo '</div>';

    // Sortierung
    echo '<div class="col-md-4">';
    echo '<label class="form-label">' . htmlspecialchars($languageService->get('label_sort')) . '</label>';
    echo '<input class="form-control" name="sort_order" value="1" inputmode="numeric">';
    echo '</div>';
    echo '</div>';

    // Buttons
    echo '<div class="d-flex gap-2 pt-2">';
    echo '<button class="btn btn-primary" type="submit" name="save_server">'
         . htmlspecialchars($languageService->get('save')) . '</button>';
    echo '</div>';

    echo '</form>';
    echo '</div>';
    echo '</div>';
    return;
}

// Am Ende: Link zum Hinzufügen
echo '<a href="admincenter.php?site=admin_gametracker&action=add" class="btn btn-primary mb-4">' . $languageService->get('add') . '</a>';

// Lade alle aktiven Server aus der DB
$servers = safe_query("SELECT * FROM plugins_gametracker_servers WHERE active = 1 ORDER BY sort_order");

if (mysqli_num_rows($servers)) {
    $queryList = [];
    while ($ds = mysqli_fetch_array($servers)) {
        $queryList[] = [
            'id'         => 'server_' . (int)$ds['id'],
            'type'       => strtolower($ds['game']),
            'host'       => $ds['ip'] . ':' . $ds['port'],
            'game'       => strtolower($ds['game']),
            'game_pic'   => strtolower($ds['game_pic']),
            'ip'         => $ds['ip'],
            'port'       => $ds['port'],
            'query_port' => $ds['query_port'],
            'name'       => $ds['name'] ?? '', // falls vorhanden
        ];
    }

    $gq = new GameQ();
    $gq->addServers($queryList);
    $results = $gq->process();

    echo '<div class="row g-3">';

    foreach ($queryList as $server) {
        $id = $server['id'];
        $info = $results[$id] ?? null;

        echo '<div class="col-md-6 col-lg-4">';
        echo '<div class="card h-100 shadow-sm">';

        // Header
        $hostname = stripColorCodes($info['gq_hostname'] ?? $server['name'] ?? '');
        echo '<div class="card-header">';
        echo '<span class="fw-semibold">' . htmlspecialchars($hostname) . '</span>';
        echo '</div>';

        echo '<div class="card-body">';

        if (!empty($info['gq_online'])) {
            $gameRaw = $server['game'];
            $modName = strtolower($info['gq_mod'] ?? '');
            $gqMod   = $modName ?: $gameRaw;
            $detectedGame = $modFriendlyNames[$gqMod] ?? ($modFriendlyNames[$gameRaw] ?? ucfirst($gqMod));

            $players    = (int)($info['gq_numplayers'] ?? 0);
            $maxPlayers = (int)($info['gq_maxplayers'] ?? 0);
            $badgeClass = ($players > 0) ? 'bg-success' : 'bg-secondary';

            // Datenzeilen vorbereiten (Query-Port nur wenn vorhanden)
            $rows = [
                $languageService->get('label_game')    => $detectedGame,
                $languageService->get('label_map')     => $info['mapname'] ?? $info['map'] ?? '-',
                $languageService->get('label_mod')     => $info['gq_mod'] ?? '-',
                $languageService->get('label_version') => $info['shortversion'] ?? $info['version'] ?? '-',
                $languageService->get('label_ip')      => $server['ip'] ?? '-',
                $languageService->get('label_port')    => $server['port'] ?? '-',
            ];
            if (!empty($server['query_port'])) {
                $rows[$languageService->get('label_query')] = (string)$server['query_port'];
            }

            // Layout: links Liste, rechts Bild
            echo '<div class="row g-3">';
            echo '<div class="col-8">';

            echo '<div class="list-group list-group-flush">';

            // Players separat mit Badge rechts
            echo '<div class="list-group-item py-2">';
            echo '<div class="d-flex justify-content-between align-items-center gap-3">';
            echo '<span class="fw-semibold">' . htmlspecialchars($languageService->get('label_players')) . '</span>';
            echo '<span class="badge ' . $badgeClass . '">' . $players . ' / ' . $maxPlayers . '</span>';
            echo '</div>';
            echo '</div>';

            // Standard-Zeilen
            foreach ($rows as $label => $value) {
                echo '<div class="list-group-item py-2">';
                echo '<div class="d-flex justify-content-between gap-3">';
                echo '<span class="fw-semibold">' . htmlspecialchars((string)$label) . '</span>';
                echo '<span class="text-end text-break">' . htmlspecialchars((string)$value) . '</span>';
                echo '</div>';
                echo '</div>';
            }

            echo '</div>'; // list-group
            echo '</div>';   // col-8

            // Rechte Spalte: Map-Bild
            echo '<div class="col-4 text-end">';

            $mapname = strtolower($info['mapname'] ?? $info['map'] ?? '');
            $mapImagePath = "https://image.gametracker.com/images/maps/160x120/" . $server['game_pic'] . "/" . $mapname . ".jpg";
            $fallbackImage = '/includes/plugins/gametracker/images/map_no_image.jpg';

            echo '<img src="' . $mapImagePath . '"'
               . ' alt="' . htmlspecialchars($mapname) . '"'
               . ' class="img-fluid rounded shadow-sm"'
               . ' style="max-height:250px;"'
               . ' onerror="this.onerror=null;this.src=\'' . $fallbackImage . '\';">';

            echo '</div>'; // col-4
            echo '</div>';   // row

        } else {
            echo '<div class="alert alert-warning mb-0">' . $languageService->get('alert_server') . '</div>';
        }

        echo '</div>'; // card-body

        // Footer Buttons
        echo '<div class="card-footer d-flex justify-content-between gap-2">';
        echo '<a href="admincenter.php?site=admin_gametracker&action=edit&id=' . (int)str_replace('server_', '', $id) . '" class="btn btn-primary">'
           . $languageService->get('edit') . '</a>';

        $serverId  = (int)str_replace('server_', '', $id);
        $deleteUrl = 'admincenter.php?site=admin_gametracker&delete=' . $serverId;

        echo '<a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"'
           . ' data-confirm-url="' . htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') . '">'
           . $languageService->get('delete') . '</a>';
        echo '</div>';

        echo '</div>'; // card
        echo '</div>';   // col
    }

    echo '</div>'; // row
} else {
    nx_alert('info', 'no_entries_found', false);
}
?>