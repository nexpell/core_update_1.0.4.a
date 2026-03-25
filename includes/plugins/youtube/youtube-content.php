<?php

use nexpell\SeoUrlHandler;

global $languageService;

$fullWidthVideo = $fullWidthVideo ?? ($_GET['fullWidthVideoId'] ?? null);
$videosToDisplay = $videosToDisplay ?? (isset($_GET['otherVideoIds']) ? explode(',', (string)$_GET['otherVideoIds']) : []);
$displayMode = $displayMode ?? ($_GET['displayMode'] ?? 'grid');
$page = $page ?? max(1, (int)($_GET['page'] ?? 1));
$totalVideos = $totalVideos ?? (isset($allVideos) ? count($allVideos) : (int)($_GET['totalVideos'] ?? 0));
$videosPerPageFirst = $videosPerPageFirst ?? (int)($_GET['videosPerPageFirst'] ?? 0);
$videosPerPageOther = $videosPerPageOther ?? (int)($_GET['videosPerPageOther'] ?? 0);
$defaultVideoId = $defaultVideoId ?? 'D_x8ms9nGQw';

if (!function_exists('youtube_is_video_valid')) {
    function youtube_is_video_valid($videoId): bool
    {
        return is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) === 1;
    }
}

$videosAfterFirst = max(0, $totalVideos - $videosPerPageFirst);
$totalPagesOther = $videosPerPageOther > 0 ? (int)ceil($videosAfterFirst / $videosPerPageOther) : 0;
$totalPages = ($totalVideos > $videosPerPageFirst) ? 1 + $totalPagesOther : 1;
?>

<style>
.youtube-video-full { width: 100%; margin-bottom: 1rem; }
.youtube-video-grid { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }
.youtube-video-grid-item { flex: 1 1 calc(33.333% - 1rem); }
.youtube-video-list-item { width: 100%; margin-bottom: 1rem; }
</style>

<div class="youtube-widget-container">
    <?php if ($fullWidthVideo): ?>
        <div class="youtube-video-full">
            <iframe width="100%" height="315"
                src="https://www.youtube.com/embed/<?=
                    htmlspecialchars(
                        youtube_is_video_valid($fullWidthVideo) ? $fullWidthVideo : $defaultVideoId,
                        ENT_QUOTES,
                        'UTF-8'
                    )
                ?>"
                frameborder="0" allowfullscreen>
            </iframe>
        </div>
    <?php endif; ?>

    <div class="<?= $displayMode === 'grid' ? 'youtube-video-grid' : 'youtube-video-list' ?>">
        <?php foreach ($videosToDisplay as $videoId): ?>
            <div class="<?= $displayMode === 'grid' ? 'youtube-video-grid-item' : 'youtube-video-list-item' ?>">
                <iframe width="100%" height="215"
                    src="https://www.youtube.com/embed/<?=
                        htmlspecialchars(
                            youtube_is_video_valid($videoId) ? $videoId : $defaultVideoId,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ?>"
                    frameborder="0" allowfullscreen>
                </iframe>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($totalPages > 1): ?>
    <?php
    $baseUrl = SeoUrlHandler::convertToSeoUrl('index.php?site=youtube');
    $baseUrl = rtrim($baseUrl, '/') . '/';
    echo $tpl->renderPagination($baseUrl, (int)$page, (int)$totalPages);
    ?>
<?php endif; ?>
