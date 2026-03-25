<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;

global $languageService;
if (method_exists($languageService, 'readPluginModule')) {
    $languageService->readPluginModule('carousel');
}
$currentLang = strtolower((string)$languageService->detectLanguage());
if (!function_exists('carousel_lang_key')) {
    function carousel_lang_key(int $slideID, string $field): string
    {
        return 'carousel_' . $slideID . '_' . $field;
    }
}
if (!function_exists('carousel_get_text')) {
    function carousel_get_text(int $slideID, string $field, string $lang, string $fallbackLang = 'de'): string
    {
        $allowedFields = ['title', 'subtitle', 'description'];
        if (!in_array($field, $allowedFields, true)) {
            return '';
        }

        $key = escape(carousel_lang_key($slideID, $field));
        $langEsc = escape(strtolower($lang));
        $fallbackEsc = escape(strtolower($fallbackLang));

        $res = safe_query("SELECT content FROM plugins_carousel_lang WHERE content_key = '{$key}' AND language = '{$langEsc}' LIMIT 1");
        if ($res && ($row = mysqli_fetch_assoc($res)) && ($row['content'] ?? '') !== '') {
            return (string)$row['content'];
        }

        $resFallback = safe_query("SELECT content FROM plugins_carousel_lang WHERE content_key = '{$key}' AND language = '{$fallbackEsc}' LIMIT 1");
        if ($resFallback && ($rowFallback = mysqli_fetch_assoc($resFallback)) && ($rowFallback['content'] ?? '') !== '') {
            return (string)$rowFallback['content'];
        }

        $resAny = safe_query("
            SELECT content
            FROM plugins_carousel_lang
            WHERE content_key REGEXP '^carousel_[0-9]+_{$field}$'
              AND language = '{$langEsc}'
            ORDER BY CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(content_key, '_', 2), '_', -1) AS UNSIGNED) ASC
            LIMIT 1
        ");
        if ($resAny && ($rowAny = mysqli_fetch_assoc($resAny)) && ($rowAny['content'] ?? '') !== '') {
            return (string)$rowAny['content'];
        }

        $resAnyFallback = safe_query("
            SELECT content
            FROM plugins_carousel_lang
            WHERE content_key REGEXP '^carousel_[0-9]+_{$field}$'
              AND language = '{$fallbackEsc}'
            ORDER BY CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(content_key, '_', 2), '_', -1) AS UNSIGNED) ASC
            LIMIT 1
        ");
        if ($resAnyFallback && ($rowAnyFallback = mysqli_fetch_assoc($resAnyFallback)) && ($rowAnyFallback['content'] ?? '') !== '') {
            return (string)$rowAnyFallback['content'];
        }

        return '';
    }
}

$tpl = new Template();
$filepath = "../includes/plugins/carousel/images/";

// Lade Einstellungen (z.B. Carousel Höhe)
$ds = mysqli_fetch_array(safe_query("SELECT * FROM plugins_carousel_settings")); 
$carousel_height = (int)($ds['carousel_height']); // Fallback 75 vh

echo '
<header id="hero" style="height: ' . $carousel_height . 'vh;">
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
';

// Datensätze laden
$carousel = safe_query("SELECT * FROM plugins_carousel WHERE type = 'carousel' AND visible = 1 ORDER BY sort");

// Prüfen, ob überhaupt Slides da sind
if (mysqli_num_rows($carousel) > 0) {

    // Carousel Indicators
    echo '<div class="carousel-indicators" id="hero-carousel-indicators">';
    $indicatorIndex = 0;
    while ($row = mysqli_fetch_array($carousel)) {
        echo '<button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="' . $indicatorIndex . '"'
            . ($indicatorIndex === 0 ? ' class="active" aria-current="true"' : '')
            . ' aria-label="Slide ' . ($indicatorIndex + 1) . '"></button>';
        $indicatorIndex++;
    }
    echo '</div>';

    mysqli_data_seek($carousel, 0); // Cursor zurücksetzen

    // Carousel Items
    echo '<div class="carousel-inner" role="listbox">';

    $slideIndex = 0;
    while ($db = mysqli_fetch_array($carousel)) {
        $slideIndex++;
        $interval_ms = 10000; // Standard Intervall

        $media_file = $filepath . $db['media_file'];
        $media_type = $db['media_type']; // "image" oder "video"
        $link_url = $db['link'];

        $slideID = (int)($db['id'] ?? 0);
        $title = carousel_get_text($slideID, 'title', $currentLang);
        $subtitle = carousel_get_text($slideID, 'subtitle', $currentLang);
        $description = carousel_get_text($slideID, 'description', $currentLang);

        // Media HTML bauen
        $common_classes = 'pic img-fluid w-100';
        $common_style = 'max-height:' . $carousel_height . 'vh; object-fit:cover;';

        if ($media_type === 'image') {
            $media_html = '<img src="' . htmlspecialchars($media_file) . '" alt="' . htmlspecialchars($title) . '" class="' . $common_classes . '" style="' . $common_style . '">';
        } elseif ($media_type === 'video') {
            $media_html = '<video class="' . $common_classes . '" style="' . $common_style . '; margin-top: 15px;" autoplay muted loop playsinline>
                <source src="' . htmlspecialchars($media_file) . '" type="video/mp4">
                ' . htmlspecialchars($languageService->get('video_not_supported')) . '
            </video>';
        }

        // Link Button (falls Link vorhanden)
        $link = '';
        if (!empty($link_url)) {
            $link = '<a href="' . htmlspecialchars($link_url) . '" class="btn btn-primary scrollto">' . $languageService->get('read_more') . '</a>';
        }

        // Template-Replacements
        $replaces = [
            'carouselID'       => $db['id'],
            'carousel_pic'     => $media_html,
            'title'            => $title,
            'subtitle'         => $subtitle,
            'link'             => $link,
            'description'      => $description
        ];

        echo '<div class="carousel-item ' . ($slideIndex === 1 ? 'active' : '') . '" data-bs-interval="' . $interval_ms . '">';
        echo $tpl->loadTemplate("carousel_header", "content", $replaces, 'plugin');
        echo '</div>';
    }

    echo '</div>'; // .carousel-inner

    // Controls
    echo '
    <a class="carousel-control-prev" href="#heroCarousel" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </a>
    <a class="carousel-control-next" href="#heroCarousel" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </a>
    ';

} else {
    echo '<p>' . $languageService->get('no_entries') . '</p>';
}

echo '
  </div>
</header>';
?>
