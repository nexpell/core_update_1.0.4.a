<?php
declare(strict_types=1);

/** Provider: Gallery-Bilddetailseiten aus plugins_gallery */
return function (array &$pages, array $CTX): void {
    /** @var mysqli $db */
    $db         = $CTX['db'];
    $languages  = $CTX['languages'];
    $BASE       = $CTX['BASE'];
    $useSeoUrls = $CTX['useSeoUrls'];
    $SLUG_MAP   = $CTX['SLUG_MAP'];

    $table = 'plugins_gallery';

    $cols = [];
    $cr = $db->query("SHOW COLUMNS FROM `{$table}`");
    if (!$cr) {
        return;
    }
    while ($c = $cr->fetch_assoc()) {
        $cols[strtolower((string)$c['Field'])] = (string)$c['Field'];
    }
    $cr->free();

    $idCol      = $cols['id'] ?? null;
    $dateCol    = $cols['upload_date'] ?? null;
    $titleCol   = $cols['title'] ?? null;
    $activeCol  = $cols['is_active'] ?? null;

    if (!$idCol) {
        return;
    }

    $selectCols = [$idCol];
    if ($dateCol) {
        $selectCols[] = $dateCol;
    }
    if ($titleCol) {
        $selectCols[] = $titleCol;
    }
    if ($activeCol) {
        $selectCols[] = $activeCol;
    }

    $select = implode(',', array_map(static fn(string $c): string => "`{$c}`", $selectCols));
    $where = $activeCol ? " WHERE `{$activeCol}` IN (1,'1','true','TRUE')" : '';

    $res = $db->query("SELECT {$select} FROM `{$table}`{$where} ORDER BY `{$idCol}` DESC");
    if (!$res) {
        return;
    }

    while ($row = $res->fetch_assoc()) {
        $id = (int)($row[$idCol] ?? 0);
        if ($id <= 0) {
            continue;
        }

        if ($activeCol) {
            $activeValue = strtolower(trim((string)($row[$activeCol] ?? '0')));
            if (!in_array($activeValue, ['1', 'true', 'yes', 'on'], true)) {
                continue;
            }
        }

        $lastmod = date('Y-m-d');
        if ($dateCol && !empty($row[$dateCol])) {
            $ts = strtotime((string)$row[$dateCol]);
            if ($ts !== false) {
                $lastmod = date('Y-m-d', $ts);
            }
        }

        $contentKey = "gallery/detail/{$id}";
        $qBase = [
            'site'   => 'gallery',
            'action' => 'detail',
            'id'     => $id,
        ];

        foreach ($languages as $lang) {
            $loc = sitemap_build_loc($contentKey, $lang, $BASE, $useSeoUrls, $SLUG_MAP, $qBase);
            if (!isset($pages[$contentKey])) {
                $pages[$contentKey] = ['langs' => [], 'lastmods' => []];
            }
            $pages[$contentKey]['langs'][$lang] = $loc;
            $pages[$contentKey]['lastmods'][$lang] = $lastmod;
        }
    }

    $res->free();

    $listKey = 'gallery';
    if (!isset($pages[$listKey])) {
        foreach ($languages as $lang) {
            $pages[$listKey]['langs'][$lang] =
                sitemap_build_loc($listKey, $lang, $BASE, $useSeoUrls, $SLUG_MAP);
            $pages[$listKey]['lastmods'][$lang] = date('Y-m-d');
        }
    }
};
