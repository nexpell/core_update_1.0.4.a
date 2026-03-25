<?php
/**
 * NEXPELL Forum Installer
 * FINAL – clean ACL design
 */


global $_database;

/* =========================================================
   BOARDS
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_boards (
    boardID INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    position INT NOT NULL DEFAULT 0,
    PRIMARY KEY (boardID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");

safe_query("
INSERT IGNORE INTO plugins_forum_boards (boardID, title, description, position) VALUES
(1, 'Allgemeine Diskussionen', 'Alles rund um die Community', 1),
(2, 'Technik & Support', 'Hardware, Software & Hilfe', 2),
(3, 'Community Talk', 'Off-Topic & Vorstellung', 3);
");


/* =========================================================
   CATEGORIES
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_categories (
    catID INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    boardID INT(10) UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    position INT NOT NULL DEFAULT 0,
    PRIMARY KEY (catID),
    KEY idx_boardID (boardID),
    CONSTRAINT fk_forum_categories_board
        FOREIGN KEY (boardID)
        REFERENCES plugins_forum_boards (boardID)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");

safe_query("
INSERT IGNORE INTO plugins_forum_categories (catID, boardID, title, description, position) VALUES
(1, 1, 'Allgemeines', 'Diskussionen rund um allgemeine Themen', 1),
(2, 2, 'Support', 'Hilfe, Fragen und technische Probleme', 2),
(3, 3, 'Off-Topic', 'Alles was sonst nirgends reinpasst', 3);
");


/* =========================================================
   THREADS
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_threads (
    threadID INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    catID INT(10) UNSIGNED NOT NULL,
    userID INT(10) UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    created_at INT NOT NULL,
    updated_at INT NOT NULL,
    views INT DEFAULT 0,
    is_locked TINYINT(1) DEFAULT 0,
    is_pinned TINYINT(1) NOT NULL DEFAULT 0,
    last_post_at INT NOT NULL DEFAULT 0,
    last_post_userID INT NOT NULL DEFAULT 0,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (threadID),
    KEY idx_catID (catID),
    CONSTRAINT fk_forum_threads_category
        FOREIGN KEY (catID)
        REFERENCES plugins_forum_categories (catID)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");


/* =========================================================
   POSTS
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_posts (
    postID INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    threadID INT(10) UNSIGNED NOT NULL,
    userID INT(10) UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    created_at INT NOT NULL,
    edited_at INT DEFAULT NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    edited_by INT DEFAULT NULL,
    PRIMARY KEY (postID),
    KEY idx_threadID (threadID),
    CONSTRAINT fk_forum_posts_thread
        FOREIGN KEY (threadID)
        REFERENCES plugins_forum_threads (threadID)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");


/* =========================================================
   READ TRACKING
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_read (
    userID INT NOT NULL,
    threadID INT NOT NULL,
    last_read_at INT NOT NULL,
    PRIMARY KEY (userID, threadID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");


/* =========================================================
   POST LIKES
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_post_likes (
    postID INT NOT NULL,
    userID INT NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (postID, userID),
    KEY idx_userID (userID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");


/* =========================================================
   ACL – BOARD
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_permissions_board (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    boardID INT(10) UNSIGNED NOT NULL,
    role_id INT(10) UNSIGNED NOT NULL,
    can_view TINYINT(1) DEFAULT NULL,
    can_read TINYINT(1) DEFAULT NULL,
    can_post TINYINT(1) DEFAULT NULL,
    can_reply TINYINT(1) DEFAULT NULL,
    can_edit TINYINT(1) DEFAULT NULL,
    can_delete TINYINT(1) DEFAULT NULL,
    is_mod TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_board_role (boardID, role_id),
    KEY idx_role (role_id),
    CONSTRAINT fk_acl_board
        FOREIGN KEY (boardID)
        REFERENCES plugins_forum_boards (boardID)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");


/* =========================================================
   ACL – CATEGORY
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_permissions_categories (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    catID INT(10) UNSIGNED NOT NULL,
    role_id INT(10) UNSIGNED NOT NULL,
    can_view TINYINT(1) DEFAULT NULL,
    can_read TINYINT(1) DEFAULT NULL,
    can_post TINYINT(1) DEFAULT NULL,
    can_reply TINYINT(1) DEFAULT NULL,
    can_edit TINYINT(1) DEFAULT NULL,
    can_delete TINYINT(1) DEFAULT NULL,
    is_mod TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_category_role (catID, role_id),
    KEY idx_role (role_id),
    CONSTRAINT fk_acl_category
        FOREIGN KEY (catID)
        REFERENCES plugins_forum_categories (catID)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");


/* =========================================================
   ACL – THREAD
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_permissions_threads (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    threadID INT(10) UNSIGNED NOT NULL,
    role_id INT(10) UNSIGNED NOT NULL,
    can_view TINYINT(1) DEFAULT NULL,
    can_read TINYINT(1) DEFAULT NULL,
    can_post TINYINT(1) DEFAULT NULL,
    can_reply TINYINT(1) DEFAULT NULL,
    can_edit TINYINT(1) DEFAULT NULL,
    can_delete TINYINT(1) DEFAULT NULL,
    is_mod TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_thread_role (threadID, role_id),
    CONSTRAINT fk_acl_thread
        FOREIGN KEY (threadID)
        REFERENCES plugins_forum_threads (threadID)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");

/* ============================================================
     * 9) GLOBAL ACL TEMPLATE (roleID 1–16)
     * ============================================================ */
 $res = safe_query("SELECT boardID FROM plugins_forum_boards");
while ($b = mysqli_fetch_assoc($res)) {

    $boardID = (int)$b['boardID'];

    safe_query("
        INSERT IGNORE INTO plugins_forum_permissions_board
        (boardID, role_id, can_view, can_read, can_post, can_reply, can_edit, can_delete, is_mod)
        VALUES
        -- Admin
        ($boardID, 1, 1,1,1,1,1,1,1),

        -- Moderator
        ($boardID, 7, 1,1,1,1,NULL,NULL,1),

        -- Registrierte User
        ($boardID, 4, 1,1,1,1,NULL,NULL,NULL),
        ($boardID, 5, 1,1,1,1,NULL,NULL,NULL),
        ($boardID, 6, 1,1,1,1,NULL,NULL,NULL),
        ($boardID, 8, 1,1,1,1,NULL,NULL,NULL),
        ($boardID, 9, 1,1,1,1,NULL,NULL,NULL),
        ($boardID,10, 1,1,1,1,NULL,NULL,NULL),
        ($boardID,12, 1,1,1,1,NULL,NULL,NULL),
        ($boardID,13, 1,1,1,1,NULL,NULL,NULL),
        ($boardID,14, 1,1,1,1,NULL,NULL,NULL),
        ($boardID,16, 1,1,1,1,NULL,NULL,NULL),

        -- Gast
        ($boardID,11, 1,1,NULL,NULL,NULL,NULL,NULL),

        -- Gesperrt / Readonly
        ($boardID,15, 1,1,NULL,NULL,NULL,NULL,NULL)
    ");
}


$res = safe_query("SELECT catID FROM plugins_forum_categories");
while ($c = mysqli_fetch_assoc($res)) {

    $catID = (int)$c['catID'];

    safe_query("
        INSERT IGNORE INTO plugins_forum_permissions_categories
        (catID, role_id, can_view, can_read, can_post, can_reply, can_edit, can_delete, is_mod)
        VALUES
        -- Admin
        ($catID, 1, 1,1,1,1,1,1,1),

        -- Moderator
        ($catID, 7, 1,1,1,1,NULL,NULL,1),

        -- Registrierte User
        ($catID, 4, 1,1,1,1,NULL,NULL,NULL),
        ($catID, 5, 1,1,1,1,NULL,NULL,NULL),
        ($catID, 6, 1,1,1,1,NULL,NULL,NULL),
        ($catID, 8, 1,1,1,1,NULL,NULL,NULL),
        ($catID, 9, 1,1,1,1,NULL,NULL,NULL),
        ($catID,10, 1,1,1,1,NULL,NULL,NULL),
        ($catID,12, 1,1,1,1,NULL,NULL,NULL),
        ($catID,13, 1,1,1,1,NULL,NULL,NULL),
        ($catID,14, 1,1,1,1,NULL,NULL,NULL),
        ($catID,16, 1,1,1,1,NULL,NULL,NULL),

        -- Gast
        ($catID,11, 1,1,NULL,NULL,NULL,NULL,NULL),

        -- Gesperrt
        ($catID,15, 1,1,NULL,NULL,NULL,NULL,NULL)
    ");
}


safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_uploaded_images (
  id INT(11) NOT NULL AUTO_INCREMENT,
  userID INT(11) NOT NULL,
  filename VARCHAR(255) NOT NULL,
  created_at INT(11) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_userID (userID),
  KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
");




/* =========================================================
   PLUGIN REGISTRATION
========================================================= */
safe_query("
INSERT IGNORE INTO settings_plugins
(pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
VALUES
('', 'forum', 'admin_forum,admin_forum_permissions',
1, 'T-Seven', 'https://www.nexpell.de', 'forum,forum_boards,forum_category,forum_thread,forum_actions,',
'', '1.0.0', 'includes/plugins/forum/', 1, 1, 1, 1, 'deactivated');
");

safe_query("
INSERT IGNORE INTO settings_plugins_lang
(`content_key`, `language`, `content`, `updated_at`)
VALUES
('plugin_name_forum', 'de', 'Forum', NOW()),
('plugin_name_forum', 'en', 'Forum', NOW()),
('plugin_name_forum', 'it', 'Forum', NOW()),

('plugin_info_forum', 'de', 'Forum Plugin für Diskussionen', NOW()),
('plugin_info_forum', 'en', 'Forum plugin for discussions', NOW()),
('plugin_info_forum', 'it', 'Plugin forum per discussioni', NOW())
");


/* =========================================================
   WIDGETS
========================================================= */
safe_query("
INSERT IGNORE INTO settings_widgets (widget_key, title, plugin, modulname) VALUES
('widget_forum_content', 'Forum Content Widget', 'forum', 'forum'),
('widget_forum_sidebar', 'Forum Sidebar Widget', 'forum', 'forum');
");


/* =========================================================
   DASHBOARD NAVIGATION
========================================================= */
safe_query("
INSERT IGNORE INTO navigation_dashboard_links (catID, modulname, url, sort)
VALUES
(8, 'forum', 'admincenter.php?site=admin_forum', 1);
");

$linkID = mysqli_insert_id($_database);

safe_query("
INSERT IGNORE INTO navigation_dashboard_lang
(`content_key`, `language`, `content`, `updated_at`)
VALUES
('nav_link_{$linkID}', 'de', 'Forum', NOW()),
('nav_link_{$linkID}', 'en', 'Forum', NOW()),
('nav_link_{$linkID}', 'it', 'Forum', NOW())
");


/* =========================================================
   WEBSITE NAVIGATION
========================================================= */
safe_query("
INSERT IGNORE INTO navigation_website_sub
(mnavID, modulname, url, sort, indropdown, last_modified)
VALUES
(3, 'forum', 'index.php?site=forum', 1, 1, NOW());
");

$snavID = mysqli_insert_id($_database);

safe_query("
INSERT IGNORE INTO navigation_website_lang
(`content_key`, `language`, `content`, `updated_at`)
VALUES
('nav_sub_{$snavID}', 'de', 'Forum', NOW()),
('nav_sub_{$snavID}', 'en', 'Forum', NOW()),
('nav_sub_{$snavID}', 'it', 'Forum', NOW())
");


/* =========================================================
   ADMIN RIGHTS
========================================================= */
safe_query("
INSERT IGNORE INTO user_role_admin_navi_rights
(id, roleID, type, modulname)
VALUES
('', 1, 'link', 'forum');
");
// =========================================================
// SETTINGS: ACL DEBUG FLAG
// =========================================================
safe_query("
ALTER TABLE settings
ADD COLUMN IF NOT EXISTS forum_acl_debug TINYINT(1) NOT NULL DEFAULT 0;
");



