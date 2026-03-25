<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once BASE_PATH . '/system/core/theme_runtime_renderer.php';
