<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
global $_database;

$IDLE_LIMIT = 300;
$currentUserID = !empty($_SESSION['userID']) ? (int)$_SESSION['userID'] : 0;
$currentSessionId = session_id();

if ($currentUserID > 0) {
    $sql = "
        UPDATE users
        SET
            login_time = COALESCE(login_time, NOW()),
            last_activity = NOW(),
            is_online = 1
        WHERE userID = ?
    ";
    $stmt = $_database->prepare($sql);
    $stmt->bind_param("i", $currentUserID);
    $stmt->execute();
    $stmt->close();

    $sessionSql = "
        UPDATE user_sessions
        SET last_activity = ?
        WHERE session_id = ? AND userID = ?
    ";
    $sessionStmt = $_database->prepare($sessionSql);
    if ($sessionStmt) {
        $sessionLastSeen = time();
        $sessionStmt->bind_param("isi", $sessionLastSeen, $currentSessionId, $currentUserID);
        $sessionStmt->execute();
        $sessionStmt->close();
    }
}

if ($_database instanceof mysqli) {
    $cleanupSql = "
        UPDATE users u
        LEFT JOIN (
            SELECT userID, MAX(last_activity) AS session_last_seen
            FROM user_sessions
            GROUP BY userID
        ) us ON us.userID = u.userID
        SET
            u.total_online_seconds = u.total_online_seconds + GREATEST(
                TIMESTAMPDIFF(
                    SECOND,
                    u.login_time,
                    FROM_UNIXTIME(COALESCE(us.session_last_seen, UNIX_TIMESTAMP(u.login_time)))
                ),
                0
            ),
            u.is_online = 0,
            u.last_activity = NULL,
            u.login_time = NULL
        WHERE u.is_online = 1
          AND u.login_time IS NOT NULL
          AND (? = 0 OR u.userID <> ?)
          AND (
                us.session_last_seen IS NULL
                OR us.session_last_seen < (UNIX_TIMESTAMP(NOW()) - ?)
          )
    ";
    $cleanupStmt = $_database->prepare($cleanupSql);
    if ($cleanupStmt) {
        $cleanupStmt->bind_param("iii", $currentUserID, $currentUserID, $IDLE_LIMIT);
        $cleanupStmt->execute();
        $cleanupStmt->close();
    }
}
