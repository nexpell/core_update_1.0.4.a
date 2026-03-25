<?php
declare(strict_types=1);

namespace nexpell\forum;

if (!defined('IS_FORUM')) {
    die('Direct access not allowed');
}

class ForumACLRepository
{
    private int $boardID;
    private int $catID;
    private int $threadID;
    private int $roleID;

    public function __construct(int $boardID, int $catID, int $threadID, int $roleID)
    {
        $this->boardID  = $boardID;
        $this->catID    = $catID;
        $this->threadID = $threadID;
        $this->roleID   = $roleID;
    }

    /* =====================================================
       ACL CHECK (nur Daten + Priorität)
       KEINE DEFAULTS
    ===================================================== */
    public function can(string $perm): bool
{
    // 👑 Admin darf immer
    if ($this->roleID === 1) {
        return true;
    }

    $map = [
        'view'   => 'can_view',
        'read'   => 'can_read',
        'post'   => 'can_post',
        'reply'  => 'can_reply',
        'edit'   => 'can_edit',
        'delete' => 'can_delete',
        'mod'    => 'is_mod',
    ];

    if (!isset($map[$perm])) {
        return false;
    }

    $field = $map[$perm];

    /* ================= THREAD ================= */
    if ($this->threadID > 0) {
        $t = self::getThreadOverride($this->threadID, $this->roleID);
        if ($t && $t[$field] !== null) {
            return (bool)$t[$field];
        }
        // ❗ KEIN return false hier
    }

    /* ================= CATEGORY ================= */
    if ($this->catID > 0) {
        $c = self::getCategoryPermissions($this->catID, $this->roleID);
        if ($c && $c[$field] !== null) {
            return (bool)$c[$field];
        }
        // ❗ KEIN return false hier
    }

    /* ================= BOARD ================= */
    if ($this->boardID > 0) {
        $b = self::getBoardPermissions($this->boardID);
        if (
            isset($b[$this->roleID][$field]) &&
            $b[$this->roleID][$field] !== null
        ) {
            return (bool)$b[$this->roleID][$field];
        }
        // ❗ KEIN return false hier
    }

    // ❌ Nirgendwo explizit erlaubt
    return false;
}


    /* =====================================================
       BOARD PERMISSIONS
    ===================================================== */
    public static function getBoardPermissions(int $boardID): array
    {
        $perms = [];

        $res = safe_query("
            SELECT role_id, can_view, can_read, can_post, can_reply, can_edit, can_delete, is_mod
            FROM plugins_forum_permissions_board
            WHERE boardID = {$boardID}
        ");

        while ($row = mysqli_fetch_assoc($res)) {
            $rid = (int)$row['role_id'];

            $perms[$rid] = [
                'can_view'   => $row['can_view'],
                'can_read'   => $row['can_read'],
                'can_post'   => $row['can_post'],
                'can_reply'  => $row['can_reply'],
                'can_edit'   => $row['can_edit'],
                'can_delete' => $row['can_delete'],
                'is_mod'     => $row['is_mod'],
            ];
        }

        return $perms;
    }

    /* =====================================================
       CATEGORY PERMISSIONS
    ===================================================== */
    public static function getCategoryPermissions(int $catID, int $roleID): ?array
    {
        $res = safe_query("
            SELECT can_view, can_read, can_post, can_reply, can_edit, can_delete, is_mod
            FROM plugins_forum_permissions_categories
            WHERE catID = {$catID}
              AND role_id = {$roleID}
            LIMIT 1
        ");

        $row = mysqli_fetch_assoc($res);
        return $row ?: null;
    }

    /* =====================================================
       THREAD OVERRIDES
    ===================================================== */
    public static function getThreadOverride(int $threadID, int $roleID): ?array
    {
        $res = safe_query("
            SELECT can_view, can_read, can_post, can_reply, can_edit, can_delete, is_mod
            FROM plugins_forum_permissions_threads
            WHERE threadID = {$threadID}
              AND role_id = {$roleID}
            LIMIT 1
        ");

        $row = mysqli_fetch_assoc($res);
        return $row ?: null;
    }

    /* =====================================================
       HAS ANY ACL (KRITISCH!)
    ===================================================== */
    public static function boardHasAnyACL(int $boardID): bool
    {
        return self::hasAny('plugins_forum_permissions_board', 'boardID', $boardID);
    }

    public static function categoryHasAnyACL(int $catID): bool
    {
        return self::hasAny('plugins_forum_permissions_categories', 'catID', $catID);
    }

    public static function threadHasAnyACL(int $threadID): bool
    {
        return self::hasAny('plugins_forum_permissions_threads', 'threadID', $threadID);
    }

    private static function hasAny(string $table, string $field, int $id): bool
    {
        $res = safe_query("
            SELECT 1
            FROM {$table}
            WHERE {$field} = {$id}
            LIMIT 1
        ");

        return ($res && mysqli_num_rows($res) > 0);
    }

public static function threadHasAnyEntry(int $threadID): bool
{
    $res = safe_query("
        SELECT 1
        FROM plugins_forum_permissions_threads
        WHERE threadID = {$threadID}
        LIMIT 1
    ");
    return ($res && mysqli_num_rows($res) > 0);
}

public static function threadHasEffectiveACL(int $threadID): bool
{
    $res = safe_query("
        SELECT 1
        FROM plugins_forum_permissions_threads
        WHERE threadID = {$threadID}
          AND (
                can_view   = 1 OR
                can_read   = 1 OR
                can_post   = 1 OR
                can_reply  = 1 OR
                can_edit   = 1 OR
                can_delete = 1 OR
                is_mod     = 1
          )
        LIMIT 1
    ");
    return ($res && mysqli_num_rows($res) > 0);
}



}
