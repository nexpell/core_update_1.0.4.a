<?php
declare(strict_types=1);

// KEINE session_start()
// KEINE echo
// KEIN HTML

$lock_file = __DIR__ . '/../../system/installed.lock';

return !file_exists($lock_file);
