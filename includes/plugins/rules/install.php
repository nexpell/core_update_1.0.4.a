<?php
/*safe_query("CREATE TABLE IF NOT EXISTS plugins_rules (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL DEFAULT '',
  text text NOT NULL,
  date timestamp NOT NULL DEFAULT current_timestamp(),
  userID int(11) NOT NULL DEFAULT 0,
  is_active tinyint(1) NOT NULL DEFAULT 0,
  sort_order int(11) DEFAULT 0,
  PRIMARY KEY (id)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");*/


safe_query("CREATE TABLE IF NOT EXISTS plugins_rules (
  id INT(11) NOT NULL AUTO_INCREMENT,
  content_key VARCHAR(50) NOT NULL,
  language CHAR(2) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  userID int(11) NOT NULL DEFAULT 0,
  is_active tinyint(1) NOT NULL DEFAULT 0,
  sort_order int(11) DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_content_lang (content_key, language),
  KEY idx_content_key (content_key),
  KEY idx_language (language)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci");

## SYSTEM #####################################################################################################################################

## SYSTEM #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'rules', 'admin_rules', 1, 'T-Seven', 'https://www.nexpell.de', 'rules', '', '0.1', 'includes/plugins/rules/', 1, 1, 1, 1, 'activated');
");


## PLUGIN LANG #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO settings_plugins_lang 
        (`content_key`, `language`, `content`, `updated_at`)
    VALUES
        ('plugin_name_rules', 'de', 'Regeln', NOW()),
        ('plugin_name_rules', 'en', 'Rules', NOW()),
        ('plugin_name_rules', 'it', 'Regole', NOW()),

        ('plugin_info_rules', 'de', 'Mit diesem Plugin könnt ihr eure Regeln anzeigen lassen.', NOW()),
        ('plugin_info_rules', 'en', 'With this plugin it is possible to show the rules on the website.', NOW()),
        ('plugin_info_rules', 'it', 'Con questo plugin è possibile mostrare le regole sul sito web.', NOW())
");

## NAVIGATION #####################################################################################################################################

## NAVIGATION DASHBOARD #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO navigation_dashboard_links
        (catID, modulname, url, sort)
    VALUES
        (5, 'rules', 'admincenter.php?site=admin_rules', 1)
");
$linkID = mysqli_insert_id($_database);

safe_query("
    INSERT IGNORE INTO navigation_dashboard_lang
        (`content_key`, `language`, `content`, `updated_at`)
    VALUES
        ('nav_link_{$linkID}', 'de', 'Regeln', NOW()),
        ('nav_link_{$linkID}', 'en', 'Rules', NOW()),
        ('nav_link_{$linkID}', 'it', 'Regole', NOW())
");

## NAVIGATION WEBSITE #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO navigation_website_sub
        (`mnavID`, `modulname`, `url`, `sort`, `indropdown`, `last_modified`)
    VALUES
        (2, 'rules', 'index.php?site=rules', 1, 1, NOW())
");

$snavID = mysqli_insert_id($_database);

safe_query("
    INSERT IGNORE INTO navigation_website_lang
        (`content_key`, `language`, `content`, `updated_at`)
    VALUES
        ('nav_sub_{$snavID}', 'de', 'Regeln', NOW()),
        ('nav_sub_{$snavID}', 'en', 'Rules', NOW()),
        ('nav_sub_{$snavID}', 'it', 'Regole', NOW())
");

#######################################################################################################################################
safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'rules')
");
 ?>
