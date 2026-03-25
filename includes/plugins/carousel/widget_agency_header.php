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

$ds = mysqli_fetch_array(safe_query("SELECT * FROM plugins_carousel_settings"));
$agency_height = (int)$ds['agency_height'];

$result = safe_query("SELECT * FROM plugins_carousel WHERE type = 'agency' AND visible = 1 ORDER BY sort ASC");

if (mysqli_num_rows($result)) {
    while ($db = mysqli_fetch_array($result)) {
        $media_file = $filepath . $db['media_file'];
        $media_type = $db['media_type'];
        $slideID = (int)($db['id'] ?? 0);
        $title = carousel_get_text($slideID, 'title', $currentLang);
        $subtitle = carousel_get_text($slideID, 'subtitle', $currentLang);
        $description = carousel_get_text($slideID, 'description', $currentLang);
        $link_url = $db['link'];

        if ($title === '' && isset($db['title'])) {
            $title = (string)$db['title'];
        }
        if ($subtitle === '' && isset($db['subtitle'])) {
            $subtitle = (string)$db['subtitle'];
        }
        if ($description === '' && isset($db['description'])) {
            $description = (string)$db['description'];
        }

        $link = '';
        if (!empty($link_url)) {
            if (str_starts_with($link_url, 'https://')) {
                $link = '<a data-aos="fade-up" data-aos-delay="200" href="' . htmlspecialchars($link_url) . '" class="btn-get-started scrollto"><i class="bi bi-chevron-double-down"></i></a>';
            } else {
                $link = '<a data-aos="fade-up" data-aos-delay="200" href="' . htmlspecialchars($link_url) . '" class="btn-get-started scrollto">' . $languageService->get('read_more') . '</a>';
            }
        }

        $media_html = '';
        if ($media_type === 'image') {
            $media_html = '<img src="' . $media_file . '" alt="' . htmlspecialchars($title) . '" class="img-fluid w-100" style="max-height:' . $agency_height . 'vh; object-fit:cover;">';
        } elseif ($media_type === 'video') {
            $media_html = '<video class="img-fluid w-100" style="max-height:' . $agency_height . 'vh; object-fit:cover;" autoplay muted loop playsinline>
                <source src="' . $media_file . '" type="video/mp4">
                ' . $languageService->get('video_not_supported') . '
            </video>';
        }

        $replaces = [
            'agency_pic'     => $media_html,
            'agency_height'  => $agency_height,
            'title'          => $title,
            'subtitle'       => $subtitle,
            'link'           => $link,
            'description'    => $description
        ];

        echo $tpl->loadTemplate("agency_header", "content", $replaces, 'plugin');
    }
} else {
    echo '<div class="alert alert-danger" role="alert">' . $languageService->get('no_header') . '</div>';
}
?>
