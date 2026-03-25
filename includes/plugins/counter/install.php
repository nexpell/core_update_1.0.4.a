<?php

global $_database;

## SYSTEM #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'counter', '', 1, 'T-Seven', 'https://www.nexpell.de', 'counter', '', '1.0.3.3', 'includes/plugins/counter/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_counter', 'de', 'Counter', 'counter', NOW()),
        ('plugin_name_counter', 'en', 'Counter', 'counter', NOW()),
        ('plugin_name_counter', 'it', 'Counter', 'counter', NOW()),
        ('plugin_info_counter', 'de', 'Mit diesem Plugin koennt ihr euren Counter und Besucherstatistiken anzeigen lassen.', 'counter', NOW()),
        ('plugin_info_counter', 'en', 'With this plugin you can display your counter and visitor statistics.', 'counter', NOW()),
        ('plugin_info_counter', 'it', 'Con questo plugin puoi visualizzare il contatore e le statistiche dei visitatori.', 'counter', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('Counter', 'counter', 'With this plugin you can display your counter and visitor statistics.', '1.0.3.3', 'T-Seven', 'https://www.nexpell.de', 'counter', NOW())
    ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        description = VALUES(description),
        version = VALUES(version),
        author = VALUES(author),
        url = VALUES(url),
        folder = VALUES(folder),
        installed_date = NOW()
");

## NAVIGATION #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO navigation_website_sub
        (mnavID, modulname, url, sort, indropdown, last_modified)
    VALUES
        (5, 'counter', 'index.php?site=counter', 1, 1, NOW())
");

$snavID = (int) mysqli_insert_id($_database);
if ($snavID <= 0) {
    $snavRes = safe_query("
        SELECT snavID FROM navigation_website_sub
        WHERE modulname = 'counter' AND url = 'index.php?site=counter'
        ORDER BY snavID ASC LIMIT 1
    ");
    if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
        $snavID = (int) ($snavRow['snavID'] ?? 0);
    }
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Counter', 'counter', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Counter', 'counter', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Contatore', 'counter', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

#######################################################################################################################################
safe_query("
    INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
    VALUES ('', 1, 'link', 'counter')
");

?>
