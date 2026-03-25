<?php
/**********************************************************************
 * NEXPELL – FORUM CATEGORY RENDER
 * FINAL, STABIL
 *  - ForumACL only (kein ForumAccess, keine nx_* Helper)
 *  - Gast = Rolle 11
 *  - Admin = Rolle 1
 **********************************************************************/

if (!defined('IS_FORUM')) {
    die("Direct access not allowed");
}

use nexpell\forum\ForumACL;
use nexpell\SeoUrlHandler;

require_once __DIR__ . '/system/ForumACL.php';

global $_database;



/**********************************************************************
 * FUNKTION
 **********************************************************************/
function forum_render_category($tpl, $languageService, $userID, $catID)
{
    global $_database;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    /* ==========================================================
       USER / ROLE KONTEXT
    ========================================================== */
    $userID = (int)$userID;
    $catID  = (int)$catID;
    $roleID = (int)($_SESSION['roleID'] ?? 0);

    // Gast
    if ($userID === 0) {
        $roleID = 11;
    }

    $languageService->readPluginModule('forum');

    /* ==========================================================
       KATEGORIE LADEN
    ========================================================== */
    $res = safe_query("
        SELECT *
        FROM plugins_forum_categories
        WHERE catID = {$catID}
        LIMIT 1
    ");

    $category = mysqli_fetch_assoc($res);

    if (!$category) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Kategorie existiert nicht.</div>";
        return;
    }

    $catTitle = htmlspecialchars($category['title']);
    $catDesc  = nl2br(htmlspecialchars($category['description']));
    $boardID  = (int)$category['boardID'];

    /* ==========================================================
       ACL – KATEGORIE SICHTBAR?
    ========================================================== */
    $ctxCategory = [
        'boardID'    => $boardID,
        'categoryID' => $catID,
        'threadID'   => 0
    ];

    if (!ForumACL::canView($ctxCategory, $roleID)) {
        echo "<div class='alert alert-warning'>Du darfst diese Kategorie nicht sehen.</div>";
        return;
    }

    /* ==========================================================
       NEUES THEMA BUTTON
       (derzeit: nur eingeloggte User + sichtbare Kategorie)
    ========================================================== */
    $newThreadButtonHTML = '';
    if ($userID > 0) {
        $newThreadButtonHTML = '
            <a href="' . SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=new_thread&catID=" . (int)$catID
            ) . '" class="btn btn-primary mb-3">
                <i class="bi bi-plus-circle"></i> Neues Thema
            </a>
        ';

    }

    /* ==========================================================
       READ-INFO (NUR LESEN!)
    ========================================================== */
    $readInfo = [];

    if ($userID > 0) {
        $resR = safe_query("
            SELECT threadID, last_read_at
            FROM plugins_forum_read
            WHERE userID = {$userID}
        ");

        while ($r = mysqli_fetch_assoc($resR)) {
            $readInfo[(int)$r['threadID']] = (int)$r['last_read_at'];
        }
    }

    /* ==========================================================
       THREADS LADEN (PINNED OBEN)
    ========================================================== */
    $threads = safe_query("
        SELECT 
            t.threadID,
            t.title,
            t.views,
            t.updated_at,
            t.is_locked,
            t.is_pinned,
            (
                SELECT COUNT(*)
                FROM plugins_forum_posts p
                WHERE p.threadID = t.threadID
            ) AS posts
        FROM plugins_forum_threads t
        WHERE t.catID = {$catID}
          AND t.is_deleted = 0
        ORDER BY
            t.is_pinned DESC,
            t.updated_at DESC
    ");

    /* ==========================================================
       THREADS RENDERN
    ========================================================== */
    $threadHTML = '';

    while ($t = mysqli_fetch_assoc($threads)) {

        $threadID = (int)$t['threadID'];

        $ctxThread = [
            'boardID'    => $boardID,
            'categoryID' => $catID,
            'threadID'   => $threadID
        ];

        // ACL – Thread sichtbar?
        if (!ForumACL::canView($ctxThread, $roleID)) {
            continue;
        }

        /* --------------------------------------------------------
           LETZTER BEITRAG
        -------------------------------------------------------- */
        /*$lp = safe_query("
            SELECT 
                p.postID,
                p.threadID,
                p.created_at,
                u.username
            FROM plugins_forum_posts p
            LEFT JOIN users u ON u.userID = p.userID
            WHERE p.threadID = {$threadID}
            ORDER BY p.created_at DESC
            LIMIT 1
        ");

        $last = mysqli_fetch_assoc($lp);

        if ($last && !empty($last['postID'])) {

            $threadID = (int)$last['threadID'];
            $postID   = (int)$last['postID'];

            // posts pro Seite absichern
            $perPage = (int)($settings['posts_per_page'] ?? 10);
            if ($perPage <= 0) {
                $perPage = 10;
            }

            // Position des letzten Posts ermitteln (robust)
            $res = safe_query("
                SELECT COUNT(*) AS pos
                FROM plugins_forum_posts
                WHERE threadID = $threadID
                  AND created_at <= (
                      SELECT created_at
                      FROM plugins_forum_posts
                      WHERE postID = $postID
                      LIMIT 1
                  )
            ");

            $row      = mysqli_fetch_assoc($res);
            $position = max(1, (int)$row['pos']);
            $page     = (int)ceil($position / $perPage);

            // KORREKTE URL mit page + anchor
            $lastPostUrl = SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=thread&id=$threadID&page=$page"
            ) . "#post$postID";

            $lastPostHTML = '
                <a href="' . $lastPostUrl . '">
                    ' . date('d.m.Y H:i', (int)$last['created_at']) . '
                    von ' . htmlspecialchars($last['username'], ENT_QUOTES, "UTF-8") . '
                </a>
            ';

        } else {
            $lastPostHTML = "<span class='text-muted fst-italic'>Keine Beiträge</span>";
        }*/

        $lp = safe_query("
            SELECT 
                p.postID,
                p.threadID,
                p.created_at,
                u.username
            FROM plugins_forum_posts p
            LEFT JOIN users u ON u.userID = p.userID
            WHERE p.threadID = {$threadID}
            ORDER BY p.created_at DESC
            LIMIT 1
        ");

        $last = mysqli_fetch_assoc($lp);

        if ($last && !empty($last['postID'])) {

            $threadID = (int)$last['threadID'];
            $postID   = (int)$last['postID'];

            // posts pro Seite absichern
            $perPage = (int)($settings['posts_per_page'] ?? 10);
            if ($perPage <= 0) {
                $perPage = 10;
            }

            // Position des letzten Posts ermitteln (robust)
            $res = safe_query("
                SELECT COUNT(*) AS pos
                FROM plugins_forum_posts
                WHERE threadID = $threadID
                  AND created_at <= (
                      SELECT created_at
                      FROM plugins_forum_posts
                      WHERE postID = $postID
                      LIMIT 1
                  )
            ");

            $row      = mysqli_fetch_assoc($res);
            $position = max(1, (int)$row['pos']);
            $page     = (int)ceil($position / $perPage);

            // KORREKTE URL mit page + anchor
            $lastPostUrl = SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=thread&id=$threadID&page=$page"
            ) . "#post$postID";

            $lastPostHTML = '
                <a href="' . $lastPostUrl . '">
                    ' . date('d.m.Y H:i', (int)$last['created_at']) . '
                    von ' . htmlspecialchars($last['username'], ENT_QUOTES, "UTF-8") . '
                </a>
            ';

        } else {
            $lastPostHTML = "<span class='text-muted fst-italic'>Keine Beiträge</span>";
        }


        /* --------------------------------------------------------
           NEU-STATUS
        -------------------------------------------------------- */
        $isNew = false;
        if ($userID > 0 && $last) {
            $lastRead = $readInfo[$threadID] ?? 0;
            if ((int)$last['created_at'] > $lastRead) {
                $isNew = true;
            }
        }

        $newBadge = ($userID > 0 && $isNew)
            ? "<span class='badge bg-danger ms-2'>neu</span>"
            : '';

        /* --------------------------------------------------------
           STATUS BADGES
        -------------------------------------------------------- */
        $statusBadges = '';

        if ((int)$t['is_locked'] === 1) {
            $statusBadges .= "
                <span class='badge bg-warning text-dark ms-2'>
                    <i class='bi bi-lock-fill'></i> geschlossen
                </span>
            ";
        }

        if ((int)$t['is_pinned'] === 1) {
            $statusBadges .= "
                <span class='badge bg-info text-dark ms-2'>
                    <i class='bi bi-pin-fill'></i> gepinnt
                </span>
            ";
        }

        $perPage = (int)($settings['posts_per_page'] ?? 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $totalPosts = max(1, (int)$t['posts']); // inkl. Startpost
        $lastPage   = (int)ceil($totalPosts / $perPage);

        /* --------------------------------------------------------
           THREAD ZEILE
        -------------------------------------------------------- */
        $threadUrl = SeoUrlHandler::convertToSeoUrl(
            "index.php?site=forum&action=thread&id=" . (int)$threadID . "&page=" . $lastPage
        );

        $threadHTML .= "
            <li class='list-group-item d-flex justify-content-between align-items-center'>
                <div>
                    <a href='{$threadUrl}'
                       class='fw-semibold text-decoration-none'>
                        " . htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') . "
                    </a>
                    {$newBadge}
                    {$statusBadges}
                    <br>
                    <small class='text-muted'>
                        Antworten: " . max(0, (int)$t['posts'] - 1) . " ·
                        Aufrufe: " . (int)$t['views'] . "
                    </small>
                </div>

                <div class='text-end' style='min-width:220px;'>
                    <div class='small text-muted'>Letzter Beitrag:</div>
                    {$lastPostHTML}
                </div>
            </li>
        ";

    }

    if ($threadHTML !== '') {
        $threadHTML = "<ul class='list-group mb-3'>{$threadHTML}</ul>";
    } else {
        $threadHTML = "<div class='alert alert-info'>Keine Threads vorhanden.</div>";
    }

    /* ==========================================================
       TEMPLATE
    ========================================================== */
    echo $tpl->loadTemplate(
        'forum_category',
        'category',
        [
            'forum_url'             => SeoUrlHandler::convertToSeoUrl('index.php?site=forum'),
            'category_title'        => $catTitle,
            'category_description'  => $catDesc,
            'category_locked_alert' => '',
            'new_thread_button'     => $newThreadButtonHTML,
            'threads_html'          => $threadHTML
        ],
        'plugin'
    );
}
