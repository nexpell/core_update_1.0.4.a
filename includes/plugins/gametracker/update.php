<?php

global $_database;

safe_query("CREATE TABLE IF NOT EXISTS plugins_gametracker_servers (
  id int(11) NOT NULL AUTO_INCREMENT,
  ip varchar(100) NOT NULL,
  port int(11) NOT NULL,
  query_port int(11) DEFAULT NULL,
  game varchar(50) NOT NULL,
  game_pic varchar(255) NOT NULL,
  active tinyint(1) DEFAULT 1,
  sort_order int(11) DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

safe_query("INSERT IGNORE INTO plugins_gametracker_servers (id, ip, port, query_port, game, game_pic, active, sort_order) VALUES
(1, '85.14.192.114', 28960, NULL, 'coduo', 'uo', 1, 0)");

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'gametracker', 'admin_gametracker', 1, 'T-Seven', 'https://www.nexpell.de', 'gametracker', '', '1.0.3.3', 'includes/plugins/gametracker/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_gametracker', 'de', 'Gametracker', 'gametracker', NOW()),
        ('plugin_name_gametracker', 'en', 'Gametracker', 'gametracker', NOW()),
        ('plugin_name_gametracker', 'it', 'Gametracker', 'gametracker', NOW()),
        ('plugin_info_gametracker', 'de', 'Zeigt Informationen zu deinen Spielservern wie Name, Karte, Spieleranzahl und Status direkt auf deiner Website an.', 'gametracker', NOW()),
        ('plugin_info_gametracker', 'en', 'Displays information about your game servers such as name, map, player count, and status directly on your website.', 'gametracker', NOW()),
        ('plugin_info_gametracker', 'it', 'Mostra le informazioni dei tuoi server di gioco come nome, mappa, numero di giocatori e stato direttamente sul tuo sito web.', 'gametracker', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('GameTracker', 'gametracker', 'Displays information about your game servers such as name, map, player count, and status directly on your website.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'gametracker', NOW())
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
        ('widget_gametracker_sidebar', 'Gametracker Sidebar', 'gametracker', 'gametracker', NULL, 'left,right', 1, '1.0.3.3', NOW())
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
    WHERE modulname = 'gametracker' AND url = 'admincenter.php?site=admin_gametracker'
    ORDER BY linkID ASC LIMIT 1
");
if ($linkRes && ($linkRow = mysqli_fetch_assoc($linkRes))) {
    $linkID = (int) ($linkRow['linkID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_links
            (catID, modulname, url, sort)
        VALUES
            (11, 'gametracker', 'admincenter.php?site=admin_gametracker', 1)
    ");
    $linkID = (int) mysqli_insert_id($_database);
}

if ($linkID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_link_{$linkID}', 'de', 'Spielserver', 'gametracker', NOW()),
            ('nav_link_{$linkID}', 'en', 'Game Servers', 'gametracker', NOW()),
            ('nav_link_{$linkID}', 'it', 'Server di gioco', 'gametracker', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'gametracker' AND url = 'index.php?site=gametracker'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (6, 'gametracker', 'index.php?site=gametracker', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Spielserver', 'gametracker', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Game Servers', 'gametracker', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Server di gioco', 'gametracker', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'gametracker')
");
?>
