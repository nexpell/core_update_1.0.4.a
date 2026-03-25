<?php
declare(strict_types=1);

namespace nexpell\forum;

class ForumContext
{
    public int $boardID    = 0;
    public int $categoryID = 0;
    public int $threadID   = 0;

    /**
     * Ermittelt automatisch den aktuellen Forum-Kontext
     * (Board / Kategorie / Thread)
     */
public static function fromRequest(): self
{
    $ctx = new self();

    $action = $_GET['action'] ?? '';
    $id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // =====================================================
    // THREAD
    // =====================================================
    if ($action === 'thread' && $id > 0) {

        $ctx->threadID = $id;

        $res = safe_query("
            SELECT t.threadID, t.catID, c.boardID
            FROM plugins_forum_threads t
            JOIN plugins_forum_categories c ON c.catID = t.catID
            WHERE t.threadID = {$id}
            LIMIT 1
        ");

        if ($row = mysqli_fetch_assoc($res)) {
            $ctx->threadID   = (int)$row['threadID'];
            $ctx->categoryID = (int)$row['catID'];
            $ctx->boardID    = (int)$row['boardID'];
        }

        return $ctx;
    }

    // =====================================================
    // CATEGORY
    // =====================================================
    if ($action === 'category' && $id > 0) {

        $ctx->categoryID = $id;

        $res = safe_query("
            SELECT catID, boardID
            FROM plugins_forum_categories
            WHERE catID = {$id}
            LIMIT 1
        ");

        if ($row = mysqli_fetch_assoc($res)) {
            $ctx->categoryID = (int)$row['catID'];
            $ctx->boardID    = (int)$row['boardID'];
        }

        return $ctx;
    }

    // =====================================================
    // BOARD
    // =====================================================
    if ($action === 'board' && $id > 0) {
        $ctx->boardID = $id;
    }

    return $ctx;
}

}
