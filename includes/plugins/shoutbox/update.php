<?php

global $_database;

safe_query("CREATE TABLE IF NOT EXISTS plugins_shoutbox_messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  username VARCHAR(50) NOT NULL,
  message TEXT NOT NULL,
  PRIMARY KEY (id),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'shoutbox', 'admin_shoutbox', 1, 'T-Seven', 'https://www.nexpell.de', 'shoutbox', '', '1.0.3.3', 'includes/plugins/shoutbox/', 1, 1, 0, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_shoutbox', 'de', 'Shoutbox', 'shoutbox', NOW()),
        ('plugin_name_shoutbox', 'en', 'Shoutbox', 'shoutbox', NOW()),
        ('plugin_name_shoutbox', 'it', 'Shoutbox', 'shoutbox', NOW()),
        ('plugin_info_shoutbox', 'de', 'Mit diesem Plugin koennt ihr eine Shoutbox auf der Webseite anzeigen lassen.', 'shoutbox', NOW()),
        ('plugin_info_shoutbox', 'en', 'With this plugin you can display a shoutbox on the website.', 'shoutbox', NOW()),
        ('plugin_info_shoutbox', 'it', 'Con questo plugin puoi visualizzare una shoutbox sul sito web.', 'shoutbox', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('Shoutbox', 'shoutbox', 'With this plugin you can display a shoutbox on the website.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'shoutbox', NOW())
    ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        description = VALUES(description),
        version = VALUES(version),
        author = VALUES(author),
        url = VALUES(url),
        folder = VALUES(folder),
        installed_date = NOW()
");

safe_query("
    INSERT IGNORE INTO settings_widgets
        (widget_key, title, modulname, plugin, description, allowed_zones, active, version, created_at)
    VALUES
        ('widget_shoutbox_sidebar', 'Shoutbox Sidebar', 'shoutbox', 'shoutbox', NULL, 'left,right', 1, '1.0.3.3', NOW())
    ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        modulname = VALUES(modulname),
        plugin = VALUES(plugin),
        description = VALUES(description),
        allowed_zones = VALUES(allowed_zones),
        active = VALUES(active),
        version = VALUES(version)
");

$linkID = 0;
$linkRes = safe_query("
    SELECT linkID FROM navigation_dashboard_links
    WHERE modulname = 'shoutbox' AND url = 'admincenter.php?site=admin_shoutbox'
    ORDER BY linkID ASC LIMIT 1
");
if ($linkRes && ($linkRow = mysqli_fetch_assoc($linkRes))) {
    $linkID = (int) ($linkRow['linkID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_links
            (catID, modulname, url, sort)
        VALUES
            (11, 'shoutbox', 'admincenter.php?site=admin_shoutbox', 1)
    ");
    $linkID = (int) mysqli_insert_id($_database);
}

if ($linkID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_link_{$linkID}', 'de', 'Shoutbox', 'shoutbox', NOW()),
            ('nav_link_{$linkID}', 'en', 'Shoutbox', 'shoutbox', NOW()),
            ('nav_link_{$linkID}', 'it', 'Shoutbox', 'shoutbox', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'shoutbox' AND url = 'index.php?site=shoutbox'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (3, 'shoutbox', 'index.php?site=shoutbox', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Shoutbox', 'shoutbox', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Shoutbox', 'shoutbox', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Shoutbox', 'shoutbox', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'shoutbox')
");
?>
