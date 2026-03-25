<?php

## SYSTEM #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'lastlogin', 'admin_lastlogin', 1, 'T-Seven', 'https://www.nexpell.de', '', '', '0.1', 'includes/plugins/lastlogin/', 1, 1, 1, 1, 'deactivated');
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (`content_key`, `language`, `content`, `updated_at`)
    VALUES
        ('plugin_name_lastlogin', 'de', 'Lastlogin', NOW()),
        ('plugin_name_lastlogin', 'en', 'Lastlogin', NOW()),
        ('plugin_name_lastlogin', 'it', 'Lastlogin', NOW()),

        ('plugin_info_lastlogin', 'de', 'Mit diesem Plugin ist es möglich die Aktivität der User und Mitglieder zu überprüfen.', NOW()),
        ('plugin_info_lastlogin', 'en', 'With this plugin it is possible to check the activity of the users and members.', NOW()),
        ('plugin_info_lastlogin', 'it', 'Con questo plugin è possibile controllare l''attività degli utenti e dei membri.', NOW())
");

## NAVIGATION #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO navigation_dashboard_links
        (catID, modulname, url, sort)
    VALUES
        (3, 'lastlogin', 'admincenter.php?site=admin_lastlogin', 2)
");
$linkID = mysqli_insert_id($_database);

safe_query("
    INSERT IGNORE INTO navigation_dashboard_lang
        (`content_key`, `language`, `content`, `updated_at`)
    VALUES
        ('nav_link_{$linkID}', 'de', 'Letzte Anmeldung', NOW()),
        ('nav_link_{$linkID}', 'en', 'Last Login', NOW()),
        ('nav_link_{$linkID}', 'it', 'Ultimi Login', NOW())
");

#######################################################################################################################################
safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'lastlogin')
");
 ?>