<?php
/**********************************************************************
 * NEXPELL – FORUM MODULE
 * Datei: forum_thread.php
 * STABIL – NUR ForumACL::canView()
 **********************************************************************/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if (!defined('IS_FORUM')) {
    die("Direct access not allowed");
}



use nexpell\forum\ForumACL;
use nexpell\forum\ForumAccess;
use nexpell\user\UserPoints;
use nexpell\SeoUrlHandler;

require_once __DIR__ . '/system/ForumACL.php';
require_once __DIR__ . '/system/ForumAccess.php';
require_once BASE_PATH . '/system/classes/UserPoints.php';



/**********************************************************************
 * RENDER THREAD
 **********************************************************************/
function forum_render_thread($tpl, $languageService, $userID, $threadID)
{
    global $_database;

    $userID  = (int)$userID;
    $roleIDs = [];

    if ($userID === 0) {
        $roleIDs = [11]; // Gast
    } else {
        $roleIDs = $_SESSION['roles'] ?? [12]; // Fallback User
    }

    /* Legacy: erste Rolle für bestehende ACL-Funktionen */
    $roleID = $roleIDs[0] ?? 12;


    $languageService->readPluginModule("forum");

    if ($threadID > 0 && $userID > 0) {
        $now = time();

        $stmt = $_database->prepare("
            INSERT INTO plugins_forum_read (userID, threadID, last_read_at)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE last_read_at = VALUES(last_read_at)
        ");
        if ($stmt) {
            $stmt->bind_param("iii", $userID, $threadID, $now);
            $stmt->execute();
            $stmt->close();
        }
    }

    if ($threadID <= 0) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Thread nicht gefunden.</div>";
        return;
    }

    /* ==========================================================
       THREAD + BOARD + CATEGORY
    ========================================================== */
    $res = safe_query("
        SELECT t.*,
               c.catID,
               c.boardID,
               c.title AS category_title,
               b.title AS board_title
        FROM plugins_forum_threads t
        JOIN plugins_forum_categories c ON c.catID = t.catID
        JOIN plugins_forum_boards b ON b.boardID = c.boardID
        WHERE t.threadID = {$threadID}
        LIMIT 1
    ");

    $thread = mysqli_fetch_assoc($res);
    if (!$thread) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Thread existiert nicht.</div>";
        return;
    }

    // 🔑 IDs IMMER ZUERST DEFINIEREN
    $boardID    = (int)$thread['boardID'];
    $categoryID = (int)$thread['catID'];
    $threadID   = (int)$threadID;

    // ✅ CONTEXT DEFINIEREN
    $ctx = [
        'boardID'    => $boardID,
        'categoryID' => $categoryID,
        'threadID'   => $threadID
    ];

    /* ==========================================================
       ACL – VIEW = READ
    ========================================================== */
    if (!ForumACL::canView($ctx, $roleID)) {
        echo "<div class='alert alert-danger'>Kein Zugriff auf diesen Thread.</div>";
        return;
    }

    /* ==========================================================
       STATUS
    ========================================================== */
    $isLocked = ((int)$thread['is_locked'] === 1);
    #$isMod    = ($roleID === 1);
    $canReply = ForumAccess::canReply(
    $userID,
    $roleID,
    $boardID,
    $categoryID,
    $threadID,
    $isLocked
);

    /* ==========================================================
       POSTS + PAGINATION
    ========================================================== */
    $postsPerPage = 10;
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($page - 1) * $postsPerPage;

    // ================================================================
    // PAGINATION – TOTAL PAGES (IMMER DEFINIERT)
    // ================================================================
    $totalRes = safe_query("
        SELECT COUNT(*) AS cnt
        FROM plugins_forum_posts
        WHERE threadID = {$threadID}
          AND is_deleted = 0
    ");

    $totalPosts = (int)(mysqli_fetch_assoc($totalRes)['cnt'] ?? 0);
    $totalPages = max(1, (int)ceil($totalPosts / $postsPerPage));


    $resPosts = safe_query("
        SELECT p.*, 
               u.username,
               COALESCE(up.signatur,'') AS signatur,
               eu.username AS editor_name
        FROM plugins_forum_posts p
        LEFT JOIN users u ON u.userID = p.userID
        LEFT JOIN user_profiles up ON up.userID = p.userID
        LEFT JOIN users eu ON eu.userID = p.edited_by
        WHERE p.threadID = {$threadID}
          AND p.is_deleted = 0
        ORDER BY p.created_at ASC
        LIMIT {$offset}, {$postsPerPage}
    ");


    $posts = [];

    while ($row = mysqli_fetch_assoc($resPosts)) {

        $postID     = (int)$row['postID'];
        $postUserID = (int)$row['userID'];
        $isOwnPost  = ($userID > 0 && $userID === $postUserID);

        $time = (int)($row['edited_at'] ?: $row['created_at']);
        $row['post_time'] = date('d.m.Y H:i', $time);

        // ------------------------------------------------
        // ZEIT / EDIT-STATUS (IMMER DEFINIERT)
        // ------------------------------------------------
        $createdAt = (int)$row['created_at'];
        $editedAt  = (int)($row['edited_at'] ?? 0);

        // ------------------------------------------------
        // ROLLE DES EDITORS (PRIORISIERT)
        // ------------------------------------------------
        $editorRole = 0;
        $editorID   = (int)($row['edited_by'] ?? 0);
        $editorName = htmlspecialchars($row['editor_name'] ?? '', ENT_QUOTES);

        if ($editorID > 0) {

            $resEditorRole = safe_query("
                SELECT roleID
                FROM user_role_assignments
                WHERE userID = {$editorID}
                ORDER BY
                    CASE roleID
                        WHEN 1 THEN 1   -- Admin
                        WHEN 7 THEN 2   -- Moderator
                        ELSE 99
                    END
                LIMIT 1
            ");

            if ($er = mysqli_fetch_assoc($resEditorRole)) {
                $editorRole = (int)$er['roleID'];
            }
        }


        if ($editedAt > 0 && $editedAt > $createdAt) {

            $row['post_time']       = date('d.m.Y H:i', $editedAt);
            $row['post_time_icon']  = '<i class="bi bi-pencil-square text-warning"></i>';
            $row['post_time_title'] = 'Bearbeitet am ' . date('d.m.Y H:i', $editedAt);
            $row['is_edited']       = true;

            $row['edited_by_info'] = '';

            $authorID = (int)$row['userID'];

            if (
                $editorID > 0 &&
                $editorID !== $authorID &&
                in_array($editorRole, [1, 7], true)
            ) {
                $roleLabel = ($editorRole === 1)
                    ? '<span class="badge bg-danger">Admin</span>'
                    : '<span class="badge bg-warning text-dark">Moderator</span>';

                $row['edited_by_info'] = '
                    <div class="text-muted small mt-1">
                        Bearbeitet von '.$roleLabel.' '.$editorName.'
                    </div>';
            }

        } else {

            $row['post_time']       = date('d.m.Y H:i', $createdAt);
            $row['post_time_icon']  = '<i class="bi bi-calendar-plus text-muted"></i>';
            $row['post_time_title'] = 'Erstellt am ' . date('d.m.Y H:i', $createdAt);
            $row['is_edited']       = false;
            $row['edited_by_info']  = '';
        }



        $row['avatar'] = getavatar($postUserID)
            ?: "/includes/themes/default/images/no_avatar.png";



        // ==================================================
        // ACHIEVEMENTS PLUGIN STATUS (IMMER DEFINIERT)
        // ==================================================
        $achievements_plugin_active = false;

        $achievements_plugin_path = BASE_PATH . '/includes/plugins/achievements/engine_achievements.php';

        if (
            file_exists($achievements_plugin_path)
            && !function_exists('achievements_get_user_icons_html')
        ) {
            require_once $achievements_plugin_path;
        }

        if (function_exists('achievements_get_user_icons_html')) {
            $achievements_plugin_active = true;
        }

        // ------------------------------------------------
        // ACHIEVEMENTS (IMMER DEFINIERT)
        // ------------------------------------------------
        $row['achievement_icons'] = '';

        if ($achievements_plugin_active && $postUserID > 0) {

            $icons = achievements_get_user_icons_html($postUserID);

            if (!empty($icons)) {
                $row['achievement_icons'] = '
                    <div class="mt-2 text-center">
                        '.$icons.'
                    </div>';
            }
        }




        
        // ------------------------------------------------
        // USER POINTS (IMMER DEFINIERT)
        // ------------------------------------------------
        $userPointsCache = [];

        $row['points'] = 0;

        if ($postUserID > 0) {

            if (!isset($userPointsCache[$postUserID])) {
                $userPointsCache[$postUserID] = UserPoints::get($postUserID);
            }

            $row['points'] = $userPointsCache[$postUserID];
        }

        $userPostCountCache = [];

        $row['post_count'] = 0;

        if ($postUserID > 0) {

            if (!isset($userPostCountCache[$postUserID])) {

                $resCnt = safe_query("
                    SELECT COUNT(*) AS cnt
                    FROM plugins_forum_posts
                    WHERE userID = {$postUserID}
                      AND is_deleted = 0
                ");

                $userPostCountCache[$postUserID] =
                    (int)(mysqli_fetch_assoc($resCnt)['cnt'] ?? 0);
            }

            $row['post_count'] = $userPostCountCache[$postUserID];
        }


   

        // ------------------------------------------------
        // ROLLE DES POST-AUTORS
        // ------------------------------------------------
        $postRoleID = 12; // Fallback: User

        $resRole = safe_query("
            SELECT roleID
            FROM user_role_assignments
            WHERE userID = {$postUserID}
            ORDER BY
                CASE roleID
                    WHEN 1 THEN 1   -- Admin
                    WHEN 7 THEN 2   -- Moderator
                    WHEN 11 THEN 3  -- User
                    ELSE 99
                END
            LIMIT 1
        ");

        if ($r = mysqli_fetch_assoc($resRole)) {
            $postRoleID = (int)$r['roleID'];
        }  

        $row['role_badge'] = '';

        if ($postRoleID === 1) {
            $row['role_badge'] = '<span class="badge bg-danger">Admin</span>';
        }
        elseif ($postRoleID === 7) {
            $row['role_badge'] = '<span class="badge bg-warning text-dark">Moderator</span>';
        }
        elseif ($postRoleID === 12) {
            $row['role_badge'] = '<span class="badge bg-secondary">User</span>';
        }   

        /* ---------------- Buttons ---------------- */


        $row['quote_button']  = '';
        $row['edit_button']   = '';
        $row['delete_button'] = '';

        $isModerator =
            in_array(7, $roleIDs, true) // 🔥 Moderator-Rolle
            || ForumAccess::isModerator(
                $userID,
                $roleID,
                $boardID,
                $categoryID,
                $threadID
            );

        // 🔑 globale Rechte aus ACL
        $hasGlobalEdit = false;
        $hasGlobalDelete = false;

        foreach ($roleIDs as $rid) {
            if (ForumACL::check($userID, $rid, $boardID, $categoryID, $threadID, 'edit')) {
                $hasGlobalEdit = true;
            }
            if (ForumACL::check($userID, $rid, $boardID, $categoryID, $threadID, 'delete')) {
                $hasGlobalDelete = true;
            }
        }



        /* =========================================================
           QUOTE
           - Moderator
           - User nur wenn Antworten erlaubt
        ========================================================= */
        if ($isModerator || $canReply) {
            $quoteUrl = SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=quote&postID={$postID}&threadID={$threadID}"
            );

            $row['quote_button'] = '
                <a href="' . htmlspecialchars($quoteUrl, ENT_QUOTES, 'UTF-8') . '"
                   class="btn btn-sm btn-secondary me-1">
                    <i class="bi bi-chat-quote"></i>
                </a>';

        }

        /* =========================================================
           EDIT
           - Moderator
           - global can_edit
           - eigener Post
        ========================================================= */
        if ($isModerator || $hasGlobalEdit || $isOwnPost) {
            $editUrl = SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=edit_post&postID={$postID}"
            );

            $row['edit_button'] = '
                <a href="' . htmlspecialchars($editUrl, ENT_QUOTES, 'UTF-8') . '"
                   class="btn btn-sm btn-warning me-1">
                    <i class="bi bi-pencil"></i>
                </a>';

        }

        /* =========================================================
           DELETE
           - Moderator
           - global can_delete
           - eigener Post NUR wenn Antworten erlaubt
        ========================================================= */
        if (
            $isModerator
            || $hasGlobalDelete
            || ($isOwnPost && $canReply)
        ) {
            $deleteUrl = SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=delete_post&postID={$postID}&threadID={$threadID}"
            );

            

                $row['delete_button'] = '
            <button type="button"
                    class="btn btn-sm btn-danger me-1"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmDeleteModal"
                    data-delete-url="' . htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') . '">
                <i class="bi bi-trash"></i>
            </button>

            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                                Beitrag löschen
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            Möchtest du diesen Beitrag wirklich löschen?<br>
                            <small class="text-muted">Dieser Vorgang kann nicht rückgängig gemacht werden.</small>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                Abbrechen
                            </button>

                            <a href="#"
                               id="confirmDeleteBtn"
                               class="btn btn-danger">
                                <i class="bi bi-trash"></i> Löschen
                            </a>
                        </div>

                    </div>
                </div>
            </div>';  

        }


        /* ---------------- Like ---------------- */




        // ✅ Likes IMMER laden
        $count = getLikeCount($postID);
        $row['likes'] = (int)$count;



        $row['like_button'] = '';
        #$row['likes'] = 0; // ✅ IMMER initialisieren

        // ----------------------------------------
        // LIKE-BUTTON (IMMER anzeigen bei Login)
        // ----------------------------------------

        ?><style>
            .forum-like-btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            .forum-like-btn.is-disabled {
                opacity: 0.6;
                cursor: not-allowed;
                pointer-events: auto; /* 🔑 wichtig für title */
            }

            .forum-like-btn.is-disabled:active {
                pointer-events: none;
            }
        </style>
        <?php


        // ------------------------------------
        // FALL 1: Eingeloggt & fremder Beitrag
        // ------------------------------------
        if ($userID > 0 && !$isOwnPost) {

            $liked = userLikedPost($postID, $userID); 
        $count = getLikeCount($postID); // ✅ likes IMMER setzen 
        $row['likes'] = (int)$count; 

        // Likes immer initialisieren
        $row['likes'] = (int)getLikeCount($postID);
        $row['like_button'] = '';

            $row['like_button'] = '
                <button class="forum-like-btn btn btn-sm '.($liked ? 'btn-danger' : 'btn-outline-danger').'"
                        data-post="'.$postID.'"
                        data-liked="'.($liked ? 1 : 0).'">
                    <i class="bi '.($liked ? 'bi bi-hand-thumbs-up-fill' : 'bi bi-hand-thumbs-up').'"></i>
                    <span class="like-count">'.$row['likes'].'</span>
                </button>';
        }

        // ------------------------------------
        // FALL 2: Eingeloggt & eigener Beitrag
        // ------------------------------------
        elseif ($userID > 0 && $isOwnPost) {

            $row['like_button'] = '
                <button class="forum-like-btn btn btn-sm btn-outline-secondary is-disabled"
                        aria-disabled="true"
                        title="Du kannst deinen eigenen Beitrag nicht liken">
                    <i class="bi bi-hand-thumbs-up"></i>
                    <span class="like-count">'.$row['likes'].'</span>
                </button>';
        }

        // ------------------------------------
        // FALL 3: Gast
        // ------------------------------------
        else {

            $row['like_button'] = '
                <button class="forum-like-btn btn btn-sm btn-outline-secondary is-disabled"
                        aria-disabled="true"
                        title="Bitte anmelden, um Beiträge zu liken">
                    <i class="bi bi-hand-thumbs-up"></i>
                    <span class="like-count">'.$row['likes'].'</span>
                </button>';
        }

        $posts[] = $row;
    }



// ================================================================
// TEMPLATE-DATEN
// ================================================================
// Posts in HTML rendern, statt als foreach-Array
// Posts in HTML rendern
$postsHTML = '';

foreach ($posts as $p) {

    $postsHTML .= '
    <div class="card shadow-sm border rounded p-3 mb-4" id="post'. $p['postID'] .'">
        <div class="row">

            <!-- User Info -->
            <div class="col-md-2 text-center border-end pe-3
            d-flex flex-column align-items-center">

            <img src="' . $p['avatar'] . '"
                 class="img-fluid mb-2"
                 style="max-width:80px;">

            <div class="fw-bold">
                ' . htmlspecialchars($p['username']) . '
            </div>

            <small class="text-muted mb-1">
                ' . (int)$p['post_count'] . ' Beiträge
            </small>

            <div class="badge bg-primary mt-2">
                <i class="bi bi-star-fill"></i> ' . (int)$p['points'] . ' Punkte
            </div>

            ' . (!empty($p['achievement_icons'])
                ? '<div class="mt-2 text-center">' . $p['achievement_icons'] . '</div>'
                : '') . '

            ' . (!empty($p['role_badge'])
                ? '<div class="mt-2">' . $p['role_badge'] . '</div>'
                : '') . '
            </div>
            <!-- Content -->
            <div class="col-md-10">
                <div class="d-flex justify-content-between">
                    <div>
                        '.$p['post_time_icon'].' '.$p['post_time_title'].'<br>'.($p['edited_by_info'] ?? '').'
                    </div>

                    <div>
                        '. $p['quote_button'] .'
                        '. $p['edit_button'] .'
                        '. $p['delete_button'] .'
                    </div>
                </div>

                <hr>

                <div class="post-content">' . html_entity_decode($p['content'], ENT_QUOTES | ENT_HTML5) . '</div>


                <div class="forum-signature mt-4 pt-2 border-top text-muted" style="font-size:0.9em;">
                    '. $p['signatur'] .'
                </div>

                <div class="mt-3 text-end">
                    '. $p['like_button'] .'

                </div>
            </div>
        </div>
    </div>';
}

if (mysqli_affected_rows($_database) === 0) {

    // Post existiert evtl. nicht mehr (gelöscht)
    echo json_encode([
        'success' => true,
        'likes'   => 0,
        'deleted' => true
    ]);
    exit;
}

// ==========================================================
// URL / TITEL
// ==========================================================
$categoryID = (int)$thread['catID'];
$forumURL = SeoUrlHandler::convertToSeoUrl(
    "index.php?site=forum"
);

$categoryURL = SeoUrlHandler::convertToSeoUrl(
    "index.php?site=forum&action=category&id=" . (int)$categoryID
);


$threadTitle   = htmlspecialchars($thread['title'] ?? '');
$categoryTitle = htmlspecialchars($thread['category_title'] ?? '');

// ==========================================================
// LOGIN-STATUS
// ==========================================================
$isLoggedIn = ($userID > 0);

// ==========================================================
// QUOTE (OPTIONAL)
// ==========================================================
$quoteText = $_SESSION['quote_text'] ?? '';
unset($_SESSION['quote_text']);

// ==========================================================
// STATUS / FORM
// ==========================================================
// ==========================================================
// STATUS / FORM (FINAL LOGIK)
// ==========================================================

$threadBadges = '';

$reply_show_form = 0;
$reply_status_message = '';
$reply_status_type = '';

if ($isLocked) {
    $reply_status_message = 'Dieser Thread ist geschlossen.';
    $reply_status_type = 'warning';
}
elseif (!$isLoggedIn) {
    $reply_status_message = 'Bitte melde dich an, um zu antworten.';
    $reply_status_type = 'info';
}
elseif (!$canReply && !$isModerator) {
    $reply_status_message = 'Du hast keine Berechtigung, zu antworten.';
    $reply_status_type = 'danger';
}


/* BADGES */
if ((int)$thread['is_locked'] === 1) {
    $threadBadges .= '
        <span class="badge bg-danger me-2">
            <i class="bi bi-lock-fill"></i> Gesperrt
        </span>';
}

if ((int)$thread['is_pinned'] === 1) {
    $threadBadges .= '
        <span class="badge bg-warning text-dark me-2">
            <i class="bi bi-pin-fill"></i> Gepinnt
        </span>';
}


// ==========================================================
// THREAD STATUS (für Template)
// ==========================================================
$threadIsPinned = ((int)($thread['is_pinned'] ?? 0) === 1);


// ==========================================================
// REPLY-FORM
// ==========================================================


$replyFormHTML = '';

if (
    $isLoggedIn
    && (
        (!$isLocked && $canReply)      // normaler User
        || ($isLocked && $isModerator) // Admin / Moderator trotz Lock
    )
) {

    $replyActionUrl = SeoUrlHandler::convertToSeoUrl(
        "index.php?site=forum&action=reply&threadID=" . (int)$threadID
    );

    $replyFormHTML = '
        <h4>Antwort schreiben</h4>' .
        $tpl->loadTemplate(
            'forum_forms',
            'reply',
            [
                'threadID'    => (int)$threadID,
                'form_action' => $replyActionUrl,
                'quote_text'  => $quoteText
            ],
            'plugin'
        );
}





// ==========================================================
// PAGINATION (SEO-KORREKT)
// ==========================================================
$threadBaseUrl = SeoUrlHandler::convertToSeoUrl(
    "index.php?site=forum&action=thread&id=" . (int)$threadID
);
$threadBaseUrl = rtrim($threadBaseUrl, '/') . '/';

$paginationHTML = $tpl->renderPagination(
    $threadBaseUrl,
    (int)$page,
    (int)$totalPages,
    'page'
);




// ==========================================================
// TEMPLATE-DATEN
// ==========================================================
$data_array = [
    'forum_url'            => $forumURL,
    'category_url'         => $categoryURL,
    'category_title'       => $categoryTitle,

    // Thread-Status
    'thread_status_badges' => $threadBadges,
    'thread_is_pinned'     => $threadIsPinned ? 1 : 0,

    // Reply
    
    'reply_thread_id'      => (int)$threadID,
    'reply_csrf'           => $_SESSION['csrf'],
    
    'reply_form' => $replyFormHTML,

    'reply_show_form'       => $reply_show_form,
    'reply_status_message' => $reply_status_message,
    'reply_status_type'    => $reply_status_type,

    // Content
    'thread_title'         => $threadTitle,
    'posts_html'           => $postsHTML,
    'pagination'           => $paginationHTML
];


// ==========================================================
// TEMPLATE RENDERN
// ==========================================================
echo $tpl->loadTemplate(
    "forum_thread",
    "thread",
    $data_array,
    "plugin"
);

}

function getLikeCount(int $postID): int
{
    $res = safe_query("
        SELECT COUNT(*) AS cnt
        FROM plugins_forum_post_likes
        WHERE postID = {$postID}
    ");
    return (int)(mysqli_fetch_assoc($res)['cnt'] ?? 0);
}

function userLikedPost(int $postID, int $userID): bool
{
    $res = safe_query("
        SELECT 1
        FROM plugins_forum_post_likes
        WHERE postID = {$postID}
          AND userID = {$userID}
        LIMIT 1
    ");
    return mysqli_num_rows($res) > 0;
}


?>
