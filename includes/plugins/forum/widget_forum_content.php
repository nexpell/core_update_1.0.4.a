<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

global $languageService,$_database;

$lang = $languageService->detectLanguage();
$languageService->readPluginModule('forum');

$tpl = new Template();
$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style'] ?? '');

// Header-Daten
$data_array = [
    'class'    => $class,
    'title' => $languageService->get('title'),
    'subtitle' => 'Forum'
];

//echo $tpl->loadTemplate("forum", "head", $data_array, 'plugin');

/* =========================================================
 * SETTINGS
 * ========================================================= */
$perPage = (int)($settings['posts_per_page'] ?? 10);
if ($perPage <= 0) {
    $perPage = 10;
}

/* =========================================================
 * SQL: Letzte 5 Threads mit letztem Post
 * ========================================================= */
$sql = "
    SELECT 
        t.threadID,
        t.title AS threadTitle,
        c.title AS categoryTitle,
        MAX(p.created_at) AS last_post_date,
        MAX(p.postID) AS lastPostID,
        (
            SELECT u.username
            FROM plugins_forum_posts p2
            LEFT JOIN users u ON u.userID = p2.userID
            WHERE p2.threadID = t.threadID
              AND p2.is_deleted = 0
            ORDER BY p2.created_at DESC
            LIMIT 1
        ) AS lastUser,
        COUNT(p.postID) - 1 AS replyCount
    FROM plugins_forum_threads t
    LEFT JOIN plugins_forum_categories c 
        ON t.catID = c.catID
    INNER JOIN plugins_forum_posts p 
        ON p.threadID = t.threadID 
       AND p.is_deleted = 0
    WHERE t.is_locked = 0
      AND t.is_deleted = 0
    GROUP BY t.threadID
    ORDER BY last_post_date DESC
    LIMIT 5
";
?>

<div class="card mb-4 mt-4">
  <div class="card-body">

    <h4 class="mb-1">
      <span class="head-boxes-title2">
        <i class="bi bi-chat-dots-fill me-2"></i>
        Letzter Beitrag
      </span>
    </h4>
    <small class="text-muted">
        Die letzten 5 Beiträge aus dem Forum
    </small>

    <hr>

    <?php
    if ($result = $_database->query($sql)) {

        while ($row = $result->fetch_assoc()) {

            $threadID   = (int)$row['threadID'];
            $lastPostID = (int)$row['lastPostID'];

            $title    = htmlspecialchars($row['threadTitle'] ?? 'Unbekannter Titel', ENT_QUOTES, 'UTF-8');
            $catTitle = htmlspecialchars($row['categoryTitle'] ?? 'Unbekannte Kategorie', ENT_QUOTES, 'UTF-8');
            $lastUser = htmlspecialchars($row['lastUser'] ?? 'Unbekannt', ENT_QUOTES, 'UTF-8');

            $replyCount = max(0, (int)$row['replyCount']);

            $lastPostRaw = $row['last_post_date'] ?? null;
            if (is_numeric($lastPostRaw)) {
                $lastPostDate = date("d.m.Y H:i", (int)$lastPostRaw);
            } elseif (!empty($lastPostRaw)) {
                $ts = strtotime((string)$lastPostRaw);
                $lastPostDate = $ts ? date("d.m.Y H:i", $ts) : "Datum unbekannt";
            } else {
                $lastPostDate = "Datum unbekannt";
            }

            /* =========================================================
             * SEITE DES LETZTEN POSTS BERECHNEN
             * ========================================================= */
            $page = 1;

            if ($lastPostID > 0) {
                $posRes = $_database->query("
                    SELECT COUNT(*) AS pos
                    FROM plugins_forum_posts
                    WHERE threadID = {$threadID}
                      AND is_deleted = 0
                      AND created_at <= (
                          SELECT created_at
                          FROM plugins_forum_posts
                          WHERE postID = {$lastPostID}
                          LIMIT 1
                      )
                ");

                if ($posRow = $posRes->fetch_assoc()) {
                    $position = max(1, (int)$posRow['pos']);
                    $page = (int)ceil($position / $perPage);
                }
            }

            /* =========================================================
             * SEO-LINK MIT PAGE + ANCHOR
             * ========================================================= */
            $link = SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=thread&id={$threadID}&page={$page}"
            ) . "#post{$lastPostID}";
            ?>

            <div class="d-flex flex-wrap mb-1 align-items-start">

              <div class="flex-shrink-0 text-center me-3 mb-2">
                <span class="badge bg-secondary">
                    <?php echo $lastPostDate; ?>
                </span>
              </div>

              <div class="flex-grow-1 text-truncate" style="min-width:0;">
                <div class="text-truncate">

                  <span class="d-inline-flex align-items-center gap-1 flex-wrap">
                    <span class="text-nowrap">
                      Forum:
                      <span class="text-primary">
                        <?php echo $catTitle; ?>
                      </span>
                      /
                    </span>

                    <a href="<?php echo $link; ?>"
                       class="text-decoration-none fw-bold text-truncate"
                       style="max-width:100%;">
                        <?php echo $title; ?>
                    </a>
                  </span>

                  <small class="text-muted d-block mt-1">
                    (<?php echo $replyCount; ?> Antworten)
                    – Letzter Beitrag von
                    <b><?php echo $lastUser; ?></b>
                  </small>

                </div>
              </div>

            </div>

            <hr class="my-1">

            <?php
        }

        $result->free();

    } else {
        echo "
            <div class='alert alert-danger'>
                Fehler bei der Datenbankabfrage:<br>
                " . htmlspecialchars($_database->error, ENT_QUOTES, 'UTF-8') . "
            </div>
        ";
    }
    ?>

  </div>
</div>
