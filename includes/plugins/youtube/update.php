<?php

global $_database;

safe_query("CREATE TABLE IF NOT EXISTS plugins_youtube (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  plugin_name varchar(50) NOT NULL,
  setting_key varchar(50) NOT NULL,
  setting_value text NOT NULL,
  is_first tinyint(1) NOT NULL DEFAULT 0,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY plugin_key_unique (plugin_name, setting_key)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

safe_query("CREATE TABLE IF NOT EXISTS plugins_youtube_settings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  plugin_name VARCHAR(50) NOT NULL,
  setting_key VARCHAR(50) NOT NULL,
  setting_value TEXT,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

safe_query("INSERT IGNORE INTO plugins_youtube (id, plugin_name, setting_key, setting_value, is_first, updated_at) VALUES
(2, 'youtube', 'video_1', 'FAfxTvlq87s', 0, '2025-08-23 16:17:11'),
(6, 'youtube', 'video_2', 'PPQeNNvOdis', 0, '2025-08-23 18:25:48'),
(8, 'youtube', 'video_4', 'N6DW31S_oyI', 0, '2025-08-23 17:23:35'),
(9, 'youtube', 'video_5', 'hqQY9UkGC_A', 0, '2025-08-23 15:57:28'),
(10, 'youtube', 'video_6', 'ft4jcPSLJfY', 0, '2025-08-23 18:22:53'),
(11, 'youtube', 'video_7', '8wRW57nBLMI', 0, '2025-08-23 16:55:32'),
(12, 'youtube', 'video_8', 'a0nPjZkxCzQ', 0, '2025-08-23 16:16:04'),
(13, 'youtube', 'video_9', 'C3sW15lSAlM', 0, '2025-08-23 17:39:41'),
(14, 'youtube', 'video_10', 'wTUtBMMLseQ', 0, '2025-08-23 17:40:08'),
(15, 'youtube', 'video_11', 'ahzO3kqxP8Q', 1, '2025-08-23 18:48:50')");

safe_query("INSERT IGNORE INTO plugins_youtube_settings (id, plugin_name, setting_key, setting_value, updated_at) VALUES
(1, 'youtube', 'default_video_id', 'N6DW31S_oyI', '2025-08-23 18:49:00'),
(2, 'youtube', 'videos_per_page', '4', '2025-08-23 18:49:00'),
(3, 'youtube', 'videos_per_page_other', '6', '2025-08-23 18:49:00'),
(4, 'youtube', 'display_mode', 'grid', '2025-08-23 18:49:00'),
(5, 'youtube', 'first_full_width', '1', '2025-08-23 18:49:00')");

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'youtube', 'admin_youtube', 1, 'T-Seven', 'https://www.nexpell.de', 'youtube', '', '1.0.3.3', 'includes/plugins/youtube/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_youtube', 'de', 'Youtube', 'youtube', NOW()),
        ('plugin_name_youtube', 'en', 'Youtube', 'youtube', NOW()),
        ('plugin_name_youtube', 'it', 'Youtube', 'youtube', NOW()),
        ('plugin_info_youtube', 'de', 'Mit diesem Plugin koennt ihr eure Youtube-Videos anzeigen lassen.', 'youtube', NOW()),
        ('plugin_info_youtube', 'en', 'With this plugin you can display your Youtube videos.', 'youtube', NOW()),
        ('plugin_info_youtube', 'it', 'Con questo plugin puoi visualizzare i tuoi video Youtube sul sito web.', 'youtube', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('Youtube', 'youtube', 'With this plugin you can display your Youtube videos.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'youtube', NOW())
    ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        description = VALUES(description),
        version = VALUES(version),
        author = VALUES(author),
        url = VALUES(url),
        folder = VALUES(folder),
        installed_date = NOW()
");

$linkID = 0;
$linkRes = safe_query("
    SELECT linkID FROM navigation_dashboard_links
    WHERE modulname = 'youtube' AND url = 'admincenter.php?site=admin_youtube'
    ORDER BY linkID ASC LIMIT 1
");
if ($linkRes && ($linkRow = mysqli_fetch_assoc($linkRes))) {
    $linkID = (int) ($linkRow['linkID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_links
            (catID, modulname, url, sort)
        VALUES
            (9, 'youtube', 'admincenter.php?site=admin_youtube', 1)
    ");
    $linkID = (int) mysqli_insert_id($_database);
}

if ($linkID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_link_{$linkID}', 'de', 'Youtube', 'youtube', NOW()),
            ('nav_link_{$linkID}', 'en', 'Youtube', 'youtube', NOW()),
            ('nav_link_{$linkID}', 'it', 'Youtube', 'youtube', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'youtube' AND url = 'index.php?site=youtube'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (4, 'youtube', 'index.php?site=youtube', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Youtube', 'youtube', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Youtube', 'youtube', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Youtube', 'youtube', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'youtube')
");
?>
