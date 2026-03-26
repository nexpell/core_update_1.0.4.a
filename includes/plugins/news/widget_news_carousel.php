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
$newsBuilderSettings = news_widget_builder_settings('widget_news_carousel', isset($settings) && is_array($settings) ? $settings : []);
$limit = (int)$newsBuilderSettings['limit'];
$orderSql = news_widget_builder_order_sql((string)$newsBuilderSettings['order']);
$whereSql = news_widget_builder_where_sql($GLOBALS['_database'], $newsBuilderSettings);

echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">';
echo '<link rel="stylesheet" href="/includes/plugins/news/css/news_carousel.css">';

$query = "
    SELECT a.id, a.title, a.updated_at, a.banner_image, a.slug,
           c.name AS category_name, c.image AS category_image
    FROM plugins_news a
    LEFT JOIN plugins_news_categories c ON a.category_id = c.id
    {$whereSql}
    {$orderSql}
    LIMIT " . intval($limit);

$res = safe_query($query);

if (mysqli_num_rows($res) > 0):
?>
<div class="news-carousel-widget">
  <?= news_widget_builder_heading_html($newsBuilderSettings, 'News Carousel', 'h5', 'mb-3') ?>
  
  <div class="swiper newsSwiper">
    <div class="swiper-wrapper">
      <?php while ($news = mysqli_fetch_assoc($res)):

        $image = !empty($news['category_image'])
          ? '/includes/plugins/news/images/news_categories/' . $news['category_image']
          : '/includes/plugins/news/images/no-image.jpg';

        $ts = strtotime($news['updated_at']);

        $day   = date('d', $ts);
        $month = strtolower(date('F', $ts));
        $year  = date('Y', $ts);
        $title = htmlspecialchars($news['title']);
        $category_name = htmlspecialchars($news['category_name']);

        // SEO-Link generieren
        $url_watch = SeoUrlHandler::buildPluginUrl('plugins_news', $news['id'], $lang);

      ?>
      <div class="swiper-slide">
        <div class="card h-100">
          <img src="<?= $image ?>" class="card-img-top" alt="<?= $title ?>" style="object-fit:cover; height:180px;">
          <div class="card-body d-flex flex-column">
            
            <h6 class="card-title mb-2 text-truncate" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
              <?= $title ?>
            </h6>

            <!-- Datum links, Kategorie rechts -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted"><?= !empty($newsBuilderSettings['show_date']) ? $day . ' ' . $languageService->get($month) . ' ' . $year : '&nbsp;' ?></small>
                <?php if (!empty($newsBuilderSettings['show_category'])): ?>
                <span class="badge bg-primary"><?= $category_name ?></span>
                <?php endif; ?>
            </div>

            <a href="<?= htmlspecialchars($url_watch) ?>" class="btn btn-sm btn-primary mt-auto">Mehr lesen</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Navigation + Pagination -->
  <div class="carousel-controls d-flex justify-content-between align-items-center mt-2">
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination flex-grow-1 text-center"></div>
    <div class="swiper-button-next"></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
var swiper = new Swiper(".newsSwiper", {
  slidesPerView: <?= (int)$newsBuilderSettings['slides_mobile'] ?>,
  spaceBetween: 15,
  loop: true, // Endlos-Loop
  <?php if ((int)$newsBuilderSettings['autoplay_delay'] > 0): ?>
  autoplay: {
    delay: <?= (int)$newsBuilderSettings['autoplay_delay'] ?>,
    disableOnInteraction: false, // auch nach Klick weiterlaufen
  },
  <?php endif; ?>
  navigation: {
    nextEl: ".news-carousel-widget .swiper-button-next",
    prevEl: ".news-carousel-widget .swiper-button-prev",
  },
  pagination: {
    el: ".news-carousel-widget .swiper-pagination",
    clickable: true,
  },
  breakpoints: {
    768: { slidesPerView: <?= (int)$newsBuilderSettings['slides_tablet'] ?> },
    992: { slidesPerView: <?= (int)$newsBuilderSettings['slides_desktop'] ?> }
  }
});
</script>

<?php endif; ?>
