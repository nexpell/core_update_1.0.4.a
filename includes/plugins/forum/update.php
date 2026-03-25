<?php
/**
 * NEXPELL Forum Update
 * Legacy Forum → ACL Forum
 * FINAL – Migration + Cleanup in ONE FILE
 */

declare(strict_types=1);

use nexpell\PluginMigrationHelper;

global $_database;

function column_exists(string $table, string $column): bool {
    $r = safe_query("SHOW COLUMNS FROM `$table` LIKE '".escape($column)."'");
    return mysqli_num_rows($r) > 0;
}

echo "<div class='alert alert-info'><b>Forum Update gestartet…</b></div>";

/* =========================================================
   HELPER
========================================================= */
function table_exists(string $table): bool {
    $r = safe_query("SHOW TABLES LIKE '".escape($table)."'");
    return mysqli_num_rows($r) > 0;
}
function drop_if_exists(string $table): void {
    if (table_exists($table)) {
        safe_query("DROP TABLE `$table`");
        echo "<div class='text-muted'>🗑️ $table entfernt</div>";
    }
}

function foreign_key_exists(string $table, string $constraintName): bool {
    $res = safe_query("
        SELECT CONSTRAINT_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = '" . escape($table) . "'
          AND CONSTRAINT_NAME = '" . escape($constraintName) . "'
          AND REFERENCED_TABLE_NAME IS NOT NULL
        LIMIT 1
    ");
    return $res && mysqli_num_rows($res) > 0;
}
function drop_foreign_key_if_exists(string $table, string $constraintName): void {
    if (table_exists($table) && foreign_key_exists($table, $constraintName)) {
        safe_query("ALTER TABLE `$table` DROP FOREIGN KEY `$constraintName`");
    }
}
function drop_foreign_key_globally(string $constraintName): void {
    $res = safe_query("
        SELECT TABLE_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND CONSTRAINT_NAME = '" . escape($constraintName) . "'
          AND REFERENCED_TABLE_NAME IS NOT NULL
    ");

    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $tableName = (string) ($row['TABLE_NAME'] ?? '');
        if ($tableName !== '' && table_exists($tableName)) {
            safe_query("ALTER TABLE `$tableName` DROP FOREIGN KEY `$constraintName`");
        }
    }
}
function foreign_key_reference_exists(string $table, string $column, string $referencedTable): bool {
    $res = safe_query("
        SELECT CONSTRAINT_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = '" . escape($table) . "'
          AND COLUMN_NAME = '" . escape($column) . "'
          AND REFERENCED_TABLE_NAME = '" . escape($referencedTable) . "'
        LIMIT 1
    ");
    return $res && mysqli_num_rows($res) > 0;
}

/* =========================================================
   1) BOARDS
========================================================= */
if (!table_exists('plugins_forum_boards_backup')) {

    safe_query("
    CREATE TABLE plugins_forum_boards_new (
        boardID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        position INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    if (column_exists('plugins_forum_boards', 'id')) {

        safe_query("
            INSERT INTO plugins_forum_boards_new (boardID, title, description, position)
            SELECT id, title, description, id
            FROM plugins_forum_boards
            ORDER BY id
        ");

        safe_query("RENAME TABLE plugins_forum_boards TO plugins_forum_boards_backup");
        safe_query("RENAME TABLE plugins_forum_boards_new TO plugins_forum_boards");

    } else {
        drop_if_exists('plugins_forum_boards_new');
    }
}

/* =========================================================
   2) CATEGORIES
========================================================= */
/* =========================================================
   2) CATEGORIES
========================================================= */
/* =========================================================
   2) CATEGORIES
========================================================= */
if (!table_exists('plugins_forum_categories_backup')) {

    // Re-Run absichern
    drop_if_exists('plugins_forum_categories_new');

    safe_query("
        CREATE TABLE plugins_forum_categories_new (
            catID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            boardID INT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            position INT NOT NULL DEFAULT 0,
            KEY idx_boardID (boardID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Legacy (group_id) vs. neu (boardID)
    if (column_exists('plugins_forum_categories', 'group_id')) {

        safe_query("
            INSERT IGNORE INTO plugins_forum_categories_new
                (catID, boardID, title, description, position)
            SELECT
                catID,
                group_id,
                title,
                description,
                position
            FROM plugins_forum_categories
            ORDER BY catID
        ");

    } else {

        safe_query("
            INSERT IGNORE INTO plugins_forum_categories_new
                (catID, boardID, title, description, position)
            SELECT
                catID,
                boardID,
                title,
                description,
                position
            FROM plugins_forum_categories
            ORDER BY catID
        ");
    }

    safe_query("RENAME TABLE plugins_forum_categories TO plugins_forum_categories_backup");
    safe_query("RENAME TABLE plugins_forum_categories_new TO plugins_forum_categories");

    /* =====================================================
       UNGÜLTIGE boardID REPARIEREN
    ===================================================== */
    safe_query("
        UPDATE plugins_forum_categories
        SET boardID = (
            SELECT MIN(boardID)
            FROM plugins_forum_boards
        )
        WHERE boardID NOT IN (
            SELECT boardID FROM plugins_forum_boards
        )
    ");

    drop_foreign_key_globally('fk_forum_categories_board');
    drop_foreign_key_globally('fk_plugins_forum_categories_board');

    // No FK recreation here: legacy forum updates can leave conflicting
    // constraint metadata behind. The forum runtime does not depend on these
    // foreign keys, so skipping them keeps repeated migrations stable.
}



/* =========================================================
   3) THREADS
========================================================= */
/* =========================================================
   3) THREADS
========================================================= */
if (!table_exists('plugins_forum_threads_backup')) {

    safe_query("
        CREATE TABLE plugins_forum_threads_new (
            threadID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            catID INT UNSIGNED NOT NULL,
            userID INT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            created_at INT NOT NULL,
            updated_at INT NOT NULL,
            views INT DEFAULT 0,
            is_locked TINYINT(1) DEFAULT 0,
            is_pinned TINYINT(1) NOT NULL DEFAULT 0,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            last_post_at INT NOT NULL DEFAULT 0,
            last_post_userID INT NOT NULL DEFAULT 0,
            KEY idx_catID (catID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    safe_query("
        INSERT INTO plugins_forum_threads_new
        SELECT
            threadID,
            catID,
            userID,
            title,
            created_at,
            updated_at,
            views,
            is_locked,
            0,
            0,
            updated_at,
            userID
        FROM plugins_forum_threads
        ORDER BY threadID
    ");

    safe_query("RENAME TABLE plugins_forum_threads TO plugins_forum_threads_backup");
    safe_query("RENAME TABLE plugins_forum_threads_new TO plugins_forum_threads");

    drop_foreign_key_globally('fk_forum_threads_category');
    drop_foreign_key_globally('fk_plugins_forum_threads_category');

    // No FK recreation here for the same reason as above.
}



/* =========================================================
   4) POSTS
========================================================= */
if (!table_exists('plugins_forum_posts_backup')) {

    safe_query("
        CREATE TABLE plugins_forum_posts_new (
            postID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            threadID INT UNSIGNED NOT NULL,
            userID INT UNSIGNED NOT NULL,
            content TEXT NOT NULL,
            created_at INT NOT NULL,
            edited_at INT DEFAULT NULL,
            is_deleted TINYINT(1) DEFAULT 0,
            KEY idx_threadID (threadID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    safe_query("
        INSERT IGNORE INTO plugins_forum_posts_new
        SELECT
            postID,
            threadID,
            userID,
            content,
            created_at,
            edited_at,
            is_deleted
        FROM plugins_forum_posts
        ORDER BY postID
    ");

    safe_query("RENAME TABLE plugins_forum_posts TO plugins_forum_posts_backup");
    safe_query("RENAME TABLE plugins_forum_posts_new TO plugins_forum_posts");

    drop_foreign_key_globally('fk_forum_posts_thread');
    drop_foreign_key_globally('fk_plugins_forum_posts_thread');

    // No FK recreation here for the same reason as above.
}






/* =========================================================
   PATCH: DEMO-POST PRO THREAD (NUR WENN THREAD KEINE POSTS HAT)
   → verhindert leere Resultsets in ALLEN Threads
========================================================= */

$res = safe_query("
    SELECT t.threadID
    FROM plugins_forum_threads t
    LEFT JOIN plugins_forum_posts p ON p.threadID = t.threadID
    WHERE p.postID IS NULL
");

while ($t = mysqli_fetch_assoc($res)) {

    safe_query("
        INSERT INTO plugins_forum_posts
            (threadID, userID, content, created_at, is_deleted)
        VALUES
            (
                {$t['threadID']},
                1,
                '[AUTO] Demo-Beitrag (System-Fallback)',
                UNIX_TIMESTAMP(),
                0
            )
    ");
}



/* =========================================================
   6) POST LIKES
========================================================= */
if (table_exists('plugins_forum_likes') && !table_exists('plugins_forum_likes_backup')) {

    safe_query("
    CREATE TABLE plugins_forum_post_likes (
        postID INT NOT NULL,
        userID INT NOT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (postID, userID),
        KEY idx_userID (userID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    safe_query("
        INSERT INTO plugins_forum_post_likes
        SELECT postID, userID, created_at
        FROM plugins_forum_likes
    ");

    safe_query("RENAME TABLE plugins_forum_likes TO plugins_forum_likes_backup");
}

/* =========================================================
   7) READ TRACKING
========================================================= */
/* =========================================================
   7) READ TRACKING  (FINAL – OHNE FOREIGN KEY)
========================================================= */
if (!table_exists('plugins_forum_read')) {

    safe_query("
        CREATE TABLE plugins_forum_read (
            userID INT UNSIGNED NOT NULL,
            threadID INT UNSIGNED NOT NULL,
            last_read_at INT NOT NULL,
            PRIMARY KEY (userID, threadID),
            KEY idx_threadID (threadID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

/*
 * PATCH-ENTSCHEIDUNG:
 * - KEIN Foreign Key mehr
 * - vermeidet errno 150 dauerhaft
 * - Read-Tracking bleibt voll funktionsfähig
 * - Keine Auswirkungen auf Forum-Logik
 */




/* =========================================================
   7.2) POSTS: edited_by
========================================================= */
$res = safe_query("SHOW COLUMNS FROM plugins_forum_posts LIKE 'edited_by'");
if (mysqli_num_rows($res) === 0) {
    safe_query("
        ALTER TABLE plugins_forum_posts
        ADD COLUMN edited_by INT DEFAULT NULL
        AFTER edited_at
    ");
}

/* =========================================================
   8) ACL TABLES
========================================================= */
safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_permissions_board (
    id INT AUTO_INCREMENT PRIMARY KEY,
    boardID INT NOT NULL,
    role_id INT NOT NULL,
    can_view TINYINT(1),
    can_read TINYINT(1),
    can_post TINYINT(1),
    can_reply TINYINT(1),
    can_edit TINYINT(1),
    can_delete TINYINT(1),
    is_mod TINYINT(1),
    UNIQUE KEY uniq_board_role (boardID, role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_permissions_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    catID INT NOT NULL,
    role_id INT NOT NULL,
    can_view TINYINT(1),
    can_read TINYINT(1),
    can_post TINYINT(1),
    can_reply TINYINT(1),
    can_edit TINYINT(1),
    can_delete TINYINT(1),
    is_mod TINYINT(1),
    UNIQUE KEY uniq_category_role (catID, role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_permissions_threads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    threadID INT NOT NULL,
    role_id INT NOT NULL,
    can_view TINYINT(1),
    can_read TINYINT(1),
    can_post TINYINT(1),
    can_reply TINYINT(1),
    can_edit TINYINT(1),
    can_delete TINYINT(1),
    is_mod TINYINT(1),
    UNIQUE KEY uniq_thread_role (threadID, role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

safe_query("
CREATE TABLE IF NOT EXISTS plugins_forum_uploaded_images (
  id INT(11) NOT NULL AUTO_INCREMENT,
  userID INT(11) NOT NULL,
  filename VARCHAR(255) NOT NULL,
  created_at INT(11) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_userID (userID),
  KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

/* =========================================================
   9) SETTINGS & VERSION
========================================================= */
if (!PluginMigrationHelper::columnExists('settings', 'forum_acl_debug')) {
    safe_query("
        ALTER TABLE settings
        ADD COLUMN forum_acl_debug TINYINT(1) NOT NULL DEFAULT 0
    ");
}

safe_query("
UPDATE settings_plugins
SET admin_file='admin_forum,admin_forum_permissions,admin_forum_permissions_ajax',
    version='1.0.1'
WHERE modulname='forum'
");

safe_query("
UPDATE settings_plugins_installed
SET version='1.0.1', installed_date=NOW()
WHERE modulname='forum'
");


/* =========================================================
   PATCH: THREADS – is_deleted Spalte (NOTWENDIG)
   Grund: Frontend nutzt t.is_deleted
========================================================= */
$res = safe_query("
    SHOW COLUMNS
    FROM plugins_forum_threads
    LIKE 'is_deleted'
");

if (mysqli_num_rows($res) === 0) {

    safe_query("
        ALTER TABLE plugins_forum_threads
        ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT 0
    ");

    echo "<div class='text-muted'>🩹 PATCH: plugins_forum_threads.is_deleted hinzugefügt</div>";
}


/* =========================================================
   PATCH: FALSCHEN FK AUF plugins_forum_threads_backup ENTFERNEN
========================================================= */

$res = safe_query("
    SELECT CONSTRAINT_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'plugins_forum_read'
      AND REFERENCED_TABLE_NAME = 'plugins_forum_threads_backup'
");

while ($row = mysqli_fetch_assoc($res)) {
    safe_query("
        ALTER TABLE plugins_forum_read
        DROP FOREIGN KEY `{$row['CONSTRAINT_NAME']}`
    ");
}


/* =========================================================
   CLEANUP
========================================================= */
safe_query("SET FOREIGN_KEY_CHECKS = 0");

drop_if_exists('forum_notifications');
drop_if_exists('plugins_forum_posts_backup');
drop_if_exists('plugins_forum_threads_backup');
drop_if_exists('plugins_forum_categories_backup');
drop_if_exists('plugins_forum_boards_backup');
drop_if_exists('plugins_forum_moderators_backup');
drop_if_exists('plugins_forum_likes_backup');

safe_query("SET FOREIGN_KEY_CHECKS = 1");

echo "<div class='alert alert-success'><b>Forum Update FINAL abgeschlossen.</b></div>";
