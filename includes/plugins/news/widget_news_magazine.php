<?php
#echo '<link rel="stylesheet" href="/includes/plugins/news/css/news_magazine.css">' . PHP_EOL;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;

use nexpell\SeoUrlHandler;

global $languageService;
require_once __DIR__ . '/builder_widget_helper.php';

$lang = $languageService->detectLanguage();
$languageService->readPluginModule('news');
$newsBuilderSettings = news_widget_builder_settings('widget_news_magazine', isset($settings) && is_array($settings) ? $settings : []);
$limit = (int)$newsBuilderSettings['limit'];
$orderSql = news_widget_builder_order_sql((string)$newsBuilderSettings['order']);
$whereSql = news_widget_builder_where_sql($GLOBALS['_database'], $newsBuilderSettings);

$query = "
    SELECT a.id, a.title, a.updated_at, a.banner_image,
           a.content, c.name AS category_name, c.image AS category_image
    FROM plugins_news a
    LEFT JOIN plugins_news_categories c ON a.category_id = c.id
    {$whereSql}
    {$orderSql}
    LIMIT " . intval($limit) . "
";


$res = safe_query($query);
$news = [];
while ($row = mysqli_fetch_assoc($res)) {
    // SEO-Link zur News generieren
    $row['link'] = SeoUrlHandler::buildPluginUrl('plugins_news', $row['id'], $lang);
    $news[] = $row;
}

if (count($news) > 0):
?>
<?= news_widget_builder_heading_html($newsBuilderSettings, 'News Magazine', 'h5', 'mb-3') ?>
<div class="news-magazine-widget d-flex flex-wrap gap-3">
    <!-- Featured News -->
    <?php
    $featured = array_shift($news);
    $featured_image = !empty($featured['category_image'])
        ? '/includes/plugins/news/images/news_categories/' . $featured['category_image']
        : '/includes/plugins/news/images/no-image.jpg';

    // Link zur News
    $featured_link = $featured['link']; 
    ?>
    <div class="featured-news flex-grow-1">
        <a href="<?= $featured_link ?>">
            <div class="card featured-wrapper">
                <img src="<?= $featured_image ?>" alt="<?= htmlspecialchars($featured['title']) ?>" 
                     class="featured-img">

                <div class="featured-text">
                    <small>
                      <?php
                        $featuredMeta = [];
                        if (!empty($newsBuilderSettings['show_date'])) {
                            $featuredMeta[] = date('d.m.Y', strtotime($featured['updated_at']));
                        }
                        if (!empty($newsBuilderSettings['show_category'])) {
                            $featuredMeta[] = htmlspecialchars($featured['category_name']);
                        }
                        echo implode(' | ', $featuredMeta);
                      ?>
                    </small>
                    <h3><?= htmlspecialchars($featured['title']) ?></h3>

                    <?php $plain_content = news_widget_builder_excerpt((string)$featured['content'], (int)$newsBuilderSettings['featured_excerpt_chars']); ?>
                    <p><?= $plain_content ?></p>
                </div>
            </div>
        </a>
    </div>

    <!-- Smaller News -->
    <div class="smaller-news d-flex flex-column flex-grow-1">
        <?php foreach ($news as $n):
            $img = !empty($n['category_image'])
                ? '/includes/plugins/news/images/news_categories/' . $n['category_image']
                : '/includes/plugins/news/images/no-image.jpg';
            $news_link = $n['link']; // SEO-Link verwenden
        ?>
            <div class="card small-news-card">
                <a href="<?= $news_link ?>">
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($n['title']) ?>">
                    <div class="small-news-text">
                        <small class="text-muted">
                          <?php
                            $itemMeta = [];
                            if (!empty($newsBuilderSettings['show_date'])) {
                                $itemMeta[] = date('d.m.Y', strtotime($n['updated_at']));
                            }
                            if (!empty($newsBuilderSettings['show_category'])) {
                                $itemMeta[] = htmlspecialchars($n['category_name']);
                            }
                            echo implode(' | ', $itemMeta);
                          ?>
                        </small>
                        <h6><?= htmlspecialchars($n['title']) ?></h6>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<script>
(function(){
  function ensureOrder() {
    var container = document.querySelector('.news-magazine-widget');
    if (!container) return;
    var featured = container.querySelector('.featured-news');
    var smaller = container.querySelector('.smaller-news');
    if (!featured || !smaller) return;
    if (window.innerWidth <= 768) {
      // stelle sicher, dass featured zuerst steht, smaller danach
      if (container.firstElementChild !== featured) container.insertBefore(featured, container.firstChild);
      if (container.lastElementChild !== smaller) container.appendChild(smaller);
    } else {
      // keine Veränderung auf Desktop
    }
  }
  window.addEventListener('resize', ensureOrder);
  document.addEventListener('DOMContentLoaded', ensureOrder);
})();
</script>
