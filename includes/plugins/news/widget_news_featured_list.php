<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;

use nexpell\SeoUrlHandler;

global $languageService;
require_once __DIR__ . '/builder_widget_helper.php';

$lang = $languageService->detectLanguage();
$languageService->readPluginModule('news');
// widget_news_featured_list.php
// Featured + List News Widget

$newsBuilderSettings = news_widget_builder_settings('widget_news_featured_list', isset($settings) && is_array($settings) ? $settings : []);
$limit = (int)$newsBuilderSettings['limit'];
$orderSql = news_widget_builder_order_sql((string)$newsBuilderSettings['order']);
$whereSql = news_widget_builder_where_sql($GLOBALS['_database'], $newsBuilderSettings);

echo '<link rel="stylesheet" href="/includes/plugins/news/css/news_featured_list.css">' . PHP_EOL;

$query = "
    SELECT a.id, a.title, a.content, a.updated_at, a.category_id, c.name AS category_name, c.image AS category_image
    FROM plugins_news a
    LEFT JOIN plugins_news_categories c ON a.category_id = c.id
    {$whereSql}
    {$orderSql}
    LIMIT " . intval($limit);

$res = safe_query($query);

if (!$res || mysqli_num_rows($res) === 0) {
    echo '<div class="news-featured-empty">Keine News verfügbar.</div>';
    return;
}

echo news_widget_builder_heading_html($newsBuilderSettings, 'News Featured', 'h5', 'mb-3');

// Erste News als Featured
$featured = mysqli_fetch_assoc($res);
$fid   = (int)$featured['id'];
$ftitle = htmlspecialchars($featured['title']);
$fplain = strip_tags($featured['content']);
$fexcerpt = news_widget_builder_excerpt($fplain, (int)$newsBuilderSettings['featured_excerpt_chars']);

$fcat_image = $featured['category_image'] ?? '';
$fimage = $fcat_image
    ? '/includes/plugins/news/images/news_categories/' . $fcat_image
    : '/includes/plugins/news/images/no-image.jpg';

// SEO-Link
$furl = SeoUrlHandler::buildPluginUrl('plugins_news', $fid, $lang);

$fcategory = htmlspecialchars($featured['category_name'] ?? 'Kategorie');
$ts = ($featured['updated_at'] ?? '');
$ts = is_numeric($ts) ? (int)$ts : (strtotime($ts) ?: time());
$fdate = date("d.m.Y", $ts);

echo '<div class="news-featured-list">';

// Featured Block
echo '<div class="featured-news border">';
echo '    <img src="' . htmlspecialchars($fimage) . '" alt="' . $fcategory . '">';
echo '  <a href="' . htmlspecialchars($furl) . '" class="featured-thumb">';
if (!empty($newsBuilderSettings['show_category'])) {
    echo '    <span class="featured-badge">' . $fcategory . '</span>';
}
echo '  </a>';
echo '  <div class="featured-body">';
echo '    <h2 class="featured-title"><a href="' . htmlspecialchars($furl) . '">' . $ftitle . '</a></h2>';
echo '    <p class="featured-excerpt">' . htmlspecialchars($fexcerpt) . '</p>';
if (!empty($newsBuilderSettings['show_date'])) {
    echo '    <div class="featured-meta"><small>' . $fdate . '</small></div>';
}
echo '  </div>';
echo '</div>';

// Restliche News als Grid/List
if (mysqli_num_rows($res) > 0) {
    echo '<div class="news-list">';
    while ($row = mysqli_fetch_assoc($res)) {
        $id = (int)$row['id'];
        $title = htmlspecialchars($row['title']);
        $plain = strip_tags($row['content']);
        $excerpt = news_widget_builder_excerpt($plain, (int)$newsBuilderSettings['list_excerpt_chars']);

        $cat_image = $row['category_image'] ?? '';
        $image = $cat_image
            ? '/includes/plugins/news/images/news_categories/' . $cat_image
            : '/includes/plugins/news/images/no-image.jpg';

        // SEO-Link zur News
        $url = SeoUrlHandler::buildPluginUrl('plugins_news', $id, $lang);
        $category = htmlspecialchars($row['category_name'] ?? 'Kategorie');
        $ts = strtotime($row['updated_at'] ?? '') ?: time();
        $datum = date("d.m.Y", $ts);

        echo '<article class="news-item border">';
        echo '    <img src="' . htmlspecialchars($image) . '" alt="' . $category . '">';
        echo '  <div class="news-body">';
        echo '    <h5 class="news-title"><a href="' . htmlspecialchars($url) . '">' . $title . '</a></h5>';
        echo '    <p class="news-excerpt">' . htmlspecialchars($excerpt) . '</p>';
        $metaParts = [];
        if (!empty($newsBuilderSettings['show_date'])) {
            $metaParts[] = $datum;
        }
        if (!empty($newsBuilderSettings['show_category'])) {
            $metaParts[] = $category;
        }
        if (!empty($metaParts)) {
            echo '    <div class="news-meta"><small>' . htmlspecialchars(implode(' | ', $metaParts), ENT_QUOTES, 'UTF-8') . '</small></div>';
        }
        echo '  </div>';
        echo '</article>';
    }
    echo '</div>';
}

echo '</div>';
 // .news-featured-list
