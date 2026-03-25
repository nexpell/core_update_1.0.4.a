<?php

global $_database;

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'search', '', 1, 'T-Seven', 'https://www.nexpell.de', 'search', '', '1.0.3.3', 'includes/plugins/search/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_search', 'de', 'Search', 'search', NOW()),
        ('plugin_name_search', 'en', 'Search', 'search', NOW()),
        ('plugin_name_search', 'it', 'Search', 'search', NOW()),
        ('plugin_info_search', 'de', 'Mit diesem Plugin koennt ihr eure Suche auf der Website anzeigen lassen.', 'search', NOW()),
        ('plugin_info_search', 'en', 'With this plugin you can display the search on your website.', 'search', NOW()),
        ('plugin_info_search', 'it', 'Con questo plugin potete mostrare la funzione di ricerca sul vostro sito web.', 'search', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('Suche', 'search', 'With this plugin you can display the search on your website.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'search', NOW())
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
        ('widget_search_sidebar', 'Search Sidebar', 'search', 'search', NULL, '', 1, '1.0.3.3', NOW())
    ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        modulname = VALUES(modulname),
        plugin = VALUES(plugin),
        description = VALUES(description),
        allowed_zones = VALUES(allowed_zones),
        active = VALUES(active),
        version = VALUES(version)
");

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'search' AND url = 'index.php?site=search'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (5, 'search', 'index.php?site=search', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Suche', 'search', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Search', 'search', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Ricerca', 'search', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'search')
");
?>
