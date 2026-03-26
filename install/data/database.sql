


CREATE TABLE IF NOT EXISTS `backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `filename` text NOT NULL,
  `description` text,
  `createdby` int(11) NOT NULL DEFAULT '0',
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `banned_ips` (
  `banID` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `deltime` datetime NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`banID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `captcha` (
  `hash` VARCHAR(255) NOT NULL,
  `captcha` INT(11) NOT NULL DEFAULT '0',
  `deltime` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS comments (
  commentID INT(11) NOT NULL AUTO_INCREMENT,
  plugin VARCHAR(50) NOT NULL,
  itemID INT(11) NOT NULL,
  userID INT(11) NOT NULL,
  comment TEXT NOT NULL,
  date DATETIME NOT NULL DEFAULT current_timestamp(),
  parentID INT(11) DEFAULT 0,
  modulname varchar(100) NOT NULL,
  PRIMARY KEY (commentID),
  KEY plugin_item (plugin, itemID),
  KEY userID (userID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




CREATE TABLE IF NOT EXISTS `contact` (
  `contactID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `email` (
  `emailID` int(1) NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `debug` int(1) NOT NULL,
  `auth` int(1) NOT NULL,
  `html` int(1) NOT NULL,
  `smtp` int(1) NOT NULL,
  `secure` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `email` (`emailID`, `user`, `password`, `host`, `port`, `debug`, `auth`, `html`, `smtp`, `secure`) 
VALUES (1, '', '', '', 25, 0, 0, 1, 0, 0);


CREATE TABLE IF NOT EXISTS `failed_login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('failed','blocked') DEFAULT 'failed',
  `reason` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS link_clicks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plugin VARCHAR(50),
    itemID INT,
    url TEXT,
    clicked_at DATETIME,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `navigation_dashboard_categories` (
  `catID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `modulname` varchar(255) NOT NULL,
  `fa_name` varchar(255) NOT NULL DEFAULT '',
  `sort_art` int(11) DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`catID`),
  UNIQUE KEY `modulname` (`modulname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `navigation_dashboard_categories` (`catID`, `name`, `modulname`, `fa_name`, `sort_art`, `sort`)
VALUES (1, '[[lang:de]]System & Einstellungen[[lang:en]]System & Settings[[lang:it]]Sistema e Impostazioni', 'cat_system', 'bi bi-gear', 0, 1),
(2, '[[lang:de]]Statistiken[[lang:en]]Statistics[[lang:it]]Statistiche', 'cat_statistics', 'bi bi-bar-chart-line', 0, 2),
(3, '[[lang:de]]Benutzer & Rollen[[lang:en]]Users & Roles[[lang:it]]Utenti e Ruoli', 'cat_users', 'bi bi-person', 0, 3),
(4, '[[lang:de]]Sicherheit[[lang:en]]Security[[lang:it]]Sicurezza', 'cat_security', 'bi bi-shield-lock', 0, 4),
(5, '[[lang:de]]Teamverwaltung[[lang:en]]Team Management[[lang:it]]Gestione Team', 'cat_team', 'bi bi-people', 0, 5),
(6, '[[lang:de]]Design & Layout[[lang:en]]Design & Layout[[lang:it]]Design e Layout', 'cat_design', 'bi bi-layout-text-window-reverse', 0, 6),
(7, '[[lang:de]]Plugins & Erweiterungen[[lang:en]]Plugins & Extensions[[lang:it]]Plugin ed Estensioni', 'cat_plugins', 'bi bi-puzzle', 0, 7),
(8, '[[lang:de]]Webinhalte[[lang:en]]Website Content[[lang:it]]Contenuti Web', 'cat_content', 'bi bi-card-checklist', 0, 8),
(9, '[[lang:de]]Medien & Projekte[[lang:en]]Media & Projects[[lang:it]]Media e Progetti', 'cat_media', 'bi bi-image', 0, 9),
(10, '[[lang:de]]Header & Slider[[lang:en]]Header & Slider[[lang:it]]Header e Slider', 'cat_slider_header', 'bi bi-fast-forward-btn', 0, 10),
(11, '[[lang:de]]Game & Voice Tools[[lang:en]]Game & Voice Tools[[lang:it]]Game e Voice Tools', 'cat_tools_game', 'bi bi-controller', 0, 11),
(12, '[[lang:de]]Social Media[[lang:en]]Social Media[[lang:it]]Social Media', 'cat_social', 'bi bi-steam', 0, 12),
(13, '[[lang:de]]Downloads & Partner[[lang:en]]Downloads & Partners[[lang:it]]Download e Sponsor', 'cat_partners', 'bi bi-link', 0, 13);



CREATE TABLE IF NOT EXISTS `navigation_dashboard_links` (
  `linkID` int(11) NOT NULL AUTO_INCREMENT,
  `catID` int(11) NOT NULL DEFAULT 0,
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`linkID`),
  UNIQUE KEY `unique_modulname` (`modulname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
INSERT INTO `navigation_dashboard_links` (`catID`, `modulname`, `name`, `url`, `sort`)
VALUES
(1, 'ac_overview', '[[lang:de]]Webserver-Info[[lang:en]]Webserver Info[[lang:it]]Informazioni Sul Sito', 'admincenter.php?site=overview', 1),
(1, 'ac_settings', '[[lang:de]]Allgemeine Einstellungen[[lang:en]]General Settings[[lang:it]]Impostazioni Generali', 'admincenter.php?site=settings', 2),
(1, 'ac_dashboard_navigation', '[[lang:de]]Admincenter Navigation[[lang:en]]Admincenter Navigation[[lang:it]]Menu Navigazione Admin', 'admincenter.php?site=dashboard_navigation', 3),
(1, 'ac_email', '[[lang:de]]E-Mail[[lang:en]]E-Mail[[lang:it]]E-Mail', 'admincenter.php?site=email', 4),
(1, 'ac_contact', '[[lang:de]]Kontakte[[lang:en]]Contacts[[lang:it]]Contatti', 'admincenter.php?site=contact', 5),
(1, 'ac_database', '[[lang:de]]Datenbank[[lang:en]]Database[[lang:it]]Database', 'admincenter.php?site=database', 6),
(1, 'ac_languages', '[[lang:de]]Sprachen verwalten[[lang:en]]Manage Languages[[lang:it]]Gestisci lingue', 'admincenter.php?site=languages', 7),
(1, 'ac_editlang', '[[lang:de]]Spracheditor[[lang:en]]Language Editor[[lang:it]]Editor di Linguaggi', 'admincenter.php?site=editlang', 8),
(1, 'ac_seo_meta', '[[lang:de]]SEO-Metadaten[[lang:en]]SEO Metadata[[lang:it]]Metadati SEO', 'admincenter.php?site=seo_meta', 9),
(1, 'ac_update_core', '[[lang:de]]Core aktualisieren[[lang:en]]Update Core[[lang:it]]Aggiorna Core', 'admincenter.php?site=update_core', 10),
(2, 'ac_statistic', '[[lang:de]]Seiten Statistiken[[lang:en]]Page Statistics[[lang:it]]Pagina delle Statistiche', 'admincenter.php?site=statistic', 1),
(2, 'ac_visitor_statistic', '[[lang:de]]Besucher Statistiken[[lang:en]]Visitor Statistics[[lang:it]]Statistiche Visitatori', 'admincenter.php?site=visitor_statistic', 2),
(2, 'ac_db_stats', '[[lang:de]]Besucher / Seitenzugriffe[[lang:en]]Visitors / Pageviews[[lang:it]]Visitatori / Visualizzazioni di pagina', 'admincenter.php?site=db_stats', 3),
(3, 'ac_user_roles', '[[lang:de]]Registrierte Benutzer und Rollen[[lang:en]]Registered Users and Roles[[lang:it]]Utenti registrati e ruoli', 'admincenter.php?site=user_roles', 1),
(4, 'ac_security_overview', '[[lang:de]]Admin Security[[lang:en]]Admin Security[[lang:it]]Sicurezza Admin', 'admincenter.php?site=security_overview', 1),
(4, 'ac_log_viewer', '[[lang:de]]Zugriffsprotokoll[[lang:en]]Access Log Viewer[[lang:it]]Visualizzatore Log di Accesso', 'admincenter.php?site=log_viewer', 1),
(6, 'ac_webside_navigation', '[[lang:de]]Webseiten Navigation[[lang:en]]Website Navigation[[lang:it]]Menu Navigazione Web', 'admincenter.php?site=webside_navigation', 1),
(6, 'ac_stylesheet', '[[lang:de]]Stylesheet bearbeiten[[lang:en]]Edit stylesheet[[lang:it]]Modifica stylesheet', 'admincenter.php?site=edit_stylesheet', 2),
(6, 'ac_headstyle', '[[lang:de]]Kopfzeilen-Stil[[lang:en]]Head Style[[lang:it]]Stile intestazione', 'admincenter.php?site=headstyle', 3),
(6, 'ac_startpage', '[[lang:de]]Startseite[[lang:en]]Start Page[[lang:it]]Pagina Principale', 'admincenter.php?site=settings_startpage', 4),
(6, 'ac_static', '[[lang:de]]Statische Seiten[[lang:en]]Static Pages[[lang:it]]Pagine Statiche', 'admincenter.php?site=settings_static', 5),
(6, 'ac_imprint', '[[lang:de]]Impressum[[lang:en]]Imprint[[lang:it]]Impronta Editoriale', 'admincenter.php?site=settings_imprint', 6),
(6, 'ac_privacy_policy', '[[lang:de]]Datenschutz-Bestimmungen[[lang:en]]Privacy Policy[[lang:it]]Informativa sulla Privacy', 'admincenter.php?site=settings_privacy_policy', 7),
(7, 'ac_plugin_manager', '[[lang:de]]Plugin Manager[[lang:en]]PluginManager[[lang:it]]Gestore di Plugin', 'admincenter.php?site=plugin_manager', 1),
(7, 'ac_plugin_widgets_setting', '[[lang:de]]Widgets verwalten[[lang:en]]Manage widgets[[lang:it]]Gestire i widget', 'admincenter.php?site=plugin_widgets_setting', 2),
(7, 'ac_plugin_installer', '[[lang:de]]Plugin Installer[[lang:en]]Plugin Installer[[lang:it]]Installazione Plugin', 'admincenter.php?site=plugin_installer', 3);















CREATE TABLE IF NOT EXISTS `navigation_website_main` (
  `mnavID` int(11) NOT NULL AUTO_INCREMENT,
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '#',
  `default` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(11) NOT NULL DEFAULT 0,
  `isdropdown` tinyint(1) NOT NULL DEFAULT 0,
  `windows` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`mnavID`),
  UNIQUE KEY `unique_modulname` (`modulname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `navigation_website_main` 
(`mnavID`, `modulname`, `name`, `url`, `default`, `sort`, `isdropdown`, `windows`)
VALUES 
(1, 'nav_home',      '[[lang:de]]Aktuelles[[lang:en]]News[[lang:it]]Notizie', '#', 1, 1, 1, 1),
(2, 'nav_about',     '[[lang:de]]Über uns[[lang:en]]About Us[[lang:it]]Chi siamo', '#', 1, 2, 1, 1),
(3, 'nav_community', '[[lang:de]]COMMUNITY[[lang:en]]COMMUNITY[[lang:it]]COMMUNITY', '#', 1, 3, 1, 1),
(4, 'nav_media',     '[[lang:de]]MEDIEN[[lang:en]]MEDIA[[lang:it]]MEDIA', '#', 1, 4, 1, 1),
(5, 'nav_service',   '[[lang:de]]Service[[lang:en]]Service[[lang:it]]Servizio', '#', 1, 5, 1, 1),
(6, 'nav_network',   '[[lang:de]]Netzwerk[[lang:en]]Network[[lang:it]]Rete', '#', 1, 6, 1, 1);



CREATE TABLE IF NOT EXISTS `navigation_website_sub` (
  `snavID` int(11) NOT NULL AUTO_INCREMENT,
  `mnavID` int(11) NOT NULL DEFAULT 0,
  `modulname` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '#',
  `sort` int(11) NOT NULL DEFAULT 0,
  `indropdown` tinyint(1) NOT NULL DEFAULT 1,
  `last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`snavID`),
  UNIQUE KEY `unique_modulname` (`modulname`),
  KEY `idx_mnavID` (`mnavID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `plugins_footer_easy` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `link_number` tinyint(1) NOT NULL COMMENT '1–5',
  `copyright_link_name` varchar(255) NOT NULL DEFAULT '',
  `copyright_link` varchar(255) NOT NULL DEFAULT '',
  `new_tab` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_number` (`link_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `plugins_footer_easy` (`link_number`, `copyright_link_name`, `copyright_link`, `new_tab`)
VALUES (1, '[[lang:de]]Impressum[[lang:en]]Imprint[[lang:it]]Impronta Editoriale', 'index.php?site=imprint', 0),
(2, '[[lang:de]]Datenschutz[[lang:en]]Privacy Policy[[lang:it]]Informativa sulla Privacy', 'index.php?site=privacy_policy', 0),
(3, '[[lang:de]]Kontakt[[lang:en]]Contact[[lang:it]]Contatti', 'index.php?site=contact', 0),
(4, '', '', 0),
(5, '', '', 0);



CREATE TABLE IF NOT EXISTS ratings (
  ratingID INT(11) NOT NULL AUTO_INCREMENT,
  plugin VARCHAR(50) NOT NULL,
  itemID INT(11) NOT NULL,
  userID INT(11) NOT NULL,
  rating TINYINT(4) NOT NULL,
  date DATETIME NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (ratingID),
  UNIQUE KEY unique_vote (plugin, itemID, userID),
  KEY userID (userID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `settings` (
  `settingID` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `hptitle` VARCHAR(255) NOT NULL,
  `hpurl` VARCHAR(255) NOT NULL,
  `clanname` VARCHAR(255) NOT NULL,
  `clantag` VARCHAR(255) NOT NULL,
  `adminname` VARCHAR(255) NOT NULL,
  `adminemail` VARCHAR(255) NOT NULL CHECK (`adminemail` LIKE '%@%'),
  `since` YEAR NOT NULL DEFAULT '2025',
  `webkey` VARCHAR(255) NOT NULL DEFAULT 'PLACEHOLDER_WEBKEY',
  `seckey` VARCHAR(255) NOT NULL DEFAULT 'PLACEHOLDER_SECKEY',
  `closed` TINYINT(1) NOT NULL DEFAULT 0,
  `default_language` VARCHAR(5) NOT NULL DEFAULT 'de',
  `startpage` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `settings_headstyle_config` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `selected_style` VARCHAR(64) NOT NULL DEFAULT 'head-style-1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT IGNORE INTO `settings_headstyle_config` (`id`, `selected_style`)
VALUES (1, 'head-boxes-1');



CREATE TABLE IF NOT EXISTS `settings_imprint` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `represented_by` varchar(255) NOT NULL,
  `tax_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `disclaimer` text DEFAULT NULL,
  `address` varchar(255) DEFAULT '',
  `postal_code` varchar(20) DEFAULT '',
  `city` varchar(100) DEFAULT '',
  `register_office` varchar(100) DEFAULT '',
  `register_number` varchar(100) DEFAULT '',
  `vat_id` varchar(50) DEFAULT '',
  `supervisory_authority` varchar(255) DEFAULT '',
  `editor` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `settings_languages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `iso_639_1` char(2) NOT NULL COMMENT 'ISO 639-1 language code, z.B. \"en\"',
  `iso_639_2` char(3) DEFAULT NULL COMMENT 'Optional ISO 639-2 code, z.B. \"eng\"',
  `name_en` varchar(100) NOT NULL COMMENT 'Language name in English, z.B. \"English\"',
  `name_native` varchar(100) DEFAULT NULL COMMENT 'Native language name, z.B. \"Deutsch\"',
  `name_de` varchar(100) DEFAULT NULL COMMENT 'Language name in German, z.B. \"Deutsch\"',
  `active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Is the language active for selection',
  `flag` varchar(255) DEFAULT NULL COMMENT 'Pfad oder CSS-Klasse für Flagge, z.B. \"/admin/images/flags/de.svg\" oder \"fi fi-de\"',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iso_639_1` (`iso_639_1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `settings_languages` (`id`, `iso_639_1`, `iso_639_2`, `name_en`, `name_native`, `name_de`, `active`, `flag`, `created_at`, `updated_at`)
VALUES 
(1, 'en', 'eng', 'English', 'English', 'Englisch', 1, '/admin/images/flags/gb.svg', NOW(), NULL),
(2, 'de', 'deu', 'German', 'Deutsch', 'Deutsch', 1, '/admin/images/flags/de.svg', NOW(), NULL),
(3, 'it', 'ita', 'Italian', 'Italiano', 'Italienisch', 1, '/admin/images/flags/it.svg', NOW(), NULL),
(4, 'fr', 'fra', 'French', 'Français', 'Französisch', 0, '/admin/images/flags/fr.svg', NOW(), NULL),
(5, 'es', 'spa', 'Spanish', 'Español', 'Spanisch', 0, '/admin/images/flags/es.svg', NOW(), NULL),
(6, 'pt', 'por', 'Portuguese', 'Português', 'Portugiesisch', 0, '/admin/images/flags/pt.svg', NOW(), NULL),
(7, 'pl', 'pol', 'Polish', 'Polski', 'Polnisch', 0, '/admin/images/flags/pl.svg', NOW(), NULL),
(8, 'tr', 'tur', 'Turkish', 'Türkçe', 'Türkisch', 0, '/admin/images/flags/tr.svg', NOW(), NULL);



CREATE TABLE IF NOT EXISTS `settings_plugins` (
  `pluginID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `modulname` VARCHAR(100) NOT NULL,
  `info` TEXT NOT NULL,
  `admin_file` VARCHAR(255) DEFAULT NULL,
  `activate` TINYINT(1) NOT NULL DEFAULT 1,
  `author` VARCHAR(200) DEFAULT NULL,
  `website` VARCHAR(200) DEFAULT NULL,
  `index_link` VARCHAR(255) DEFAULT NULL,
  `hiddenfiles` TEXT DEFAULT NULL,
  `version` VARCHAR(20) DEFAULT '1.0',
  `path` VARCHAR(255) NOT NULL,
  `status_display` TINYINT(1) NOT NULL DEFAULT 1,
  `plugin_display` TINYINT(1) NOT NULL DEFAULT 1,
  `widget_display` TINYINT(1) NOT NULL DEFAULT 0,
  `delete_display` TINYINT(1) NOT NULL DEFAULT 1,
  `sidebar` ENUM('deactivated','activated','full_activated') NOT NULL DEFAULT 'deactivated',
  UNIQUE KEY `unique_modulname` (`modulname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `settings_plugins` (`pluginID`, `name`, `modulname`, `info`, `admin_file`, `activate`, `author`, `website`, `index_link`, `hiddenfiles`, `version`, `path`, `status_display`, `plugin_display`, `widget_display`, `delete_display`, `sidebar`)
VALUES (1, 'Startpage', 'startpage', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', '', '', '', '', 0, 0, 1, 0, 'full_activated'),
(2, 'Privacy Policy', 'privacy_policy', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'privacy_policy', '', '', '', 0, 0, 1, 0, 'deactivated'),
(3, 'Imprint', 'imprint', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'imprint', '', '', '', 0, 0, 1, 0, 'deactivated'),
(4, 'Static', 'static', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'static', '', '', '', 0, 0, 1, 0, 'deactivated'),
(5, 'Error_404', 'error_404', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'error_404', '', '', '', 0, 0, 1, 0, 'deactivated'),
(6, 'Profile', 'profile', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'profile', '', '', '', 0, 0, 1, 0, 'deactivated'),
(7, 'Login', 'login', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'login', '', '', '', 0, 0, 1, 0, 'deactivated'),
(8, 'Lost Password', 'lostpassword', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'lostpassword', '', '', '', 0, 0, 1, 0, 'deactivated'),
(9, 'Contact', 'contact', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'contact', '', '', '', 0, 0, 1, 0, 'deactivated'),
(10, 'Register', 'register', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'register', '', '', '', 0, 0, 1, 0, 'deactivated'),
(11, 'Edit Profile', 'edit_profile', '[[lang:de]]Kein Plugin. Bestandteil vom System!!![[lang:en]]No plugin. Part of the system!!![[lang:it]]Nessun plug-in. Parte del sistema!!!', '', 1, '', '', 'edit_profile,edit_profile_save', '', '', '', 0, 1, 1, 0, 'deactivated'),
(12, 'Navigation', 'navigation_removed', '[[lang:de]]Legacy entfernt[[lang:en]]Legacy removed[[lang:it]]Legacy rimosso', '', 0, '', '', '', '', '', '', 0, 0, 0, 0, 'deactivated'),
(13, 'Footer Easy', 'footer_easy_removed', '[[lang:de]]Legacy entfernt[[lang:en]]Legacy removed[[lang:it]]Legacy rimosso', '', 0, '', '', '', '', '', '', 0, 0, 0, 0, 'deactivated');



CREATE TABLE IF NOT EXISTS `settings_plugins_installed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `modulname` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `folder` varchar(255) DEFAULT NULL,
  `installed_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `settings_privacy_policy` (
  `privacy_policyID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `date` DATETIME NOT NULL,
  `privacy_policy_text` mediumtext NOT NULL,
  `editor` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `settings_privacy_policy` (`privacy_policyID`, `date`, `privacy_policy_text`, `editor`)
VALUES (1, NOW(), '', 1);




       
        
CREATE TABLE IF NOT EXISTS `settings_seo_meta` (
  `site` varchar(64) NOT NULL,
  `language` varchar(8) NOT NULL DEFAULT 'de',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`site`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        

INSERT INTO `settings_seo_meta` (`site`, `language`, `title`, `description`) VALUES
('about', 'de', 'Über uns – Das Team hinter Nexpell', 'Lerne das Team und die Geschichte von Nexpell kennen. Ein modernes Open-Source-CMS für Gamer.'),
('about', 'en', 'About Us – The Team Behind Nexpell', 'Get to know the team and story behind Nexpell. A modern open-source CMS for gamers.'),
('about', 'it', 'Chi siamo – Il team dietro Nexpell', 'Scopri il team e la storia di Nexpell. Un CMS moderno e open-source per gamer.'),

('articles', 'de', 'Artikel – Aktuelle Beiträge und News', 'Entdecke spannende Artikel, Neuigkeiten und Hintergrundberichte rund um dein Thema. Informiere dich aktuell und fundiert.'),
('articles', 'en', 'Articles – Latest Posts and News', 'Discover exciting articles, news, and in-depth reports on your topics. Stay informed with up-to-date and reliable content.'),
('articles', 'it', 'Articoli – Ultimi post e notizie', 'Scopri articoli interessanti, notizie e approfondimenti sui tuoi argomenti. Rimani aggiornato con contenuti affidabili.'),

('contact', 'de', 'Kontakt – Nimm Kontakt mit dem Nexpell-Team auf', 'Du hast Fragen, Feedback oder möchtest mit dem Nexpell-Team in Kontakt treten? Nutze unser Kontaktformular – wir freuen uns auf deine Nachricht.'),
('contact', 'en', 'Contact – Get in Touch with the Nexpell Team', 'Have questions or feedback? Use our contact form to reach out to the Nexpell team. We look forward to hearing from you.'),
('contact', 'it', 'Contatto – Mettiti in contatto con il team di Nexpell', 'Hai domande o suggerimenti? Usa il nostro modulo di contatto per contattare il team di Nexpell. Saremo lieti di risponderti.'),

('discord', 'de', 'Nexpell Discord – Community & Support', 'Tritt dem offiziellen Nexpell-Discord bei, um dich mit der Community zu vernetzen und direkten Support von den Entwicklern zu erhalten.'),
('discord', 'en', 'Nexpell Discord – Community and Support', 'Join the official Nexpell Discord to connect with the community and get support directly from the developers.'),
('discord', 'it', 'Nexpell Discord – Community e Supporto', 'Unisciti all\'officiale Discord di Nexpell per connetterti con la community e ricevere supporto direttamente dagli sviluppatori.'),

('downloads', 'de', 'Downloads – Erweiterungen für dein Nexpell CMS', 'Lade Module, Themes und Erweiterungen für dein Nexpell CMS herunter. Direkt einsatzbereit.'),
('downloads', 'en', 'Downloads – Extensions for Your Nexpell CMS', 'Download modules, themes, and extensions for your Nexpell CMS. Ready to use out of the box.'),
('downloads', 'it', 'Download – Estensioni per il tuo CMS Nexpell', 'Scarica moduli, temi ed estensioni per il tuo CMS Nexpell. Pronti all’uso immediato.'),

('forum', 'de', 'Community Forum – Fragen, Hilfe & Austausch', 'Diskutiere Ideen und tausche dich mit anderen Nexpell-Nutzern im Forum aus.'),
('forum', 'en', 'Community Forum – Questions, Help & Exchange', 'Discuss ideas and connect with other Nexpell users in the forum.'),
('forum', 'it', 'Forum della community – Domande, aiuto e confronto', 'Discuti idee e confrontati con altri utenti Nexpell nel forum.'),

('gametracker', 'de', 'Game Server Übersicht – Echtzeit-Serverstatus', 'Behalte den Überblick über deine Gameserver. Mit Karten, Spielern und Serverstatus.'),
('gametracker', 'en', 'Game Server Overview – Real-Time Server Info', 'Track your game servers in real time: players, maps, versions, and server status – all in one place.'),
('gametracker', 'it', 'Panoramica server di gioco – Stato in tempo reale', 'Monitora i tuoi server di gioco in tempo reale: giocatori, mappe, versioni e stato del server – tutto in un colpo d’occhio.'),

('imprint', 'de', 'Impressum – Rechtliche Angaben zu Nexpell', 'Verantwortlich für Inhalte und rechtliche Informationen zu Nexpell gemäß §5 TMG.'),
('imprint', 'en', 'Legal Notice – Company and Legal Information about Nexpell', 'Responsible for content and legal information about Nexpell in accordance with §5 TMG (German law).'),
('imprint', 'it', 'Note legali – Informazioni legali su Nexpell', 'Responsabile dei contenuti e informazioni legali su Nexpell ai sensi del §5 TMG (legge tedesca).'),

('privacy_policy', 'de', 'Datenschutz – Umgang mit deinen Daten', 'Erfahre, wie wir deine Daten schützen. Unsere Datenschutzrichtlinien – DSGVO-konform.'),
('privacy_policy', 'en', 'Privacy Policy – How We Handle Your Data', 'Learn how we protect your data. Our privacy practices are GDPR-compliant.'),
('privacy_policy', 'it', 'Privacy – Come trattiamo i tuoi dati', 'Scopri come proteggiamo i tuoi dati. Le nostre politiche sulla privacy sono conformi al GDPR.'),

('shoutbox', 'de', 'Nexpell Search – Find Content Quickly & Easily', 'Use the Nexpell search plugin to efficiently find content in your CMS. Optimized for speed and accuracy.'),
('shoutbox', 'en', 'Ricerca Nexpell – Trova Contenuti Velocemente', 'Con il plugin di ricerca Nexpell puoi cercare contenuti nel CMS in modo rapido ed efficiente. Ottimizzato per velocità e precisione.'),
('shoutbox', 'it', 'Shoutbox – Kurznachrichten deiner Community', 'Poste schnelle Nachrichten, bleibe mit deinem Clan in Kontakt und stärke die Kommunikation in deiner Community.'),

('todo', 'de', 'Shoutbox – Quick Messages for Your Community', 'Post short messages, stay connected with your clan, and keep your community engaged.'),
('todo', 'en', 'Shoutbox – Messaggi rapidi per la tua community', 'Invia messaggi brevi, resta in contatto con il tuo clan e mantieni attiva la tua community.'),
('todo', 'it', 'TODO – Offene Aufgaben und wichtige To-Dos', 'Finde hier eine Übersicht aller offenen Aufgaben und wichtigen To-Dos. Behalte den Überblick über aktuelle Projekte und geplante Schritte.'),

('userlist', 'de', 'TODO – Open Tasks and Important To-Dos', 'Find an overview of all open tasks and important to-dos. Keep track of current projects and planned steps.'),
('userlist', 'en', 'TODO – Compiti aperti e cose da fare importanti', 'Trova una panoramica di tutti i compiti aperti e le cose importanti da fare. Tieni traccia dei progetti attuali e dei passi pianificati.'),
('userlist', 'it', 'Mitgliederliste – Alle registrierten Nutzer im Überblick', 'Hier findest du alle Mitglieder der Nexpell-Community mit Profilinformationen und Aktivitätsstatus.'),

('default', 'de', 'Nexpell CMS – Das modulare CMS für Communities und Clans', 'Nexpell ist ein modernes Open-Source-CMS für Clan- und Community-Webseiten. Modular aufgebaut, leicht anpassbar und kostenlos verfügbar.'),
('default', 'en', 'Nexpell CMS – The Modular CMS for Communities and Clans', 'Nexpell is a modern open-source CMS designed for clan and community websites. Modular, customizable, and free to use.'),
('default', 'it', 'Nexpell CMS – Il CMS modulare per community e clan', 'Nexpell è un moderno CMS open source per siti web di clan e community. Modulare, personalizzabile e completamente gratuito.');





CREATE TABLE IF NOT EXISTS `settings_site_lock` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `reason` TEXT NOT NULL,
  `time` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `settings_social_media` (
  `socialID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `twitch` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `youtube` varchar(255) NOT NULL,
  `rss` varchar(255) NOT NULL,
  `vine` varchar(255) NOT NULL,
  `flickr` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `instagram` varchar(255) NOT NULL,
  `since` varchar(255) NOT NULL,
  `gametracker` varchar(255) NOT NULL,
  `discord` varchar(255) NOT NULL,
  `steam` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT IGNORE INTO `settings_social_media` (`socialID`, `twitch`, `facebook`, `twitter`, `youtube`, `rss`, `vine`, `flickr`, `linkedin`, `instagram`, `since`, `gametracker`, `discord`, `steam`) VALUES
(1, 'https://www.twitch.tv/pulsradiocom', 'https://www.facebook.com/nexpell', 'https://twitter.com/nexpell', '-', '-', '-', '-', '-', '-', '2025', '85.14.228.228:28960', 'https://www.discord.gg/kErxPxb', '-');



CREATE TABLE IF NOT EXISTS `settings_startpage` (
  `pageID` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `startpage_text` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `editor` TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `settings_startpage` (`pageID`, `title`, `startpage_text`, `date`, `editor`)
VALUES (
  1,
  'Next-Generation',
  '[[lang:de]]Willkommen bei nexpell!<br><br>Herzlichen Glückwunsch — die Installation von nexpell wurde erfolgreich abgeschlossen. Sie haben damit die Basis für eine moderne, flexible und leistungsstarke Webplattform geschaffen, die Ihnen alle Freiheiten bietet, Ihre Ideen zu verwirklichen. Ganz gleich, ob Sie einen Blog, eine Galerie, ein Forum oder eine umfassende Community-Plattform aufbauen möchten — mit nexpell haben Sie das passende Werkzeug in der Hand.<br><br><strong>👉 Ihre nächsten Schritte:</strong><br>- Melden Sie sich im Admin-Panel an, um Ihre ersten Seiten, Kategorien und Inhalte zu erstellen.<br>- Konfigurieren Sie Designs, Farben und Sprachoptionen ganz nach Ihrem Geschmack.<br>- Aktivieren Sie weitere Module wie Artikel, Bewertungen oder ein Diskussionsforum, um Ihre Besucher noch besser einzubinden.<br>- Nutzen Sie die eingebauten Statistik- und Analysefunktionen, um Ihre Zielgruppe besser zu verstehen und Ihre Website weiterzuentwickeln.<br><br>nexpell wurde entwickelt, damit Sie schnell und unkompliziert starten können — und gleichzeitig alle Möglichkeiten offen bleiben, Ihre Webpräsenz individuell zu gestalten.<br><br>Wir wünschen Ihnen viel Erfolg und vor allem Freude beim Aufbau Ihrer neuen Website![[lang:en]]Welcome to nexpell!<br><br>Congratulations — the installation of nexpell has been successfully completed. You now have the foundation for a modern, flexible, and powerful web platform that gives you complete freedom to realize your ideas. Whether you want to build a blog, a gallery, a forum, or a comprehensive community platform — with nexpell, you have the right tool in hand.<br><br><strong>👉 Your next steps:</strong><br>- Log in to the admin panel to create your first pages, categories, and content.<br>- Configure designs, colors, and language options to your liking.<br>- Activate additional modules such as articles, reviews, or a discussion forum to better engage your visitors.<br>- Use the built-in statistics and analysis features to better understand your audience and further develop your website.<br><br>Nexpell was designed so you can start quickly and easily — while keeping all possibilities open to customize your web presence.<br><br>We wish you much success and, above all, joy in building your new website![[lang:it]]Benvenuto in nexpell!<br><br>Congratulazioni — l\'installazione di nexpell è stata completata con successo. Ora hai le basi per una piattaforma web moderna, flessibile e potente che ti offre piena libertà di realizzare le tue idee. Che tu voglia creare un blog, una galleria, un forum o una piattaforma comunitaria completa — con nexpell hai lo strumento giusto a portata di mano.<br><br><strong>👉 I tuoi prossimi passi:</strong><br>- Accedi al pannello di amministrazione per creare le tue prime pagine, categorie e contenuti.<br>- Configura design, colori e opzioni linguistiche secondo i tuoi gusti.<br>- Attiva moduli aggiuntivi come articoli, recensioni o un forum di discussione per coinvolgere meglio i tuoi visitatori.<br>- Utilizza le funzioni statistiche e di analisi integrate per comprendere meglio il tuo pubblico e sviluppare ulteriormente il tuo sito.<br><br>Nexpell è stato progettato per permetterti di iniziare rapidamente e facilmente — mantenendo aperte tutte le possibilità per personalizzare la tua presenza sul web.<br><br>Ti auguriamo tanto successo e, soprattutto, gioia nella costruzione del tuo nuovo sito web!',
  CURRENT_TIMESTAMP,
  '0'
);



CREATE TABLE IF NOT EXISTS `settings_static` (
  `staticID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `categoryID` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date` int(14) NOT NULL,
  `editor` int(1) DEFAULT 0,
  `access_roles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



/* Legacy theme tables removed
CREATE TABLE IF NOT EXISTS `settings_themes` (
  `themeID` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `modulname` VARCHAR(100) NOT NULL,
  `pfad` VARCHAR(255) NOT NULL,
  `version` VARCHAR(11) NOT NULL,
  `active` INT(11) DEFAULT NULL,
  `themename` VARCHAR(255) NOT NULL,
  `navbar_class` VARCHAR(50) NOT NULL,
  `navbar_theme` VARCHAR(10) NOT NULL,
  `express_active` INT(11) NOT NULL DEFAULT 0,
  `logo_pic` VARCHAR(255) DEFAULT '0',
  `reg_pic` VARCHAR(255) NOT NULL,
  `headlines` VARCHAR(255) DEFAULT '0',
  `sort` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`themeID`),
  UNIQUE KEY `unique_modulname` (`modulname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `settings_themes`
(`themeID`, `name`, `modulname`, `pfad`, `version`, `active`, `themename`, `navbar_class`, `navbar_theme`,
 `express_active`, `logo_pic`, `reg_pic`, `headlines`, `sort`)
VALUES
(1, 'Default', 'default', 'default', '0.3', 1, 'default', 'bg-light', 'light', 0, 'default_logo.png', 'default_login_bg.jpg', 'headlines_03.css', 1);



CREATE TABLE IF NOT EXISTS `settings_themes_installed` (
  `themeID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `modulname` VARCHAR(255) NOT NULL,
  `version` VARCHAR(20) NOT NULL,
  `author` VARCHAR(100) DEFAULT NULL,
  `url` VARCHAR(255) NOT NULL,
  `folder` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `installed_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`themeID`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


*/

CREATE TABLE `settings_widgets` (
  `widget_key`    varchar(128) NOT NULL,
  `title`         varchar(255) NOT NULL DEFAULT '',
  `modulname`     varchar(100) NOT NULL DEFAULT '',
  `plugin`        varchar(64)  NOT NULL DEFAULT '',
  `description`   text DEFAULT NULL,
  `allowed_zones` varchar(255) NOT NULL DEFAULT '',
  `active`        tinyint(1) NOT NULL DEFAULT 1,
  `version`       varchar(16) NOT NULL DEFAULT '1.0.0',
  `created_at`    timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`widget_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


 
    
CREATE TABLE IF NOT EXISTS `settings_widgets_positions` (
  `id`            int(11) NOT NULL AUTO_INCREMENT,
  `title`         varchar(255) DEFAULT NULL,
  `modulname`     varchar(100) NOT NULL DEFAULT '',
  `widget_key`    varchar(128) NOT NULL DEFAULT '',
  `position`      varchar(32) NOT NULL DEFAULT 'top',
  `page`          varchar(64) NOT NULL DEFAULT 'index',
  `instance_id`   varchar(64) NOT NULL DEFAULT '',
  `settings`      text DEFAULT NULL,
  `sort_order`    int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `settings_widgets_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `modulname` varchar(100) NOT NULL DEFAULT '',
  `widget_key` varchar(128) NOT NULL DEFAULT '',
  `position` varchar(32) NOT NULL DEFAULT 'top',
  `page` varchar(64) NOT NULL DEFAULT 'index',
  `instance_id` varchar(64) NOT NULL DEFAULT '',
  `settings` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,  
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_page_instance` (`page`, `instance_id`),
  KEY `idx_widget_key` (`widget_key`),
  KEY `idx_page_position_sort` (`page`, `position`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `tags` (
  `rel` varchar(255) NOT NULL,
  `ID` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `registerdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastlogin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password_hash` varchar(255) NOT NULL,
  `password_pepper` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_hide` tinyint(1) NOT NULL DEFAULT 1,
  `email_change` varchar(255) NOT NULL,
  `email_activate` varchar(255) NOT NULL,
  `role` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `activation_code` varchar(64) DEFAULT NULL,
  `activation_expires` int(11) DEFAULT NULL,
  `visits` int(11) NOT NULL DEFAULT 0,
  `language` varchar(2) NOT NULL,
  `last_update` datetime DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `total_online_seconds` int(11) DEFAULT 0,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`userID`),
  KEY `idx_last_update` (`last_update`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS user_profiles (
  userID int(10) UNSIGNED NOT NULL PRIMARY KEY,
  firstname varchar(100) DEFAULT NULL,
  lastname varchar(100) DEFAULT NULL,
  location varchar(150) DEFAULT NULL,
  about_me text DEFAULT NULL,
  avatar varchar(255) DEFAULT NULL,
  birthday date DEFAULT NULL,
  gender varchar(50) DEFAULT NULL,
  signatur varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `user_profiles` (`userID`, `firstname`, `lastname`, `location`, `about_me`, `avatar`, `birthday`, `gender`, `signatur`)
VALUES (1, NULL, NULL, NULL, '', NULL, NULL, NULL, '');



CREATE TABLE IF NOT EXISTS `user_register_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` datetime NOT NULL DEFAULT current_timestamp,
  `status` enum('success','failed') NOT NULL DEFAULT 'failed',
  `reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`),
  KEY `attempt_time` (`attempt_time`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `user_roles` (
  `roleID` INT(11) NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(50) NOT NULL,
  `modulname` VARCHAR(100) NOT NULL DEFAULT '',
  `description` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_default` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`roleID`),
  UNIQUE KEY `unique_role_name` (`role_name`),
  UNIQUE KEY `unique_modulname` (`modulname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    
INSERT INTO `user_roles` (`roleID`, `role_name`, `modulname`, `description`, `is_active`, `is_default`)
VALUES 
(1, 'Admin',                 'ac_admin',        'Vollzugriff', 1, 0),
(2, 'Co-Admin',              'ac_coadmin',      'Unterstützt Admin', 1, 0),
(3, 'Leader',                'ac_leader',       'Clan-Leiter', 1, 0),
(4, 'Co-Leader',             'ac_coleader',     'Vertretung', 1, 0),
(5, 'Squad-Leader',          'ac_squadleader',  'Squad-Leitung', 1, 0),
(6, 'War-Organisator',       'ac_warorganizer', 'Turnierorga', 1, 0),
(7, 'Moderator',             'ac_moderator',    'Moderation', 1, 0),
(8, 'Redakteur',             'ac_editor',       'News/Content', 1, 0),
(9, 'Member',                'ac_member',       'Mitglied', 1, 0),
(10,'Trial-Member',          'ac_trialmember',  'Probezeit', 1, 0),
(11,'Gast',                  'ac_guest',        'Besucher', 1, 0),
(12,'Registrierter Benutzer','ac_registered',   'Angemeldet', 1, 0),
(13,'Ehrenmitglied',         'ac_honor',        'Ehrenstatus', 1, 0),
(14,'Streamer',              'ac_streamer',     'Streams', 1, 0),
(15,'Designer',              'ac_designer',     'Grafiken', 1, 0),
(16,'Techniker',             'ac_technician',   'Technik', 1, 0);



CREATE TABLE IF NOT EXISTS `user_role_admin_navi_rights` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `roleID` INT(11) NOT NULL,
  `type` ENUM('link','category') NOT NULL,
  `modulname` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_access` (`roleID`, `type`, `modulname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `user_role_admin_navi_rights` (`roleID`, `type`, `modulname`)
VALUES 
(1, 'link', 'ac_overview'),
(1, 'link', 'ac_visitor_statistic'),
(1, 'link', 'ac_settings'),
(1, 'link', 'ac_dashboard_navigation'),
(1, 'link', 'ac_email'),
(1, 'link', 'ac_contact'),
(1, 'link', 'ac_database'),
(1, 'link', 'ac_startpage'),
(1, 'link', 'ac_static'),
(1, 'link', 'ac_imprint'),
(1, 'link', 'ac_db_stats'),
(1, 'link', 'ac_editlang'),
(1, 'link', 'ac_headstyle'),
(1, 'link', 'ac_languages'),
(1, 'link', 'ac_log_viewer'),
(1, 'link', 'ac_plugin_installer'),
(1, 'link', 'ac_plugin_manager'),
(1, 'link', 'ac_plugin_widgets_save'),
(1, 'link', 'ac_plugin_widgets_setting'),
(1, 'link', 'ac_privacy_policy'),
(1, 'link', 'ac_security_overview'),
(1, 'link', 'ac_seo_meta'),
(1, 'link', 'ac_site_lock'),
(1, 'link', 'ac_statistic'),
(1, 'link', 'ac_stylesheet'),
(1, 'link', 'ac_update_core'),
(1, 'link', 'ac_user_roles'),
(1, 'link', 'ac_webside_navigation'),
(1, 'link', 'footer_easy'),
(1, 'category', 'cat_content'),
(1, 'category', 'cat_design'),
(1, 'category', 'cat_media'),
(1, 'category', 'cat_partners'),
(1, 'category', 'cat_plugins'),
(1, 'category', 'cat_security'),
(1, 'category', 'cat_slider_header'),
(1, 'category', 'cat_social'),
(1, 'category', 'cat_statistics'),
(1, 'category', 'cat_system'),
(1, 'category', 'cat_team'),
(1, 'category', 'cat_tools_game'),
(1, 'category', 'cat_users');



CREATE TABLE IF NOT EXISTS `user_role_assignments` (
  `assignmentID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `roleID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`assignmentID`),
  KEY `roleID` (`roleID`),
  KEY `user_role_assignments` (`userID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;



CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `userID` int(11) NOT NULL,
  `user_ip` varchar(45) DEFAULT NULL,
  `session_data` text DEFAULT NULL,
  `browser` text DEFAULT NULL,
  `last_activity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session` (`session_id`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS user_settings (
    userID INT UNSIGNED NOT NULL,
    language VARCHAR(10) DEFAULT 'de',
    dark_mode TINYINT(1) DEFAULT 0,
    email_notifications TINYINT(1) DEFAULT 1,
    private_profile TINYINT(1) DEFAULT 0,
    PRIMARY KEY (userID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `user_settings` (`userID`, `language`, `dark_mode`, `email_notifications`, `private_profile`)
VALUES (1, 'de', 0, 1, 0);



CREATE TABLE IF NOT EXISTS user_socials (
    userID INT UNSIGNED NOT NULL,
    facebook VARCHAR(255) DEFAULT NULL,
    twitter VARCHAR(255) DEFAULT NULL,
    instagram VARCHAR(255) DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    github VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (userID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `user_socials` (`userID`, `facebook`, `twitter`, `instagram`, `website`, `github`)
VALUES (1, NULL, NULL, NULL, NULL, NULL);



CREATE TABLE IF NOT EXISTS user_stats (
    userID INT UNSIGNED NOT NULL,
    points INT UNSIGNED DEFAULT 0,
    lastlogin DATETIME DEFAULT NULL,
    registerdate DATETIME DEFAULT CURRENT_TIMESTAMP,
    logins_count INT UNSIGNED DEFAULT 0,
    total_time_online INT UNSIGNED DEFAULT 0,
    PRIMARY KEY (userID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `user_username` (
  `userID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `visitors_live` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `time` INT(11) NOT NULL,
  `userID` INT(11) DEFAULT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `site` VARCHAR(255) DEFAULT NULL,
  `country_code` VARCHAR(5) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `visitors_live_history` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `time` INT(10) UNSIGNED NOT NULL,
  `userID` INT(10) UNSIGNED DEFAULT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `site` VARCHAR(255) DEFAULT NULL,
  `country_code` CHAR(2) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `visitor_daily_counter` (
  `date` DATE NOT NULL,
  `hits` INT(11) NOT NULL DEFAULT 0,
  `online` INT(11) NOT NULL DEFAULT 0,
  `maxonline` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `visitor_daily_counter_hits` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `date` DATE NOT NULL,
  `user_id` INT(11) DEFAULT NULL,
  `ip_hash` CHAR(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_date` (`user_id`, `date`),
  UNIQUE KEY `uq_iphash_date` (`ip_hash`, `date`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `visitor_daily_iplist` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `dates` DATE NOT NULL,
  `del` INT(11) NOT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `country_code` VARCHAR(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ip_date` (`ip`, `dates`),
  KEY `idx_date` (`dates`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `visitor_daily_stats` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `date` DATE NOT NULL,
  `hits` INT(11) NOT NULL DEFAULT 0,
  `online` INT(11) NOT NULL DEFAULT 0,
  `maxonline` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `visitor_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `pageviews` int(11) DEFAULT 1,
  `last_seen` datetime NOT NULL DEFAULT current_timestamp,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `page` varchar(255) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT NULL,
  `os` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `ip_hash` varchar(64) NOT NULL,
  `referer` varchar(300) NOT NULL,
  `user_agent` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;








