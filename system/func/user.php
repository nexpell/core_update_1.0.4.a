<?php

// User helper functions

function getusername($userID)
{
    $userID = (int)$userID;

    if ($userID <= 0) {
        return 'Gast';
    }

    $erg = safe_query("SELECT username FROM users WHERE `userID` = " . $userID);
    if (mysqli_num_rows($erg) === 1) {
        $ds = mysqli_fetch_array(safe_query("SELECT username FROM users WHERE `userID` = " . $userID)) ?: [];
        return (string)($ds['username'] ?? 'Gelöschtes Mitglied');
    }

    $ds = mysqli_fetch_array(safe_query("SELECT username FROM user_username WHERE `userID` = " . $userID)) ?: [];
    return (string)($ds['username'] ?? 'Gelöschtes Mitglied');
}

function getuserpic($userID)
{
    $userID = (int)$userID;

    if ($userID <= 0) {
        return 'svg-avatar.php?name=' . urlencode('Gast') . 'G';
    }

    $ds = mysqli_fetch_array(safe_query("SELECT userpic, username FROM users WHERE `userID` = " . $userID)) ?: [];

    if (empty($ds['userpic'])) {
        return 'svg-avatar.php?name=' . urlencode((string)($ds['username'] ?? 'Gelöschtes Mitglied')) . 'G';
    }

    return (string)$ds['userpic'];
}

function getavatar($userID)
{
    $userID = (int)$userID;

    if ($userID <= 0) {
        return '/images/avatars/svg-avatar.php?name=' . urlencode('Gast');
    }

    $ds = mysqli_fetch_array(safe_query("
        SELECT u.username, p.avatar
        FROM users u
        LEFT JOIN user_profiles p ON u.userID = p.userID
        WHERE u.userID = {$userID}
    ")) ?: [];

    $username = !empty($ds['username']) ? (string)$ds['username'] : 'Gelöschtes Mitglied';

    if (!empty($ds['avatar'])) {
        return (string)$ds['avatar'];
    }

    return '/images/avatars/svg-avatar.php?name=' . urlencode($username);
}

function getemail($userID)
{
    $userID = (int)$userID;
    $ds = mysqli_fetch_array(safe_query("SELECT email FROM users WHERE `userID` = " . $userID)) ?: [];

    if (isset($ds['email'])) {
        return getinput((string)$ds['email']);
    }

    return '';
}
