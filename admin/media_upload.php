<?php
// admin/media_upload.php – zentraler Bild-Upload (z.B. für Content/Builder)
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/system/config.inc.php';
require_once BASE_PATH . '/system/settings.php';
require_once BASE_PATH . '/system/classes/AccessControl.php';

$userID = (int)($_SESSION['userID'] ?? 0);

header('Content-Type: application/json; charset=utf-8');

use nexpell\AccessControl;

AccessControl::checkAdminAccess('ac_plugin_widgets_setting', true);

$csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
$csrfPost  = trim((string)($_POST['csrf'] ?? ''));
$token     = $csrfHeader ?: $csrfPost;
$sess      = $_SESSION['csrf_token'] ?? '';

if (!$token || !$sess || !hash_equals($sess, $token)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'CSRF invalid'], JSON_UNESCAPED_UNICODE);
    exit;
}

$field = 'file';
if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
    $err = $_FILES[$field]['error'] ?? 'missing';
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'upload failed', 'code' => $err], JSON_UNESCAPED_UNICODE);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$allowedExt   = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$maxSize      = 4 * 1024 * 1024; // 4 MB

$tmpPath = $_FILES[$field]['tmp_name'];
$mime   = mime_content_type($tmpPath);
$size   = (int)($_FILES[$field]['size'] ?? 0);
$name   = $_FILES[$field]['name'];

if (!in_array($mime, $allowedTypes, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid type'], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($size > $maxSize || $size < 1) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'file too big'], JSON_UNESCAPED_UNICODE);
    exit;
}

$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid extension'], JSON_UNESCAPED_UNICODE);
    exit;
}

$uploadDir = BASE_PATH . '/images/content';
if (!is_dir($uploadDir)) {
    if (!@mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'upload dir missing'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$filename = preg_replace('/[^a-z0-9._-]/', '', uniqid('img_', true)) . '.' . $ext;
$target   = $uploadDir . '/' . $filename;

if (!move_uploaded_file($tmpPath, $target)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'move failed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$url = '/images/content/' . $filename;
echo json_encode(['ok' => true, 'url' => $url], JSON_UNESCAPED_UNICODE);
