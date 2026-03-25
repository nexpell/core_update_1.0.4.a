<?php

global $_database;

safe_query("CREATE TABLE IF NOT EXISTS plugins_teamspeak (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  title varchar(100) NOT NULL,
  host varchar(255) NOT NULL,
  query_port smallint(5) UNSIGNED NOT NULL DEFAULT 10011,
  server_port smallint(5) UNSIGNED NOT NULL DEFAULT 9987,
  query_user varchar(100) NOT NULL,
  query_pass varchar(255) NOT NULL,
  cache_time int(10) UNSIGNED NOT NULL DEFAULT 60,
  show_icons tinyint(1) NOT NULL DEFAULT 1,
  enabled tinyint(1) NOT NULL DEFAULT 1,
  sort_order int(11) NOT NULL DEFAULT 0,
  server_country char(2) DEFAULT NULL,
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

## SYSTEM #######################################################################################################################

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'teamspeak', 'admin_teamspeak', 1, 'T-Seven', 'https://www.nexpell.de', 'teamspeak', '', '1.0.3.3', 'includes/plugins/teamspeak/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_teamspeak', 'de', 'TeamSpeak', 'teamspeak', NOW()),
        ('plugin_name_teamspeak', 'en', 'TeamSpeak', 'teamspeak', NOW()),
        ('plugin_name_teamspeak', 'it', 'TeamSpeak', 'teamspeak', NOW()),
        ('plugin_info_teamspeak', 'de', 'Zeigt einen TeamSpeak-Server mit Channels und Usern an.', 'teamspeak', NOW()),
        ('plugin_info_teamspeak', 'en', 'Displays a TeamSpeak server with channels and users.', 'teamspeak', NOW()),
        ('plugin_info_teamspeak', 'it', 'Mostra un server TeamSpeak con canali e utenti.', 'teamspeak', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('TeamSpeak', 'teamspeak', 'Displays a TeamSpeak server with channels and users.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'teamspeak', NOW())
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
        ('widget_teamspeak', 'Teamspeak Sidebar Widget', 'teamspeak', 'teamspeak', NULL, 'left,right', 1, '1.0.3.3', NOW()),
        ('widget_teamspeak_small', 'Teamspeak Sidebar Small Widget', 'teamspeak', 'teamspeak', NULL, 'left,right', 1, '1.0.3.3', NOW())
    ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        modulname = VALUES(modulname),
        plugin = VALUES(plugin),
        description = VALUES(description),
        allowed_zones = VALUES(allowed_zones),
        active = VALUES(active),
        version = VALUES(version)
");

## NAVIGATION ###################################################################################################################

$linkID = 0;
$linkRes = safe_query("
    SELECT linkID FROM navigation_dashboard_links
    WHERE modulname = 'teamspeak' AND url = 'admincenter.php?site=admin_teamspeak'
    ORDER BY linkID ASC LIMIT 1
");
if ($linkRes && ($linkRow = mysqli_fetch_assoc($linkRes))) {
    $linkID = (int) ($linkRow['linkID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_links
            (catID, modulname, url, sort)
        VALUES
            (11, 'teamspeak', 'admincenter.php?site=admin_teamspeak', 1)
    ");
    $linkID = (int) mysqli_insert_id($_database);
}

if ($linkID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_link_{$linkID}', 'de', 'TeamSpeak', 'teamspeak', NOW()),
            ('nav_link_{$linkID}', 'en', 'TeamSpeak', 'teamspeak', NOW()),
            ('nav_link_{$linkID}', 'it', 'TeamSpeak', 'teamspeak', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'teamspeak' AND url = 'index.php?site=teamspeak'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (6, 'teamspeak', 'index.php?site=teamspeak', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'TeamSpeak', 'teamspeak', NOW()),
            ('nav_sub_{$snavID}', 'en', 'TeamSpeak', 'teamspeak', NOW()),
            ('nav_sub_{$snavID}', 'it', 'TeamSpeak', 'teamspeak', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

## PERMISSIONS #################################################################################################################

safe_query("INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname) VALUES
('', 1, 'link', 'teamspeak')");

?>
