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
$newsBuilderSettings = news_widget_builder_settings('widget_news_flip', isset($settings) && is_array($settings) ? $settings : []);
$limit = (int)$newsBuilderSettings['limit'];
$orderSql = news_widget_builder_order_sql((string)$newsBuilderSettings['order']);
$whereSql = news_widget_builder_where_sql($GLOBALS['_database'], $newsBuilderSettings);

$query = "
    SELECT a.id, a.title, a.content, a.updated_at, a.banner_image,
           c.name AS category_name, c.image AS category_image
    FROM plugins_news a
    LEFT JOIN plugins_news_categories c ON a.category_id = c.id
    {$whereSql}
    {$orderSql}
    LIMIT " . intval($limit) . "
";

$res = safe_query($query);

if (mysqli_num_rows($res) > 0):
?>
<link rel="stylesheet" href="/includes/plugins/news/css/news_flip.css">
<?= news_widget_builder_heading_html($newsBuilderSettings, 'News Flip', 'h5', 'mb-3') ?>
<div class="news-flip-widget">
    <?php while ($news = mysqli_fetch_assoc($res)):
        $image = !empty($news['category_image'])
            ? '/includes/plugins/news/images/news_categories/' . $news['category_image']
            : '/includes/plugins/news/images/no-image.jpg';

        $title = htmlspecialchars($news['title']);
        $plainText = strip_tags($news['content']); // Alle HTML-Tags entfernen
        $shortContent = news_widget_builder_excerpt($plainText, (int)$newsBuilderSettings['content_chars']);
        $category = htmlspecialchars($news['category_name']);
        $dateText = date('d.m.Y', strtotime((string)$news['updated_at']));

        // SEO-Link zur News
        $url_watch = SeoUrlHandler::buildPluginUrl('plugins_news', intval($news['id']), $lang);
    ?>
    <div class="flip-card">
        <div class="flip-card-inner">
            <div class="flip-card-front" style="background-image: url('<?= $image ?>');">
                <div class="flip-card-front-overlay">
                    <h6><?= $title ?></h6>
                </div>
            </div>
            <div class="flip-card-back d-flex flex-column border">
                <p><?= $shortContent ?></p>
                <?php if (!empty($newsBuilderSettings['show_date'])): ?>
                <small class="text-muted mb-2"><?= htmlspecialchars($dateText, ENT_QUOTES, 'UTF-8') ?></small>
                <?php endif; ?>
                <?php if (!empty($newsBuilderSettings['show_category'])): ?>
                <span class="badge bg-primary"><?= $category ?></span>
                <?php endif; ?>
                <a href="<?= $url_watch ?>" class="btn btn-sm btn-light mt-auto">Mehr lesen</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>
