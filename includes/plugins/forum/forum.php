<?php
/**********************************************************************
 * NEXPELL — FORUM ROUTER (FINAL & CLEAN)
 **********************************************************************/

declare(strict_types=1);

// --------------------------------------------------
// SYSTEM BOOTSTRAP (PFLICHT)
// --------------------------------------------------
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 3));
}

require_once BASE_PATH . '/system/config.inc.php';

// --------------------------------------------------
// DB GLOBALISIEREN
// --------------------------------------------------
global $_database;


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('IS_FORUM', true);

require_once __DIR__ . '/system/ForumACL.php';
require_once __DIR__ . '/system/ForumContext.php';

use nexpell\LanguageService;
use nexpell\forum\ForumContext;

$forumContext = ForumContext::fromRequest($_database);

$boardID    = $forumContext->boardID;
$categoryID = $forumContext->categoryID;
$threadID   = $forumContext->threadID;

global $_database, $languageService;

// --------------------------------------------------
// USER / ROLE KONTEXT – ZENTRAL
// --------------------------------------------------
$userID = (int)($_SESSION['userID'] ?? 0);

if ($userID === 0) {
    $roleIDs = [11]; // Gast
} else {
    $roleIDs = $_SESSION['roles'] ?? [];

    if (empty($roleIDs)) {
        $roleIDs = [12]; // Fallback: User
    }
}


// ======================================================
// ROLLENNAME ERMITTELN (für Debug-Anzeige)
// ======================================================
$aclRoleName = 'Unbekannt';
$roleList    = [];

if (!empty($roleIDs)) {

    $ids = implode(',', array_map('intval', $roleIDs));

    $res = safe_query("
        SELECT roleID, role_name
        FROM user_roles
        WHERE roleID IN ($ids)
        ORDER BY roleID ASC
    ");

    while ($r = mysqli_fetch_assoc($res)) {
        $roleList[] = [
            'id'   => (int)$r['roleID'],
            'name' => $r['role_name']
        ];
    }

    if (!empty($roleList)) {
        $aclRoleName = implode(', ', array_column($roleList, 'name'));
    }
}

#echo '<pre>Rollen (Namen): ' . htmlspecialchars($aclRoleName) . '</pre>';



// ======================================================
// FORUM ACL DEBUG – GLOBAL STATUS (DB)
// ======================================================
$res = safe_query("SELECT forum_acl_debug FROM settings LIMIT 1");
$cfg = mysqli_fetch_assoc($res);
$ACL_DEBUG = ((int)($cfg['forum_acl_debug'] ?? 0) === 1);


echo'<div class="mb-3"></div>';


// ======================================================
// DEBUG BLOCK (nur Anzeige)
// ======================================================
if ($ACL_DEBUG) {
    require_once __DIR__ . '/system/ForumDebugBlock.php';
}

$forumJsPath = __DIR__ . '/js/forum.js';
if (file_exists($forumJsPath)) {
    $forumJsVersion = (string)filemtime($forumJsPath);
    $forumCsrf = (string)($_SESSION['csrf'] ?? '');
    echo '<script>window.NX_FORUM_CSRF='
        . json_encode($forumCsrf, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)
        . ';</script>';
    echo '<script src="/includes/plugins/forum/js/forum.js?v='
        . htmlspecialchars($forumJsVersion, ENT_QUOTES, 'UTF-8')
        . '"></script>';
}

// MODULE LADEN
//require_once __DIR__ . '/forum_helpers.php';
require_once __DIR__ . '/forum_boards.php';
require_once __DIR__ . '/forum_category.php';
require_once __DIR__ . '/forum_thread.php';
require_once __DIR__ . '/forum_actions.php';

$action = $_GET['action'] ?? 'board';
$userID = $_SESSION['userID'] ?? 0;


// ======================================================================
// 0) ACTION HANDLING (POST only)
// ======================================================================
switch ($action) {

    case 'reply':
        forum_action_reply($tpl, $userID);
        exit;

    case 'new_thread':
        forum_action_new_thread($tpl, $userID);
        break;

    case 'edit_post':
        forum_action_edit_post($tpl, $userID); 
        break;

    case 'delete_post':
        forum_action_delete_post($tpl, $userID);
        exit;

    case 'lock':
    case 'unlock':
        forum_action_lock($tpl, $userID);
        exit;

    case 'quote':
        forum_action_quote($tpl, $userID);
        exit;

    case 'quote_reply':
        forum_action_quote_reply($tpl, $userID);
        break;
}


// ======================================================================
// 1) DISPLAY / RENDERING
// ======================================================================
switch ($action) {

    case 'edit_post':

        // Post laden
        $postID = intval($_GET['postID'] ?? 0);

        $res = safe_query("
            SELECT p.*
            FROM plugins_forum_posts p
            WHERE p.postID = $postID
            LIMIT 1
        ");

        $post = mysqli_fetch_assoc($res);

        /*echo $tpl->loadTemplate("forum_forms", "edit_post", [
            "content"     => $post['content'] ?? "",
            "form_action" => "index.php?site=forum&action=edit_post&postID={$postID}"
        ], "plugin");*/

        break;


    case 'quote_reply':
        //$threadID = intval($_GET['threadID'] ?? 0);
        //forum_render_thread($tpl, $languageService, $userID, $threadID);
        break;

    case 'category':
        $catID = intval($_GET['id'] ?? 0);
        forum_render_category($tpl, $languageService, $userID, $catID);
        break;

    case 'thread':
        $threadID = intval($_GET['id'] ?? 0);
        forum_render_thread($tpl, $languageService, $userID, $threadID);
        break;

    case 'board':
        forum_render_boards($tpl, $languageService, $userID, (int)($_GET['id'] ?? 0));
        break;
}

