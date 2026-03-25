<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

global $languageService, $_database;

/* =========================================================
 * LANGUAGE / TEMPLATE
 * ========================================================= */
$lang = $languageService->detectLanguage();
$languageService->readPluginModule('forum');

$tpl = new Template();

$config = mysqli_fetch_array(
    safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1")
);

$class = htmlspecialchars($config['selected_style'] ?? '', ENT_QUOTES, 'UTF-8');

$data_array = [
    'class'    => $class,
    'title'    => $languageService->get('title'),
    'subtitle' => 'Forum'
];

echo $tpl->loadTemplate("forum", "head", $data_array, 'plugin');


/* =========================================================
 * SETTINGS (Pagination absichern!)
 * ========================================================= */
$perPage = (int)($settings['posts_per_page'] ?? 10);
if ($perPage <= 0) {
    $perPage = 10;
}

/* =========================================================
 * SQL – Letzte 5 Threads mit letztem Post
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

<div class="card mb-4">
  <div class="card-body p-3">

    <h5 class="mb-2">Letzte Beiträge</h5>
    <hr class="my-2">

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
                    $page     = (int)ceil($position / $perPage);
                }
            }

            /* =========================================================
             * SEO-LINK MIT PAGE + ANCHOR
             * ========================================================= */
            $link = SeoUrlHandler::convertToSeoUrl(
                "index.php?site=forum&action=thread&id={$threadID}&page={$page}"
            ) . "#post{$lastPostID}";
            ?>

            <div class="mb-2 pb-2 border-bottom small">
              <div class="text-muted mb-1" style="font-size:0.8rem;">
                <?php echo $lastPostDate; ?>
              </div>

              <div class="fw-semibold text-truncate" title="<?php echo $title; ?>">
                <a href="<?php echo $link; ?>"
                   class="text-decoration-none fw-bold text-truncate">
                    <?php echo $title; ?>
                </a>
              </div>

              <div class="text-muted" style="font-size:0.8rem;">
                Forum:
                <span class="text-primary"><?php echo $catTitle; ?></span><br>
                (<?php echo $replyCount; ?> Antworten)
                – <b><?php echo $lastUser; ?></b>
              </div>
            </div>

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
