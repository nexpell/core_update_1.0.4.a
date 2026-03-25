<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\SeoUrlHandler;

$tpl = new Template();

global $languageService;
if (isset($languageService) && method_exists($languageService, 'readModule')) {
    $languageService->readModule('userlist');
}


/* ============================================================
   HEAD laden
============================================================ */
echo $tpl->loadTemplate("userlist", "widget_memberslist_head", [], "plugin");


/* ============================================================
   SETTINGS
============================================================ */
$settings       = mysqli_fetch_assoc(safe_query("SELECT * FROM plugins_userlist_settings WHERE id=1"));
$maxUsers       = (int)($settings['users_widget_count'] ?? 25);
$widgetSort     = $settings['widget_sort'] ?? 'role';
$showOnlyOnline = (int)($settings['widget_show_online'] ?? 1);


/* ============================================================
   SORTIERUNG
============================================================ */
$orderBy = match ($widgetSort) {
    'role', 'role_id' => 'roleID ASC, u.username ASC',
    'username'        => 'u.username ASC',
    default           => 'u.lastlogin DESC'
};


/* ============================================================
   SQL – Member laden + höchste Rolle berechnen
============================================================ */
$query = safe_query("
    SELECT 
        u.userID,
        u.username,
        u.lastlogin,
        u.is_online,

        -- wichtigste Rolle (kleinste roleID)
        (
            SELECT MIN(ura2.roleID)
            FROM user_role_assignments AS ura2
            WHERE ura2.userID = u.userID
        ) AS roleID,

        -- richtiger Name dieser Rolle
        (
            SELECT ur2.role_name
            FROM user_roles AS ur2
            WHERE ur2.roleID = (
                SELECT MIN(ura3.roleID)
                FROM user_role_assignments AS ura3
                WHERE ura3.userID = u.userID
            )
        ) AS role_name

    FROM users AS u

    WHERE u.userID IN (
        SELECT userID FROM user_role_assignments WHERE roleID = 9
    )

    ORDER BY $orderBy
    LIMIT $maxUsers
");


/* ============================================================
   USER SAMMELN
============================================================ */
$members = [];

while ($ds = mysqli_fetch_assoc($query)) {

    /* --------------------------
       AVATAR
    --------------------------- */
    $avatarRaw = getavatar($ds['userID']);
    $avatar    = "/images/avatars/noavatar.png";

    if (!empty($avatarRaw) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($avatarRaw, '/'))) {
        $avatar = $avatarRaw;
    }


    /* --------------------------
       ALLE Rollen laden
    --------------------------- */
    $rolesQ = safe_query("
        SELECT ur.roleID, ur.role_name
        FROM user_role_assignments AS ura
        LEFT JOIN user_roles AS ur ON ur.roleID = ura.roleID
        WHERE ura.userID = " . (int)$ds['userID']
        . " ORDER BY ur.roleID ASC"
    );

    $roles = [];
    while ($r = mysqli_fetch_assoc($rolesQ)) {
        $roles[] = [
            'roleID'    => (int)$r['roleID'],
            'role_name' => $r['role_name']
        ];
    }

    /* -------------------------------------------
       Sortiert nach roleID (falls SQL mal spinnt)
    -------------------------------------------- */
    usort($roles, fn($a, $b) => $a['roleID'] <=> $b['roleID']);

    /* -------------------------------------------
       Badges bauen – sauber sortiert
    -------------------------------------------- */
    $roles_badges = "";
    foreach ($roles as $role) {
        $roles_badges .= '<small>[' . htmlspecialchars($role['role_name']) . ']</small>';
        $roles_badges .= ' ';
    }



    /* --------------------------
       Social Links
    --------------------------- */
    $social = mysqli_fetch_assoc(safe_query("
        SELECT facebook, twitter, instagram, website, github
        FROM user_socials
        WHERE userID = " . (int)$ds['userID']
    ));

    $social_links = "";
    if (!empty($social['twitter']))  $social_links .= '<a href="'.htmlspecialchars($social['twitter']).'" target="_blank"><i class="bi bi-twitter"></i></a>';
    if (!empty($social['facebook'])) $social_links .= '<a href="'.htmlspecialchars($social['facebook']).'" target="_blank"><i class="bi bi-facebook"></i></a>';
    if (!empty($social['instagram']))$social_links .= '<a href="'.htmlspecialchars($social['instagram']).'" target="_blank"><i class="bi bi-instagram"></i></a>';
    if (!empty($social['github']))   $social_links .= '<a href="'.htmlspecialchars($social['github']).'" target="_blank"><i class="bi bi-github"></i></a>';
    if (!empty($social['website']))  $social_links .= '<a href="'.htmlspecialchars($social['website']).'" target="_blank"><i class="bi bi-globe"></i></a>';


    /* --------------------------
       ONLINE BADGE
    --------------------------- */
    $online_badge_class = $ds['is_online'] ? "bg-success" : "bg-danger";
    $online_text        = $ds['is_online'] ? "Online"      : "Offline";


    /* --------------------------
       ARRAY EINTRAG
    --------------------------- */
    $members[] = [
        'id'                 => $ds['userID'],
        'username'           => htmlspecialchars($ds['username']),
        'profile'            => SeoUrlHandler::convertToSeoUrl('index.php?site=profile&id=' . $ds['userID']),
        'avatar'             => $avatar,
        'roles_badges'       => $roles_badges,
        'social_links'       => $social_links,
        'online_badge_class' => $online_badge_class,
        'online_text'        => $online_text,
        'roleID'             => (int)$ds['roleID']   // für Sortierung
    ];
}


/* ============================================================
   PHP-SORTIERUNG (failsafe)
============================================================ */
usort($members, function($a, $b) {
    return $a['roleID'] <=> $b['roleID'];
});


/* ============================================================
   AUSGABE
============================================================ */
foreach ($members as $member) {
    echo $tpl->loadTemplate("userlist", "widget_memberslist_content", $member, "plugin");
}

echo $tpl->loadTemplate("userlist", "widget_memberslist_foot", [], "plugin");
