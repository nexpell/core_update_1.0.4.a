<?php
/* =========================================================
   ABOUT PLUGIN - INSTALL / REPAIR
   SAFE & IDEMPOTENT
========================================================= */

/* ---------------------------
   CONTENT TABLE (rules-style)
---------------------------- */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_about (
  id INT(11) NOT NULL AUTO_INCREMENT,
  content_key VARCHAR(50) NOT NULL,
  language CHAR(2) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_content_lang (content_key, language),
  KEY idx_content_key (content_key),
  KEY idx_language (language)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
");

if (!function_exists('about_extract_lang')) {
    function about_extract_lang(string $multiLangText, string $lang): string
    {
        if (preg_match('/\[\[lang:' . preg_quote($lang, '/') . '\]\](.*?)(?=\[\[lang:|$)/s', $multiLangText, $m)) {
            return trim((string)$m[1]);
        }
        if ($lang === 'gb' && preg_match('/\[\[lang:en\]\](.*?)(?=\[\[lang:|$)/s', $multiLangText, $m)) {
            return trim((string)$m[1]);
        }
        if ($lang === 'en' && preg_match('/\[\[lang:gb\]\](.*?)(?=\[\[lang:|$)/s', $multiLangText, $m)) {
            return trim((string)$m[1]);
        }
        return trim($multiLangText);
    }
}

/* ---------------------------
   MIGRATION OLD -> NEW
---------------------------- */
$hasContentKey = safe_query("SHOW COLUMNS FROM plugins_about LIKE 'content_key'");
if (!$hasContentKey || mysqli_num_rows($hasContentKey) === 0) {
    safe_query("DROP TABLE IF EXISTS plugins_about_legacy");
    safe_query("RENAME TABLE plugins_about TO plugins_about_legacy");

    safe_query("
    CREATE TABLE plugins_about (
      id INT(11) NOT NULL AUTO_INCREMENT,
      content_key VARCHAR(50) NOT NULL,
      language CHAR(2) NOT NULL,
      content MEDIUMTEXT NOT NULL,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      UNIQUE KEY uniq_content_lang (content_key, language),
      KEY idx_content_key (content_key),
      KEY idx_language (language)
    ) ENGINE=InnoDB
      DEFAULT CHARSET=utf8mb4
      COLLATE=utf8mb4_unicode_ci
    ");

    $legacyRes = safe_query("SELECT * FROM plugins_about_legacy ORDER BY id ASC LIMIT 1");
    if ($legacyRes && mysqli_num_rows($legacyRes) > 0) {
        $legacy = mysqli_fetch_assoc($legacyRes);
        $langs = ['de', 'en', 'it'];
        $keys = ['title', 'intro', 'history', 'core_values', 'team', 'cta'];

        foreach ($keys as $key) {
            $raw = (string)($legacy[$key] ?? '');
            foreach ($langs as $iso) {
                $text = escape(about_extract_lang($raw, $iso));
                safe_query("
                    INSERT IGNORE INTO plugins_about (content_key, language, content, updated_at)
                    VALUES ('" . escape($key) . "', '" . escape($iso) . "', '$text', NOW())
                    ON DUPLICATE KEY UPDATE content=VALUES(content), updated_at=NOW()
                ");
            }
        }

        foreach (['image1', 'image2', 'image3'] as $imgKey) {
            $img = escape((string)($legacy[$imgKey] ?? ''));
            foreach ($langs as $iso) {
                safe_query("
                    INSERT IGNORE INTO plugins_about (content_key, language, content, updated_at)
                    VALUES ('" . escape($imgKey) . "', '" . escape($iso) . "', '$img', NOW())
                    ON DUPLICATE KEY UPDATE content=VALUES(content), updated_at=NOW()
                ");
            }
        }
    }
}

/* ---------------------------
   DEFAULT CONTENT (ONLY ONCE)
---------------------------- */
$countRes = safe_query("SELECT COUNT(*) AS cnt FROM plugins_about");
$countRow = mysqli_fetch_assoc($countRes);
if ((int)($countRow['cnt'] ?? 0) === 0) {
    safe_query("
    INSERT IGNORE INTO plugins_about (content_key, language, content, updated_at) VALUES
    ('title','de','Über uns',NOW()),
    ('title','en','About us',NOW()),
    ('title','it','Chi siamo',NOW()),
    ('intro','de','Willkommen auf unserer Website.',NOW()),
    ('intro','en','Welcome to our website.',NOW()),
    ('intro','it','Benvenuto sul nostro sito web.',NOW()),
    ('history','de','Unsere Geschichte.',NOW()),
    ('history','en','Our history.',NOW()),
    ('history','it','La nostra storia.',NOW()),
    ('core_values','de','Unsere Werte.',NOW()),
    ('core_values','en','Our values.',NOW()),
    ('core_values','it','I nostri valori.',NOW()),
    ('team','de','Unser Team.',NOW()),
    ('team','en','Our team.',NOW()),
    ('team','it','Il nostro team.',NOW()),
    ('cta','de','Mach mit und werde Teil der Community.',NOW()),
    ('cta','en','Join and become part of the community.',NOW()),
    ('cta','it','Unisciti e diventa parte della community.',NOW()),
    ('image1','de','intro.jpg',NOW()),
    ('image1','en','intro.jpg',NOW()),
    ('image1','it','intro.jpg',NOW()),
    ('image2','de','history.jpg',NOW()),
    ('image2','en','history.jpg',NOW()),
    ('image2','it','history.jpg',NOW()),
    ('image3','de','team.jpg',NOW()),
    ('image3','en','team.jpg',NOW()),
    ('image3','it','team.jpg',NOW())
    ");
}

/* ---------------------------
   PLUGIN REGISTRATION
---------------------------- */
safe_query("
    INSERT IGNORE INTO settings_plugins
    (pluginID, modulname, admin_file, activate, author, website,
     index_link, hiddenfiles, version, path,
     status_display, plugin_display, widget_display,
     delete_display, sidebar)
    VALUES
    (
     '',
     'about',
     'admin_about',
     1,
     'T-Seven',
     'https://www.nexpell.de',
     'about,leistung,info',
     '',
     '1.0.1',
     'includes/plugins/about/',
     1,1,1,1,'deactivated'
    )
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
    (`content_key`, `language`, `content`, `updated_at`)
    VALUES
    ('plugin_name_about', 'de', 'Über uns', NOW()),
    ('plugin_name_about', 'en', 'About Us', NOW()),
    ('plugin_name_about', 'it', 'Chi siamo', NOW()),

    ('plugin_info_about', 'de', 'Dieses Widget zeigt allgemeine Informationen ...', NOW()),
    ('plugin_info_about', 'en', 'This widget shows general information ...', NOW()),
    ('plugin_info_about', 'it', 'Questo widget mostra informazioni generali ...', NOW())
");

safe_query("
    UPDATE settings_plugins
    SET
        version = '1.0.1',
        path = 'includes/plugins/about/',
        activate = 1
    WHERE modulname = 'about'
");

/* ---------------------------
   ADMIN NAVIGATION
---------------------------- */
safe_query("
INSERT IGNORE INTO navigation_dashboard_links
(catID, modulname, url, sort)
VALUES
(
 5,
 'about',
 'admincenter.php?site=admin_about',
 1
)
");

$linkID = mysqli_insert_id($_database);

safe_query("
INSERT IGNORE INTO navigation_dashboard_lang
(`content_key`, `language`, `content`, `updated_at`)
VALUES
('nav_link_{$linkID}', 'de', 'Über uns', NOW()),
('nav_link_{$linkID}', 'en', 'About Us', NOW()),
('nav_link_{$linkID}', 'it', 'Chi siamo', NOW())
");

/* ---------------------------
   WEBSITE NAVIGATION
---------------------------- */
safe_query("
INSERT IGNORE INTO navigation_website_sub
(mnavID, modulname, url, sort, indropdown, last_modified)
VALUES
(
 2,
 'about',
 'index.php?site=about',
 1,
 1,
 NOW()
)
");

$snavID = mysqli_insert_id($_database);

safe_query("
INSERT IGNORE INTO navigation_website_lang
(`content_key`, `language`, `content`, `updated_at`)
VALUES
('nav_sub_{$snavID}', 'de', 'Über uns', NOW()),
('nav_sub_{$snavID}', 'en', 'About Us', NOW()),
('nav_sub_{$snavID}', 'it', 'Chi siamo', NOW())
");
safe_query("
INSERT IGNORE INTO navigation_website_sub
(mnavID, modulname, url, sort, indropdown, last_modified)
VALUES
(
 2,
 'leistung',
 'index.php?site=leistung',
 2,
 1,
 NOW()
)
");

$snavID = mysqli_insert_id($_database);

safe_query("
INSERT IGNORE INTO navigation_website_lang
(`content_key`, `language`, `content`, `updated_at`)
VALUES
('nav_sub_{$snavID}', 'de', 'Leistung', NOW()),
('nav_sub_{$snavID}', 'en', 'Services', NOW()),
('nav_sub_{$snavID}', 'it', 'Servizi', NOW())
");

safe_query("
INSERT IGNORE INTO navigation_website_sub
(mnavID, modulname, url, sort, indropdown, last_modified)
VALUES
(
 2,
 'info',
 'index.php?site=info',
 3,
 1,
 NOW()
)
");

$snavID = mysqli_insert_id($_database);

safe_query("
INSERT IGNORE INTO navigation_website_lang
(`content_key`, `language`, `content`, `updated_at`)
VALUES
('nav_sub_{$snavID}', 'de', 'Info', NOW()),
('nav_sub_{$snavID}', 'en', 'Info', NOW()),
('nav_sub_{$snavID}', 'it', 'Info', NOW())
");
/* ---------------------------
   ROLE RIGHTS
---------------------------- */
safe_query("
INSERT IGNORE INTO user_role_admin_navi_rights
(roleID, type, modulname)
VALUES
(1, 'link', 'about')
");
