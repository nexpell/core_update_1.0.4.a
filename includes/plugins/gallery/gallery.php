<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

global $_database, $languageService, $tpl;

function gallery_ensure_schema(mysqli $database): void
{
    static $done = false;

    if ($done) {
        return;
    }

    $requiredColumns = [
        'title' => "ALTER TABLE plugins_gallery ADD COLUMN title VARCHAR(255) NOT NULL DEFAULT '' AFTER filename",
        'caption' => "ALTER TABLE plugins_gallery ADD COLUMN caption TEXT NULL AFTER title",
        'alt_text' => "ALTER TABLE plugins_gallery ADD COLUMN alt_text VARCHAR(255) NOT NULL DEFAULT '' AFTER caption",
        'tags' => "ALTER TABLE plugins_gallery ADD COLUMN tags VARCHAR(255) NOT NULL DEFAULT '' AFTER alt_text",
        'photographer' => "ALTER TABLE plugins_gallery ADD COLUMN photographer VARCHAR(255) NOT NULL DEFAULT '' AFTER tags",
        'width' => "ALTER TABLE plugins_gallery ADD COLUMN width INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER photographer",
        'height' => "ALTER TABLE plugins_gallery ADD COLUMN height INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER width",
        'sort_page' => "ALTER TABLE plugins_gallery ADD COLUMN sort_page INT(10) UNSIGNED NOT NULL DEFAULT 1 AFTER position",
    ];

    foreach ($requiredColumns as $column => $sql) {
        $check = $database->query("SHOW COLUMNS FROM plugins_gallery LIKE '" . $database->real_escape_string($column) . "'");
        if ($check instanceof mysqli_result && $check->num_rows === 0) {
            $database->query($sql);
        }
    }

    $database->query("UPDATE plugins_gallery SET sort_page = 1 WHERE sort_page IS NULL OR sort_page < 1");

    $done = true;
}

function gallery_build_query_suffix(array $params): string
{
    $filtered = [];
    foreach ($params as $key => $value) {
        if ($value === '' || $value === null) {
            continue;
        }
        if (($key === 'category' || $key === 'page') && (int)$value <= 0) {
            continue;
        }
        if ($key === 'page' && (int)$value <= 1) {
            continue;
        }
        $filtered[$key] = $value;
    }

    return http_build_query($filtered);
}

function gallery_build_url(array $params): string
{
    $query = gallery_build_query_suffix($params);
    $url = 'index.php' . ($query !== '' ? '?' . $query : '');
    return SeoUrlHandler::convertToSeoUrl($url);
}

function gallery_build_detail_url(int $imageId): string
{
    return gallery_build_url([
        'site' => 'gallery',
        'action' => 'detail',
        'id' => $imageId,
    ]);
}

function gallery_asset_base(): string
{
    $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    return $base === '' || $base === '.' ? '' : $base;
}

function gallery_asset_url(string $path): string
{
    return gallery_asset_base() . '/' . ltrim($path, '/');
}

function gallery_variant_candidates(string $filename, string $variant = 'main', string $format = 'native'): array
{
    $base = 'includes/plugins/gallery/images';
    $stem = pathinfo($filename, PATHINFO_FILENAME);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $encodedName = rawurlencode($filename);
    $encodedWebp = rawurlencode($stem . '.webp');
    $encodedOrig = rawurlencode($stem . '_orig' . ($ext !== '' ? '.' . $ext : ''));

    if ($format === 'webp') {
        $preferred = $base . '/upload/webp';
        if ($variant === 'thumb') {
            return [
                $preferred . '/thumbs/' . $encodedWebp,
                $base . '/upload/thumbs/' . $encodedName,
                $base . '/upload/' . $encodedName,
                $base . '/' . $encodedName,
            ];
        }
        if ($variant === 'medium') {
            return [
                $preferred . '/mediums/' . $encodedWebp,
                $base . '/upload/mediums/' . $encodedName,
                $base . '/upload/' . $encodedName,
                $base . '/' . $encodedName,
            ];
        }
        return [
            $preferred . '/' . $encodedWebp,
            $base . '/upload/' . $encodedName,
            $base . '/' . $encodedName,
        ];
    }

    if ($variant === 'thumb') {
        return [
            $base . '/upload/thumbs/' . $encodedName,
            $base . '/upload/' . $encodedName,
            $base . '/upload/originals/' . $encodedOrig,
            $base . '/' . $encodedName,
        ];
    }

    if ($variant === 'medium') {
        return [
            $base . '/upload/mediums/' . $encodedName,
            $base . '/upload/' . $encodedName,
            $base . '/upload/originals/' . $encodedOrig,
            $base . '/' . $encodedName,
        ];
    }

    if ($variant === 'original') {
        return [
            $base . '/upload/originals/' . $encodedOrig,
            $base . '/upload/' . $encodedName,
            $base . '/' . $encodedName,
        ];
    }

    return [
        $base . '/upload/' . $encodedName,
        $base . '/upload/originals/' . $encodedOrig,
        $base . '/' . $encodedName,
    ];
}

function gallery_variant_web_path(string $filename, string $variant = 'main', string $format = 'native'): string
{
    if ($format === 'native' && $variant === 'main') {
        return gallery_asset_url('includes/plugins/gallery/images/upload/' . rawurlencode($filename));
    }

    if ($format === 'native' && ($variant === 'thumb' || $variant === 'medium')) {
        $directMain = gallery_asset_url('includes/plugins/gallery/images/upload/' . rawurlencode($filename));
        $basePath = defined('BASE_PATH') ? rtrim((string) BASE_PATH, '/\\') : dirname(__DIR__, 3);
        if (is_file($basePath . str_replace('/', DIRECTORY_SEPARATOR, $directMain))) {
            return $directMain;
        }
    }

    foreach (gallery_variant_candidates($filename, $variant, $format) as $candidate) {
        $basePath = defined('BASE_PATH') ? rtrim((string) BASE_PATH, '/\\') : dirname(__DIR__, 3);
        $fsPath = $basePath . str_replace('/', DIRECTORY_SEPARATOR, $candidate);
        if (is_file($fsPath)) {
            return gallery_asset_url($candidate);
        }
    }

    $candidates = gallery_variant_candidates($filename, $variant, $format);
    return gallery_asset_url($candidates[0]);
}

function gallery_variant_fs_path(string $filename, string $variant = 'main', string $format = 'native'): string
{
    $basePath = defined('BASE_PATH') ? rtrim((string) BASE_PATH, '/\\') : dirname(__DIR__, 3);
    $candidates = gallery_variant_candidates($filename, $variant, $format);
    $first = $candidates[0] ?? ('includes/plugins/gallery/images/upload/' . $filename);
    return $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($first, '/'));
}

function gallery_variant_exists(string $filename, string $variant = 'main', string $format = 'native'): bool
{
    return is_file(gallery_variant_fs_path($filename, $variant, $format));
}

gallery_ensure_schema($_database);

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars((string) ($config['selected_style'] ?? ''), ENT_QUOTES, 'UTF-8');

$data_array = [
    'class' => $class,
    'title' => $languageService->get('title_gallery'),
    'subtitle' => 'Gallery'
];
echo $tpl->loadTemplate('gallery', 'head', $data_array, 'plugin');

$data_array = ['title' => ''];
echo $tpl->loadTemplate('gallery', 'content_head', $data_array, 'plugin');

$action = (string)($_GET['action'] ?? '');
if ($action === 'show') {
    $action = 'detail';
}
$detailId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['picID']) ? (int)$_GET['picID'] : 0);

if ($action === 'detail' && $detailId > 0) {
    $detailSql = "SELECT g.*, c.name AS category_name
                  FROM plugins_gallery g
                  LEFT JOIN plugins_gallery_categories c ON g.category_id = c.id
                  WHERE g.id = ?
                  LIMIT 1";
    $detailStmt = $_database->prepare($detailSql);
    $detailImage = null;
    if ($detailStmt) {
        $detailStmt->bind_param('i', $detailId);
        $detailStmt->execute();
        $detailResult = $detailStmt->get_result();
        $detailImage = $detailResult ? $detailResult->fetch_assoc() : null;
        $detailStmt->close();
    }

    if (!$detailImage) {
        http_response_code(404);
        echo '<div class="alert alert-warning text-center">' . htmlspecialchars($languageService->get('info_no_pictures'), ENT_QUOTES, 'UTF-8') . '</div>';
        echo '</div></div></div></div>';
        return;
    }

    $filename = (string)($detailImage['filename'] ?? '');
    $title = trim((string)($detailImage['title'] ?? ''));
    $caption = trim((string)($detailImage['caption'] ?? ''));
    $altText = trim((string)($detailImage['alt_text'] ?? ''));
    $categoryName = trim((string)($detailImage['category_name'] ?? ''));
    $photographer = trim((string)($detailImage['photographer'] ?? ''));
    $tags = array_values(array_filter(array_map('trim', explode(',', (string)($detailImage['tags'] ?? '')))));
    $displayTitle = $title !== '' ? $title : ($categoryName !== '' ? $categoryName : $filename);
    $displayAlt = $altText !== '' ? $altText : $displayTitle;
    $mainPath = gallery_variant_web_path($filename, 'main');
    $mediumPath = gallery_variant_exists($filename, 'medium') ? gallery_variant_web_path($filename, 'medium') : $mainPath;
    $thumbPath = gallery_variant_exists($filename, 'thumb') ? gallery_variant_web_path($filename, 'thumb') : $mainPath;
    $mainWebpPath = gallery_variant_exists($filename, 'main', 'webp') ? gallery_variant_web_path($filename, 'main', 'webp') : '';
    $uploadDate = !empty($detailImage['upload_date']) ? date('d.m.Y', strtotime((string)$detailImage['upload_date'])) : '';
    $galleryBaseUrl = gallery_build_url(['site' => 'gallery']);
    $categoryUrl = (int)($detailImage['category_id'] ?? 0) > 0
        ? gallery_build_url(['site' => 'gallery', 'category' => (int)$detailImage['category_id']])
        : '';
    ?>
    <section class="gallery-detail card shadow-sm mb-4">
        <div class="card-body">
            <nav class="mb-4">
                <a href="<?= htmlspecialchars($galleryBaseUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                    <?= htmlspecialchars($languageService->get('title_gallery'), ENT_QUOTES, 'UTF-8') ?>
                </a>
            </nav>

            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <figure class="mb-0">
                        <picture>
                            <?php if ($mainWebpPath !== ''): ?>
                                <source type="image/webp" srcset="<?= htmlspecialchars($mainWebpPath, ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                            <img
                                src="<?= htmlspecialchars($mediumPath, ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($displayAlt, ENT_QUOTES, 'UTF-8') ?>"
                                class="img-fluid rounded shadow-sm w-100"
                                width="<?= max(1, (int)($detailImage['width'] ?? 1)) ?>"
                                height="<?= max(1, (int)($detailImage['height'] ?? 1)) ?>"
                            >
                        </picture>
                    </figure>
                </div>
                <div class="col-lg-4">
                    <div class="gallery-detail-copy">
                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <?php if ($categoryName !== '' && $categoryUrl !== ''): ?>
                                <a href="<?= htmlspecialchars($categoryUrl, ENT_QUOTES, 'UTF-8') ?>" class="badge text-bg-light text-decoration-none"><?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?></a>
                            <?php endif; ?>
                            <?php if ($uploadDate !== ''): ?>
                                <span class="badge text-bg-secondary"><?= htmlspecialchars($uploadDate, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <h1 class="h3 mb-3"><?= htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') ?></h1>

                        <?php if ($caption !== ''): ?>
                            <p class="mb-4"><?= nl2br(htmlspecialchars($caption, ENT_QUOTES, 'UTF-8')) ?></p>
                        <?php endif; ?>

                        <div class="list-group list-group-flush small mb-4">
                            <?php if ($photographer !== ''): ?>
                                <div class="list-group-item px-0 d-flex justify-content-between"><span><?= htmlspecialchars($languageService->get('label_photographer'), ENT_QUOTES, 'UTF-8') ?></span><strong><?= htmlspecialchars($photographer, ENT_QUOTES, 'UTF-8') ?></strong></div>
                            <?php endif; ?>
                            <?php if ($displayAlt !== ''): ?>
                                <div class="list-group-item px-0 d-flex justify-content-between"><span>ALT</span><strong><?= htmlspecialchars($displayAlt, ENT_QUOTES, 'UTF-8') ?></strong></div>
                            <?php endif; ?>
                            <?php if (!empty($tags)): ?>
                                <div class="list-group-item px-0"><span class="d-block mb-2"><?= htmlspecialchars($languageService->get('label_tags'), ENT_QUOTES, 'UTF-8') ?></span><div class="d-flex flex-wrap gap-2"><?php foreach ($tags as $tag): ?><span class="badge text-bg-light"><?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?></span><?php endforeach; ?></div></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?= htmlspecialchars($mainPath, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary" download>
                                <i class="bi bi-download"></i>
                                <?= htmlspecialchars($languageService->get('download'), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <a href="<?= htmlspecialchars($thumbPath, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary" target="_blank" rel="noopener">
                                <i class="bi bi-image"></i>
                                <?= htmlspecialchars($languageService->get('preview'), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    echo '</div></div></div></div>';
    return;
}

$categoryFilter = isset($_GET['category']) && is_numeric($_GET['category']) ? (int) $_GET['category'] : 0;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$searchTerm = trim((string) ($_GET['q'] ?? ''));
$searchLike = $searchTerm !== '' ? '%' . $searchTerm . '%' : '';

$whereClauses = [];
$types = '';
$params = [];

if ($categoryFilter > 0) {
    $whereClauses[] = 'g.category_id = ?';
    $types .= 'i';
    $params[] = $categoryFilter;
}

if ($searchLike !== '') {
    $whereClauses[] = '(g.title LIKE ? OR g.caption LIKE ? OR g.tags LIKE ? OR g.filename LIKE ?)';
    $types .= 'ssss';
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
}

$whereSql = $whereClauses ? ' WHERE ' . implode(' AND ', $whereClauses) : '';

$totalImages = 0;
$countSql = "SELECT COUNT(*) AS total FROM plugins_gallery g{$whereSql}";
$countStmt = $_database->prepare($countSql);
if ($countStmt) {
    if ($params) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    if ($countResult && ($row = $countResult->fetch_assoc())) {
        $totalImages = (int) ($row['total'] ?? 0);
    }
    $countStmt->close();
}

$sortPages = [];
$sortPageSql = "SELECT DISTINCT g.sort_page FROM plugins_gallery g{$whereSql} ORDER BY g.sort_page ASC";
$sortPageStmt = $_database->prepare($sortPageSql);
if ($sortPageStmt) {
    if ($params) {
        $sortPageStmt->bind_param($types, ...$params);
    }
    $sortPageStmt->execute();
    $sortPageResult = $sortPageStmt->get_result();
    while ($sortPageResult && ($row = $sortPageResult->fetch_assoc())) {
        $sortPages[] = max(1, (int) ($row['sort_page'] ?? 1));
    }
    $sortPageStmt->close();
}

if (!$sortPages) {
    $sortPages = [1];
}

$totalPages = count($sortPages);
if ($page > $totalPages) {
    $page = $totalPages;
}
$currentSortPage = $sortPages[$page - 1] ?? 1;

$categories = [];
$catResult = $_database->query("SELECT id, name FROM plugins_gallery_categories ORDER BY name ASC");
if ($catResult instanceof mysqli_result) {
    while ($cat = $catResult->fetch_assoc()) {
        $categories[] = $cat;
    }
}

$images = [];
$sql = "SELECT g.id, g.filename, g.class, g.category_id, g.title, g.caption, g.alt_text, g.tags, g.photographer, g.width, g.height, c.name AS category_name
        FROM plugins_gallery g
        LEFT JOIN plugins_gallery_categories c ON g.category_id = c.id
        {$whereSql}" . ($whereSql ? " AND" : " WHERE") . " g.sort_page = ?
        ORDER BY g.sort_page ASC, g.position ASC, g.id ASC
        ";
$stmt = $_database->prepare($sql);
if ($stmt) {
    $queryTypes = $types . 'i';
    $queryParams = array_merge($params, [$currentSortPage]);
    $stmt->bind_param($queryTypes, ...$queryParams);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($result && ($row = $result->fetch_assoc())) {
        $images[] = $row;
    }
    $stmt->close();
}

$baseQuery = [
    'site' => 'gallery',
    'category' => $categoryFilter,
    'q' => $searchTerm,
];
?>

<div class="gallery-toolbar mb-4">
    <form method="get" action="<?= htmlspecialchars(gallery_build_url([
        'site' => 'gallery',
        'category' => $categoryFilter,
    ]), ENT_QUOTES, 'UTF-8') ?>" class="gallery-search">
        <?php if ($categoryFilter > 0): ?>
            <input type="hidden" name="category" value="<?= $categoryFilter ?>">
        <?php endif; ?>
        <input
            type="search"
            name="q"
            value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>"
            class="form-control"
            placeholder="<?= htmlspecialchars($languageService->get('search_placeholder'), ENT_QUOTES, 'UTF-8') ?>"
            aria-label="<?= htmlspecialchars($languageService->get('search_placeholder'), ENT_QUOTES, 'UTF-8') ?>"
        >
        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($languageService->get('search'), ENT_QUOTES, 'UTF-8') ?></button>
    </form>

    <ul id="portfolio-flters" class="gallery-filters">
        <li class="<?= $categoryFilter === 0 ? 'active' : '' ?>">
            <a href="<?= htmlspecialchars(gallery_build_url([
                'site' => 'gallery',
                'q' => $searchTerm,
            ]), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($languageService->get('filter_all'), ENT_QUOTES, 'UTF-8') ?></a>
        </li>
        <?php foreach ($categories as $cat): ?>
            <li class="<?= $categoryFilter === (int) $cat['id'] ? 'active' : '' ?>">
                <a href="<?= htmlspecialchars(gallery_build_url([
                    'site' => 'gallery',
                    'category' => (int) $cat['id'],
                    'q' => $searchTerm,
                ]), ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars((string) $cat['name'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="gallery-summary mb-3">
    <span><?= htmlspecialchars(sprintf($languageService->get('results_count'), $totalImages), ENT_QUOTES, 'UTF-8') ?></span>
    <?php if ($searchTerm !== ''): ?>
        <span class="gallery-summary-term">
            <?= htmlspecialchars(sprintf($languageService->get('results_for'), $searchTerm), ENT_QUOTES, 'UTF-8') ?>
        </span>
    <?php endif; ?>
</div>

<div class="grid-wrapper" data-gallery-grid>
    <?php if (count($images) === 0): ?>
        <p class="gallery-empty text-center"><?= htmlspecialchars($languageService->get('info_no_pictures_category'), ENT_QUOTES, 'UTF-8') ?></p>
    <?php else: ?>
        <?php foreach ($images as $index => $image): ?>
            <?php
            $filename = (string) ($image['filename'] ?? '');
            if ($filename === '') {
                continue;
            }

            $title = trim((string) ($image['title'] ?? ''));
            $caption = trim((string) ($image['caption'] ?? ''));
            $altText = trim((string) ($image['alt_text'] ?? ''));
            $categoryName = trim((string) ($image['category_name'] ?? ''));
            $photographer = trim((string) ($image['photographer'] ?? ''));
            $tags = array_values(array_filter(array_map('trim', explode(',', (string) ($image['tags'] ?? '')))));
            $displayTitle = $title !== '' ? $title : ($categoryName !== '' ? $categoryName : $filename);
            $displayAlt = $altText !== '' ? $altText : $displayTitle;
            $mainPath = gallery_variant_web_path($filename, 'main');
            $mediumPath = gallery_variant_exists($filename, 'medium') ? gallery_variant_web_path($filename, 'medium') : $mainPath;
            $thumbPath = gallery_variant_exists($filename, 'thumb') ? gallery_variant_web_path($filename, 'thumb') : $mainPath;
            $mainWebpPath = gallery_variant_exists($filename, 'main', 'webp') ? gallery_variant_web_path($filename, 'main', 'webp') : '';
            $mediumWebpPath = gallery_variant_exists($filename, 'medium', 'webp') ? gallery_variant_web_path($filename, 'medium', 'webp') : '';
            $thumbWebpPath = gallery_variant_exists($filename, 'thumb', 'webp') ? gallery_variant_web_path($filename, 'thumb', 'webp') : '';
            $srcset = htmlspecialchars($thumbPath, ENT_QUOTES, 'UTF-8') . ' 480w, ' . htmlspecialchars($mediumPath, ENT_QUOTES, 'UTF-8') . ' 960w, ' . htmlspecialchars($mainPath, ENT_QUOTES, 'UTF-8') . ' 1600w';
            $webpSrcset = $thumbWebpPath !== '' && $mediumWebpPath !== '' && $mainWebpPath !== ''
                ? htmlspecialchars($thumbWebpPath, ENT_QUOTES, 'UTF-8') . ' 480w, ' . htmlspecialchars($mediumWebpPath, ENT_QUOTES, 'UTF-8') . ' 960w, ' . htmlspecialchars($mainWebpPath, ENT_QUOTES, 'UTF-8') . ' 1600w'
                : '';
            $imageWidth = max(0, (int) ($image['width'] ?? 0));
            $imageHeight = max(0, (int) ($image['height'] ?? 0));
            if ($imageWidth <= 0 || $imageHeight <= 0) {
                $sizeSource = gallery_variant_fs_path($filename, 'original');
                if (!is_file($sizeSource)) {
                    $sizeSource = gallery_variant_fs_path($filename, 'main');
                }
                $sizeInfo = is_file($sizeSource) ? @getimagesize($sizeSource) : false;
                if (is_array($sizeInfo)) {
                    $imageWidth = max(1, (int) ($sizeInfo[0] ?? 0));
                    $imageHeight = max(1, (int) ($sizeInfo[1] ?? 0));
                }
            }
            $imageWidth = max(1, $imageWidth);
            $imageHeight = max(1, $imageHeight);
            ?>
            <?php $detailUrl = gallery_build_detail_url((int)($image['id'] ?? 0)); ?>
            <article class="portfolio-item show <?= htmlspecialchars((string) ($image['class'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" data-gallery-item>
                <a
                    href="<?= htmlspecialchars($mainPath, ENT_QUOTES, 'UTF-8') ?>"
                    class="lightbox-trigger"
                    data-index="<?= $index ?>"
                    data-title="<?= htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') ?>"
                    data-caption="<?= htmlspecialchars($caption, ENT_QUOTES, 'UTF-8') ?>"
                    data-category="<?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?>"
                    data-photographer="<?= htmlspecialchars($photographer, ENT_QUOTES, 'UTF-8') ?>"
                    data-tags="<?= htmlspecialchars(implode(', ', $tags), ENT_QUOTES, 'UTF-8') ?>"
                    data-counter="<?= $index + 1 ?>"
                    data-total="<?= count($images) ?>"
                    data-src="<?= htmlspecialchars($mainPath, ENT_QUOTES, 'UTF-8') ?>"
                    data-download="<?= htmlspecialchars($mainPath, ENT_QUOTES, 'UTF-8') ?>"
                    data-alt="<?= htmlspecialchars($displayAlt, ENT_QUOTES, 'UTF-8') ?>"
                    data-width="<?= $imageWidth ?>"
                    data-height="<?= $imageHeight ?>"
                >
                    <picture>
                        <?php if ($webpSrcset !== ''): ?>
                            <source type="image/webp" srcset="<?= $webpSrcset ?>" sizes="(max-width: 575px) 100vw, (max-width: 991px) 50vw, 25vw">
                        <?php endif; ?>
                        <img
                            src="<?= htmlspecialchars($thumbPath, ENT_QUOTES, 'UTF-8') ?>"
                            srcset="<?= $srcset ?>"
                            sizes="(max-width: 575px) 100vw, (max-width: 991px) 50vw, 25vw"
                            alt="<?= htmlspecialchars($displayAlt, ENT_QUOTES, 'UTF-8') ?>"
                            loading="lazy"
                            width="<?= $imageWidth ?>"
                            height="<?= $imageHeight ?>"
                        >
                    </picture>
                </a>
                <div class="gallery-card-meta">
                    <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none">
                        <strong><?= htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') ?></strong>
                    </a>
                    <?php if ($categoryName !== ''): ?>
                        <small><?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?></small>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</div>
</div>
</div>

<?php if ($totalPages > 1): ?>
    <?php
    $baseUrl = gallery_build_url([
        'site' => 'gallery',
        'category' => $categoryFilter,
        'q' => $searchTerm,
    ]);
    $baseUrl = rtrim($baseUrl, '/') . '/';
    echo $tpl->renderPagination($baseUrl, (int)$page, (int)$totalPages);
    ?>
<?php endif; ?>

<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body gallery-lightbox">
                <button type="button" class="btn-close btn-close-white gallery-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <button class="gallery-nav gallery-nav-prev" id="prevBtn" aria-label="<?= htmlspecialchars($languageService->get('pagination_left'), ENT_QUOTES, 'UTF-8') ?>">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <figure class="gallery-lightbox-stage mb-0">
                    <img id="lightboxImage" src="" class="img-fluid rounded" alt="">
                    <figcaption class="gallery-lightbox-caption">
                        <div class="gallery-lightbox-topline">
                            <span id="lightboxCounter"></span>
                            <a id="lightboxDownload" href="#" download class="gallery-download"><?= htmlspecialchars($languageService->get('download'), ENT_QUOTES, 'UTF-8') ?></a>
                        </div>
                        <div id="lightboxMetaRow" class="gallery-lightbox-meta-row">
                            <span id="lightboxTitle" class="gallery-lightbox-meta-item gallery-lightbox-title"></span>
                            <span id="lightboxMeta" class="gallery-lightbox-meta-item"></span>
                            <span id="lightboxTags" class="gallery-lightbox-meta-item"></span>
                        </div>
                        <p id="lightboxCaption" class="mb-0"></p>
                    </figcaption>
                </figure>

                <button class="gallery-nav gallery-nav-next" id="nextBtn" aria-label="<?= htmlspecialchars($languageService->get('pagination_right'), ENT_QUOTES, 'UTF-8') ?>">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/includes/plugins/gallery/js/gallery.js"></script>
