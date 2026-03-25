<?php

global $_database;

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'live_visitor', '', 1, 'T-Seven', 'https://www.nexpell.de', 'live_visitor', '', '1.0.3.3', 'includes/plugins/live_visitor/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_live_visitor', 'de', 'Live Visitor', 'live_visitor', NOW()),
        ('plugin_name_live_visitor', 'en', 'Live Visitor', 'live_visitor', NOW()),
        ('plugin_name_live_visitor', 'it', 'Live Visitor', 'live_visitor', NOW()),
        ('plugin_info_live_visitor', 'de', 'Mit diesem Plugin koennt ihr eure Live-Besucher, Who is Online und Besucherstatistiken anzeigen lassen.', 'live_visitor', NOW()),
        ('plugin_info_live_visitor', 'en', 'With this plugin you can display your live visitors, Who is Online and visitor statistics.', 'live_visitor', NOW()),
        ('plugin_info_live_visitor', 'it', 'Con questo plugin puoi visualizzare i visitatori in tempo reale, Who is Online e le statistiche dei visitatori.', 'live_visitor', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('Live Visitor', 'live_visitor', 'With this plugin you can display your live visitors, Who is Online and visitor statistics.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'live_visitor', NOW())
    ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        description = VALUES(description),
        version = VALUES(version),
        author = VALUES(author),
        url = VALUES(url),
        folder = VALUES(folder),
        installed_date = NOW()
");

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'live_visitor' AND url = 'index.php?site=live_visitor'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (3, 'live_visitor', 'index.php?site=live_visitor', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Live-Besucher', 'live_visitor', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Live Visitors', 'live_visitor', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Visitatori in tempo reale', 'live_visitor', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

safe_query("
    INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
    VALUES ('', 1, 'link', 'live_visitor')
");

?>
