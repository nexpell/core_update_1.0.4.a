<?php

global $_database;

safe_query("CREATE TABLE IF NOT EXISTS plugins_gallery_categories (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

safe_query("CREATE TABLE IF NOT EXISTS plugins_gallery (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  filename varchar(255) NOT NULL,
  title varchar(255) NOT NULL DEFAULT '',
  caption text NULL,
  alt_text varchar(255) NOT NULL DEFAULT '',
  tags varchar(255) NOT NULL DEFAULT '',
  photographer varchar(255) NOT NULL DEFAULT '',
  width int(10) UNSIGNED NOT NULL DEFAULT 0,
  height int(10) UNSIGNED NOT NULL DEFAULT 0,
  class enum('wide','tall','big','') DEFAULT '',
  upload_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  position int(10) UNSIGNED NOT NULL DEFAULT 0,
  category_id int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY category_id (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

safe_query("ALTER TABLE plugins_gallery ADD COLUMN IF NOT EXISTS title varchar(255) NOT NULL DEFAULT '' AFTER filename");
safe_query("ALTER TABLE plugins_gallery ADD COLUMN IF NOT EXISTS caption text NULL AFTER title");
safe_query("ALTER TABLE plugins_gallery ADD COLUMN IF NOT EXISTS alt_text varchar(255) NOT NULL DEFAULT '' AFTER caption");
safe_query("ALTER TABLE plugins_gallery ADD COLUMN IF NOT EXISTS tags varchar(255) NOT NULL DEFAULT '' AFTER alt_text");
safe_query("ALTER TABLE plugins_gallery ADD COLUMN IF NOT EXISTS photographer varchar(255) NOT NULL DEFAULT '' AFTER tags");
safe_query("ALTER TABLE plugins_gallery ADD COLUMN IF NOT EXISTS width int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER photographer");
safe_query("ALTER TABLE plugins_gallery ADD COLUMN IF NOT EXISTS height int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER width");

$galleryInstalledNameResult = safe_query("
    SHOW COLUMNS FROM settings_plugins_installed LIKE 'name'
");
$galleryInstalledHasName = $galleryInstalledNameResult && mysqli_num_rows($galleryInstalledNameResult) > 0;

$galleryRepairRows = [
    1  => ['img_68839134a01b4.jpg', 'Nebelschleier im Morgenwald', 'Sanfter Nebel zieht zwischen hohen Tannen durch den erwachenden Wald.', 'Morgennebel zwischen hohen Tannen im stillen Wald', 'wald,nebel,morgen,natur', 'Auren Vale', ''],
    2  => ['img_688391421701b.jpg', 'Lichter der Hafenstadt', 'Warme Fensterlichter spiegeln sich auf dem Wasser einer ruhigen Hafenpromenade.', 'Leuchtende Hafenfassaden mit Spiegelung im Abendwasser', 'hafen,stadt,abend,lichter', 'Mira Sol', 'wide'],
    3  => ['img_6883914ca02e4.jpg', 'Turm ueber den Wolken', 'Ein schmaler Steinturm ragt aus einer dramatischen Wolkendecke in den Himmel.', 'Alter Steinturm ragt aus dichter Wolkendecke empor', 'turm,wolken,drama,architektur', 'Cael Thorn', 'tall'],
    4  => ['img_688391578247d.jpg', 'Pfad durch das Goldfeld', 'Ein schmaler Weg fuehrt durch ein weit leuchtendes Feld aus Sommergras.', 'Schmaler Weg durch goldenes Sommergras unter weitem Himmel', 'feld,sommer,pfad,landschaft', 'Elin Hart', 'wide'],
    5  => ['img_68839167eadb7.jpg', 'Verlassene Gleise im Regen', 'Nasse Schienen schneiden durch ein stilles Industrieareal im feinen Regen.', 'Nasse Bahngleise in stiller Industrieatmosphaere bei Regen', 'gleise,regen,urban,industrie', 'Noah Voss', ''],
    6  => ['img_688391793db05.jpg', 'Schatten im roten Canyon', 'Tiefes Abendlicht legt harte Schatten ueber die Kanten eines roten Canyons.', 'Roter Canyon mit langen Schatten im Abendlicht', 'canyon,abend,felsen,wildnis', 'Sera Flint', 'tall'],
    7  => ['img_6883918321c6a.jpg', 'Bruecke aus Stahl und Dunst', 'Eine industrielle Bruecke verschwindet im fernen Dunst ueber dem Fluss.', 'Stahlbruecke ueber Fluss im hellen Morgendunst', 'bruecke,fluss,stahl,dunst', 'Tarin Cole', ''],
    8  => ['img_6883918f85626.jpg', 'Fenster zum Sternenmeer', 'Ein grosses Panoramafenster oeffnet den Blick auf einen sternklaren Nachthimmel.', 'Panoramafenster mit Blick in sternklare Nacht', 'nacht,sterne,fenster,atmosphaere', 'Lyra Quinn', 'big'],
    9  => ['img_6883919ad6c13.jpg', 'Steinbogen an der Kueste', 'Brandung rollt gegen einen natuerlichen Felsbogen an einer rauen Kueste.', 'Natuerlicher Steinbogen an rauer Kueste mit Brandung', 'kueste,fels,meer,brandung', 'Jon Maren', ''],
    10 => ['img_688391a5ecfa1.jpg', 'Laternenweg im Altstadtregen', 'Ein enger Gassenweg wird von warmen Laternen in nassem Kopfsteinpflaster gespiegelt.', 'Nasse Altstadtgasse mit warmen Laternen bei Regen', 'altstadt,regen,laternen,gasse', 'Isla Verne', ''],
    11 => ['img_688391b3c986c.jpg', 'Schwarzsee im Nordwind', 'Dunkles Wasser liegt unter schnellen Wolken und kuehlem Wind am Ufer.', 'Dunkler See unter schnellen Wolken und kuehlem Wind', 'see,wolken,wasser,nordwind', 'Riven Ash', 'wide'],
    12 => ['img_688391be98734.jpg', 'Treppenhaus aus Licht', 'Helles Seitenlicht zeichnet grafische Linien ueber ein minimalistisches Treppenhaus.', 'Minimalistisches Treppenhaus mit starkem Seitenlicht', 'architektur,licht,linien,interior', 'Nila Crest', ''],
];

foreach ($galleryRepairRows as $id => $row) {
    [$filename, $title, $caption, $altText, $tags, $photographer, $class] = $row;
    safe_query("
        UPDATE plugins_gallery
        SET
            filename = '{$filename}',
            title = CASE WHEN title IS NULL OR title = '' OR title = CONCAT(id, '.jpg') THEN '{$title}' ELSE title END,
            caption = CASE WHEN caption IS NULL OR caption = '' THEN '{$caption}' ELSE caption END,
            alt_text = CASE WHEN alt_text IS NULL OR alt_text = '' OR alt_text = CONCAT(id, '.jpg') THEN '{$altText}' ELSE alt_text END,
            tags = CASE WHEN tags IS NULL OR tags = '' THEN '{$tags}' ELSE tags END,
            photographer = CASE WHEN photographer IS NULL OR photographer = '' THEN '{$photographer}' ELSE photographer END,
            class = '{$class}',
            category_id = CASE WHEN category_id IS NULL OR category_id = 0 THEN 1 ELSE category_id END
        WHERE id = {$id}
          AND (filename = CONCAT({$id}, '.jpg') OR filename = '' OR filename IS NULL)
    ");
}

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'gallery', 'admin_gallery', 1, 'T-Seven', 'https://www.nexpell.de', 'gallery', '', '1.0.3.3', 'includes/plugins/gallery/', 1, 1, 0, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_gallery', 'de', 'Gallery', 'gallery', NOW()),
        ('plugin_name_gallery', 'en', 'Gallery', 'gallery', NOW()),
        ('plugin_name_gallery', 'it', 'Gallery', 'gallery', NOW()),
        ('plugin_info_gallery', 'de', 'Mit diesem Plugin koennt ihr eine Gallery auf der Webseite anzeigen lassen.', 'gallery', NOW()),
        ('plugin_info_gallery', 'en', 'With this plugin you can display a gallery on the website.', 'gallery', NOW()),
        ('plugin_info_gallery', 'it', 'Con questo plugin puoi visualizzare una galleria sul sito web.', 'gallery', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

if ($galleryInstalledHasName) {
    safe_query("
        INSERT IGNORE INTO settings_plugins_installed
            (name, modulname, description, version, author, url, folder, installed_date)
        VALUES
            ('Gallery', 'gallery', 'With this plugin you can display a gallery on the website.', '1.0.3.3', 'T-Seven', 'https://www.nexpell.de', 'gallery', NOW())
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            description = VALUES(description),
            version = VALUES(version),
            author = VALUES(author),
            url = VALUES(url),
            folder = VALUES(folder),
            installed_date = NOW()
    ");
} else {
    safe_query("
        INSERT IGNORE INTO settings_plugins_installed
            (modulname, description, version, author, url, folder, installed_date)
        VALUES
            ('gallery', 'With this plugin you can display a gallery on the website.', '1.0.3.3', 'T-Seven', 'https://www.nexpell.de', 'gallery', NOW())
        ON DUPLICATE KEY UPDATE
            description = VALUES(description),
            version = VALUES(version),
            author = VALUES(author),
            url = VALUES(url),
            folder = VALUES(folder),
            installed_date = NOW()
    ");
}

$linkID = 0;
$linkRes = safe_query("
    SELECT linkID FROM navigation_dashboard_links
    WHERE modulname = 'gallery' AND url = 'admincenter.php?site=admin_gallery'
    ORDER BY linkID ASC LIMIT 1
");
if ($linkRes && ($linkRow = mysqli_fetch_assoc($linkRes))) {
    $linkID = (int) ($linkRow['linkID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_links
            (catID, modulname, url, sort)
        VALUES
            (9, 'gallery', 'admincenter.php?site=admin_gallery', 1)
    ");
    $linkID = (int) mysqli_insert_id($_database);
}

if ($linkID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_dashboard_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_link_{$linkID}', 'de', 'Gallery', 'gallery', NOW()),
            ('nav_link_{$linkID}', 'en', 'Gallery', 'gallery', NOW()),
            ('nav_link_{$linkID}', 'it', 'Gallery', 'gallery', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

$snavID = 0;
$snavRes = safe_query("
    SELECT snavID FROM navigation_website_sub
    WHERE modulname = 'gallery' AND url = 'index.php?site=gallery'
    ORDER BY snavID ASC LIMIT 1
");
if ($snavRes && ($snavRow = mysqli_fetch_assoc($snavRes))) {
    $snavID = (int) ($snavRow['snavID'] ?? 0);
} else {
    safe_query("
        INSERT IGNORE INTO navigation_website_sub
            (mnavID, modulname, url, sort, indropdown, last_modified)
        VALUES
            (4, 'gallery', 'index.php?site=gallery', 1, 1, NOW())
    ");
    $snavID = (int) mysqli_insert_id($_database);
}

if ($snavID > 0) {
    safe_query("
        INSERT IGNORE INTO navigation_website_lang
            (content_key, language, content, modulname, updated_at)
        VALUES
            ('nav_sub_{$snavID}', 'de', 'Gallery', 'gallery', NOW()),
            ('nav_sub_{$snavID}', 'en', 'Gallery', 'gallery', NOW()),
            ('nav_sub_{$snavID}', 'it', 'Gallery', 'gallery', NOW())
        ON DUPLICATE KEY UPDATE
            content = VALUES(content),
            modulname = VALUES(modulname),
            updated_at = VALUES(updated_at)
    ");
}

safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'gallery')
");
?>
