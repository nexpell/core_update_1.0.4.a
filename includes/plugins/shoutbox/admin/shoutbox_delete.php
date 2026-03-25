<?php
/**********************************************************************
 * NEXPELL – SHOUTBOX DELETE (STANDALONE)
 **********************************************************************/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



global $_database;

/* =========================================================
   SECURITY
========================================================= */

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Ungültige Anfrage');
}

// CSRF
if (
    empty($_POST['csrf']) ||
    empty($_SESSION['csrf']) ||
    !hash_equals($_SESSION['csrf'], $_POST['csrf'])
) {
    die('CSRF Fehler');
}

// OPTIONAL: Admin-Check (dringend empfohlen)
if (!isset($_SESSION['userID']) || ($_SESSION['is_admin'] ?? 0) != 1) {
    die('Kein Zugriff');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    die('Ungültige ID');
}

/* =========================================================
   DELETE
========================================================= */
safe_query("
    DELETE FROM plugins_shoutbox_messages
    WHERE id = {$id}
    LIMIT 1
");

/* =========================================================
   REDIRECT (ECHT, SAUBER)
========================================================= */
header('Location: admincenter.php?site=admin_shoutbox');
exit;
