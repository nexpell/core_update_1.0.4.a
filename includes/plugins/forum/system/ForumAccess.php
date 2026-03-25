<?php
declare(strict_types=1);

namespace nexpell\forum;

/**
 * ForumAccess
 *
 * ❗ WICHTIG:
 * - KEINE eigene Logik
 * - KEINE Defaults
 * - KEINE Repository-Zugriffe
 *
 * ➜ Diese Klasse ist NUR ein Wrapper um ForumACL
 */
class ForumAccess
{
    /* =====================================================
       BASIS
    ===================================================== */

    public static function canView(
        int $userID,
        int $roleID,
        int $boardID,
        int $categoryID,
        int $threadID = 0
    ): bool {
        return ForumACL::check($userID, $roleID, $boardID, $categoryID, $threadID, 'view');
    }

    public static function canRead(
        int $userID,
        int $roleID,
        int $boardID,
        int $categoryID,
        int $threadID = 0
    ): bool {
        return ForumACL::check($userID, $roleID, $boardID, $categoryID, $threadID, 'read');
    }

    public static function canPost(
        int $userID,
        int $roleID,
        int $boardID,
        int $categoryID
    ): bool {
        return ForumACL::check($userID, $roleID, $boardID, $categoryID, 0, 'post');
    }

    public static function canReply(
    int $userID,
    int $roleID,
    int $boardID,
    int $categoryID,
    int $threadID,
    bool $isLocked = false
): bool {

    // 🔒 Gesperrt → nur Mods/Admin
    if ($isLocked && !self::isModerator($userID, $roleID, $boardID, $categoryID, $threadID)) {
        return false;
    }

    return ForumACL::check(
        $userID,
        $roleID,
        $boardID,
        $categoryID,
        $threadID,
        'reply'
    );
}


    /* =====================================================
       POSTS
    ===================================================== */

    public static function canEditPost(
        int $userID,
        int $roleID,
        int $postUserID,
        int $boardID,
        int $categoryID,
        int $threadID
    ): bool {

        // Eigener Beitrag → edit
        if ($userID === $postUserID) {
            return ForumACL::check($userID, $roleID, $boardID, $categoryID, $threadID, 'edit');
        }

        // Fremder Beitrag → mod
        return ForumACL::check($userID, $roleID, $boardID, $categoryID, $threadID, 'mod');
    }

    public static function canDeletePost(
        int $userID,
        int $roleID,
        int $postUserID,
        int $boardID,
        int $categoryID,
        int $threadID
    ): bool {

        if ($userID === $postUserID) {
            return ForumACL::check($userID, $roleID, $boardID, $categoryID, $threadID, 'delete');
        }

        return ForumACL::check($userID, $roleID, $boardID, $categoryID, $threadID, 'mod');
    }

    /* =====================================================
       MODERATION
    ===================================================== */

    public static function isModerator(
        int $userID,
        int $roleID,
        int $boardID,
        int $categoryID,
        int $threadID = 0
    ): bool {
        return ForumACL::check($userID, $roleID, $boardID, $categoryID, $threadID, 'mod');
    }

    /* =====================================================
       USER FEEDBACK
    ===================================================== */

    public static function getDenyReason(
        int $userID,
        bool $isLocked,
        bool $canReply
    ): string {

        if ($userID === 0) {
            return 'Bitte melde dich an, um zu antworten.';
        }

        if ($isLocked) {
            return 'Dieser Thread ist gesperrt.';
        }

        if (!$canReply) {
            return 'Du hast keine Berechtigung, in diesem Thread zu antworten.';
        }

        return '';
    }
}
