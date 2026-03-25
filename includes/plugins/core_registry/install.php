<?php

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'core_registry', 'admin_core_registry', 1, 'nexpell Team', 'https://www.nexpell.de', '', '', '1.0.0', 'includes/plugins/core_registry/', 1, 1, 0, 1, 'deactivated');
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang 
        (content_key, language, content, updated_at)
    VALUES
        ('plugin_name_core_registry', 'de', 'Core Registry', NOW()),
        ('plugin_name_core_registry', 'en', 'Core Registry', NOW()),
        ('plugin_name_core_registry', 'it', 'Core Registry', NOW()),

        ('plugin_info_core_registry', 'de', 'Verwaltung & Erstellung der Core-Registry (cores.json)', NOW()),
        ('plugin_info_core_registry', 'en', 'Manage and generate Core registry (cores.json)', NOW()),
        ('plugin_info_core_registry', 'it', 'Gestione e creazione del registro Core', NOW())
");

## NAVIGATION #####################################################################################################################################

safe_query("
    INSERT IGNORE INTO navigation_dashboard_links
        (catID, modulname, url, sort)
    VALUES
        (7, 'core_registry', 'admincenter.php?site=admin_core_registry', 5)
");
$linkID = mysqli_insert_id($_database);

safe_query("
    INSERT IGNORE INTO navigation_dashboard_lang
        (content_key, language, content, updated_at)
    VALUES
        ('nav_link_{$linkID}', 'de', 'Core Registry', NOW()),
        ('nav_link_{$linkID}', 'en', 'Core Registry', NOW()),
        ('nav_link_{$linkID}', 'it', 'Registro Core', NOW())
");

#######################################################################################################################################

safe_query("INSERT IGNORE INTO user_role_admin_navi_rights (roleID, type, modulname) VALUES (1,'link','core_registry')");
