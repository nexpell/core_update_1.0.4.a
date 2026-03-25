<?php
/**********************************************************************
 * NEXPELL – FORUM ACTIONS (FINAL & CLEAN)
 *
 * - reply
 * - quote
 * - quote_reply
 * - new_thread
 * - edit_post
 * - delete_post
 * - lock / unlock
 *
 * ✅ MULTI-ROLE
 * ✅ KEIN roleID-ARRAY an ACL
 * ✅ Moderator darf fremde Beiträge bearbeiten/löschen
 **********************************************************************/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('IS_FORUM')) {
    die("Direct access not allowed");
}

use nexpell\forum\ForumACLRepository;
use nexpell\SeoUrlHandler;

require_once __DIR__ . '/system/ForumACLRepository.php';

global $_database, $tpl;

/* ============================================================
   ROLE HELPERS
============================================================ */

/**
 * Alle Rollen eines Users
 */
function forum_get_role_ids(int $userID): array
{
    if ($userID <= 0) {
        return [11]; // Gast
    }

    $roles = [];
    $res = safe_query("
        SELECT roleID
        FROM user_role_assignments
        WHERE userID = {$userID}
    ");

    while ($r = mysqli_fetch_assoc($res)) {
        $roles[] = (int)$r['roleID'];
    }

    return $roles ?: [12]; // Fallback User
}

/**
 * TRUE wenn irgendeine Rolle das Recht hat
 */
function forum_acl_can_any(
    array $roleIDs,
    int $boardID,
    int $catID,
    int $threadID,
    string $permission
): bool {
    foreach ($roleIDs as $roleID) {
        $acl = new ForumACLRepository(
            $boardID,
            $catID,
            $threadID,
            $roleID
        );

        if ($acl->can($permission)) {
            return true;
        }
    }
    return false;
}

function forum_db_escape(string $value): string
{
    global $_database;
    return mysqli_real_escape_string($_database, $value);
}

/* ============================================================
   ACTION: REPLY
============================================================ */
function forum_action_reply($tpl, int $userID): void
{
    global $_database, $settings;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $threadID = (int)($_POST['threadID'] ?? 0);
    if ($threadID <= 0) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Thread-ID fehlt.</div>";
        return;
    }

    $res = safe_query("
        SELECT t.threadID, t.catID, c.boardID
        FROM plugins_forum_threads t
        JOIN plugins_forum_categories c ON c.catID = t.catID
        WHERE t.threadID = {$threadID}
        LIMIT 1
    ");
    $thread = mysqli_fetch_assoc($res);
    if (!$thread) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Thread nicht gefunden.</div>";
        return;
    }

    $roleIDs = forum_get_role_ids($userID);

    if (!forum_acl_can_any(
        $roleIDs,
        (int)$thread['boardID'],
        (int)$thread['catID'],
        $threadID,
        'reply'
    )) {
        echo "<div class='alert alert-danger'>Keine Berechtigung.</div>";
        return;
    }

    $content = trim($_POST['content'] ?? '');
    if ($content === '') {
        echo "<div class='alert alert-warning'>Inhalt leer.</div>";
        return;
    }

        safe_query("
            INSERT INTO plugins_forum_posts
            (threadID, userID, content, created_at)
            VALUES
            ({$threadID}, {$userID}, '" . forum_db_escape($content) . "', UNIX_TIMESTAMP())
        ");

    // 🔑 NEUE POST-ID
    $postID = (int)mysqli_insert_id($_database);    

    // =====================================================
    // 🔥 UPLOADS BESTÄTIGEN (NEW THREAD)
    // =====================================================

// 🔒 QUOTE-BEREICH ENTFERNEN (damit keine fremden Bilder erkannt werden)
    /* =====================================================
       📄 SEITENBERECHNUNG
    ===================================================== */

    $perPage = (int)($settings['posts_per_page'] ?? 10);
    if ($perPage <= 0) {
        $perPage = 10;
    }

    $res = safe_query("
        SELECT COUNT(*) AS pos
        FROM plugins_forum_posts
        WHERE threadID = {$threadID}
          AND postID <= {$postID}
    ");
    $row = mysqli_fetch_assoc($res);
    $position = (int)$row['pos'];

    $page = (int)ceil($position / $perPage);
    if ($page < 1) {
        $page = 1;
    }

    /* =========================
       REDIRECT (RICHTIG!)
    ========================= */
    header("Location: " . SeoUrlHandler::convertToSeoUrl(
        "index.php?site=forum&action=thread&id={$threadID}&page={$page}#post{$postID}"
    ));
    exit;
}

/* ============================================================
   ACTION: EDIT POST
============================================================ */
function forum_action_edit_post($tpl, int $userID): void
{
    $postID = (int)($_POST['postID'] ?? $_GET['postID'] ?? 0);
    if ($postID <= 0) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Post-ID fehlt.</div>";
        return;
    }

    /* =========================
       POST LADEN
    ========================= */
    $res = safe_query("
        SELECT 
            p.postID,
            p.content,
            p.userID AS ownerID,
            p.threadID,
            t.catID,
            c.boardID
        FROM plugins_forum_posts p
        JOIN plugins_forum_threads t ON t.threadID = p.threadID
        JOIN plugins_forum_categories c ON c.catID = t.catID
        WHERE p.postID = {$postID}
        LIMIT 1
    ");
    $post = mysqli_fetch_assoc($res);
    if (!$post) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Beitrag nicht gefunden.</div>";
        return;
    }

    /* =========================
       ACL
    ========================= */
    $roleIDs = forum_get_role_ids($userID);

    $canEdit = forum_acl_can_any(
        $roleIDs,
        (int)$post['boardID'],
        (int)$post['catID'],
        (int)$post['threadID'],
        'edit'
    );


    if ((int)$post['ownerID'] !== (int)$userID && !$canEdit) {
        echo "<div class='alert alert-danger'>Keine Berechtigung.</div>";
        return;
    }

    /* =========================
       POST SPEICHERN
    ========================= */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $content  = trim($_POST['content'] ?? '');
        $editorID = (int)$userID;

        if ($content === '') {
            echo "<div class='alert alert-warning'>Inhalt darf nicht leer sein.</div>";
            return;
        }

        /* =====================================================
           🔥 BILDER LÖSCHEN (forum_images) – FINAL
        ===================================================== */

        $oldContent = $post['content'];
        $newContent = $content;

        // <img src="..."> extrahieren
        preg_match_all('/<img[^>]+src="([^"]+)"/i', $oldContent, $oldMatches);
        preg_match_all('/<img[^>]+src="([^"]+)"/i', $newContent, $newMatches);

        $oldImages = $oldMatches[1] ?? [];
        $newImages = $newMatches[1] ?? [];

        // Dateinamen aus NEUEM Content
        $newFiles = [];
        foreach ($newImages as $url) {
            $path = parse_url($url, PHP_URL_PATH);
            if ($path) {
                $newFiles[] = basename($path);
            }
        }

        foreach ($oldImages as $oldUrl) {

            $path = parse_url($oldUrl, PHP_URL_PATH);
            if (!$path) {
                continue;
            }

            // ❗ NUR forum_images (mit oder ohne führenden Slash!)
            if (strpos($path, 'includes/plugins/forum/uploads/forum_images/') === false) {
                continue;
            }

            $filename = basename($path);

            // Wird das Bild noch benutzt?
            if (in_array($filename, $newFiles, true)) {
                continue;
            }

            // 🔑 EXAKT WIE IM ALTEN FORUM
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($path, '/');

            if (is_file($filePath)) {
                unlink($filePath);
                // Debug optional:
                // error_log('DELETED: ' . $filePath);
            }
        }


        /* =========================
           UPDATE
        ========================= */
        

        $contentDb = forum_db_escape($content);

        safe_query("
            UPDATE plugins_forum_posts
            SET
                content   = '$contentDb',
                edited_at = UNIX_TIMESTAMP(),
                edited_by = {$editorID}
            WHERE postID = {$postID}
        ");

        /* =====================================================
           📄 SEITENBERECHNUNG (EDIT)
        ===================================================== */

/* =====================================================
   📄 SEITENBERECHNUNG (EDIT)
===================================================== */

global $settings;

// Posts pro Seite
$perPage = (int)($settings['posts_per_page'] ?? 10);
if ($perPage <= 0) {
    $perPage = 10;
}

// Position des Posts im Thread ermitteln
$res = safe_query("
    SELECT COUNT(*) AS pos
    FROM plugins_forum_posts
    WHERE threadID = {$post['threadID']}
      AND postID <= {$postID}
");
$row = mysqli_fetch_assoc($res);
$position = (int)$row['pos'];

// Seite berechnen
$page = (int)ceil($position / $perPage);
if ($page < 1) {
    $page = 1;
}





header("Location: " . SeoUrlHandler::convertToSeoUrl(
    "index.php?site=forum&action=thread&id={$post['threadID']}&page={$page}#post{$postID}"
));
exit;
    }

    /* =========================
       FORMULAR
    ========================= */
    echo $tpl->loadTemplate(
        "forum_forms",
        "edit_post",
        [
            'content'     => $post['content'],
            'postID'      => $postID,
            'form_action' => SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=edit_post&postID={$postID}"
            )
        ],
        "plugin"
    );
}




/* ============================================================
   ACTION: DELETE POST
============================================================ */
function forum_action_delete_post($tpl, int $userID): void
{
    $postID = (int)($_GET['postID'] ?? 0);
    if ($postID <= 0) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Post-ID fehlt.</div>";
        return;
    }

    $res = safe_query("
        SELECT p.postID, p.threadID, t.catID, c.boardID
        FROM plugins_forum_posts p
        JOIN plugins_forum_threads t ON t.threadID = p.threadID
        JOIN plugins_forum_categories c ON c.catID = t.catID
        WHERE p.postID = {$postID}
        LIMIT 1
    ");
    $post = mysqli_fetch_assoc($res);
    if (!$post) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Beitrag nicht gefunden.</div>";
        return;
    }

    $roleIDs = forum_get_role_ids($userID);

    if (!forum_acl_can_any(
        $roleIDs,
        (int)$post['boardID'],
        (int)$post['catID'],
        (int)$post['threadID'],
        'delete'
    )) {
        echo "<div class='alert alert-danger'>Keine Berechtigung.</div>";
        return;
    }

    safe_query("
        UPDATE plugins_forum_posts
        SET is_deleted = 1
        WHERE postID = {$postID}
    ");

    // --------------------------------------------------
    // Prüfen: gibt es noch sichtbare Posts?
    // --------------------------------------------------
    $res = safe_query("
        SELECT COUNT(*) AS cnt
        FROM plugins_forum_posts
        WHERE threadID = {$post['threadID']}
          AND is_deleted = 0
    ");

    $remainingPosts = (int)(mysqli_fetch_assoc($res)['cnt'] ?? 0);

    if ($remainingPosts === 0) {

        safe_query("
            UPDATE plugins_forum_threads
            SET is_deleted = 1
            WHERE threadID = {$post['threadID']}
        ");

        // Redirect zur Kategorie (Thread existiert logisch nicht mehr)
        header("Location: " . SeoUrlHandler::convertToSeoUrl(
            "index.php?site=forum&action=category&id={$post['catID']}"
        ));
        exit;
    }


    header("Location: " . SeoUrlHandler::convertToSeoUrl(
        "index.php?site=forum&action=thread&id={$post['threadID']}"
    ));
    exit;
}

/* ============================================================
   ACTION: LOCK / UNLOCK
============================================================ */
function forum_action_lock($tpl, int $userID): void
{
    $threadID = (int)($_GET['threadID'] ?? 0);
    if ($threadID <= 0) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Thread-ID fehlt.</div>";
        return;
    }

    $res = safe_query("
        SELECT t.threadID, t.catID, c.boardID
        FROM plugins_forum_threads t
        JOIN plugins_forum_categories c ON c.catID = t.catID
        WHERE t.threadID = {$threadID}
        LIMIT 1
    ");
    $thread = mysqli_fetch_assoc($res);
    if (!$thread) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Thread nicht gefunden.</div>";
        return;
    }

    $roleIDs = forum_get_role_ids($userID);

    if (!forum_acl_can_any(
        $roleIDs,
        (int)$thread['boardID'],
        (int)$thread['catID'],
        $threadID,
        'mod'
    )) {
        echo "<div class='alert alert-danger'>Moderatorrechte erforderlich.</div>";
        return;
    }

    $lock = ($_GET['action'] === 'lock') ? 1 : 0;

    safe_query("
        UPDATE plugins_forum_threads
        SET is_locked = {$lock}
        WHERE threadID = {$threadID}
    ");

    header("Location: " . SeoUrlHandler::convertToSeoUrl(
        "index.php?site=forum&action=thread&id={$threadID}"
    ));
    exit;
}


/**********************************************************************
 * ACTION: QUOTE
 * → setzt Quote-Text in Session
 **********************************************************************/
function forum_action_quote($tpl, int $userID): void
{
    $postID   = (int)($_GET['postID'] ?? 0);
    $threadID = (int)($_GET['threadID'] ?? 0);

    if ($postID <= 0 || $threadID <= 0) {
        return;
    }

    $res = safe_query("
        SELECT p.content, u.username
        FROM plugins_forum_posts p
        LEFT JOIN users u ON u.userID = p.userID
        WHERE p.postID = {$postID}
        LIMIT 1
    ");

    if ($row = mysqli_fetch_assoc($res)) {
        $_SESSION['quote_text'] = '
            <blockquote class="blockquote nx-quote">
                <strong>' . htmlspecialchars($row['username']) . ' schrieb:</strong>
                <p>' . $row['content'] . '</p>
            </blockquote>
            <p>&nbsp;</p>
        ';
    }

    header("Location: " . SeoUrlHandler::convertToSeoUrl(
        "index.php?site=forum&action=quote_reply&threadID={$threadID}"
    ));
    exit;
}


/**********************************************************************
 * ACTION: QUOTE_REPLY
 * → zeigt Antwortformular mit Quote
 **********************************************************************/
function forum_action_quote_reply($tpl, int $userID): void
{
    $threadID = (int)($_GET['threadID'] ?? 0);
    if ($threadID <= 0) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Thread-ID fehlt.</div>";
        return;
    }

    // Thread + Board laden
    $res = safe_query("
        SELECT t.threadID, t.catID, c.boardID
        FROM plugins_forum_threads t
        JOIN plugins_forum_categories c ON c.catID = t.catID
        WHERE t.threadID = {$threadID}
        LIMIT 1
    ");

    $thread = mysqli_fetch_assoc($res);
    if (!$thread) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Thread nicht gefunden.</div>";
        return;
    }

    // ACL: reply prüfen (MULTI ROLE)
    $roleIDs = forum_get_role_ids($userID);

    if (!forum_acl_can_any(
        $roleIDs,
        (int)$thread['boardID'],
        (int)$thread['catID'],
        $threadID,
        'reply'
    )) {
        echo "<div class='alert alert-danger'>Keine Berechtigung.</div>";
        return;
    }

    $quoteText = $_SESSION['quote_text'] ?? '';
    unset($_SESSION['quote_text']);

    echo $tpl->loadTemplate(
        "forum_forms",
        "quote",
        [
            'threadID'    => $threadID,
            'form_action' => SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=reply&threadID={$threadID}"
            ),
            'quote_text'  => $quoteText
        ],
        "plugin"
    );
}



/**********************************************************************
 * ACTION: NEW THREAD
 * - Multi-Role
 * - ACL: post
 **********************************************************************/
function forum_action_new_thread($tpl, int $userID): void
{
    if ($userID <= 0) {
        echo "<div class='alert alert-danger'>Bitte anmelden.</div>";
        return;
    }

    $catID = (int)($_GET['catID'] ?? 0);
    if ($catID <= 0) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Kategorie fehlt.</div>";
        return;
    }

    // Kategorie + Board laden
    $res = safe_query("
        SELECT catID, boardID
        FROM plugins_forum_categories
        WHERE catID = {$catID}
        LIMIT 1
    ");

    $cat = mysqli_fetch_assoc($res);
    if (!$cat) {
        http_response_code(404);
        echo "<div class='alert alert-danger'>Kategorie nicht gefunden.</div>";
        return;
    }

    $boardID = (int)$cat['boardID'];

    // 🔑 Multi-Role ACL: POST-Recht
    $roleIDs = forum_get_role_ids($userID);

    if (!forum_acl_can_any(
        $roleIDs,
        $boardID,
        $catID,
        0,          // threadID = 0 (neuer Thread)
        'post'
    )) {
        echo "<div class='alert alert-danger'>Keine Berechtigung.</div>";
        return;
    }

    /* ======================================================
       POST → Thread speichern
    ====================================================== */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            echo "<div class='alert alert-warning'>Titel oder Inhalt fehlt.</div>";
            return;
        }

        // ============================
        // THREAD ANLEGEN (NEU)
        // ============================
        $titleDb = forum_db_escape($title);
        $contentDb = forum_db_escape($content);

        $threadInsert = safe_query("
            INSERT INTO plugins_forum_threads
            (catID, userID, title, created_at, updated_at)
            VALUES
            ({$catID}, {$userID}, '{$titleDb}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
        ");

        global $_database;
        $threadID = mysqli_insert_id($_database);
        if (!$threadInsert || $threadID <= 0) {
            echo "<div class='alert alert-danger'>Thread konnte nicht erstellt werden.</div>";
            return;
        }

        // ============================
        // ERSTER POST (NEU)
        // ❗ HIER KEINE BILD-LÖSCHLOGIK
        // ============================
        $postInsert = safe_query("
            INSERT INTO plugins_forum_posts
            (threadID, userID, content, created_at)
            VALUES
            ({$threadID}, {$userID}, '{$contentDb}', UNIX_TIMESTAMP())
        ");
        if (!$postInsert) {
            echo "<div class='alert alert-danger'>Startbeitrag konnte nicht gespeichert werden.</div>";
            return;
        }

        header("Location: " . SeoUrlHandler::convertToSeoUrl(
            "index.php?site=forum&action=thread&id={$threadID}"
        ));
        exit;
    }


    /* ======================================================
       GET → Formular anzeigen
    ====================================================== */
    echo $tpl->loadTemplate(
        "forum_forms",
        "new_thread",
        [
            'catID' => $catID
        ],
        "plugin"
    );
}

