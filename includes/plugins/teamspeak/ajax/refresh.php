<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../system/config.inc.php';

$_database = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($_database->connect_error) {
    exit;
}

$serverID = (int)($_GET['id'] ?? 0);
$mode     = $_GET['mode'] ?? 'content'; // content | widget

if ($serverID <= 0) {
    exit;
}

/* =====================================================
   SERVER LADEN
===================================================== */
$stmt = $_database->prepare("
    SELECT *
    FROM plugins_teamspeak
    WHERE enabled = 1
      AND id = ?
    LIMIT 1
");
$stmt->bind_param('i', $serverID);
$stmt->execute();
$res = $stmt->get_result();
$srv = $res->fetch_assoc();

if (!$srv) {
    exit;
}

/* =====================================================
   CORE KLASSEN
===================================================== */
require_once __DIR__ . '/../system/TeamSpeakService.php';
require_once __DIR__ . '/../system/TeamSpeakTreeBuilder.php';

/* Renderer bewusst getrennt */
require_once __DIR__ . '/../system/TeamSpeakHtmlRenderer.php';
require_once __DIR__ . '/../system/TeamSpeakHtmlRendererWidget.php';

/* =====================================================
   DATEN HOLEN
===================================================== */
$service = new TeamSpeakService($srv);
$data    = $service->getServerData() ?? [];

if (empty($data['online'])) {
    echo '<div class="alert alert-warning mb-0">Server offline</div>';
    exit;
}

/* =====================================================
   TREE BAUEN (EINMAL!)
===================================================== */
$tree = TeamSpeakTreeBuilder::build(
    $data['channels'] ?? [],
    $data['clients']  ?? []
);

/* =====================================================
   RENDERER WÄHLEN
===================================================== */
if ($mode === 'widget') {
    echo TeamSpeakHtmlRendererWidget::render($tree);
} else {
    echo TeamSpeakHtmlRenderer::render($tree);
}
