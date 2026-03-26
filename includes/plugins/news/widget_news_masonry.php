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
// widget_masonry_news.php
// Minimaler Masonry-Widget-Renderer für Nexpell

// Konfiguration (kann später aus Plugin-Einstellungen kommen)
$newsBuilderSettings = news_widget_builder_settings('widget_news_masonry', isset($settings) && is_array($settings) ? $settings : []);
$limit = (int)$newsBuilderSettings['limit'];
$columns_desktop = (int)$newsBuilderSettings['columns_desktop'];
$columns_tablet  = (int)$newsBuilderSettings['columns_tablet'];
$columns_mobile  = (int)$newsBuilderSettings['columns_mobile'];
$excerpt_chars   = (int)$newsBuilderSettings['excerpt_chars'];
$orderSql = news_widget_builder_order_sql((string)$newsBuilderSettings['order']);
$whereSql = news_widget_builder_where_sql($GLOBALS['_database'], $newsBuilderSettings);

// Lade CSS/JS (wenn dein System loadWidgetHeadAssets unterstützt, passe Namen an)
echo '<link rel="stylesheet" href="/includes/plugins/news/css/news_masonry.css">' . PHP_EOL;
echo '<script defer src="/includes/plugins/news/js/masonry_news.js"></script>' . PHP_EOL;

// Query: Aktive News (anpassen nach gewünschter Sortierung)
$query = "
    SELECT a.id, a.title, a.content, a.updated_at, a.category_id, c.name AS category_name, c.image AS category_image
    FROM plugins_news a
    LEFT JOIN plugins_news_categories c ON a.category_id = c.id
    {$whereSql}
    {$orderSql}
    LIMIT " . intval($limit);

$res = safe_query($query);

if (!$res || mysqli_num_rows($res) === 0) {
    echo '<div class="masonry-empty">Keine News verfügbar.</div>';
    return;
}

// Wrapper mit data-Attributen für responsive Spalten
echo news_widget_builder_heading_html($newsBuilderSettings, 'News Masonry', 'h5', 'mb-3');
echo '<div class="masonry-wrapper"'
   . ' data-columns-desktop="' . (int)$columns_desktop . '"'
   . ' data-columns-tablet="' . (int)$columns_tablet . '"'
   . ' data-columns-mobile="' . (int)$columns_mobile . '">';

echo '<div class="masonry-grid">';

while ($row = mysqli_fetch_assoc($res)) {
    $id = (int)$row['id'];
    $title = htmlspecialchars($row['title']);
    $plain = strip_tags($row['content']);
    $excerpt = news_widget_builder_excerpt($plain, $excerpt_chars);

    $cat_image = $row['category_image'] ?? '';
    $image = $cat_image
        ? '/includes/plugins/news/images/news_categories/' . $cat_image
        : '/includes/plugins/news/images/no-image.jpg';

    // SEO-Link zur News
    $url = SeoUrlHandler::buildPluginUrl('plugins_news', $id, $lang);
    $category = htmlspecialchars($row['category_name'] ?? 'Kategorie');
    $dateText = date('d.m.Y', strtotime((string)$row['updated_at']));

    $maxTitleLength = 50;
    $shortTitle = mb_strlen($title) > $maxTitleLength 
        ? mb_substr($title, 0, $maxTitleLength) . '...' 
        : $title;

    echo '<article class="masonry-item border">';
    echo '  <div class="masonry-thumb">';
    echo '    <img src="' . htmlspecialchars($image) . '" alt="' . $category . '">';
    echo '  </div>';
    echo '  <div class="masonry-body">';
    echo '    <h4 class="masonry-title"><a href="' . htmlspecialchars($url) . '">' . $shortTitle . '</a></h4>';
    echo '    <p class="masonry-excerpt">' . htmlspecialchars($excerpt) . '</p>';
    echo '    <div class="masonry-meta">';
    if (!empty($newsBuilderSettings['show_date'])) {
        echo '      <span class="masonry-date">' . htmlspecialchars($dateText, ENT_QUOTES, 'UTF-8') . '</span>';
    }
    if (!empty($newsBuilderSettings['show_category'])) {
        echo '      <span class="masonry-cat">' . $category . '</span>';
    }
    echo '      <a class="masonry-readmore" href="' . htmlspecialchars($url) . '">' . $languageService->get('read_more') . '</a>';
    echo '    </div>';
    echo '  </div>';
    echo '</article>';
}

echo '</div>'; // .masonry-grid
echo '</div>'; // .masonry-wrapper
 // .masonry-wrapper
