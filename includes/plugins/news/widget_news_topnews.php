<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

global $languageService,$_database;
require_once __DIR__ . '/builder_widget_helper.php';

$lang = $languageService->detectLanguage();
$languageService->readPluginModule('news');
$newsBuilderSettings = news_widget_builder_settings('widget_news_topnews', isset($settings) && is_array($settings) ? $settings : []);
$limit = (int)$newsBuilderSettings['limit'];
$orderSql = news_widget_builder_order_sql((string)$newsBuilderSettings['order']);
$whereSql = news_widget_builder_where_sql($_database, $newsBuilderSettings);

$topNewsResult = $_database->query("
    SELECT a.id, a.title, a.updated_at, a.sort_order, c.name as category_name, c.image as category_image
    FROM plugins_news a
    LEFT JOIN plugins_news_categories c ON a.category_id = c.id
    {$whereSql}
    {$orderSql}
    LIMIT " . intval($limit) . "
");

// Hilfsfunktion: Text kürzen
if (!function_exists('shortenText')) {
    function shortenText($text, $maxLength = 100) {
        if (strlen($text) > $maxLength) {
            return substr($text, 0, $maxLength) . '…';
        }
        return $text;
    }
}

if ($topNewsResult && $topNewsResult->num_rows > 0) {

    echo '<div class="top-news-widget">';
    echo news_widget_builder_heading_html($newsBuilderSettings, 'Top News', 'h5', 'mb-3');
    echo '<div class="list-group">';

    while ($news = $topNewsResult->fetch_assoc()) {
        $image = !empty($news['category_image']) 
            ? "/includes/plugins/news/images/news_categories/{$news['category_image']}" 
            : "/includes/plugins/news/images/no-image.jpg";

        $ts = strtotime($news['updated_at']);
        $day = date('d', $ts);
        $month = date('F', $ts);
        $year = date('Y', $ts);
        $title = htmlspecialchars($news['title']);
        $category_name = htmlspecialchars($news['category_name']);
        // News-Link
        $url_watch_seo = SeoUrlHandler::buildPluginUrl('plugins_news', $news['id'], $lang);

        echo '<a href="' . $url_watch_seo . '" class="list-group-item list-group-item-action d-flex align-items-center">';
        echo '<img src="' . $image . '" alt="' . $category_name . '" class="me-3 rounded" style="width:160px; height:auto; object-fit:cover;">';
        echo '<div class="flex-grow-1">';
        echo '<div class="d-flex justify-content-between align-items-start">';
        $metaText = !empty($newsBuilderSettings['show_date']) ? $day . ' ' . $languageService->get(strtolower($month)) . ' ' . $year : '';
        echo '<div><strong>' . $title . '</strong><br><small class="text-muted">' . htmlspecialchars($metaText, ENT_QUOTES, 'UTF-8') . '</small></div>';
        if (!empty($newsBuilderSettings['show_category'])) {
            echo '<span class="badge bg-primary">' . $category_name . '</span>';
        }
        echo '</div></div></a>';
    }

    echo '</div></div>';
}
?>
