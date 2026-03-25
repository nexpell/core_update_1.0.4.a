<?php
/**********************************************************************
 * NEXPELL - FORUM MODULE
 * Datei: forum_boards.php
 **********************************************************************/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('IS_FORUM')) {
    die("Direct access not allowed");
}

use nexpell\forum\ForumACL;
use nexpell\SeoUrlHandler;

require_once __DIR__ . '/system/ForumACL.php';

global $_database;

$tpl = new Template();

$config = mysqli_fetch_array(
    safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1")
);

$class = htmlspecialchars($config['selected_style'] ?? '', ENT_QUOTES, 'UTF-8');

$data_array = [
    'class' => $class,
    'title' => $languageService->get('title'),
    'subtitle' => 'Forum'
];

echo $tpl->loadTemplate("forum", "head", $data_array, 'plugin');

function forum_stats_for_category(int $catID): array
{
    global $_database;

    $catID = (int)$catID;

    $row = mysqli_fetch_assoc(safe_query("
        SELECT
            COUNT(DISTINCT t.threadID) AS threadCount,
            COUNT(p.postID) AS postCount,
            MAX(p.created_at) AS lastPostTime
        FROM plugins_forum_threads t
        LEFT JOIN plugins_forum_posts p
            ON p.threadID = t.threadID
           AND p.is_deleted = 0
        WHERE t.catID = {$catID}
          AND t.is_deleted = 0
    "));

    $lastPost = null;

    if (!empty($row['lastPostTime'])) {
        $lp = mysqli_fetch_assoc(safe_query("
            SELECT
                p.postID,
                p.threadID,
                p.created_at,
                u.username
            FROM plugins_forum_posts p
            INNER JOIN plugins_forum_threads t
                ON t.threadID = p.threadID
               AND t.is_deleted = 0
            LEFT JOIN users u
                ON u.userID = p.userID
            WHERE t.catID = {$catID}
              AND p.is_deleted = 0
            ORDER BY p.created_at DESC
            LIMIT 1
        "));

        if ($lp) {
            $lastPost = $lp;
        }
    }

    return [
        'threadCount' => (int)($row['threadCount'] ?? 0),
        'postCount' => (int)($row['postCount'] ?? 0),
        'lastPost' => $lastPost
    ];
}

function forum_render_boards($tpl, $languageService, $userID)
{
    global $_database, $settings;

    $userID = (int)$userID;
    $roleID = (int)($_SESSION['roleID'] ?? 0);

    if ($userID === 0) {
        $roleID = 11;
    }

    $languageService->readPluginModule("forum");

    $boards = [];
    $res = safe_query("
        SELECT boardID, title, description, position
        FROM plugins_forum_boards
        ORDER BY position ASC, boardID ASC
    ");

    while ($row = mysqli_fetch_assoc($res)) {
        $boards[] = $row;
    }

    if (!$boards) {
        echo "<div class='alert alert-info'>Keine Boards gefunden.</div>";
        return;
    }

    $categoriesByBoard = [];
    $res = safe_query("
        SELECT catID, boardID, title, description, position
        FROM plugins_forum_categories
        ORDER BY position ASC, catID ASC
    ");

    while ($row = mysqli_fetch_assoc($res)) {
        $categoriesByBoard[(int)$row['boardID']][] = $row;
    }

    $renderBoards = [];

    foreach ($boards as $board) {
        $boardID = (int)$board['boardID'];
        $ctxBoard = [
            'boardID' => $boardID,
            'categoryID' => 0,
            'threadID' => 0
        ];

        if (!ForumACL::canView($ctxBoard, $roleID)) {
            continue;
        }

        $categories = $categoriesByBoard[$boardID] ?? [];
        $categoryHTML = '';

        foreach ($categories as $cat) {
            $catID = (int)$cat['catID'];
            $ctxCat = [
                'boardID' => $boardID,
                'categoryID' => $catID,
                'threadID' => 0
            ];

            if (!ForumACL::canView($ctxCat, $roleID)) {
                continue;
            }

            $stats = forum_stats_for_category($catID);
            $newBadge = '';

            if ($userID > 0) {
                $resUnread = safe_query("
                    SELECT 1
                    FROM plugins_forum_threads t
                    INNER JOIN plugins_forum_posts p
                        ON p.threadID = t.threadID
                       AND p.is_deleted = 0
                    LEFT JOIN plugins_forum_read r
                        ON r.threadID = t.threadID
                       AND r.userID = {$userID}
                    WHERE t.catID = {$catID}
                      AND t.is_deleted = 0
                    GROUP BY t.threadID, r.last_read_at
                    HAVING r.last_read_at IS NULL
                        OR r.last_read_at < MAX(p.created_at)
                    LIMIT 1
                ");

                if ($resUnread && mysqli_num_rows($resUnread) > 0) {
                    $newBadge = '<span class="badge bg-danger ms-2">neu</span>';
                }
            }

            $lastLink = '<span class="text-muted fst-italic">Noch keine Beitr&auml;ge</span>';

            if (!empty($stats['lastPost'])) {
                $lp = $stats['lastPost'];
                $threadID = (int)$lp['threadID'];
                $postID = (int)$lp['postID'];
                $perPage = (int)($settings['posts_per_page'] ?? 10);

                if ($perPage <= 0) {
                    $perPage = 10;
                }

                $resPos = safe_query("
                    SELECT COUNT(*) AS position
                    FROM plugins_forum_posts
                    WHERE threadID = {$threadID}
                      AND is_deleted = 0
                      AND postID <= {$postID}
                ");

                $posRow = mysqli_fetch_assoc($resPos);
                $position = (int)($posRow['position'] ?? 0);

                if ($position > 0) {
                    $page = max(1, (int)ceil($position / $perPage));
                    $lastPostUrl = SeoUrlHandler::convertToSeoUrl(
                        "index.php?site=forum&action=thread&id={$threadID}&page={$page}"
                    ) . "#post{$postID}";

                    $lastLink = '
                        <a href="' . htmlspecialchars($lastPostUrl, ENT_QUOTES, 'UTF-8') . '">
                            ' . date('d.m.Y H:i', (int)$lp['created_at']) . '
                            von ' . htmlspecialchars((string)($lp['username'] ?? 'Unbekannt'), ENT_QUOTES, 'UTF-8') . '
                        </a>';
                }
            }

            $categoryUrl = SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=category&id=" . $catID
            );

            $categoryHTML .= "
                <li class='list-group-item d-flex justify-content-between align-items-center'>
                    <div>
                        <a href='{$categoryUrl}' class='fw-semibold text-decoration-none'>
                            " . htmlspecialchars($cat['title'], ENT_QUOTES, 'UTF-8') . "
                        </a>
                        {$newBadge}
                        <br>
                        <small class='text-muted'>
                            " . htmlspecialchars($cat['description'], ENT_QUOTES, 'UTF-8') . "
                        </small>
                    </div>

                    <div class='text-end' style='min-width:220px;'>
                        <span class='badge bg-primary me-2'>
                            Themen: " . (int)$stats['threadCount'] . "
                        </span>
                        <span class='badge bg-secondary me-2'>
                            Beitr&auml;ge: " . (int)$stats['postCount'] . "
                        </span>
                        <div class='small text-muted mt-1'>
                            Letzter Beitrag: {$lastLink}
                        </div>
                    </div>
                </li>";
        }

        if ($categoryHTML === '') {
            continue;
        }

        $renderBoards[] = [
            'board_title' => htmlspecialchars($board['title'], ENT_QUOTES, 'UTF-8'),
            'board_description' => nl2br(htmlspecialchars($board['description'], ENT_QUOTES, 'UTF-8')),
            'categories_html' => "<ul class='list-group mb-3'>{$categoryHTML}</ul>"
        ];
    }

    echo $tpl->loadTemplate(
        "forum_boards",
        "boards",
        [
            'boards' => $renderBoards,
            'debug' => ''
        ],
        "plugin"
    );
}
