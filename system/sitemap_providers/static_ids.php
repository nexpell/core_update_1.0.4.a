<?php
declare(strict_types=1);

require_once __DIR__ . '/../classes/SeoUrlHandler.php';

return function (array &$pages, array $CTX): void {
    /** @var mysqli $db */
    $db = $CTX['db'];
    $languages = $CTX['languages'];
    $BASE = $CTX['BASE'];

    $res = $db->query("SELECT * FROM settings_static");
    if (!$res) {
        return;
    }

    while ($row = $res->fetch_assoc()) {
        $id = (int)($row['staticID'] ?? 0);
        if ($id <= 0) {
            continue;
        }

        foreach (['is_active', 'active', 'visible', 'displayed'] as $flagCol) {
            if (!array_key_exists($flagCol, $row)) {
                continue;
            }

            $flagValue = strtolower(trim((string)($row[$flagCol] ?? '0')));
            if (!in_array($flagValue, ['1', 'true', 'yes', 'on'], true)) {
                continue 2;
            }
        }

        $lastmod = pickDate($row, ['last_modified', 'changed', 'created_at', 'created', 'date', 'updated_at']);
        $contentKey = "static/{$id}";

        foreach ($languages as $lang) {
            $safeLang = mysqli_real_escape_string($db, (string)$lang);
            $slug = '';

            $slugRes = $db->query("
                SELECT content
                FROM settings_content_lang
                WHERE content_key = 'static_title_{$id}'
                  AND language IN ('{$safeLang}', 'de', 'en', 'it')
                ORDER BY FIELD(language, '{$safeLang}', 'de', 'en', 'it')
                LIMIT 1
            ");

            if ($slugRes && ($slugRow = $slugRes->fetch_assoc())) {
                $slug = \nexpell\SeoUrlHandler::slugify(trim((string)($slugRow['content'] ?? '')));
                $slugRes->free();
            }

            if ($slug !== '') {
                $loc = rtrim($BASE, '/') . '/' . trim($lang, '/') . '/page/' . rawurlencode($slug) . '/' . $id;
            } else {
                $loc = rtrim($BASE, '/') . \nexpell\SeoUrlHandler::convertToSeoUrl(
                    'index.php?' . http_build_query([
                        'lang' => $lang,
                        'site' => 'static',
                        'staticID' => $id,
                    ])
                );
            }

            if (!isset($pages[$contentKey])) {
                $pages[$contentKey] = ['langs' => [], 'lastmods' => []];
            }

            $pages[$contentKey]['langs'][$lang] = $loc;
            $pages[$contentKey]['lastmods'][$lang] = $lastmod;
        }
    }

    $res->free();
};
