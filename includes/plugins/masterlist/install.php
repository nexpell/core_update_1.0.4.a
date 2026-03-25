<?php

global $_database;

## SYSTEM #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'masterlist', '', 1, 'T-Seven', 'https://www.nexpell.de', 'masterlist', '', '1.0.3.3', 'includes/plugins/masterlist/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_masterlist', 'de', 'Masterlist', 'masterlist', NOW()),
        ('plugin_name_masterlist', 'en', 'Masterlist', 'masterlist', NOW()),
        ('plugin_name_masterlist', 'it', 'Masterlist', 'masterlist', NOW()),
        ('plugin_info_masterlist', 'de', 'Mit diesem Plugin koennt ihr eure Masterlist mit Slider und Seite anzeigen lassen.', 'masterlist', NOW()),
        ('plugin_info_masterlist', 'en', 'With this plugin you can display your masterlist with slider and page.', 'masterlist', NOW()),
        ('plugin_info_masterlist', 'it', 'Con questo plugin puoi visualizzare la tua masterlist con slider e pagina.', 'masterlist', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('Masterlist', 'masterlist', 'With this plugin you can display your masterlist with slider and page.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'masterlist', NOW())
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

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'masterlist' AND url = 'index.php?site=masterlist'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (6, 'masterlist', 'index.php?site=masterlist', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Masterliste', 'masterlist', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Game Masterlist', 'masterlist', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Lista giochi', 'masterlist', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

#######################################################################################################################################
safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'masterlist')
");
?>
