<?php

global $_database;

safe_query("CREATE TABLE IF NOT EXISTS plugins_sponsors (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  slug varchar(255) DEFAULT NULL,
  logo varchar(255) DEFAULT NULL,
  level enum('Platin Sponsor','Gold Sponsor','Silber Sponsor','Bronze Sponsor','Partner','Unterstuetzer') DEFAULT 'Unterstuetzer',
  description text DEFAULT NULL,
  updated_at timestamp NOT NULL DEFAULT current_timestamp(),
  userID int(11) NOT NULL,
  sort_order int(11) DEFAULT 0,
  is_active tinyint(1) DEFAULT 1,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

safe_query("INSERT IGNORE INTO plugins_sponsors (id, name, slug, logo, level, description, updated_at, userID, sort_order, is_active) VALUES
(1, 'Firma A', 'https://www.nexpell.de', '1.png', 'Platin Sponsor', NULL, '2025-06-01 13:46:22', 1, 1, 1),
(2, 'Firma B', 'https://www.nexpell.de', '2.png', 'Gold Sponsor', NULL, '2025-06-01 13:46:22', 1, 2, 1),
(3, 'Firma C', 'https://www.nexpell.de', '3.png', 'Silber Sponsor', NULL, '2025-06-01 13:46:22', 1, 3, 1),
(4, 'Firma D', 'https://www.nexpell.de', '4.png', 'Bronze Sponsor', NULL, '2025-06-01 13:46:22', 1, 4, 1),
(5, 'Firma E', 'https://www.nexpell.de', '5.png', 'Partner', NULL, '2025-06-01 13:46:22', 1, 5, 1),
(6, 'Firma F', 'https://www.nexpell.de', '6.png', 'Unterstuetzer', NULL, '2025-06-01 13:46:22', 1, 6, 1)");

safe_query("CREATE TABLE IF NOT EXISTS plugins_sponsors_settings (
  sponsorssetID INT(11) NOT NULL AUTO_INCREMENT,
  sponsors INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (sponsorssetID)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

safe_query("INSERT IGNORE INTO plugins_sponsors_settings (sponsorssetID, sponsors) VALUES (1, 5)");

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'sponsors', 'admin_sponsors', 1, 'T-Seven', 'https://www.nexpell.de', 'sponsors', '', '1.0.3.3', 'includes/plugins/sponsors/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_sponsors', 'de', 'Sponsors', 'sponsors', NOW()),
        ('plugin_name_sponsors', 'en', 'Sponsors', 'sponsors', NOW()),
        ('plugin_name_sponsors', 'it', 'Sponsors', 'sponsors', NOW()),
        ('plugin_info_sponsors', 'de', 'Mit diesem Plugin koennt ihr eure Sponsoren anzeigen lassen.', 'sponsors', NOW()),
        ('plugin_info_sponsors', 'en', 'With this plugin you can display your sponsors.', 'sponsors', NOW()),
        ('plugin_info_sponsors', 'it', 'Con questo plugin puoi visualizzare i tuoi sponsor.', 'sponsors', NOW()),
        ('sponsors_headline', 'de', 'Unsere Sponsoren & Partner', 'sponsors', NOW()),
        ('sponsors_headline', 'en', 'Our Sponsors & Partners', 'sponsors', NOW()),
        ('sponsors_headline', 'it', 'I nostri sponsor e partner', 'sponsors', NOW()),
        ('sponsors_intro', 'de', 'Unsere Sponsoren und Partner unterstuetzen nexpell als modernes, modulares Content-Management-System fuer Clans, Vereine und Projekte. Sie tragen dazu bei, dass wir kontinuierlich neue Features entwickeln und die Software frei und offen fuer alle bereitstellen koennen. Vielen Dank fuer eure wertvolle Unterstuetzung!', 'sponsors', NOW()),
        ('sponsors_intro', 'en', 'Our sponsors and partners support nexpell as a modern, modular content management system for clans, clubs, and projects. They help us continuously develop new features and keep the software free and open for everyone. Thank you for your valuable support!', 'sponsors', NOW()),
        ('sponsors_intro', 'it', 'I nostri sponsor e partner supportano nexpell come sistema di gestione dei contenuti moderno e modulare per clan, associazioni e progetti. Ci aiutano a sviluppare continuamente nuove funzionalita e a mantenere il software libero e aperto a tutti. Grazie per il vostro prezioso supporto!', 'sponsors', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('Sponsors', 'sponsors', 'With this plugin you can display your sponsors.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'sponsors', NOW())
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
    WHERE modulname = 'sponsors' AND url = 'admincenter.php?site=admin_sponsors'
    ORDER BY linkID ASC LIMIT 1
");
if ($linkRes && ($linkRow = mysqli_fetch_assoc($linkRes))) {
    $linkID = (int) ($linkRow['linkID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_links
            (catID, modulname, url, sort)
        VALUES
            (13, 'sponsors', 'admincenter.php?site=admin_sponsors', 1)
    ");
    $linkID = (int) mysqli_insert_id($_database);
}

if ($linkID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_link_{$linkID}', 'de', 'Sponsoren', 'sponsors', NOW()),
            ('nav_link_{$linkID}', 'en', 'Sponsors', 'sponsors', NOW()),
            ('nav_link_{$linkID}', 'it', 'Sponsor', 'sponsors', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'sponsors' AND url = 'index.php?site=sponsors'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (5, 'sponsors', 'index.php?site=sponsors', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Sponsoren', 'sponsors', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Sponsors', 'sponsors', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Sponsor', 'sponsors', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'sponsors')
");
?>
