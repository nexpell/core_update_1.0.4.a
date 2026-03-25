<?php
global $str, $modulname, $version, $_database;

$modulname = 'userlist';
$version = '0.1';
$str = 'Userlist';

echo "<div class='card'><div class='card-header'>{$str} Database Update</div><div class='card-body'>";

safe_query("CREATE TABLE IF NOT EXISTS plugins_userlist_settings (
    id int(11) NOT NULL AUTO_INCREMENT,
    users_per_page INT DEFAULT 10,
    users_widget_count INT DEFAULT 5,
    widget_show_online TINYINT(1) DEFAULT 1,
    widget_sort ENUM('lastlogin','registerdate','username') DEFAULT 'lastlogin',
    show_avatars TINYINT(1) DEFAULT 1,
    show_roles TINYINT(1) DEFAULT 1,
    show_website TINYINT(1) DEFAULT 1,
    show_lastlogin TINYINT(1) DEFAULT 1,
    show_online_status TINYINT(1) DEFAULT 1,
    show_registerdate TINYINT(1) DEFAULT 1,
    default_sort ENUM('username','registerdate','lastlogin','is_online','website') DEFAULT 'username',
    default_order ENUM('ASC','DESC') DEFAULT 'ASC',
    enable_search TINYINT(1) DEFAULT 1,
    enable_role_filter TINYINT(1) DEFAULT 1,
    default_role VARCHAR(100) DEFAULT '',
    pagination_style ENUM('simple','full') DEFAULT 'full',
    table_style ENUM('striped','bordered','compact') DEFAULT 'striped',
    avatar_size ENUM('small','medium','large') DEFAULT 'small',
    highlight_online_users TINYINT(1) DEFAULT 1,
PRIMARY KEY (id)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8");

safe_query("INSERT IGNORE INTO plugins_userlist_settings (id, users_per_page, users_widget_count, widget_show_online, widget_sort, show_avatars, show_roles, show_website, show_lastlogin, show_online_status, show_registerdate, default_sort, default_order, enable_search, enable_role_filter, default_role, pagination_style, table_style, avatar_size, highlight_online_users) VALUES
(1, 10, 5, 1, 'lastlogin', 1, 1, 1, 1, 1, 1, 'username', 'ASC', 1, 1, '', 'full', 'striped', 'small', 1)");

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'userlist', 'admin_userlist', 1, 'T-Seven', 'https://www.nexpell.de', 'userlist', '', '0.1', 'includes/plugins/userlist/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_userlist', 'de', 'Userlist', 'userlist', NOW()),
        ('plugin_name_userlist', 'en', 'Userlist', 'userlist', NOW()),
        ('plugin_name_userlist', 'it', 'Userlist', 'userlist', NOW()),
        ('plugin_info_userlist', 'de', 'Mit diesem Plugin koennt ihr eure Registered Users anzeigen lassen.', 'userlist', NOW()),
        ('plugin_info_userlist', 'en', 'With this plugin you can display your registered user.', 'userlist', NOW()),
        ('plugin_info_userlist', 'it', 'Con questo plugin puoi visualizzare la lista dei tuoi utenti registrati.', 'userlist', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("INSERT IGNORE INTO settings_widgets
    (widget_key, title, modulname, plugin, description, allowed_zones, active, version, created_at)
VALUES
    ('widget_lastregistered_sidebar', 'Last Registered Sidebar', 'userlist', 'userlist', NULL, 'left,right', 1, '1.0.0', NOW()),
    ('widget_useronline_sidebar', 'User Online Sidebar', 'userlist', 'userlist', NULL, 'left,right', 1, '1.0.0', NOW()),
    ('widget_memberslist_content', 'User Memberlist', 'userlist', 'userlist', NULL, 'maintop,mainbottom', 1, '1.0.0', NOW())");

$linkID = 0;
$linkRes = safe_query("
    SELECT linkID FROM navigation_dashboard_links
    WHERE modulname = 'userlist' AND url = 'admincenter.php?site=admin_userlist'
    ORDER BY linkID ASC LIMIT 1
");
if ($linkRes && ($linkRow = mysqli_fetch_assoc($linkRes))) {
    $linkID = (int) ($linkRow['linkID'] ?? 0);
} else {
    safe_query("
        INSERT INTO navigation_dashboard_links
            (catID, modulname, url, sort)
        VALUES
            (3, 'userlist', 'admincenter.php?site=admin_userlist', 1)
    ");
    $linkID = (int) mysqli_insert_id($_database);
}
if ($linkID > 0) {
    safe_query("
        INSERT INTO navigation_dashboard_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_link_{$linkID}', 'de', 'Userliste-Einstellungen', 'userlist', NOW()),
            ('nav_link_{$linkID}', 'en', 'User List Settings', 'userlist', NOW()),
            ('nav_link_{$linkID}', 'it', 'Impostazioni elenco utenti', 'userlist', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'userlist' AND url = 'index.php?site=userlist'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (3, 'userlist', 'index.php?site=userlist', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}
if ($snavID > 0) {
    safe_query("
        INSERT INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Mitglieder', 'userlist', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Members', 'userlist', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Membri', 'userlist', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights
    (id, roleID, type, modulname)
  VALUES
    ('', 1, 'link', 'userlist')
");

echo "</div></div>";
?>
