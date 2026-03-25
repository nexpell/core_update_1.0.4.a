<?php
global $str, $modulname, $version, $_database;

$modulname = 'carousel';
$version = '0.3';
$str = 'Carousel';

echo "<div class='card'><div class='card-header'>{$str} Database Update</div><div class='card-body'>";

safe_query("CREATE TABLE IF NOT EXISTS plugins_carousel (
  id int(11) NOT NULL AUTO_INCREMENT,
  type enum('sticky','parallax','agency','carousel') NOT NULL,
  link varchar(255) DEFAULT NULL,
  media_type enum('image','video') NOT NULL,
  media_file varchar(255) DEFAULT NULL,
  visible tinyint(1) DEFAULT 1,
  sort int(11) DEFAULT 0,
  created_at datetime DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY idx_type_visible_sort (type, visible, sort)
) AUTO_INCREMENT=9
  DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci");

safe_query("CREATE TABLE IF NOT EXISTS plugins_carousel_lang (
  id INT(11) NOT NULL AUTO_INCREMENT,
  content_key VARCHAR(80) NOT NULL,
  language CHAR(2) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_content_lang (content_key, language),
  KEY idx_content_key (content_key),
  KEY idx_language (language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

safe_query("INSERT IGNORE INTO plugins_carousel (id, type, link, media_type, media_file, visible, sort, created_at) VALUES
(1, 'sticky',   'https://www.nexpell.de', 'image', 'block_687148bb0318b.jpg', 1, 0, '2025-07-11 19:24:11'),
(2, 'parallax', 'https://www.nexpell.de', 'image', 'block_6871494833ec1.jpg', 1, 0, '2025-07-11 19:26:32'),
(3, 'agency',   'https://www.nexpell.de', 'image', 'block_687149651d571.jpg', 1, 0, '2025-07-11 19:27:01'),
(4, 'carousel', 'https://www.nexpell.de', 'image', 'block_687149d478869.jpg', 1, 0, '2025-07-11 19:28:52'),
(5, 'carousel', 'https://www.nexpell.de', 'image', 'block_687149e906f43.jpg', 1, 0, '2025-07-11 19:29:13'),
(6, 'carousel', 'https://www.nexpell.de', 'image', 'block_687149fd5a1af.jpg', 1, 0, '2025-07-11 19:29:33'),
(7, 'carousel', 'https://www.nexpell.de', 'image', 'block_68714d40abe62.jpg', 1, 0, '2025-07-11 19:29:57'),
(8, 'carousel', 'https://www.nexpell.de', 'video', 'block_68714a4106e25.mp4', 1, 0, '2025-07-11 19:30:41')");

safe_query("INSERT INTO plugins_carousel_lang (content_key, language, content, updated_at) VALUES
('carousel_1_title','de','ne<span>x</span>pell - Das moderne CMS fuer flexible Webentwicklung',NOW()),
('carousel_1_title','en','ne<span>x</span>pell - The modern CMS for flexible web development',NOW()),
('carousel_1_title','it','ne<span>x</span>pell - Il CMS moderno per uno sviluppo web flessibile',NOW()),
('carousel_1_subtitle','de','nexpell kombiniert Benutzerfreundlichkeit, Performance und Erweiterbarkeit in einem schlanken, modernen Content-Management-System.',NOW()),
('carousel_1_subtitle','en','nexpell combines user-friendliness, performance, and extensibility in a sleek, modern content management system.',NOW()),
('carousel_1_subtitle','it','nexpell combina facilita d''uso, prestazioni ed estensibilita in un sistema di gestione dei contenuti moderno e snello.',NOW()),
('carousel_1_description','de','Modular, flexibel und leistungsstark - so gestaltest du moderne Websites ohne Grenzen.',NOW()),
('carousel_1_description','en','Modular, flexible, and powerful - this is how you create modern websites without limits.',NOW()),
('carousel_1_description','it','Modulare, flessibile e potente - cosi crei siti web moderni senza limiti.',NOW())
ON DUPLICATE KEY UPDATE
  content = VALUES(content),
  updated_at = VALUES(updated_at)");

safe_query("CREATE TABLE IF NOT EXISTS plugins_carousel_settings (
  carouselID int(11) NOT NULL AUTO_INCREMENT,
  carousel_height varchar(255) NOT NULL DEFAULT '0',
  parallax_height varchar(255) NOT NULL DEFAULT '0',
  sticky_height varchar(255) NOT NULL DEFAULT '0',
  agency_height varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (carouselID)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

safe_query("INSERT IGNORE INTO plugins_carousel_settings (carouselID, carousel_height, parallax_height, sticky_height, agency_height) VALUES
(1, '75vh', '75vh', '75vh', '75vh')");

safe_query("INSERT IGNORE INTO settings_plugins
  (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
VALUES
  ('', 'carousel', 'admin_carousel', 1, 'T-Seven', 'https://www.nexpell.de', '', '', '0.1', 'includes/plugins/carousel/', 1, 1, 0, 1, 'deactivated')");

safe_query("INSERT INTO settings_plugins_lang
  (content_key, language, content, modulname, updated_at)
VALUES
  ('plugin_name_carousel', 'de', 'Carousel', 'carousel', NOW()),
  ('plugin_name_carousel', 'en', 'Carousel', 'carousel', NOW()),
  ('plugin_name_carousel', 'it', 'Carousel', 'carousel', NOW()),
  ('plugin_info_carousel', 'de', 'Mit diesem Plugin koennt ihr ein Carousel in die Webseite einbinden.', 'carousel', NOW()),
  ('plugin_info_carousel', 'en', 'With this plugin you can integrate a carousel into your website.', 'carousel', NOW()),
  ('plugin_info_carousel', 'it', 'Con questo plugin puoi integrare un carosello nel sito web.', 'carousel', NOW())
ON DUPLICATE KEY UPDATE
  content = VALUES(content),
  modulname = VALUES(modulname),
  updated_at = VALUES(updated_at)");

safe_query("INSERT IGNORE INTO settings_widgets
  (widget_key, title, plugin, description, modulname, allowed_zones, active, version, created_at)
VALUES
  ('widget_agency_header', 'Agency Header', 'carousel', NULL, 'carousel', 'undertop', 1, '1.0.0', NOW()),
  ('widget_carousel_header', 'Carousel Header', 'carousel', NULL, 'carousel', 'top,undertop', 1, '1.0.0', NOW()),
  ('widget_parallax_header', 'Parallax Header', 'carousel', NULL, 'carousel', 'undertop', 1, '1.0.0', NOW()),
  ('widget_sticky_header', 'Sticky Header', 'carousel', NULL, 'carousel', 'top,undertop', 1, '1.0.0', NOW())");

$dashboardLinkId = 0;
$dashboardLinkRes = safe_query("SELECT linkID FROM navigation_dashboard_links
  WHERE modulname = 'carousel' AND url = 'admincenter.php?site=admin_carousel'
  ORDER BY linkID ASC LIMIT 1");
if ($dashboardLinkRes && ($dashboardLinkRow = mysqli_fetch_assoc($dashboardLinkRes))) {
    $dashboardLinkId = (int) ($dashboardLinkRow['linkID'] ?? 0);
} else {
    safe_query("INSERT INTO navigation_dashboard_links
      (catID, modulname, url, sort)
    VALUES
      (10, 'carousel', 'admincenter.php?site=admin_carousel', 1)");
    $dashboardLinkId = (int) mysqli_insert_id($_database);
}
if ($dashboardLinkId > 0) {
    safe_query("INSERT INTO navigation_dashboard_lang
      (content_key, language, content, modulname, updated_at)
    VALUES
      ('nav_link_{$dashboardLinkId}', 'de', 'Carousel', 'carousel', NOW()),
      ('nav_link_{$dashboardLinkId}', 'en', 'Carousel', 'carousel', NOW()),
      ('nav_link_{$dashboardLinkId}', 'it', 'Carosello Immagini', 'carousel', NOW())
    ON DUPLICATE KEY UPDATE
      content = VALUES(content),
      modulname = VALUES(modulname),
      updated_at = VALUES(updated_at)");
}

safe_query("INSERT IGNORE INTO user_role_admin_navi_rights
  (id, roleID, type, modulname)
VALUES
  ('', 1, 'link', 'carousel')");

echo "</div></div>";
?>
