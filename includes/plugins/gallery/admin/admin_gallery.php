<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\AccessControl;
use nexpell\LanguageService;

global $_database, $languageService;

AccessControl::checkAdminAccess('gallery');

function gallery_admin_ensure_schema(mysqli $database): void
{
    static $done = false;
    if ($done) {
        return;
    }

    $columns = [
        'title' => "ALTER TABLE plugins_gallery ADD COLUMN title VARCHAR(255) NOT NULL DEFAULT '' AFTER filename",
        'caption' => "ALTER TABLE plugins_gallery ADD COLUMN caption TEXT NULL AFTER title",
        'alt_text' => "ALTER TABLE plugins_gallery ADD COLUMN alt_text VARCHAR(255) NOT NULL DEFAULT '' AFTER caption",
        'tags' => "ALTER TABLE plugins_gallery ADD COLUMN tags VARCHAR(255) NOT NULL DEFAULT '' AFTER alt_text",
        'photographer' => "ALTER TABLE plugins_gallery ADD COLUMN photographer VARCHAR(255) NOT NULL DEFAULT '' AFTER tags",
        'width' => "ALTER TABLE plugins_gallery ADD COLUMN width INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER photographer",
        'height' => "ALTER TABLE plugins_gallery ADD COLUMN height INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER width",
        'sort_page' => "ALTER TABLE plugins_gallery ADD COLUMN sort_page INT(10) UNSIGNED NOT NULL DEFAULT 1 AFTER position",
    ];

    foreach ($columns as $name => $sql) {
        $check = $database->query("SHOW COLUMNS FROM plugins_gallery LIKE '" . $database->real_escape_string($name) . "'");
        if ($check instanceof mysqli_result && $check->num_rows === 0) {
            $database->query($sql);
        }
    }

    $done = true;
}

function gallery_admin_base_path(): string
{
    return defined('BASE_PATH') ? rtrim((string) BASE_PATH, '/\\') : dirname(__DIR__, 4);
}

function gallery_admin_upload_url(string $filename, string $variant = 'main', string $format = 'native'): string
{
    $path = '/includes/plugins/gallery/images/upload';
    if ($format === 'webp') {
        $path .= '/webp';
    }
    if ($variant === 'thumb') {
        $path .= '/thumbs';
    } elseif ($variant === 'medium') {
        $path .= '/mediums';
    } elseif ($variant === 'original') {
        $path .= '/originals';
    }
    if ($format === 'webp') {
        $filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
    } elseif ($variant === 'original') {
        $filename = pathinfo($filename, PATHINFO_FILENAME) . '_orig.' . pathinfo($filename, PATHINFO_EXTENSION);
    }
    return $path . '/' . rawurlencode($filename);
}

function gallery_admin_upload_path(string $filename = '', string $variant = 'main', string $format = 'native'): string
{
    $base = gallery_admin_base_path() . str_replace('/', DIRECTORY_SEPARATOR, '/includes/plugins/gallery/images/upload');
    if ($format === 'webp') {
        $base .= DIRECTORY_SEPARATOR . 'webp';
    }
    if ($variant === 'thumb') {
        $base .= DIRECTORY_SEPARATOR . 'thumbs';
    } elseif ($variant === 'medium') {
        $base .= DIRECTORY_SEPARATOR . 'mediums';
    } elseif ($variant === 'original') {
        $base .= DIRECTORY_SEPARATOR . 'originals';
    }
    if ($filename === '') {
        return $base;
    }
    if ($format === 'webp') {
        $filename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
    } elseif ($variant === 'original') {
        $filename = pathinfo($filename, PATHINFO_FILENAME) . '_orig.' . pathinfo($filename, PATHINFO_EXTENSION);
    }
    return $base . DIRECTORY_SEPARATOR . $filename;
}

function gallery_admin_ensure_directories(): void
{
    $dirs = [gallery_admin_upload_path(), gallery_admin_upload_path('', 'thumb'), gallery_admin_upload_path('', 'medium'), gallery_admin_upload_path('', 'original'), gallery_admin_upload_path('', 'main', 'webp'), gallery_admin_upload_path('', 'thumb', 'webp'), gallery_admin_upload_path('', 'medium', 'webp')];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }
}

function gallery_admin_allowed_class(string $class): string
{
    return in_array($class, ['', 'wide', 'tall', 'big'], true) ? $class : '';
}

function gallery_admin_fetch_categories(mysqli $database): array
{
    $items = [];
    $result = $database->query("SELECT id, name FROM plugins_gallery_categories ORDER BY name ASC");
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    return $items;
}

function gallery_admin_category_map(mysqli $database): array
{
    $map = [];
    foreach (gallery_admin_fetch_categories($database) as $row) {
        $map[(int) $row['id']] = $row['name'];
    }
    return $map;
}

function gallery_admin_image_create(string $path, string $mime)
{
    if ($mime === 'image/jpeg') return imagecreatefromjpeg($path);
    if ($mime === 'image/png') return imagecreatefrompng($path);
    if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) return imagecreatefromwebp($path);
    return null;
}

function gallery_admin_image_save($image, string $path, string $mime, int $quality = 85): bool
{
    if ($mime === 'image/jpeg') return imagejpeg($image, $path, $quality);
    if ($mime === 'image/png') return imagepng($image, $path, 6);
    if ($mime === 'image/webp' && function_exists('imagewebp')) return imagewebp($image, $path, $quality);
    return false;
}

function gallery_admin_autorotate(string $path, string $mime): void
{
    if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) return;
    $exif = @exif_read_data($path);
    $orientation = (int) ($exif['Orientation'] ?? 1);
    if ($orientation === 1) return;
    $image = imagecreatefromjpeg($path);
    if (!$image) return;
    if ($orientation === 3) $image = imagerotate($image, 180, 0);
    elseif ($orientation === 6) $image = imagerotate($image, -90, 0);
    elseif ($orientation === 8) $image = imagerotate($image, 90, 0);
    imagejpeg($image, $path, 90);
    imagedestroy($image);
}

function gallery_admin_add_watermark($image, int $width, int $height, string $text): void
{
    $font = gallery_admin_base_path() . str_replace('/', DIRECTORY_SEPARATOR, '/includes/plugins/gallery/images/fonts/OpenSans-Regular.ttf');
    if (!is_file($font) || !function_exists('imagettftext')) return;
    $fontSize = max(12, (int) round(min($width, $height) / 28));
    $padding = max(16, (int) round($fontSize * 0.75));
    $bbox = imagettfbbox($fontSize, 0, $font, $text);
    if (!$bbox) return;
    $textWidth = abs($bbox[2] - $bbox[0]);
    $x = max($padding, $width - $textWidth - $padding);
    $y = max($padding + $fontSize, $height - $padding);
    $color = imagecolorallocatealpha($image, 255, 255, 255, 50);
    imagettftext($image, $fontSize, 0, $x, $y, $color, $font, $text);
}

function gallery_admin_create_variant(string $sourcePath, string $destinationPath, string $mime, int $maxWidth, int $maxHeight, bool $watermark = false, string $watermarkText = ''): bool
{
    $source = gallery_admin_image_create($sourcePath, $mime);
    $info = @getimagesize($sourcePath);
    if (!$source || !$info) return false;
    [$width, $height] = $info;
    $ratio = min($maxWidth / max(1, $width), $maxHeight / max(1, $height), 1);
    $newWidth = max(1, (int) floor($width * $ratio));
    $newHeight = max(1, (int) floor($height * $ratio));
    $canvas = imagecreatetruecolor($newWidth, $newHeight);
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);
    $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
    imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $transparent);
    imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    if ($watermark && $watermarkText !== '') gallery_admin_add_watermark($canvas, $newWidth, $newHeight, $watermarkText);
    $saved = gallery_admin_image_save($canvas, $destinationPath, $mime);
    if ($saved && function_exists('imagewebp')) imagewebp($canvas, preg_replace('/\.[^.]+$/', '.webp', $destinationPath), 82);
    imagedestroy($source);
    imagedestroy($canvas);
    return $saved;
}

function gallery_admin_delete_files(string $filename): void
{
    $paths = [gallery_admin_upload_path($filename), gallery_admin_upload_path($filename, 'thumb'), gallery_admin_upload_path($filename, 'medium'), gallery_admin_upload_path($filename, 'original'), gallery_admin_upload_path($filename, 'main', 'webp'), gallery_admin_upload_path($filename, 'thumb', 'webp'), gallery_admin_upload_path($filename, 'medium', 'webp')];
    foreach ($paths as $path) if (is_file($path)) @unlink($path);
}

gallery_admin_ensure_schema($_database);
gallery_admin_ensure_directories();

$action = (string) ($_GET['action'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$filterClass = gallery_admin_allowed_class((string) ($_GET['filter_class'] ?? ''));
$filterCat = isset($_GET['filter_cat']) && is_numeric($_GET['filter_cat']) ? (int) $_GET['filter_cat'] : 0;
$searchTerm = trim((string) ($_GET['q'] ?? ''));
$sortBy = (string) ($_GET['sort_by'] ?? 'upload_date');
$sortDir = strtolower((string) ($_GET['sort_dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
$perPage = 10;
$allowedSorts = ['upload_date', 'filename', 'title', 'category_id'];
if (!in_array($sortBy, $allowedSorts, true)) $sortBy = 'upload_date';
$settings = mysqli_fetch_assoc(safe_query("SELECT * FROM `settings`"));
$hpTitle = trim((string) ($settings['hptitle'] ?? 'nexpell'));

if ($action === 'delete' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    $filename = '';
    $title = '';
    $stmt = $_database->prepare("SELECT filename, title FROM plugins_gallery WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($filename, $title);
        $stmt->fetch();
        $stmt->close();
    }
    if ($filename !== '') {
        gallery_admin_delete_files($filename);
        $stmt = $_database->prepare("DELETE FROM plugins_gallery WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $ok = $stmt->execute();
            $stmt->close();
            if ($ok) {
                nx_audit_delete('admin_gallery', (string) $id, $title !== '' ? $title : $filename, 'admincenter.php?site=admin_gallery');
                nx_redirect('admincenter.php?site=admin_gallery', 'success', 'alert_deleted', false);
            }
        }
    }
    nx_redirect('admincenter.php?site=admin_gallery', 'danger', 'alert_not_found', false);
}

if ($action === 'add_cat' || $action === 'edit_cat') {
    $isEdit = $action === 'edit_cat';
    $category = ['id' => 0, 'name' => ''];
    if ($isEdit && isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $stmt = $_database->prepare("SELECT id, name FROM plugins_gallery_categories WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && ($row = $result->fetch_assoc())) $category = $row;
            else nx_redirect('admincenter.php?site=admin_gallery', 'danger', 'alert_not_found', false);
            $stmt->close();
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            nx_alert('warning', 'alert_missing_required', false);
        } elseif ($isEdit) {
            $stmt = $_database->prepare("UPDATE plugins_gallery_categories SET name = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('si', $name, $category['id']);
                $stmt->execute();
                $stmt->close();
            }
            nx_audit_update('admin_gallery', (string) $category['id'], true, $name, 'admincenter.php?site=admin_gallery');
            nx_redirect('admincenter.php?site=admin_gallery', 'success', 'alert_saved', false);
        } else {
            $stmt = $_database->prepare("INSERT INTO plugins_gallery_categories (name) VALUES (?)");
            if ($stmt) {
                $stmt->bind_param('s', $name);
                $stmt->execute();
                $newId = (int) $_database->insert_id;
                $stmt->close();
                nx_audit_create('admin_gallery', (string) $newId, $name, 'admincenter.php?site=admin_gallery');
            }
            nx_redirect('admincenter.php?site=admin_gallery', 'success', 'alert_saved', false);
        }
    }
    ?>
    <div class="card shadow-sm mt-4">
        <div class="card-header"><div class="card-title"><i class="bi bi-folder-plus"></i> <?= htmlspecialchars($languageService->get('title_category'), ENT_QUOTES, 'UTF-8') ?></div></div>
        <div class="card-body">
            <form method="post">
                <label for="name" class="form-label"><?= htmlspecialchars($languageService->get('name'), ENT_QUOTES, 'UTF-8') ?></label>
                <input type="text" class="form-control mb-3" id="name" name="name" value="<?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?>" required>
                <button type="submit" class="btn btn-primary"><?= htmlspecialchars($languageService->get('save'), ENT_QUOTES, 'UTF-8') ?></button>
            </form>
        </div>
    </div>
    <?php
    exit;
}

if ($action === 'add' || $action === 'edit') {
    $id = max(0, (int) ($_GET['id'] ?? 0));
    $isEdit = $id > 0;
    $categories = gallery_admin_fetch_categories($_database);
    $data = ['filename' => '', 'class' => '', 'category_id' => 0, 'title' => '', 'caption' => '', 'alt_text' => '', 'tags' => '', 'photographer' => '', 'width' => 0, 'height' => 0];
    if ($isEdit) {
        $stmt = $_database->prepare("SELECT filename, class, category_id, title, caption, alt_text, tags, photographer, width, height FROM plugins_gallery WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && ($row = $result->fetch_assoc())) $data = array_merge($data, $row);
            else nx_redirect('admincenter.php?site=admin_gallery', 'danger', 'alert_not_found', false);
            $stmt->close();
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim((string) ($_POST['title'] ?? ''));
        $caption = trim((string) ($_POST['caption'] ?? ''));
        $altText = trim((string) ($_POST['alt_text'] ?? ''));
        $tags = trim((string) ($_POST['tags'] ?? ''));
        $photographer = trim((string) ($_POST['photographer'] ?? ''));
        $class = gallery_admin_allowed_class((string) ($_POST['class'] ?? ''));
        $categoryId = isset($_POST['category_id']) && is_numeric($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
        $filename = (string) $data['filename'];
        $width = (int) $data['width'];
        $height = (int) $data['height'];
        $errorKey = '';
        if ($categoryId <= 0) $errorKey = 'alert_missing_required';
        if (isset($_FILES['image']) && (int) $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp = (string) $_FILES['image']['tmp_name'];
            $size = (int) ($_FILES['image']['size'] ?? 0);
            $info = @getimagesize($tmp);
            $mime = $info['mime'] ?? '';
            if (!$info || !in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) $errorKey = 'gallery_error_invalid_type';
            elseif ($size > 12 * 1024 * 1024) $errorKey = 'gallery_error_file_too_large';
            else {
                $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $newFilename = uniqid('img_') . '.' . $extMap[$mime];
                $originalPath = gallery_admin_upload_path($newFilename, 'original');
                if (!move_uploaded_file($tmp, $originalPath)) {
                    $errorKey = 'alert_upload_failed';
                } else {
                    gallery_admin_autorotate($originalPath, $mime);
                    $imageInfo = @getimagesize($originalPath);
                    $width = (int) ($imageInfo[0] ?? 0);
                    $height = (int) ($imageInfo[1] ?? 0);
                    $okMain = gallery_admin_create_variant($originalPath, gallery_admin_upload_path($newFilename), $mime, 2200, 2200, true, '© ' . $hpTitle);
                    $okMedium = gallery_admin_create_variant($originalPath, gallery_admin_upload_path($newFilename, 'medium'), $mime, 1280, 1280, false);
                    $okThumb = gallery_admin_create_variant($originalPath, gallery_admin_upload_path($newFilename, 'thumb'), $mime, 640, 640, false);
                    if (!$okMain || !$okMedium || !$okThumb) {
                        gallery_admin_delete_files($newFilename);
                        $errorKey = 'alert_upload_failed';
                    } else {
                        if ($isEdit && $filename !== '') gallery_admin_delete_files($filename);
                        $filename = $newFilename;
                    }
                }
            }
        } elseif (!$isEdit) {
            $errorKey = 'alert_missing_required';
        }
        if ($errorKey === '') {
            if ($isEdit) {
                $stmt = $_database->prepare("UPDATE plugins_gallery SET filename = ?, class = ?, category_id = ?, title = ?, caption = ?, alt_text = ?, tags = ?, photographer = ?, width = ?, height = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param('ssisssssiii', $filename, $class, $categoryId, $title, $caption, $altText, $tags, $photographer, $width, $height, $id);
                    $ok = $stmt->execute();
                    $stmt->close();
                    if ($ok) {
                        nx_audit_update('admin_gallery', (string) $id, true, $title !== '' ? $title : $filename, 'admincenter.php?site=admin_gallery');
                        nx_redirect('admincenter.php?site=admin_gallery', 'success', 'alert_saved', false);
                    }
                }
            } else {
                $stmt = $_database->prepare("INSERT INTO plugins_gallery (filename, title, caption, alt_text, tags, photographer, width, height, class, upload_date, position, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 0, ?)");
                if ($stmt) {
                    $stmt->bind_param('ssssssiisi', $filename, $title, $caption, $altText, $tags, $photographer, $width, $height, $class, $categoryId);
                    $ok = $stmt->execute();
                    $newId = (int) $_database->insert_id;
                    $stmt->close();
                    if ($ok) {
                        nx_audit_create('admin_gallery', (string) $newId, $title !== '' ? $title : $filename, 'admincenter.php?site=admin_gallery');
                        nx_redirect('admincenter.php?site=admin_gallery', 'success', 'alert_saved', false);
                    }
                }
            }
            nx_alert('danger', 'alert_db_error', false);
        } else {
            nx_alert('warning', $errorKey, false);
        }
        $data = ['filename' => $filename, 'class' => $class, 'category_id' => $categoryId, 'title' => $title, 'caption' => $caption, 'alt_text' => $altText, 'tags' => $tags, 'photographer' => $photographer, 'width' => $width, 'height' => $height];
    }
    ?>
    <div class="card shadow-sm mt-4">
        <div class="card-header"><div class="card-title"><i class="bi bi-image"></i> <?= htmlspecialchars($languageService->get('title_gallery'), ENT_QUOTES, 'UTF-8') ?></div></div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6"><label for="category_id" class="form-label"><?= htmlspecialchars($languageService->get('title_category'), ENT_QUOTES, 'UTF-8') ?></label><select class="form-select" name="category_id" id="category_id" required><option value=""><?= htmlspecialchars($languageService->get('select_choose'), ENT_QUOTES, 'UTF-8') ?></option><?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>" <?= (int) $category['id'] === (int) $data['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label for="class" class="form-label"><?= htmlspecialchars($languageService->get('label_layout'), ENT_QUOTES, 'UTF-8') ?></label><select class="form-select" name="class" id="class"><option value="" <?= (string) $data['class'] === '' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('select_standard'), ENT_QUOTES, 'UTF-8') ?></option><option value="wide" <?= (string) $data['class'] === 'wide' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('select_wide'), ENT_QUOTES, 'UTF-8') ?></option><option value="tall" <?= (string) $data['class'] === 'tall' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('select_tall'), ENT_QUOTES, 'UTF-8') ?></option><option value="big" <?= (string) $data['class'] === 'big' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('select_big'), ENT_QUOTES, 'UTF-8') ?></option></select></div>
                    <div class="col-md-6"><label for="title" class="form-label"><?= htmlspecialchars($languageService->get('label_title'), ENT_QUOTES, 'UTF-8') ?></label><input class="form-control" type="text" name="title" id="title" value="<?= htmlspecialchars((string) $data['title'], ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-md-6"><label for="alt_text" class="form-label"><?= htmlspecialchars($languageService->get('label_alt_text'), ENT_QUOTES, 'UTF-8') ?></label><input class="form-control" type="text" name="alt_text" id="alt_text" value="<?= htmlspecialchars((string) $data['alt_text'], ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-md-6"><label for="tags" class="form-label"><?= htmlspecialchars($languageService->get('label_tags'), ENT_QUOTES, 'UTF-8') ?></label><input class="form-control" type="text" name="tags" id="tags" value="<?= htmlspecialchars((string) $data['tags'], ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($languageService->get('tags_placeholder'), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-md-6"><label for="photographer" class="form-label"><?= htmlspecialchars($languageService->get('label_photographer'), ENT_QUOTES, 'UTF-8') ?></label><input class="form-control" type="text" name="photographer" id="photographer" value="<?= htmlspecialchars((string) $data['photographer'], ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-12"><label for="caption" class="form-label"><?= htmlspecialchars($languageService->get('label_caption'), ENT_QUOTES, 'UTF-8') ?></label><textarea class="form-control" name="caption" id="caption" rows="4"><?= htmlspecialchars((string) $data['caption'], ENT_QUOTES, 'UTF-8') ?></textarea></div>
                    <div class="col-12"><label for="image" class="form-label"><?= htmlspecialchars($languageService->get('label_datatypes'), ENT_QUOTES, 'UTF-8') ?></label><input class="form-control" type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" <?= $isEdit ? '' : 'required' ?>><small class="text-muted"><?= htmlspecialchars($languageService->get('upload_hint'), ENT_QUOTES, 'UTF-8') ?></small></div>
                </div>
                <?php if ($isEdit && $data['filename'] !== ''): ?>
                    <div class="row mt-4"><div class="col-lg-4"><img src="<?= htmlspecialchars(gallery_admin_upload_url((string) $data['filename'], 'thumb'), ENT_QUOTES, 'UTF-8') ?>" class="img-thumbnail" alt="<?= htmlspecialchars((string) ($data['title'] ?: $data['filename']), ENT_QUOTES, 'UTF-8') ?>"></div><div class="col-lg-8"><ul class="list-unstyled small"><li><?= htmlspecialchars($languageService->get('li_watermark'), ENT_QUOTES, 'UTF-8') ?>: <a href="<?= htmlspecialchars(gallery_admin_upload_url((string) $data['filename']), ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars(gallery_admin_upload_url((string) $data['filename']), ENT_QUOTES, 'UTF-8') ?></a></li><li><?= htmlspecialchars($languageService->get('li_medium'), ENT_QUOTES, 'UTF-8') ?>: <a href="<?= htmlspecialchars(gallery_admin_upload_url((string) $data['filename'], 'medium'), ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars(gallery_admin_upload_url((string) $data['filename'], 'medium'), ENT_QUOTES, 'UTF-8') ?></a></li><li><?= htmlspecialchars($languageService->get('li_thumbnail'), ENT_QUOTES, 'UTF-8') ?>: <a href="<?= htmlspecialchars(gallery_admin_upload_url((string) $data['filename'], 'thumb'), ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars(gallery_admin_upload_url((string) $data['filename'], 'thumb'), ENT_QUOTES, 'UTF-8') ?></a></li><li><?= htmlspecialchars($languageService->get('li_originial'), ENT_QUOTES, 'UTF-8') ?>: <a href="<?= htmlspecialchars(gallery_admin_upload_url((string) $data['filename'], 'original'), ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars(gallery_admin_upload_url((string) $data['filename'], 'original'), ENT_QUOTES, 'UTF-8') ?></a></li></ul></div></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary mt-4"><?= htmlspecialchars($languageService->get('save'), ENT_QUOTES, 'UTF-8') ?></button>
            </form>
        </div>
    </div>
    <?php
    exit;
}

if ($action === 'sort') {
    $result = $_database->query("SELECT id, filename, class, title, sort_page FROM plugins_gallery ORDER BY sort_page ASC, position ASC, id ASC");
    $allImages = [];
    if ($result instanceof mysqli_result) while ($row = $result->fetch_assoc()) $allImages[] = $row;

    $pages = [];
    foreach ($allImages as $image) {
        $pageNumber = max(1, (int) ($image['sort_page'] ?? 1));
        if (!isset($pages[$pageNumber])) {
            $pages[$pageNumber] = [];
        }
        $pages[$pageNumber][] = $image;
    }

    if (!$pages) {
        $pages[1] = [];
    }

    ksort($pages);
    $nextPage = ((int) array_key_last($pages)) + 1;
    $pages[$nextPage] = [];
    ?>
    <link rel="stylesheet" href="/includes/plugins/gallery/admin/css/admin_gallery.css">
    <div class="card shadow-sm mt-4"><div class="card-header"><div class="card-title"><i class="bi bi-images"></i> <?= htmlspecialchars($languageService->get('sort'), ENT_QUOTES, 'UTF-8') ?></div></div><div class="card-body"><div class="admin-gallery"><div id="sortable-gallery"><?php $displayPage = 1; foreach ($pages as $pageImages): ?><div class="card-site"><h2><?= htmlspecialchars($languageService->get('sort_site'), ENT_QUOTES, 'UTF-8') ?> <?= $displayPage++ ?></h2><div class="grid-wrapper"><?php foreach ($pageImages as $image): ?><div class="sortable-item <?= htmlspecialchars((string) $image['class'], ENT_QUOTES, 'UTF-8') ?>" data-id="<?= (int) $image['id'] ?>"><img src="<?= htmlspecialchars(gallery_admin_upload_url((string) $image['filename'], 'thumb'), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) ($image['title'] ?: $image['filename']), ENT_QUOTES, 'UTF-8') ?>"></div><?php endforeach; ?></div></div><?php endforeach; ?></div></div><div class="d-flex gap-2 justify-content-center flex-wrap mt-3"><a href="admincenter.php?site=admin_gallery" class="btn btn-secondary">Zurueck</a><button id="save-order" disabled class="btn btn-primary"><?= htmlspecialchars($languageService->get('save'), ENT_QUOTES, 'UTF-8') ?></button></div></div></div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script src="/includes/plugins/gallery/admin/js/gallery.js"></script>
    <?php
    exit;
}

$categoryMap = gallery_admin_category_map($_database);
$where = [];
$types = '';
$params = [];
$searchLike = $searchTerm !== '' ? '%' . $searchTerm . '%' : '';
if ($filterClass !== '') { $where[] = 'class = ?'; $types .= 's'; $params[] = $filterClass; }
if ($filterCat > 0) { $where[] = 'category_id = ?'; $types .= 'i'; $params[] = $filterCat; }
if ($searchLike !== '') { $where[] = '(filename LIKE ? OR title LIKE ? OR tags LIKE ? OR photographer LIKE ?)'; $types .= 'ssss'; array_push($params, $searchLike, $searchLike, $searchLike, $searchLike); }
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
$totalItems = 0;
$stmt = $_database->prepare("SELECT COUNT(*) FROM plugins_gallery{$whereSql}");
if ($stmt) { if ($params) $stmt->bind_param($types, ...$params); $stmt->execute(); $stmt->bind_result($totalItems); $stmt->fetch(); $stmt->close(); }
$totalPages = max(1, (int) ceil($totalItems / max(1, $perPage)));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;
$images = [];
$stmt = $_database->prepare("SELECT id, filename, class, upload_date, category_id, title, tags, photographer FROM plugins_gallery{$whereSql} ORDER BY {$sortBy} {$sortDir}, id DESC LIMIT ? OFFSET ?");
if ($stmt) { $queryTypes = $types . 'ii'; $queryParams = array_merge($params, [$perPage, $offset]); $stmt->bind_param($queryTypes, ...$queryParams); $stmt->execute(); $result = $stmt->get_result(); while ($result && ($row = $result->fetch_assoc())) $images[] = $row; $stmt->close(); }
?>
<div class="card shadow-sm mt-4"><div class="card-header d-flex justify-content-between align-items-center"><div class="card-title"><i class="bi bi-images"></i> <?= htmlspecialchars($languageService->get('title_gallery'), ENT_QUOTES, 'UTF-8') ?></div><div class="d-flex gap-2 flex-wrap"><a href="admincenter.php?site=admin_gallery&action=add" class="btn btn-secondary"><?= htmlspecialchars($languageService->get('btn_new_picture'), ENT_QUOTES, 'UTF-8') ?></a><a href="admincenter.php?site=admin_gallery&action=add_cat" class="btn btn-secondary"><?= htmlspecialchars($languageService->get('btn_new_category'), ENT_QUOTES, 'UTF-8') ?></a><a href="admincenter.php?site=admin_gallery&action=sort" class="btn btn-secondary"><?= htmlspecialchars($languageService->get('btn_sort'), ENT_QUOTES, 'UTF-8') ?></a></div></div><div class="card-body"><form method="get" action="admincenter.php" class="row g-3 align-items-end mb-3"><input type="hidden" name="site" value="admin_gallery"><div class="col-md-3"><label for="q" class="form-label"><?= htmlspecialchars($languageService->get('search'), ENT_QUOTES, 'UTF-8') ?></label><input type="search" class="form-control" name="q" id="q" value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($languageService->get('search_placeholder_admin'), ENT_QUOTES, 'UTF-8') ?>"></div><div class="col-md-2"><label for="filter_class" class="form-label"><?= htmlspecialchars($languageService->get('label_class'), ENT_QUOTES, 'UTF-8') ?></label><select class="form-select" name="filter_class" id="filter_class"><option value=""><?= htmlspecialchars($languageService->get('select_all'), ENT_QUOTES, 'UTF-8') ?></option><option value="wide" <?= $filterClass === 'wide' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('select_wide'), ENT_QUOTES, 'UTF-8') ?></option><option value="tall" <?= $filterClass === 'tall' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('select_tall'), ENT_QUOTES, 'UTF-8') ?></option><option value="big" <?= $filterClass === 'big' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('select_big'), ENT_QUOTES, 'UTF-8') ?></option></select></div><div class="col-md-2"><label for="filter_cat" class="form-label"><?= htmlspecialchars($languageService->get('title_category'), ENT_QUOTES, 'UTF-8') ?></label><select class="form-select" name="filter_cat" id="filter_cat"><option value="0"><?= htmlspecialchars($languageService->get('select_all'), ENT_QUOTES, 'UTF-8') ?></option><?php foreach ($categoryMap as $catId => $catName): ?><option value="<?= $catId ?>" <?= $filterCat === $catId ? 'selected' : '' ?>><?= htmlspecialchars((string) $catName, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div><div class="col-md-2"><label for="sort_by" class="form-label"><?= htmlspecialchars($languageService->get('label_sort'), ENT_QUOTES, 'UTF-8') ?></label><select class="form-select" name="sort_by" id="sort_by"><option value="upload_date" <?= $sortBy === 'upload_date' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('label_upload_date'), ENT_QUOTES, 'UTF-8') ?></option><option value="filename" <?= $sortBy === 'filename' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('label_filename'), ENT_QUOTES, 'UTF-8') ?></option><option value="title" <?= $sortBy === 'title' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('label_title'), ENT_QUOTES, 'UTF-8') ?></option><option value="category_id" <?= $sortBy === 'category_id' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('title_category'), ENT_QUOTES, 'UTF-8') ?></option></select></div><div class="col-md-2"><label for="sort_dir" class="form-label"><?= htmlspecialchars($languageService->get('sort_direction'), ENT_QUOTES, 'UTF-8') ?></label><select class="form-select" name="sort_dir" id="sort_dir"><option value="asc" <?= $sortDir === 'asc' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('sort_ascend'), ENT_QUOTES, 'UTF-8') ?></option><option value="desc" <?= $sortDir === 'desc' ? 'selected' : '' ?>><?= htmlspecialchars($languageService->get('sort_descend'), ENT_QUOTES, 'UTF-8') ?></option></select></div><div class="col-md-1"><button class="btn btn-primary w-100" type="submit"><?= htmlspecialchars($languageService->get('search'), ENT_QUOTES, 'UTF-8') ?></button></div></form><div class="table-responsive"><table class="table align-middle gallery-admin-table"><thead><tr><th><?= htmlspecialchars($languageService->get('preview'), ENT_QUOTES, 'UTF-8') ?></th><th><?= htmlspecialchars($languageService->get('label_title'), ENT_QUOTES, 'UTF-8') ?></th><th><?= htmlspecialchars($languageService->get('label_filename'), ENT_QUOTES, 'UTF-8') ?></th><th><?= htmlspecialchars($languageService->get('title_category'), ENT_QUOTES, 'UTF-8') ?></th><th><?= htmlspecialchars($languageService->get('label_layout'), ENT_QUOTES, 'UTF-8') ?></th><th><?= htmlspecialchars($languageService->get('label_photographer'), ENT_QUOTES, 'UTF-8') ?></th><th><?= htmlspecialchars($languageService->get('label_tags'), ENT_QUOTES, 'UTF-8') ?></th><th><?= htmlspecialchars($languageService->get('label_upload_date'), ENT_QUOTES, 'UTF-8') ?></th><th class="gallery-admin-actions-col"><?= htmlspecialchars($languageService->get('actions'), ENT_QUOTES, 'UTF-8') ?></th></tr></thead><tbody><?php if (!$images): ?><tr><td colspan="9" class="text-center"><?= htmlspecialchars($languageService->get('info_no_pictures'), ENT_QUOTES, 'UTF-8') ?></td></tr><?php else: ?><?php foreach ($images as $image): ?><tr><td><img src="<?= htmlspecialchars(gallery_admin_upload_url((string) $image['filename'], 'thumb'), ENT_QUOTES, 'UTF-8') ?>" width="96" class="img-thumbnail" alt="<?= htmlspecialchars((string) (($image['title'] ?: $image['filename'])), ENT_QUOTES, 'UTF-8') ?>"></td><td><?= htmlspecialchars((string) ($image['title'] ?: '-'), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string) $image['filename'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string) ($categoryMap[(int) $image['category_id']] ?? $languageService->get('td_no_category')), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string) ($image['class'] !== '' ? $image['class'] : $languageService->get('select_standard')), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string) ($image['photographer'] ?: '-'), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string) ($image['tags'] ?: '-'), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string) $image['upload_date'], ENT_QUOTES, 'UTF-8') ?></td><td class="gallery-admin-actions-col"><div class="gallery-admin-actions"><a href="admincenter.php?site=admin_gallery&action=edit&id=<?= (int) $image['id'] ?>" class="btn btn-warning btn-sm"><?= htmlspecialchars($languageService->get('edit'), ENT_QUOTES, 'UTF-8') ?></a><a href="admincenter.php?site=admin_gallery&action=delete&id=<?= (int) $image['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?= htmlspecialchars($languageService->get('confirm_delete_image'), ENT_QUOTES, 'UTF-8') ?>');"><?= htmlspecialchars($languageService->get('delete'), ENT_QUOTES, 'UTF-8') ?></a></div></td></tr><?php endforeach; ?><?php endif; ?></tbody></table></div><?php if ($totalPages > 1): ?><nav class="mt-3" aria-label="Pagination"><ul class="pagination justify-content-center mb-0"><?php for ($p = 1; $p <= $totalPages; $p++): ?><?php $query = http_build_query(['site' => 'admin_gallery', 'page' => $p, 'filter_class' => $filterClass, 'filter_cat' => $filterCat, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'q' => $searchTerm]); ?><li class="page-item <?= $p === $page ? 'active' : '' ?>"><a class="page-link" href="admincenter.php?<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a></li><?php endfor; ?></ul></nav><?php endif; ?></div></div>
