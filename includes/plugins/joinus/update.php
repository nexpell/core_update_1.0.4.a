<?php
declare(strict_types=1);

global $_database;

safe_query("
CREATE TABLE IF NOT EXISTS plugins_joinus_applications (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) DEFAULT NULL,
  email VARCHAR(255) DEFAULT NULL,
  message TEXT DEFAULT NULL,
  role INT(11) NOT NULL DEFAULT 0,
  role_custom VARCHAR(255) DEFAULT NULL,
  squad_id INT(11) DEFAULT NULL,
  type VARCHAR(50) DEFAULT 'team',
  status ENUM('new','review','accepted','rejected') DEFAULT 'new',
  admin_note TEXT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  last_status VARCHAR(20) DEFAULT 'new',
  processed_at DATETIME DEFAULT NULL,
  mail_sent_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

safe_query("
CREATE TABLE IF NOT EXISTS plugins_joinus_roles (
  id INT(11) NOT NULL AUTO_INCREMENT,
  role_id INT(11) NOT NULL,
  is_enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_joinus_role (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

safe_query("
CREATE TABLE IF NOT EXISTS plugins_joinus_squads (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  is_enabled TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

safe_query("
CREATE TABLE IF NOT EXISTS plugins_joinus_types (
  id INT(11) NOT NULL AUTO_INCREMENT,
  type_key VARCHAR(32) NOT NULL,
  label VARCHAR(255) NOT NULL,
  is_enabled TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_joinus_type (type_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

safe_query("
INSERT IGNORE INTO plugins_joinus_types (type_key, label, is_enabled, sort_order) VALUES
('team',    'Team',    1, 1),
('partner', 'Partner', 1, 2),
('squad',   'Squad',   1, 3)
");

safe_query("
INSERT IGNORE INTO plugins_joinus_squads (id, name, is_enabled) VALUES
(1, 'Alpha Squad (CS2)', 1),
(2, 'Bravo Squad (Valorant)', 1),
(3, 'Community Squad', 1)
");

$res = safe_query("
    SELECT roleID
    FROM user_roles
    WHERE is_active = 1 AND roleID != 1
");

while ($res && ($row = mysqli_fetch_assoc($res))) {
    safe_query("
        INSERT IGNORE INTO plugins_joinus_roles (role_id, is_enabled)
        VALUES (" . (int) $row['roleID'] . ", 1)
    ");
}

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'joinus', 'admin_joinus', 1, 'T-Seven', 'https://www.nexpell.de', 'joinus', '', '1.0.3.3', 'includes/plugins/joinus/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_joinus', 'de', 'JoinUs', 'joinus', NOW()),
        ('plugin_name_joinus', 'en', 'JoinUs', 'joinus', NOW()),
        ('plugin_name_joinus', 'it', 'JoinUs', 'joinus', NOW()),
        ('plugin_info_joinus', 'de', 'JoinUs Bewerbungsformular', 'joinus', NOW()),
        ('plugin_info_joinus', 'en', 'JoinUs application form', 'joinus', NOW()),
        ('plugin_info_joinus', 'it', 'Modulo di candidatura JoinUs', 'joinus', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('JoinUs', 'joinus', 'The JoinUs plugin allows visitors and community members to apply in a structured way for team, squad, or partner roles.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'joinus', NOW())
    ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        description = VALUES(description),
        version = VALUES(version),
        author = VALUES(author),
        url = VALUES(url),
        folder = VALUES(folder),
        installed_date = NOW()
");

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'joinus' AND url = 'index.php?site=joinus'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (3, 'joinus', 'index.php?site=joinus', 3, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Join Us', 'joinus', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Join Us', 'joinus', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Unisciti', 'joinus', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

$linkID = 0;
$linkRes = safe_query("
    SELECT linkID FROM navigation_dashboard_links
    WHERE modulname = 'joinus' AND url = 'admincenter.php?site=admin_joinus'
    ORDER BY linkID ASC LIMIT 1
");
if ($linkRes && ($linkRow = mysqli_fetch_assoc($linkRes))) {
    $linkID = (int) ($linkRow['linkID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_links
            (catID, modulname, url, sort)
        VALUES
            (5, 'joinus', 'admincenter.php?site=admin_joinus', 1)
    ");
    $linkID = (int) mysqli_insert_id($_database);
}

if ($linkID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_link_{$linkID}', 'de', 'JoinUs', 'joinus', NOW()),
            ('nav_link_{$linkID}', 'en', 'JoinUs', 'joinus', NOW()),
            ('nav_link_{$linkID}', 'it', 'JoinUs', 'joinus', NOW())
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
('', 1, 'link', 'joinus')
");

return true;
