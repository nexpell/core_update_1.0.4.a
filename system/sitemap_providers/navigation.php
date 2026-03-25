<?php
declare(strict_types=1);

/** Provider: Navigationseinträge aus navigation_website_sub (robust für index.php?site=...) */
return function (array &$pages, array $CTX): void {
    /** @var mysqli $db */
    $db         = $CTX['db'];
    $languages  = $CTX['languages'];
    $BASE       = $CTX['BASE'];
    $useSeoUrls = $CTX['useSeoUrls'];
    $SLUG_MAP   = $CTX['SLUG_MAP'];
    $DENYLIST   = $CTX['DENYLIST'];

    // Helper aus Hauptdatei
    if (!function_exists('sitemap_register_page') || !function_exists('sitemap_build_loc')) return;

    $sql = "SELECT url, COALESCE(DATE(last_modified), DATE(NOW())) AS last_modified
            FROM navigation_website_sub
            WHERE indropdown = 1";
    $res = $db->query($sql);
    if (!$res) return;

    $baseHost = (string)(parse_url((string)$BASE, PHP_URL_HOST) ?? '');
    $langPattern = implode('|', array_map('preg_quote', $languages));

    while ($row = $res->fetch_assoc()) {
        $urlRaw = html_entity_decode(trim((string)($row['url'] ?? '')), ENT_QUOTES);
        if ($urlRaw === '') {
            continue;
        }

        // Platzhalter/Anker/Nicht-HTTP-Schemes ignorieren
        $urlRaw = str_ireplace(['{current_lang}', '%7Bcurrent_lang%7D', '%7bcurrent_lang%7d'], '', $urlRaw);
        if (
            str_starts_with($urlRaw, '#')
            || preg_match('~^(?:mailto|tel|javascript):~i', $urlRaw)
        ) {
            continue;
        }

        // 1) Sprache vorne im Pfad entfernen (falls vorhanden)
        $pathOnly = $urlRaw;
        $queryStr = '';
        if (preg_match('~^https?://~i', $urlRaw)) {
            $u = parse_url($urlRaw);
            $host = strtolower((string)($u['host'] ?? ''));
            // Externe Hosts niemals in die Sitemap übernehmen
            if ($host !== '' && $baseHost !== '' && $host !== strtolower($baseHost)) {
                continue;
            }
            $pathOnly = (string)($u['path'] ?? '');
            $queryStr = (string)($u['query'] ?? '');
        } elseif (false !== ($qpos = strpos($urlRaw, '?'))) {
            $pathOnly = substr($urlRaw, 0, $qpos);
            $queryStr = substr($urlRaw, $qpos + 1);
        }
        if ($langPattern !== '') {
            $pathOnly = preg_replace('~^/?(' . $langPattern . ')(?:/|$)~i', '', ltrim($pathOnly, '/'));
        } else {
            $pathOnly = ltrim($pathOnly, '/');
        }

        // 2) Query in Kleinbuchstaben-Schlüssel parsen
        $query = [];
        if ($queryStr !== '') {
            parse_str($queryStr, $tmp);
            foreach ($tmp as $k => $v) $query[strtolower((string)$k)] = $v;
        }

        // 3) contentKey bestimmen
        //    - Wenn site=... existiert → nimm das
        //    - Sonst nimm erstes Segment aus Pfad
        //    - Legacy-ID-Formen auf kanonische Detailpfade abbilden
        $contentKey = '';
        $queryBase  = [];

        if (!empty($query['site'])) {
            $site = strtolower(trim((string)$query['site']));

            // Static-Seiten kommen aus dem dedizierten static_ids-Provider
            // mit der kanonischen Slug-URL in die Sitemap.
            if ($site === 'static') {
                continue;
            }

            $queryBase['site'] = $site;

            if ($site === 'static' && isset($query['staticid'])) {
                $contentKey = "static/" . (int)$query['staticid'];
                $queryBase = ['site' => 'static', 'staticID' => (int)$query['staticid']];
            } elseif ($site === 'articles' && isset($query['articleid'])) {
                $contentKey = "articles/" . (int)$query['articleid'];
                $queryBase = ['site' => 'articles', 'action' => 'watch', 'id' => (int)$query['articleid']];
            } elseif ($site === 'downloads' && isset($query['downloadid'])) {
                $contentKey = "downloads/detail/" . (int)$query['downloadid'];
                $queryBase = ['site' => 'downloads', 'action' => 'detail', 'id' => (int)$query['downloadid']];
            } elseif ($site === 'downloads' && isset($query['action'], $query['id']) && strtolower((string)$query['action']) === 'detail') {
                $contentKey = "downloads/detail/" . (int)$query['id'];
                $queryBase = ['site' => 'downloads', 'action' => 'detail', 'id' => (int)$query['id']];
            } elseif ($site === 'wiki' && isset($query['action'], $query['id']) && strtolower((string)$query['action']) === 'detail') {
                $contentKey = "wiki/detail/" . (int)$query['id'];
                $queryBase = ['site' => 'wiki', 'action' => 'detail', 'id' => (int)$query['id']];
            } else {
                $contentKey = $site;
                $queryBase['site'] = $site;
            }
        } else {
            $path = trim($pathOnly, '/');
            if ($path !== '') {
                // Legacy static/staticid/11 -> static/11
                if (preg_match('~^static/staticid/([0-9]+)$~i', $path, $m)) {
                    continue;
                } elseif (preg_match('~^articles/watch/([0-9]+)$~i', $path, $m)) {
                    $contentKey = 'articles/' . $m[1];
                    $queryBase = ['site' => 'articles', 'action' => 'watch', 'id' => (int)$m[1]];
                } elseif (preg_match('~^downloads/detail/([0-9]+)$~i', $path, $m)) {
                    $contentKey = 'downloads/detail/' . $m[1];
                    $queryBase = ['site' => 'downloads', 'action' => 'detail', 'id' => (int)$m[1]];
                } elseif (preg_match('~^wiki/detail/([0-9]+)$~i', $path, $m)) {
                    $contentKey = 'wiki/detail/' . $m[1];
                    $queryBase = ['site' => 'wiki', 'action' => 'detail', 'id' => (int)$m[1]];
                } elseif (preg_match('~^wiki/(?:page/)?([0-9]+)$~i', $path, $m)) {
                    $contentKey = 'wiki/page/' . $m[1];
                    $queryBase = ['site' => 'wiki', 'page' => $m[1]];
                } else {
                    // einfacher Abschnitt: about, forum, downloads, ...
                    $contentKey = strtolower(explode('/', $path)[0]);
                    $queryBase = $contentKey !== '' ? ['site' => $contentKey] : [];
                }
            }
        }

        if ($contentKey === '') continue;
        $firstSeg = explode('/', $contentKey)[0];
        if (in_array($firstSeg, $DENYLIST, true)) continue;

        // 4) lastmod
        $d = (string)($row['last_modified'] ?? '');
        $lastmod = ($d !== '' && strtotime($d) !== false) ? date('Y-m-d', strtotime($d)) : date('Y-m-d');

        // 5) registrieren – sitemap_build_loc sorgt dafür, dass:
        //    - SEO: BASE/{lang}/{contentKey}
        //    - non-SEO: BASE/index.php?site=...&lang=...&id=...
        sitemap_register_page($pages, $contentKey, $lastmod, [], $languages, $BASE, $useSeoUrls, $SLUG_MAP, $queryBase);
    }

    $res->free();
};
