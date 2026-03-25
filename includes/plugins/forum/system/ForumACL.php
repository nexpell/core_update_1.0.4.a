<?php
declare(strict_types=1);

namespace nexpell\forum;

require_once __DIR__ . '/ForumACLRepository.php';

class ForumACL
{
    /**
     * Zentrale ACL-Prüfung
     *
     * Priorität:
     *   Thread → Kategorie → Board
     *
     * Grundregel:
     * - Existiert irgendwo ACL → DEFAULT = DENY
     * - Nur explizite 1/0 zählen
     */
    public static function check(
        int $userID,
        int $roleID,
        int $boardID,
        int $categoryID,
        int $threadID,
        string $permission
    ): bool {

        /* =========================
           ADMIN
        ========================= */
        if ($roleID === 1) {
            return true;
        }

        $map = [
            'view'   => 'can_view',
            'read'   => 'can_read',
            'post'   => 'can_post',
            'reply'  => 'can_reply',
            'edit'   => 'can_edit',
            'delete' => 'can_delete',
            'mod'    => 'is_mod'
        ];

        if (!isset($map[$permission])) {
            return false;
        }

        $field = $map[$permission];
        $guestRoleID = 11; // 🔒 EINHEITLICH GAST

        /* =========================
           THREAD
        ========================= */
        if ($threadID > 0) {

            $row = ForumACLRepository::getThreadOverride($threadID, $roleID);
            if ($row && $row[$field] !== null) {
                return (bool)$row[$field];
            }

            if (
                ForumACLRepository::threadHasEffectiveACL($threadID)
                && in_array($permission, ['view', 'read', 'post', 'reply'], true)
            ) {
                return false;
            }
        }

        /* =========================
           CATEGORY
        ========================= */
        if ($categoryID > 0) {

            $row = ForumACLRepository::getCategoryPermissions($categoryID, $roleID);
            if ($row && $row[$field] !== null) {
                return (bool)$row[$field];
            }

            if (ForumACLRepository::categoryHasAnyACL($categoryID)) {
                return false;
            }
        }

        /* =========================
           BOARD
        ========================= */
        if ($boardID > 0) {

            $perms = ForumACLRepository::getBoardPermissions($boardID);

            if (isset($perms[$roleID][$field]) && $perms[$roleID][$field] !== null) {
                return (bool)$perms[$roleID][$field];
            }

            if (ForumACLRepository::boardHasAnyACL($boardID)) {
                return false;
            }
        }

        /* =========================
           DEFAULT (NUR WENN GAR KEINE ACL EXISTIERT)
        ========================= */
        if ($userID === 0 || $roleID === $guestRoleID) {
            return in_array($permission, ['view', 'read'], true);
        }

        return true;
    }

    /**
     * Shortcut für Kategorie-Rendering
     */
    public static function canView(array $ctx, int $roleID): bool
    {
        return self::check(
            (int)($ctx['userID']   ?? 0),
            $roleID,
            (int)($ctx['boardID']  ?? 0),
            (int)($ctx['categoryID'] ?? 0),
            (int)($ctx['threadID'] ?? 0),
            'view'
        );
    }
}
