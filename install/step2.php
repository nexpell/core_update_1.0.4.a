<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', '1');
error_reporting(E_ALL);

/* =====================================================
   LOCK CHECK
===================================================== */
$lockOk = require __DIR__ . '/system/check_lock.php';
if ($lockOk === false) {
    header('Location: locked.php');
    exit;
}

/* =====================================================
   HEADER / LANGUAGE
===================================================== */
header('Content-Type: text/html; charset=utf-8');

require __DIR__ . '/system/language_handler.php';

$step_path = __DIR__ . "/languages/{$_SESSION['lang']}/step2.php";
if (!file_exists($step_path)) {
    die("Language file missing.");
}

$translations = require $step_path;
$html_lang = htmlspecialchars($_SESSION['lang'], ENT_QUOTES, 'UTF-8');

/* =====================================================
   SYSTEM LIMITS
===================================================== */
set_time_limit(300);
ini_set('memory_limit', '512M');

/* =====================================================
   VARIABLES
===================================================== */
$messages = [];
$redirect = false;
$isInstallationStarted = ($_SERVER['REQUEST_METHOD'] === 'POST');

$updateUrl    = "https://github.com/nexpell/nexpell-core/archive/refs/heads/main.zip";
$tempZipPath  = __DIR__ . "/main.zip";
$extractPath  = __DIR__;
$webrootPath  = dirname(__DIR__);
$extractedDir = __DIR__ . "/nexpell-core-main";

/* =====================================================
   HELPERS
===================================================== */

function addMessage(array &$messages, string $message, string $type = "info", string $icon = "ℹ️"): void {
    $messages[] = [
        'message' => $message,
        'type'    => $type,
        'icon'    => $icon
    ];
}

function downloadFile(string $url): string|false {

    // Versuch 1: file_get_contents
    $data = @file_get_contents($url);
    if ($data !== false) {
        return $data;
    }

    // Versuch 2: cURL Fallback
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    return false;
}

function chmodRecursive(string $path, int $perm = 0777): void {
    if (!file_exists($path)) return;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        @chmod($item->getPathname(), $perm);
    }

    @chmod($path, $perm);
}

function deleteFolder(string $folder): bool {
    if (!is_dir($folder)) return false;

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        $filePath = $file->getPathname();
        $file->isDir() ? @rmdir($filePath) : @unlink($filePath);
    }

    return @rmdir($folder);
}

/* =====================================================
   INSTALL LOGIC
===================================================== */

if ($isInstallationStarted) {

    addMessage($messages, $translations['msg_download_start']);

    $zipData = downloadFile($updateUrl);

    if ($zipData === false) {
        addMessage($messages, $translations['msg_download_error'], "danger", "❌");
        renderTemplateAndExit($messages);
    }

    file_put_contents($tempZipPath, $zipData);

    $zip = new ZipArchive;

    if ($zip->open($tempZipPath) === true) {

        addMessage($messages, $translations['msg_unzip_start']);

        // ZIP Security Check
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (strpos($entry, '..') !== false) {
                $zip->close();
                @unlink($tempZipPath);
                addMessage($messages, $translations['msg_security_zip'], "danger", "⚠️");
                renderTemplateAndExit($messages);
            }
        }

        $zip->extractTo($extractPath);
        $zip->close();
        @unlink($tempZipPath);

        addMessage($messages, $translations['msg_unzip_success']);

        if (is_dir($extractedDir)) {

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($extractedDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {

                $sourcePath   = $item->getPathname();
                $relativePath = substr($sourcePath, strlen($extractedDir) + 1);
                $targetPath   = $webrootPath . '/' . $relativePath;

                if ($item->isDir()) {
                    if (!is_dir($targetPath)) {
                        @mkdir($targetPath, 0777, true);
                    }
                } else {
                    if (!@copy($sourcePath, $targetPath)) {
                        addMessage($messages, $translations['msg_copy_error'] . $relativePath, "danger", "❌");
                    }
                }
            }

            addMessage($messages, $translations['msg_copy_success']);

            chmodRecursive($extractedDir);

            if (deleteFolder($extractedDir)) {
                addMessage($messages, $translations['msg_delete_temp'] . $extractedDir);
            } else {
                addMessage($messages, $translations['msg_delete_temp_error'] . $extractedDir, "warning");
            }

        } else {
            addMessage($messages, $translations['msg_extract_folder_error'] . $extractedDir, "danger");
        }

        addMessage($messages, $translations['msg_update_success'], "success", "✅");
        $redirect = true;

    } else {
        addMessage($messages, $translations['msg_unzip_open_error'], "danger", "❌");
    }
}

renderTemplateAndExit($messages, $redirect);


/* =====================================================
   TEMPLATE
===================================================== */

function renderTemplateAndExit(array $messages, bool $redirect = false): void {

    global $translations, $html_lang, $isInstallationStarted;
    ?>
<!DOCTYPE html>
<html lang="<?= $html_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($translations['step_title']) ?></title>
    <link href="/install/css/bootstrap.min.css" rel="stylesheet">
    <link href="/install/css/installer.css" rel="stylesheet">
    <?php if ($redirect): ?>
        <meta http-equiv="refresh" content="10;url=step3.php">
    <?php endif; ?>
</head>
<body>

<div class="container my-5">
    <div class="text-center">
        <img src="/install/images/logo.png" alt="nexpell Logo" class="install-logo mb-4">
        <h2><?= htmlspecialchars($translations['step_title']) ?></h2>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body text-center">

            <h3><?= htmlspecialchars($translations['installer_title']) ?></h3>

            <?php if (!$isInstallationStarted): ?>

                <p class="text-muted"><?= htmlspecialchars($translations['step_download_info']) ?></p>

                <form method="post" class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <?= htmlspecialchars($translations['button_start_download']) ?>
                    </button>
                </form>

            <?php else: ?>

                <p class="text-muted"><?= htmlspecialchars($translations['step_description']) ?></p>

                <?php foreach ($messages as $msg): ?>
                    <div class="alert alert-<?= htmlspecialchars($msg['type']) ?> text-start">
                        <?= htmlspecialchars($msg['message']) ?>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>

        <div class="card-footer text-center text-muted small">
            &copy; <?= date("Y") ?> nexpell Installer
        </div>
    </div>
</div>

<script src="/install/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
    exit;
}
