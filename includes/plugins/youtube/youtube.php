<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\SeoUrlHandler;

global $languageService, $_database, $tpl;

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style'] ?? '', ENT_QUOTES, 'UTF-8');

$data_array = [
    'class' => $class,
    'title' => $languageService->get('title'),
    'subtitle' => 'youtube'
];

echo $tpl->loadTemplate("youtube", "head", $data_array, 'plugin');

$settings = [];
$result = $_database->query("SELECT setting_key, setting_value FROM plugins_youtube_settings WHERE plugin_name='youtube'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

$defaultVideoId = $settings['default_video_id'] ?? 'D_x8ms9nGQw';
$videosPerPageFirst = max(1, (int)($settings['videos_per_page'] ?? 4));
$videosPerPageOther = max(1, (int)($settings['videos_per_page_other'] ?? 6));
$displayMode = $settings['display_mode'] ?? 'grid';
$firstFullWidth = (int)($settings['first_full_width'] ?? 1);

$allVideos = [];
$firstVideo = null;
$result = $_database->query("
    SELECT setting_value, is_first
    FROM plugins_youtube
    WHERE plugin_name='youtube' AND setting_key LIKE 'video_%'
    ORDER BY id DESC
");

if ($result) {
    $tempVideos = [];
    while ($row = $result->fetch_assoc()) {
        $videoId = trim((string)($row['setting_value'] ?? ''));
        if ($videoId === '') {
            continue;
        }

        if ((int)$row['is_first'] === 1) {
            $firstVideo = $videoId;
        }
        $tempVideos[] = $videoId;
    }

    if ($firstVideo !== null) {
        $tempVideos = array_values(array_diff($tempVideos, [$firstVideo]));
        array_unshift($tempVideos, $firstVideo);
    }

    $allVideos = array_values($tempVideos);
}

if (empty($allVideos)) {
    $allVideos[] = $defaultVideoId;
    $firstVideo = $defaultVideoId;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$videosToDisplay = [];
$fullWidthVideo = null;

if ($page === 1) {
    if ($displayMode === 'grid' && $firstFullWidth && $firstVideo !== null) {
        $fullWidthVideo = $allVideos[0] ?? null;
        $videosToDisplay = array_slice($allVideos, 1, max(0, $videosPerPageFirst - 1));
    } else {
        $videosToDisplay = array_slice($allVideos, 0, $videosPerPageFirst);
    }
} else {
    $offset = $videosPerPageFirst + (($page - 2) * $videosPerPageOther);
    $videosToDisplay = array_slice($allVideos, $offset, $videosPerPageOther);
}

include __DIR__ . '/youtube-content.php';
