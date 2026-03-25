<?php
declare(strict_types=1);

/**
 * ForumPermission.php
 *
 * ADMIN-ONLY Permission Resolver
 * Verwendet:
 *  - plugins_forum_permissions_board
 *  - plugins_forum_permissions_categories
 *  - plugins_forum_permissions_threads
 */

class ForumPermission
{
    /* =========================================================
       USER ROLES
    ========================================================= */

    public static function getUserRoles(int $userID): array
    {
        $roles = [];
        $res = safe_query("
            SELECT ur.role_name
            FROM user_role_assignments ura
            JOIN user_roles ur ON ur.roleID = ura.roleID
            WHERE ura.userID = {$userID}
        ");

        while ($row = mysqli_fetch_assoc($res)) {
            $roles[] = $row['role_name'];
        }
        return $roles;
    }

    public static function getRoleIDsByUser(int $userID): array
    {
        $ids = [];
        $res = safe_query("
            SELECT roleID
            FROM user_role_assignments
            WHERE userID = {$userID}
        ");

        while ($row = mysqli_fetch_assoc($res)) {
            $ids[] = (int)$row['roleID'];
        }
        return $ids;
    }

    public static function isAdmin(array $roles): bool
    {
        return in_array('Admin', $roles, true);
    }

    public static function isModerator(array $roles): bool
    {
        return in_array('Moderator', $roles, true);
    }

    /* =========================================================
       RAW PERMISSION FETCH
    ========================================================= */

    private static function getRawPermission(
        string $table,
        string $idField,
        int $objectID,
        array $roleIDs,
        string $permField
    ): ?int {
        if (!$roleIDs || $objectID <= 0) {
            return null;
        }

        $roles = implode(',', array_map('intval', $roleIDs));

        $res = safe_query("
            SELECT {$permField}
            FROM {$table}
            WHERE {$idField} = {$objectID}
              AND role_id IN ({$roles})
            ORDER BY {$permField} DESC
            LIMIT 1
        ");

        if ($res && mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            return (int)$row[$permField];
        }

        return null;
    }

    /* =========================================================
       RESOLVER (Thread → Category → Board)
    ========================================================= */

    public static function resolvePermission(
        int $userID,
        int $boardID,
        int $catID,
        int $threadID,
        string $field
    ): bool {

        $roles = self::getUserRoles($userID);

        if (self::isAdmin($roles) || self::isModerator($roles)) {
            return true;
        }

        $roleIDs = self::getRoleIDsByUser($userID);

        // THREAD
        if ($threadID > 0) {
            $v = self::getRawPermission(
                'plugins_forum_permissions_threads',
                'threadID',
                $threadID,
                $roleIDs,
                $field
            );
            if ($v !== null) return (bool)$v;
        }

        // CATEGORY
        if ($catID > 0) {
            $v = self::getRawPermission(
                'plugins_forum_permissions_categories',
                'catID',
                $catID,
                $roleIDs,
                $field
            );
            if ($v !== null) return (bool)$v;
        }

        // BOARD
        if ($boardID > 0) {
            $v = self::getRawPermission(
                'plugins_forum_permissions_board',
                'boardID',
                $boardID,
                $roleIDs,
                $field
            );
            if ($v !== null) return (bool)$v;
        }

        return false;
    }

    /* =========================================================
       CONVENIENCE METHODS
    ========================================================= */

    public static function canView(int $u, int $b, int $c, int $t): bool {
        return self::resolvePermission($u,$b,$c,$t,'can_view');
    }
    public static function canRead(int $u, int $b, int $c, int $t): bool {
        return self::resolvePermission($u,$b,$c,$t,'can_read');
    }
    public static function canPost(int $u, int $b, int $c): bool {
        return self::resolvePermission($u,$b,$c,0,'can_post');
    }
    public static function canReply(int $u, int $b, int $c, int $t): bool {
        return self::resolvePermission($u,$b,$c,$t,'can_reply');
    }
    public static function canEdit(int $u, int $b, int $c, int $t): bool {
        return self::resolvePermission($u,$b,$c,$t,'can_edit');
    }
    public static function canDelete(int $u, int $b, int $c, int $t): bool {
        return self::resolvePermission($u,$b,$c,$t,'can_delete');
    }
    public static function isThreadMod(int $u, int $b, int $c, int $t): bool {
        return self::resolvePermission($u,$b,$c,$t,'is_mod');
    }

    /* =========================================================
       ADMIN: LOCAL PERMISSION CHECK
    ========================================================= */

public static function hasLocalPermission(string $type, int|string $id): bool
{
    $id = (int)$id;

    if ($id <= 0) {
        return false;
    }

    switch ($type) {
        case 'forum':
            $table = 'plugins_forum_permissions_board';
            $field = 'boardID';
            break;

        case 'category':
            $table = 'plugins_forum_permissions_categories';
            $field = 'catID';
            break;

        case 'thread':
            $table = 'plugins_forum_permissions_threads';
            $field = 'threadID';
            break;

        default:
            return false;
    }

    $res = safe_query("
        SELECT 1
        FROM {$table}
        WHERE {$field} = {$id}
        LIMIT 1
    ");

    return ($res && mysqli_num_rows($res) > 0);
}


}
