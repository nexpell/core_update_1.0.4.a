<!-- Footer Consent -->
<?php
$nxbFooterWidgets = $GLOBALS['nxb_widgets_by_position']['footer'] ?? [];
$nxbIsBuilder = !empty($_GET['builder']) && $_GET['builder'] === '1';
if ($nxbIsBuilder || !empty($nxbFooterWidgets)): ?>
<div class="nx-fixed-block">
  <div class="nx-live-zone nx-zone" data-nx-zone="footer" style="margin:0;padding:0;border:none;">
    <?php if (!empty($nxbFooterWidgets)): ?>
      <?php foreach ($nxbFooterWidgets as $w) echo $w; ?>
    <?php elseif ($nxbIsBuilder): ?>
      <div class="builder-placeholder">Footer hier ablegen</div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
</div>


<!-- Cookie Consent -->
<div id="cookie-overlay" style="display:none;"></div>
<?php require_once BASE_PATH . '/components/cookie/cookie-consent.php'; ?>

<?php
    echo $components_js ?? '';
    echo $theme_js ?? '';
    echo '<!--Plugin & Widget js-->' . PHP_EOL;
    echo $plugin_js ?? '';
    echo '<!--Plugin & Widget js END-->' . PHP_EOL;
?>

<!-- ... dein HTML-Header etc. ... -->

<script defer src="/components/js/nx_editor.js"></script>

<!-- reCAPTCHA Loader -->
<script src="https://www.google.com/recaptcha/api.js?hl=de" async defer></script>


<?php
if (defined('DEBUG_PERFORMANCE') && DEBUG_PERFORMANCE) {
    $userId = $_SESSION['userID'] ?? null; // Session-Variable prüfen

    if ($userId) {
        // mysqli Prepared Statement
        $stmt = $_database->prepare("
            SELECT 1
            FROM user_role_assignments ura
            JOIN user_roles ur ON ura.roleID = ur.roleID
            WHERE ura.userID = ? AND ur.role_name = 'admin'
            LIMIT 1
        ");

        // Parameter binden
        $stmt->bind_param('i', $userId);

        // Ausführen
        $stmt->execute();

        // Ergebnis holen
        $stmt->store_result();
        $isAdmin = $stmt->num_rows > 0;

        if ($isAdmin) {
            include BASE_PATH . '/system/performance_debug.php';
        }

        $stmt->close();
    }
}
?>
</body>
</html>
