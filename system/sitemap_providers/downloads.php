<?php
declare(strict_types=1);

/** Provider: Downloads aus plugins_downloads - Detailseiten als action=detail&id=... */
return function (array &$pages, array $CTX): void {
    /** @var mysqli $db */
    $db         = $CTX['db'];
    $languages  = $CTX['languages'];
    $BASE       = $CTX['BASE'];
    $useSeoUrls = $CTX['useSeoUrls'];
    $SLUG_MAP   = $CTX['SLUG_MAP'];

    $table = 'plugins_downloads';

    $cols = [];
    $cr = $db->query("SHOW COLUMNS FROM `{$table}`");
    if (!$cr) {
        error_log('[sitemap] downloads: Tabelle fehlt');
        return;
    }
    while ($c = $cr->fetch_assoc()) {
        $cols[strtolower((string)$c['Field'])] = (string)$c['Field'];
    }
    $cr->free();

    $idCol       = $cols['id'] ?? null;
    $titleCol    = $cols['title'] ?? null;
    $filenameCol = $cols['filename'] ?? ($cols['file'] ?? null);
    $updatedCol  = $cols['updated_at'] ?? null;
    $uploadedCol = $cols['uploaded_at'] ?? null;
    $activeCol   = $cols['is_active'] ?? ($cols['active'] ?? ($cols['visible'] ?? null));
    $accessCol   = $cols['access_roles'] ?? null;

    if (!$idCol) {
        error_log('[sitemap] downloads: keine ID-Spalte gefunden');
        return;
    }

    $selectCols = [$idCol];
    if ($titleCol) {
        $selectCols[] = $titleCol;
    }
    if ($filenameCol) {
        $selectCols[] = $filenameCol;
    }
    if ($updatedCol) {
        $selectCols[] = $updatedCol;
    }
    if ($uploadedCol) {
        $selectCols[] = $uploadedCol;
    }
    if ($activeCol) {
        $selectCols[] = $activeCol;
    }
    if ($accessCol) {
        $selectCols[] = $accessCol;
    }

    $select = implode(',', array_map(static fn(string $c): string => "`{$c}`", $selectCols));
    $whereParts = [];
    if ($activeCol) {
        $whereParts[] = "`{$activeCol}` IN (1,'1','true','TRUE')";
    }
    $where = $whereParts ? (' WHERE ' . implode(' AND ', $whereParts)) : '';
    $orderBy = $updatedCol
        ? " ORDER BY `{$updatedCol}` DESC"
        : ($uploadedCol ? " ORDER BY `{$uploadedCol}` DESC" : '');

    $pickDate = static function (array $row, ?string $col1, ?string $col2): string {
        $val = null;
        if ($col1 && !empty($row[$col1])) {
            $val = $row[$col1];
        } elseif ($col2 && !empty($row[$col2])) {
            $val = $row[$col2];
        }

        if (!$val) {
            return date('Y-m-d');
        }

        $ts = strtotime((string)$val);
        return $ts !== false ? date('Y-m-d', $ts) : date('Y-m-d');
    };

    $added = 0;
    $batch = 1000;
    $offset = 0;

    while (true) {
        $sql = "SELECT {$select} FROM `{$table}`{$where}{$orderBy} LIMIT {$batch} OFFSET {$offset}";
        $res = $db->query($sql);
        if (!$res) {
            break;
        }

        $count = 0;
        while ($row = $res->fetch_assoc()) {
            $count++;

            if ($activeCol) {
                $activeValue = strtolower(trim((string)($row[$activeCol] ?? '0')));
                if (!in_array($activeValue, ['1', 'true', 'yes', 'on'], true)) {
                    continue;
                }
            }

            if ($accessCol) {
                $rolesValue = trim((string)($row[$accessCol] ?? ''));
                if ($rolesValue !== '') {
                    $rolesLower = strtolower($rolesValue);
                    if (!in_array($rolesLower, ['public', 'all', 'guest', '0', '[]'], true)) {
                        continue;
                    }
                }
            }

            $id = trim((string)($row[$idCol] ?? ''));
            if ($id === '') {
                continue;
            }

            $lastmod = $pickDate($row, $updatedCol, $uploadedCol);
            $contentKey = "downloads/detail/{$id}";
            $qBase = ['site' => 'downloads', 'action' => 'detail', 'id' => $id];

            foreach ($languages as $lang) {
                $loc = sitemap_build_loc($contentKey, $lang, $BASE, $useSeoUrls, $SLUG_MAP, $qBase);
                if (!isset($pages[$contentKey])) {
                    $pages[$contentKey] = ['lastmods' => [], 'langs' => []];
                }
                $pages[$contentKey]['langs'][$lang] = $loc;
                $pages[$contentKey]['lastmods'][$lang] = $lastmod;
            }

            $added++;
        }

        $res->free();
        if ($count < $batch) {
            break;
        }
        $offset += $batch;
    }

    error_log("[sitemap] downloads: hinzugefuegt {$added} detail-keys");
};
