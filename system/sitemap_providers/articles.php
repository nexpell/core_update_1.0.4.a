<?php
declare(strict_types=1);

/** Provider: Articles (NUR Detailseiten /{id}) */
return function (array &$pages, array $CTX): void {

    /** @var mysqli $db */
    $db         = $CTX['db'];
    $languages  = $CTX['languages'];
    $BASE       = $CTX['BASE'];
    $useSeoUrls = $CTX['useSeoUrls'];
    $SLUG_MAP   = $CTX['SLUG_MAP'];

    $table = 'plugins_articles';

    /* ---------------- Spalten erkennen ---------------- */
    $cols = [];
    $cr = $db->query("SHOW COLUMNS FROM `{$table}`");
    if (!$cr) return;

    while ($c = $cr->fetch_assoc()) {
        $cols[strtolower($c['Field'])] = $c['Field'];
    }
    $cr->free();

    $idCol      = $cols['id'] ?? $cols['article_id'] ?? null;
    $updatedCol = $cols['updated_at'] ?? $cols['created_at'] ?? null;
    $activeCol  = $cols['is_active'] ?? $cols['published'] ?? null;
    $publishCol = $cols['publish_at'] ?? null;

    if (!$idCol) return;

    /* ---------------- SELECT ---------------- */
    $select = ["`{$idCol}`"];
    if ($updatedCol) $select[] = "`{$updatedCol}`";
    if ($activeCol)  $select[] = "`{$activeCol}`";
    if ($publishCol) $select[] = "`{$publishCol}`";

    $sql = "SELECT " . implode(',', $select) . " FROM `{$table}`";
    $whereParts = [];
    if ($activeCol) {
        $whereParts[] = "`{$activeCol}` IN (1,'1','true','TRUE')";
    }
    if ($publishCol) {
        $whereParts[] = "(`{$publishCol}` IS NULL OR `{$publishCol}` = '' OR `{$publishCol}` <= NOW())";
    }
    if ($whereParts) {
        $sql .= " WHERE " . implode(' AND ', $whereParts);
    }

    $res = $db->query($sql);
    if (!$res) return;

    /* ---------------- Articles registrieren ---------------- */
    while ($row = $res->fetch_assoc()) {

        $id = (int)$row[$idCol];
        if ($id <= 0) continue;

        if ($activeCol) {
            $activeValue = strtolower(trim((string)($row[$activeCol] ?? '0')));
            if (!in_array($activeValue, ['1', 'true', 'yes', 'on'], true)) {
                continue;
            }
        }

        if ($publishCol) {
            $publishValue = trim((string)($row[$publishCol] ?? ''));
            if ($publishValue !== '') {
                $publishTs = strtotime($publishValue);
                if ($publishTs !== false && $publishTs > time()) {
                    continue;
                }
            }
        }

        // lastmod
        $lastmod = date('Y-m-d');
        if ($updatedCol && !empty($row[$updatedCol])) {
            if (is_numeric($row[$updatedCol])) {
                $lastmod = date('Y-m-d', (int)$row[$updatedCol]);
            } else {
                $ts = strtotime((string)$row[$updatedCol]);
                if ($ts !== false) {
                    $lastmod = date('Y-m-d', $ts);
                }
            }
        }

        /**
         * 🔥 ECHTE Detailseite
         * SEO:     /de/articles/6
         * non-SEO: index.php?site=articles&action=watch&id=6
         */
        $contentKey = "articles/{$id}";
        $qBase = [
            'site'   => 'articles',
            'action' => 'watch',
            'id'     => $id
        ];

        foreach ($languages as $lang) {
            $loc = sitemap_build_loc(
                $contentKey,
                $lang,
                $BASE,
                $useSeoUrls,
                $SLUG_MAP,
                $qBase
            );

            if (!isset($pages[$contentKey])) {
                $pages[$contentKey] = ['langs'=>[], 'lastmods'=>[]];
            }

            $pages[$contentKey]['langs'][$lang]    = $loc;
            $pages[$contentKey]['lastmods'][$lang] = $lastmod;
        }
    }

    $res->free();
};
