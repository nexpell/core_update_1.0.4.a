<?php
declare(strict_types=1);

require_once __DIR__ . '/../classes/SeoUrlHandler.php';

return function (array &$pages, array $CTX): void {
    /** @var mysqli $db */
    $db = $CTX['db'];
    $languages = $CTX['languages'];
    $BASE = $CTX['BASE'];

    $table = 'plugins_gametracker_servers';
    $cols = [];

    $cr = $db->query("SHOW COLUMNS FROM `{$table}`");
    if (!$cr) {
        return;
    }

    while ($c = $cr->fetch_assoc()) {
        $cols[strtolower((string)$c['Field'])] = (string)$c['Field'];
    }
    $cr->free();

    $idCol = $cols['id'] ?? null;
    $activeCol = $cols['active'] ?? ($cols['is_active'] ?? null);
    if ($idCol === null) {
        return;
    }

    $where = '';
    if ($activeCol !== null) {
        $where = " WHERE `{$activeCol}` IN (1,'1','true','TRUE')";
    }

    $today = date('Y-m-d');
    $res = $db->query("SELECT `{$idCol}` FROM `{$table}`{$where} ORDER BY `{$idCol}` DESC");
    if (!$res) {
        return;
    }

    while ($row = $res->fetch_assoc()) {
        $id = trim((string)($row[$idCol] ?? ''));
        if ($id === '') {
            continue;
        }

        $contentKey = "gametracker/server/{$id}";
        foreach ($languages as $lang) {
            $loc = rtrim($BASE, '/') . \nexpell\SeoUrlHandler::convertToSeoUrl(
                'index.php?' . http_build_query([
                    'lang' => $lang,
                    'site' => 'gametracker',
                    'action' => 'serverdetails',
                    'id' => (int)$id,
                ])
            );

            if (!isset($pages[$contentKey])) {
                $pages[$contentKey] = ['lastmods' => [], 'langs' => []];
            }

            $pages[$contentKey]['langs'][$lang] = $loc;
            $pages[$contentKey]['lastmods'][$lang] = $today;
        }
    }

    $res->free();
};
