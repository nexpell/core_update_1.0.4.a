<?php
/**
 * NEXPELL – Plugin Registry
 * install.php
 */



/* =========================================================
   SYSTEM: PLUGIN REGISTRIEREN
========================================================= */
safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'plugin_registry', 'admin_plugin_registry', 1, 'nexpell Team', 'https://www.nexpell.de', '', '', '1.0.0', 'includes/plugins/plugin_registry/', 1, 1, 0, 1, 'deactivated');
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang 
        (content_key, language, content, updated_at)
    VALUES
        ('plugin_name_plugin_registry', 'de', 'Plugin Registry', NOW()),
        ('plugin_name_plugin_registry', 'en', 'Plugin Registry', NOW()),
        ('plugin_name_plugin_registry', 'it', 'Plugin Registry', NOW()),

        ('plugin_info_plugin_registry', 'de', 'Verwaltung & Erstellung der Plugin-Registry (plugins.json)', NOW()),
        ('plugin_info_plugin_registry', 'en', 'Manage and generate plugin registry (plugins.json)', NOW()),
        ('plugin_info_plugin_registry', 'it', 'Gestione e creazione del registro plugin', NOW())
");

/* =========================================================
   ADMIN NAVIGATION
========================================================= */
safe_query("
    INSERT IGNORE INTO navigation_dashboard_links
        (catID, modulname, url, sort)
    VALUES
        (7, 'plugin_registry', 'admincenter.php?site=admin_plugin_registry', 4)
");
$linkID = mysqli_insert_id($_database);

safe_query("
    INSERT IGNORE INTO navigation_dashboard_lang
        (content_key, language, content, updated_at)
    VALUES
        ('nav_link_{$linkID}', 'de', 'Plugin Registry', NOW()),
        ('nav_link_{$linkID}', 'en', 'Plugin Registry', NOW()),
        ('nav_link_{$linkID}', 'it', 'Registro Plugin', NOW())
");

/* =========================================================
   ADMIN RECHTE (SUPERADMIN)
========================================================= */
safe_query("
INSERT IGNORE INTO user_role_admin_navi_rights
(
    id,
    roleID,
    type,
    modulname
)
VALUES
(
    '',
    1,
    'link',
    'plugin_registry'
)
");


