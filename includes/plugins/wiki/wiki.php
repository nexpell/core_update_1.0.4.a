<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\RoleManager;
use nexpell\SeoUrlHandler;

global $languageService;

$lang = $languageService->detectLanguage();
$languageService->readPluginModule('wiki');

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

    // Header-Daten
    $data_array = [
        'class'    => $class,
        'title' => $languageService->get('title'),
        'subtitle' => 'nexpell WIKI'
    ];
    
    echo $tpl->loadTemplate("wiki", "head", $data_array, 'plugin');

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = '';
}




if ($action == "detail" && isset($_GET['id']) && is_numeric($_GET['id'])) {

    $id = (int)$_GET['id'];
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'de';

    // Artikel laden
    $article_result = safe_query("
        SELECT w.*, c.name AS catname
        FROM plugins_wiki w
        LEFT JOIN plugins_wiki_categories c ON w.category_id = c.id
        WHERE w.id = $id AND w.is_active = 1
    ");

    if ($article = mysqli_fetch_assoc($article_result)) {
        $timestamp = (int)$article['updated_at'];
        $tag = date("d", $timestamp);
        $monatname = date("M", $timestamp);
        $year = date("Y", $timestamp);
        $categoryId = (int)($article['category_id'] ?? 0);
        $categoryName = (string)($article['catname'] ?? '');

        $banner_image = $article['banner_image'];
        $image = $banner_image
            ? "/includes/plugins/wiki/images/" . $banner_image
            : "/includes/plugins/wiki/images/no-image.jpg";

        $profileUrl = SeoUrlHandler::convertToSeoUrl('index.php?site=profile&userID=' . intval($article['userID']));
        $username = '<a href="' . htmlspecialchars($profileUrl) . '">
                        <img src="' . htmlspecialchars(getavatar($article['userID'])) . '" 
                             class="img-fluid align-middle rounded me-1" 
                             style="height: 23px; width: 23px;" 
                             alt="' . htmlspecialchars(getusername($article['userID'])) . '">
                        <strong>' . htmlspecialchars(getusername($article['userID'])) . '</strong>
                    </a>';

        $translate = new multiLanguage($lang);
        $translate->detectLanguages($article['title']);
        $title = $translate->getTextByLanguage($article['title']);
        $short_title = mb_strlen($title) > 70 ? mb_substr($title, 0, 70) . '...' : $title;
        $shortDescription = trim((string)($article['desc_short'] ?? ''));
        $categoryUrl = $categoryId > 0
            ? SeoUrlHandler::convertToSeoUrl('index.php?site=wiki&cat=' . $categoryId)
            : SeoUrlHandler::convertToSeoUrl('index.php?site=wiki');
        $relatedArticles = [];

        if ($categoryId > 0) {
            $relatedResult = safe_query("
                SELECT id, title, desc_short
                FROM plugins_wiki
                WHERE is_active = 1
                  AND category_id = {$categoryId}
                  AND id != {$id}
                ORDER BY updated_at DESC
                LIMIT 3
            ");

            while ($related = mysqli_fetch_assoc($relatedResult)) {
                $relatedTranslate = new multiLanguage($lang);
                $relatedTranslate->detectLanguages($related['title']);
                $relatedTitle = $relatedTranslate->getTextByLanguage($related['title']);
                $relatedText = trim(strip_tags((string)($related['desc_short'] ?? '')));
                if ($relatedText !== '' && mb_strlen($relatedText) > 120) {
                    $relatedText = mb_substr($relatedText, 0, 117) . '...';
                }

                $relatedArticles[] = [
                    'title' => $relatedTitle,
                    'url' => SeoUrlHandler::convertToSeoUrl(
                        'index.php?site=wiki&action=detail&id=' . (int)$related['id']
                    ),
                    'text' => $relatedText,
                ];
            }
        }

        // Screenshots laden
        $screenshots = json_decode($article['screenshots'], true) ?? [];


?>

<div class="card shadow-sm mb-3">
    <div class="position-relative rounded-top" style="height:280px; overflow:hidden;">
        
        <img src="<?php echo $image; ?>" 
             class="w-100 h-100 rounded-top" 
             style="object-fit:cover;" 
             alt="<?php echo htmlspecialchars($title); ?>">
        
        <div class="position-absolute top-0 start-0 w-100 h-100 rounded-top" 
             style="background-color: rgba(0, 0, 0, 0.7);"></div>
        
        <div class="position-absolute bottom-0 start-0 p-3 text-white">
            <h2 class="mb-0"><?php #echo htmlspecialchars($title); ?></h2>
            <?php if (!empty($article['version'])): ?>
                <span class="badge bg-secondary rounded-pill">v<?php echo htmlspecialchars($article['version']); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="d-flex align-items-center">
                    <h2 class="card-title mb-0 me-2">
                        <?php echo htmlspecialchars($title); ?>
                    </h2>
                    <?php if (!empty($article['version'])): ?>
                        <span class="badge bg-secondary rounded-pill">v<?php echo htmlspecialchars($article['version']); ?></span>
                    <?php endif; ?>
                </div>

                <small class="text-muted">
                    Kategorie: <strong><?php echo htmlspecialchars($article['catname']); ?></strong> | 
                    <?php echo $languageService->get('by'); ?> <?php echo $username; ?> | 
                    <?php echo $tag . ' ' . $monatname . ' ' . $year; ?>
                </small>
            </div>

            <hr>

            <?php if ($shortDescription !== ''): ?>
                <div class="alert alert-light border mb-3">
                    <strong>Einordnung:</strong> <?php echo htmlspecialchars($shortDescription, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="mb-3 small text-muted">
                <span class="me-3">
                    Kategorie:
                    <a href="<?php echo htmlspecialchars($categoryUrl, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($categoryName !== '' ? $categoryName : 'Wiki', ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </span>
                <?php if (!empty($article['version'])): ?>
                    <span class="me-3">Version: <?php echo htmlspecialchars((string)$article['version'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
                <span>Letzte Aktualisierung: <?php echo $tag . ' ' . $monatname . ' ' . $year; ?></span>
            </div>

            <div class="card-text mb-3">
                <?php echo $article['desc_long']; ?>
            </div>

                <?php if (!empty($screenshots)): ?>
                <h5>Screenshots</h5>
                <div class="d-flex flex-wrap mb-3">
                    <?php foreach ($screenshots as $index => $img): ?>
                        <img src="/includes/plugins/wiki/images/<?php echo htmlspecialchars($img); ?>" 
                             class="img-thumbnail me-2 mb-2 screenshot-thumb" 
                             style="max-height:120px; cursor:pointer;" 
                             data-index="<?= $index ?>" 
                             alt="Screenshot">
                    <?php endforeach; ?>
                </div>

                <!-- Lightbox -->
                <div id="lightbox" class="lightbox-overlay flex-column">
                    <span class="lightbox-close">&times;</span>
                    <div class="position-relative w-100 text-center">
                        <button class="lightbox-prev">&#10094;</button>
                        <img class="lightbox-img" id="lightbox-img" src="" alt="Screenshot">
                        <button class="lightbox-next">&#10095;</button>
                    </div>
                    <div class="lightbox-thumbnails mt-2 d-flex justify-content-center flex-wrap">
                        <?php foreach ($screenshots as $index => $img): ?>
                            <img src="/includes/plugins/wiki/images/<?php echo htmlspecialchars($img); ?>" 
                                 class="img-thumbnail me-1 mb-1 lb-thumb" 
                                 style="max-height:60px; cursor:pointer;" 
                                 data-index="<?= $index ?>" 
                                 alt="Screenshot">
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            <?php if (!empty($relatedArticles)): ?>
                <section class="mt-4">
                    <h5>Verwandte Wiki-Artikel</h5>
                    <div class="list-group">
                        <?php foreach ($relatedArticles as $relatedArticle): ?>
                            <a href="<?php echo htmlspecialchars($relatedArticle['url'], ENT_QUOTES, 'UTF-8'); ?>" class="list-group-item list-group-item-action">
                                <strong><?php echo htmlspecialchars($relatedArticle['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <?php if ($relatedArticle['text'] !== ''): ?>
                                    <div class="small text-muted mt-1"><?php echo htmlspecialchars($relatedArticle['text'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <a href="<?= SeoUrlHandler::convertToSeoUrl('index.php?site=wiki'); ?>" class="btn btn-secondary">
                ← <?= $languageService->get('back'); ?>
            </a>
        </div>
    </div>


<?php
    } else {
        $categoryFallback = safe_query("
            SELECT id
            FROM plugins_wiki_categories
            WHERE id = {$id}
            LIMIT 1
        ");

        if ($categoryFallback && mysqli_num_rows($categoryFallback) > 0) {
            header('Location: ' . SeoUrlHandler::convertToSeoUrl('index.php?site=wiki&cat=' . $id), true, 301);
            exit;
        }

        http_response_code(404);
        echo '<div class="alert alert-warning">Artikel nicht gefunden.</div>';
    }

} elseif ($action === 'category' && isset($_GET['cat'])) {
    // Kategorie mit Artikeln anzeigen
}

 elseif ($action == "") {

    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'de';
    $selected_cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 10; // Anzahl Artikel pro Seite
    $offset = ($page - 1) * $perPage;

    // Kategorien laden
    $cats_result = safe_query("SELECT * FROM plugins_wiki_categories ORDER BY sort_order ASC");

    // Artikel laden (mit Limit für Pagination)
    if ($selected_cat == 0) {
        $totalResult = safe_query("SELECT COUNT(*) AS total FROM plugins_wiki WHERE is_active=1");
        $total = mysqli_fetch_assoc($totalResult)['total'];
        $articles_result = safe_query("SELECT * FROM plugins_wiki WHERE is_active=1 ORDER BY updated_at DESC LIMIT $offset, $perPage");
    } else {
        $totalResult = safe_query("SELECT COUNT(*) AS total FROM plugins_wiki WHERE category_id=$selected_cat AND is_active=1");
        $total = mysqli_fetch_assoc($totalResult)['total'];
        $articles_result = safe_query("SELECT * FROM plugins_wiki WHERE category_id=$selected_cat AND is_active=1 ORDER BY updated_at DESC LIMIT $offset, $perPage");
    }

    $num_articles = $articles_result ? mysqli_num_rows($articles_result) : 0;
?>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 mb-3">
        <div class="list-group">
            <a href="<?php echo htmlspecialchars(SeoUrlHandler::convertToSeoUrl("index.php?site=wiki")); ?>" 
               class="list-group-item list-group-item-action <?php echo $selected_cat == 0 ? 'active' : ''; ?>">
               Alle Kategorien
            </a>
            <?php while ($c = mysqli_fetch_array($cats_result)) : ?>
                <?php
                    $cat_id = (int)$c['id'];
                    $count_result = mysqli_fetch_assoc(safe_query("SELECT COUNT(*) AS cnt FROM plugins_wiki WHERE category_id = '$cat_id'"));
                    $entry_count = $count_result['cnt'] ?? 0;
                ?>
                <a href="<?php echo htmlspecialchars(SeoUrlHandler::convertToSeoUrl("index.php?site=wiki&cat=$cat_id")); ?>"
                   class="list-group-item list-group-item-action <?php echo $selected_cat == $cat_id ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($c['name']); ?> 
                    <span class="badge bg-secondary float-end"><?php echo $entry_count; ?></span>
                </a>
            <?php endwhile; ?>


        </div>
    </div>

    <!-- Content -->
    <div class="col-md-9">
        <?php if ($num_articles > 0): ?>
            <?php while ($article = mysqli_fetch_assoc($articles_result)) :
                // --- Artikel-Verarbeitung wie vorher ---
                $timestamp = (int)$article['updated_at'];
                $tag = date("d", $timestamp);
                $monatname = date("M", $timestamp);
                $year = date("Y", $timestamp);

                $banner_image = $article['banner_image'];
                $image = $banner_image
                    ? "/includes/plugins/wiki/images/" . $banner_image
                    : "/includes/plugins/wiki/images/no-image.jpg";

                $profileUrl = SeoUrlHandler::convertToSeoUrl('index.php?site=profile&userID=' . intval($article['userID']));
                $username = '<a href="' . htmlspecialchars($profileUrl) . '">
                    <img src="' . htmlspecialchars(getavatar($article['userID'])) . '" 
                         class="img-fluid align-middle rounded me-1" 
                         style="height: 23px; width: 23px;" 
                         alt="' . htmlspecialchars(getusername($article['userID'])) . '">
                    <strong>' . htmlspecialchars(getusername($article['userID'])) . '</strong>
                </a>';

                $translate = new multiLanguage($lang);
                $translate->detectLanguages($article['title']);
                $title = $translate->getTextByLanguage($article['title']);
                $short_title = mb_strlen($title) > 70 ? mb_substr($title, 0, 70) . '...' : $title;

                $screenshots = json_decode($article['banner_image'], true) ?? [];
            ?>
                <div class="card mb-3 shadow-sm">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <div class="h-100 d-flex">
                                <img src="<?php echo $image; ?>" 
                                     class="img-fluid rounded-start flex-fill" 
                                     alt="<?php echo htmlspecialchars($title); ?>" 
                                     style="width:100%; height:150px; object-fit:cover;">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0 me-2">
                                            
                                            <?php echo htmlspecialchars($short_title); ?>
                                     

                                        </h5>
                                        <?php if (!empty($article['version'])): ?>
                                            <span class="badge bg-secondary rounded-pill">v<?php echo htmlspecialchars($article['version']); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <small class="text-muted">
                                        <?php echo $languageService->get('by'); ?> <?php echo $username; ?> | 
                                        <?php echo $tag . ' ' . $monatname . ' ' . $year; ?>
                                    </small>
                                </div>

                                <p class="card-text">
                                    <?php
                                        $short_content = mb_strlen($article['desc_short']) > 200 
                                            ? mb_substr($article['desc_short'], 0, 200) . '...' 
                                            : $article['desc_short'];
                                        echo $short_content;
                                    ?>
                                </p>

                                <?php if (!empty($screenshots)): ?>
                                    <div class="mb-2">
                                        <?php foreach ($screenshots as $img): ?>
                                            <img src="/includes/plugins/wiki/images/<?php echo htmlspecialchars($img); ?>" class="img-thumbnail me-1 mb-1" style="max-height:60px;" alt="Screenshot">
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <a href="<?php echo htmlspecialchars(SeoUrlHandler::convertToSeoUrl(
                                            'index.php?site=wiki&action=detail&id=' . intval($article['id'])
                                    )); ?>" class="btn btn-sm btn-primary">
                                    <?php echo $languageService->get('read_more'); ?>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <!-- Pagination -->
            <?php

            $totalPages = (int)ceil($total / $perPage);

if ($totalPages > 1) {

    if (!empty($selected_cat) && (int)$selected_cat > 0) {
        // Kategorie-Pagination
        $wikiBaseUrl = SeoUrlHandler::convertToSeoUrl(
            'index.php?site=wiki&cat=' . (int)$selected_cat
        );
    } else {
        // Übersicht-Pagination (KEIN cat!)
        $wikiBaseUrl = SeoUrlHandler::convertToSeoUrl(
            'index.php?site=wiki'
        );
    }

    // für Pagination anhängen
    $wikiBaseUrl = rtrim($wikiBaseUrl, '/') . '/';

    echo $tpl->renderPagination(
        $wikiBaseUrl,
        (int)$page,
        $totalPages,
        'page'
    );
}
?>

        <?php else: ?>
            <div class="alert alert-info">
                Keine Artikel in dieser Kategorie.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php 
} 
?>
<style>

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const screenshots = <?php echo json_encode($screenshots ?? []); ?>;
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lbThumbs = document.querySelectorAll('.lb-thumb');
    const thumbsTop = document.querySelectorAll('.screenshot-thumb');
    const closeBtn = document.querySelector('.lightbox-close');
    const prevBtn = document.querySelector('.lightbox-prev');
    const nextBtn = document.querySelector('.lightbox-next');
    let currentIndex = 0;

    if (!lightbox || !lightboxImg || screenshots.length === 0) {
        return;
    }

    function updateLightbox() {
        lightboxImg.src = '/includes/plugins/wiki/images/' + screenshots[currentIndex];
        lbThumbs.forEach((thumb, i) => thumb.classList.toggle('active', i === currentIndex));
    }

    function openLightbox(index) {
        currentIndex = index;
        updateLightbox();
        lightbox.style.display = 'flex';
    }

    thumbsTop.forEach((thumb, i) => {
        thumb.addEventListener('click', () => openLightbox(i));
    });

    lbThumbs.forEach((thumb, i) => {
        thumb.addEventListener('click', () => openLightbox(i));
    });

    closeBtn.addEventListener('click', () => lightbox.style.display = 'none');

    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + screenshots.length) % screenshots.length;
        updateLightbox();
    });

    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % screenshots.length;
        updateLightbox();
    });

    document.addEventListener('keydown', e => {
        if (lightbox.style.display === 'flex') {
            if (e.key === 'Escape') lightbox.style.display = 'none';
            if (e.key === 'ArrowLeft') prevBtn.click();
            if (e.key === 'ArrowRight') nextBtn.click();
        }
    });
});
</script>
