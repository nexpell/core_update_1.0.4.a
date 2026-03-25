<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow', true);

/* ========= DB DIREKT ========= */
require_once __DIR__ . '/../../../../system/config.inc.php';

// Debug
if (!defined('NXB_DEBUG')) define('NXB_DEBUG', true);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// === DB-Verbindung ===
global $_database;
$_database = $_database ?? new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($_database->connect_errno) {
    http_response_code(500);
    echo 'DB error: ' . $_database->connect_error;
    exit;
}
$_database->set_charset('utf8mb4');

if (!$_database instanceof mysqli) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'DB not initialized']);
    exit;
}

/* ========= METHOD ========= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(404);
    echo json_encode(['success'=>false,'error'=>'Method']);
    exit;
}

/* ========= AUTH ========= */
$userID = (int)($_SESSION['userID']);
if ($userID <= 0) {
    echo json_encode(['success'=>false,'error'=>'Login']);
    exit;
}

/* ========= INPUT ========= */
$postID = (int)($_POST['postID']);
if ($postID <= 0) {
    echo json_encode(['success'=>false,'error'=>'PostID']);
    exit;
}

/* ========= POST EXISTIERT ========= */
$stmt = $_database->prepare(
    "SELECT userID FROM plugins_forum_posts WHERE postID = ? LIMIT 1"
);
$stmt->bind_param('i', $postID);
$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();
$stmt->close();

if (!$post) {
    echo json_encode(['success'=>false,'error'=>'Post not found']);
    exit;
}

/* ========= EIGENER POST ========= */
if ((int)$post['userID'] === $userID) {
    echo json_encode(['success'=>false,'error'=>'Own post']);
    exit;
}

/* ========= TOGGLE LIKE ========= */
$stmt = $_database->prepare(
    "SELECT 1 FROM plugins_forum_post_likes WHERE postID=? AND userID=?"
);
$stmt->bind_param('ii', $postID, $userID);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($exists) {
    // UNLIKE
    $stmt = $_database->prepare(
        "DELETE FROM plugins_forum_post_likes WHERE postID=? AND userID=?"
    );
    $stmt->bind_param('ii', $postID, $userID);
    $stmt->execute();
    $stmt->close();
} else {
    // LIKE
    $stmt = $_database->prepare(
        "INSERT INTO plugins_forum_post_likes (postID, userID, created_at)
         VALUES (?, ?, NOW())"
    );
    $stmt->bind_param('ii', $postID, $userID);
    $stmt->execute();
    $stmt->close();
}

/* ========= COUNT ========= */
$stmt = $_database->prepare(
    "SELECT COUNT(*) FROM plugins_forum_post_likes WHERE postID=?"
);
$stmt->bind_param('i', $postID);
$stmt->execute();
$stmt->bind_result($likes);
$stmt->fetch();
$stmt->close();

/* ========= RESPONSE ========= */
echo json_encode([
    'success' => true,
    'likes'   => (int)$likes,
    'liked'   => !$exists
]);
exit;
