<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\user\UserPoints;

require_once __DIR__ . '/../../../system/classes/UserPoints.php';

// Hilfsfunktionen
if (!function_exists('table_Exists')) {
    function table_Exists($table) {
        global $_database;
        $result = $_database->query("SHOW TABLES LIKE '" . $_database->real_escape_string($table) . "'");
        return $result && $result->num_rows > 0;
    }
}

if (!function_exists('column_Exists')) {
    function column_Exists($table, $column) {
        global $_database;
        $table = $_database->real_escape_string((string)$table);
        $column = $_database->real_escape_string((string)$column);
        $result = $_database->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        return $result && $result->num_rows > 0;
    }
}

function achievements_get_trackable_activities_config(): array {
    return [
        ['type'=>'Artikel',        'lang_key'=>'articles',  'table'=>'plugins_articles',         'user_col'=>'userID', 'plugin'=>'Artikel',        'source'=>'plugin'],
        ['type'=>'News',           'lang_key'=>'news',      'table'=>'plugins_news',             'user_col'=>'userID', 'plugin'=>'News',           'source'=>'plugin'],
        ['type'=>'Clan-Regeln',    'lang_key'=>'clanrules', 'table'=>'plugins_rules',            'user_col'=>'userID', 'plugin'=>'Clan-Regeln',    'source'=>'plugin'],
        ['type'=>'Partners',       'lang_key'=>'partners',  'table'=>'plugins_partners',         'user_col'=>'userID', 'plugin'=>'Partner',        'source'=>'plugin'],
        ['type'=>'Sponsoren',      'lang_key'=>'sponsors',  'table'=>'plugins_sponsors',         'user_col'=>'userID', 'plugin'=>'Sponsoren',      'source'=>'plugin'],
        ['type'=>'Links',          'lang_key'=>'links',     'table'=>'plugins_links',            'user_col'=>'userID', 'plugin'=>'Links',          'source'=>'plugin'],
        ['type'=>'Forum-Posts',    'lang_key'=>'posts',     'table'=>'plugins_forum_posts',      'user_col'=>'userID', 'plugin'=>'Forum',          'source'=>'plugin'],
        ['type'=>'Forum-Themen',   'lang_key'=>'threads',   'table'=>'plugins_forum_threads',    'user_col'=>'userID', 'plugin'=>'Forum',          'source'=>'plugin'],
        ['type'=>'Forum-Likes',    'lang_key'=>'likes',     'table'=>'plugins_forum_post_likes', 'user_col'=>'userID', 'plugin'=>'Forum',          'source'=>'plugin'],
        ['type'=>'Downloads',      'lang_key'=>'downloads', 'table'=>'plugins_downloads',        'user_col'=>'userID', 'plugin'=>'Downloads',      'source'=>'plugin'],
        ['type'=>'ToDo',           'lang_key'=>'todo',      'table'=>'plugins_todo',             'user_col'=>'userID', 'plugin'=>'ToDo',           'source'=>'plugin'],

        // Core/System: immer vorhanden -> keine Prüfung, kein "Plugin nicht installiert"
        ['type'=>'Kommentare',     'lang_key'=>'comments',  'table'=>'comments',                 'user_col'=>'userID', 'source'=>'core'],
        ['type'=>'Ratings',        'lang_key'=>'ratings',   'table'=>'ratings',                  'user_col'=>'userID', 'source'=>'core'],
        ['type'=>'Logins',         'lang_key'=>'logins',    'table'=>'user_sessions',            'user_col'=>'userID', 'source'=>'core'],
    ];
}

function achievements_get_activity_count(string $table, string $userCol, int $userID): int {
    global $_database;

    if (!table_Exists($table) || !column_Exists($table, $userCol)) {
        return 0;
    }

    if ($table === 'plugins_partners') {
        $stmt = $_database->prepare("
            SELECT COUNT(DISTINCT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(content_key, '_', 2), '_', -1) AS UNSIGNED)) AS count
            FROM `plugins_partners`
            WHERE `{$userCol}` = ?
              AND content_key LIKE 'partner\\_%\\_name'
        ");
    } elseif ($table === 'plugins_rules') {
        $stmt = $_database->prepare("
            SELECT COUNT(DISTINCT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(content_key, '_', 2), '_', -1) AS UNSIGNED)) AS count
            FROM `plugins_rules`
            WHERE `{$userCol}` = ?
              AND content_key LIKE 'rule\\_%\\_title'
        ");
    } else {
        $stmt = $_database->prepare("
            SELECT COUNT(*) AS count
            FROM `{$table}`
            WHERE `{$userCol}` = ?
        ");
    }

    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return (int)($row['count'] ?? 0);
}

/**
 * Sammelt alle relevanten Statistiken für einen bestimmten Benutzer.
 * OPTIMIERT: Nutzt einen statischen Cache, um die Berechnung pro Seitenaufruf nur einmal pro User durchzuführen.
 */
function achievements_get_user_stats($userID) {
    global $languageService, $_database;
    static $user_stats_cache = [];

    $userID = (int)$userID;

    if (isset($user_stats_cache[$userID])) {
        return $user_stats_cache[$userID];
    }

    // Bonus-Punkte (Achievements-only)
    $bonus_points = 0;
    if (table_Exists('plugins_achievements_admin_log')) {
        $stmt_bonus = $_database->prepare("
            SELECT COALESCE(SUM(value), 0) AS total_bonus
            FROM plugins_achievements_admin_log
            WHERE user_id = ? AND log_type = 'bonus_points'
        ");
        if ($stmt_bonus) {
            $stmt_bonus->bind_param("i", $userID);
            $stmt_bonus->execute();
            $row_bonus = $stmt_bonus->get_result()->fetch_assoc();
            $bonus_points = (int)($row_bonus['total_bonus'] ?? 0);
            $stmt_bonus->close();
        }
    }

    // Trackable Activities (Counts)
    $raw_config = achievements_get_trackable_activities_config();

    $tables = [];
    foreach ($raw_config as $config) {
        $tables[] = array_merge($config, [
            'label' => $languageService->get($config['lang_key'])
        ]);
    }

    $labels_map = [];
    foreach ($tables as $config) {
        $labels_map[$config['type']] = $config['label'];
    }

    $counts = [];
    foreach ($tables as $config) {
        $type = (string)$config['type'];
        $counts[$type] = 0;

        if (!empty($config['table']) && !empty($config['user_col']) && table_Exists($config['table'])) {
            $counts[$type] = achievements_get_activity_count(
                (string)$config['table'],
                (string)$config['user_col'],
                $userID
            );
        }
    }

    // Punkte/Level: Single Source of Truth = UserPoints
    // (Achievements-Weights greifen dort automatisch, wenn Plugin + settings table existiert.)
    $userpoints = UserPoints::getFull($userID);

    $base_total_points = (int)($userpoints['total_points'] ?? 0);
    $points_per_level  = (int)($userpoints['points_per_level'] ?? 100);

    // Punkte pro Kategorie direkt aus UserPoints-Details
    $points_per_category = [];
    if (!empty($userpoints['details']) && is_array($userpoints['details'])) {
        foreach ($userpoints['details'] as $d) {
            if (!isset($d['type'])) {
                continue;
            }
            $points_per_category[(string)$d['type']] = (int)($d['points'] ?? 0);
        }
    }

    // Sicherstellen, dass alle Types aus dem Achievements-Tracking einen Key haben
    foreach ($counts as $type => $_c) {
        if (!array_key_exists($type, $points_per_category)) {
            $points_per_category[$type] = 0;
        }
    }

    // --- Legacy-Aliase für bestehende Achievements/Conditions (Forum/Likes) ---
    // Viele alte Achievements referenzieren trigger_condition = 'Forum' oder 'Likes',
    // während UserPoints/Config sauberer auf 'Forum-Posts'/'Forum-Likes' trennt.
    $counts['Forum'] = $counts['Forum'] ?? ($counts['Forum-Posts'] ?? 0);
    $counts['Likes'] = $counts['Likes'] ?? ($counts['Forum-Likes'] ?? 0);

    $points_per_category['Forum'] = $points_per_category['Forum'] ?? ($points_per_category['Forum-Posts'] ?? 0);
    $points_per_category['Likes'] = $points_per_category['Likes'] ?? ($points_per_category['Forum-Likes'] ?? 0);

    // Optional: Labels für alte Keys (falls irgendwo direkt "Forum"/"Likes" angezeigt wird)
    $labels_map['Forum'] = $labels_map['Forum'] ?? ($languageService->get('forum') ?? 'Forum');
    $labels_map['Likes'] = $labels_map['Likes'] ?? ($languageService->get('likes') ?? 'Likes');
    // --- /Legacy-Aliase ---

    // Bonus-Punkte addieren (UserPoints bleibt unangetastet)
    $total_points = $base_total_points + $bonus_points;
    $level = ($points_per_level > 0) ? (int)floor($total_points / $points_per_level) : 0;

    // User-Rollen und Registrierung
    $role_name = 'Benutzer';
    $roles = ['Benutzer'];
    $reg_date = null;

    $stmt = $_database->prepare("
        SELECT
            GROUP_CONCAT(DISTINCT r.role_name SEPARATOR ',') AS role_names,
            u.registerdate AS registration_date
        FROM users u
        LEFT JOIN user_role_assignments ura ON u.userID = ura.userID
        LEFT JOIN user_roles r ON ura.roleID = r.roleID
        WHERE u.userID = ?
        GROUP BY u.userID
    ");
    if ($stmt) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $reg_date = $user_data['registration_date'] ?? null;

        if (!empty($user_data['role_names'])) {
            $roles = array_values(array_filter(array_map('trim', explode(',', $user_data['role_names']))));
            if (!empty($roles[0])) {
                $role_name = $roles[0];
            }
        }
    }

    $stats = [
        'counts' => $counts,
        'points_per_category' => $points_per_category,
        'total_points' => $total_points,
        'bonus_points' => $bonus_points,
        'level' => $level,
        'role_name' => $role_name,
        'roles' => $roles,
        'reg_date' => $reg_date,
        'points_per_level' => $points_per_level,
        'labels' => $labels_map
    ];

    $user_stats_cache[$userID] = $stats;
    return $stats;
}

if (!function_exists('checkAndAddAchievement')) {
    function checkAndAddAchievement($achievements, $value, &$earned_achievements) {
        if (empty($achievements)) return;
        
        usort($achievements, fn($a, $b) => (int)$b['trigger_value'] <=> (int)$a['trigger_value']);
        
        foreach ($achievements as $ach) {
            if ($value >= (int)$ach['trigger_value']) {
                $earned_achievements[] = $ach;
                if (!$ach['is_standalone']) {
                    return; 
                }
            }
        }
    }
}

function achievements_get_earned_data($userID) {
    global $_database;

    static $cached_results = [];
    if (isset($cached_results[$userID])) {
        return $cached_results[$userID];
    }

    // --- User-Statistiken laden ---
    $user_stats = achievements_get_user_stats($userID);
    $earned_achievements_map = [];

    // --- Manuelle Awards ---
    $manual_award_ids = [];
    if (table_Exists('plugins_achievements_admin_log')) {
        $stmt_manual = $_database->prepare("
            SELECT l.related_id as achievement_id
            FROM plugins_achievements_admin_log l
            WHERE l.user_id = ? AND l.log_type = 'manual_award'
        ");
        if ($stmt_manual) {
            $stmt_manual->bind_param("i", $userID);
            $stmt_manual->execute();
            $manual_awards_result = $stmt_manual->get_result();
            while($row = $manual_awards_result->fetch_assoc()) {
                $manual_award_ids[] = $row['achievement_id'];
            }
            $stmt_manual->close();
        }
    }

    // --- Alle Achievements laden ---
    $all_achievements = [];
    if (table_Exists('plugins_achievements')) {
        $result = $_database->query("SELECT * FROM plugins_achievements ORDER BY type, trigger_value DESC");
        if ($result instanceof mysqli_result) {
            while($row = $result->fetch_assoc()) {
                $all_achievements[$row['type']][] = $row;
            }
        }
    }

    // --- Manuelle Awards eintragen ---
    if (!empty($manual_award_ids)) {
        foreach ($all_achievements as $type => $ach_group) {
            foreach ($ach_group as $ach_data) {
                if (in_array($ach_data['id'], $manual_award_ids)) {
                    $earned_achievements_map[$ach_data['id']] = $ach_data;
                }
            }
        }
    }

    // --- Bonus-Achievement ---
    if ($user_stats['bonus_points'] > 0 && isset($all_achievements['bonus_points'][0])) {
        $bonus_ach = $all_achievements['bonus_points'][0];
        $bonus_ach['description'] = str_replace('{points}', '<b>' . $user_stats['bonus_points'] . '</b>', $bonus_ach['description']);
        $bonus_ach['allow_html'] = 1;
        $earned_achievements_map[$bonus_ach['id']] = $bonus_ach;
    }

    // --- Role-Achievements (mehrere Rollen) ---
    if(isset($all_achievements['role'])) {
        foreach($all_achievements['role'] as $ach) {
            $required_roles = !empty($ach['trigger_value']) 
                ? array_map('trim', explode(',', $ach['trigger_value'])) 
                : [];
            $user_roles = $user_stats['roles'] ?? ['Benutzer'];
            $is_unlocked = !array_diff($required_roles, $user_roles);
            if ($is_unlocked) {
                $earned_achievements_map[$ach['id']] = $ach;
            }
        }
    }

    // --- Level / Points / Activity / Category Points ---
    $temp_earned = [];
    checkAndAddAchievement($all_achievements['level'] ?? [], $user_stats['level'], $temp_earned);
    checkAndAddAchievement($all_achievements['points'] ?? [], $user_stats['total_points'], $temp_earned);
    
    // --- Registration-Time Achievements prüfen ---
    if (!empty($user_stats['reg_date']) && !empty($all_achievements['registration_time'])) {
        foreach ($all_achievements['registration_time'] as $ach) {
            $reg_data = achievements_helper_calculate_registration_progress(
                $user_stats['reg_date'],
                $ach['trigger_value'],
                $ach['trigger_condition']
            );

            // Für Jahre: 1 Jahr = 365 Tage
            if ($ach['trigger_condition'] === 'years') {
                $reg_data['current_units'] = floor($reg_data['current_days'] / 365);
                $reg_data['required_units'] = $ach['trigger_value'];
                $reg_data['is_unlocked'] = $reg_data['current_units'] >= $ach['trigger_value'];
            }

            checkAndAddAchievement([$ach], $reg_data['current_units'], $temp_earned);
        }
    }

    // --- Activity / Category Points ---
    $activity_types = ['activity_count', 'category_points'];
    foreach($activity_types as $type) {
        if(isset($all_achievements[$type])) {
            $grouped_by_condition = [];
            foreach($all_achievements[$type] as $ach) {
                $grouped_by_condition[$ach['trigger_condition']][] = $ach;
            }
            foreach($grouped_by_condition as $condition => $ach_group) {
                $source_value = ($type === 'activity_count') 
                    ? ($user_stats['counts'][$condition] ?? 0) 
                    : ($user_stats['points_per_category'][$condition] ?? 0);
                checkAndAddAchievement($ach_group, $source_value, $temp_earned);
            }
        }
    }

    // --- UserPoints <-> Achievements Type-Brücke (damit beides sicher zusammenpasst) ---
    // Falls UserPoints intern noch "Forum" statt "Forum-Posts" nutzt:
    if (!isset($points_per_category['Forum-Posts']) && isset($points_per_category['Forum'])) {
        $points_per_category['Forum-Posts'] = (int)$points_per_category['Forum'];
    }
    // Falls UserPoints intern noch "Likes" statt "Forum-Likes" nutzt (oder umgekehrt):
    if (!isset($points_per_category['Forum-Likes']) && isset($points_per_category['Likes'])) {
        $points_per_category['Forum-Likes'] = (int)$points_per_category['Likes'];
    }

    // optional: gleiches für Counts (nur falls du irgendwo Forum/Likes “historisch” brauchst)
    $counts['Forum-Posts'] = $counts['Forum-Posts'] ?? ($counts['Forum'] ?? 0);
    $counts['Forum-Likes'] = $counts['Forum-Likes'] ?? ($counts['Likes'] ?? 0);

    // --- Gefundene Achievements zusammenführen ---
    foreach ($temp_earned as $ach) {
        if (!isset($earned_achievements_map[$ach['id']])) {
            $earned_achievements_map[$ach['id']] = $ach;
        }
    }

    // --- Clan-Name ersetzen ---
    $earned_achievements_data = array_values($earned_achievements_map);
    if (!empty($earned_achievements_data)) {
        $clan_name_result = $_database->query("SELECT clanname FROM settings LIMIT 1");
        $clan_name = $clan_name_result ? htmlspecialchars(mysqli_fetch_assoc($clan_name_result)['clanname']) : 'Clan-Name';

        foreach ($earned_achievements_data as &$data) {
            $data['description'] = str_replace('{clan_name}', $clan_name, $data['description']);
        }
        unset($data);
    }

    $cached_results[$userID] = $earned_achievements_data;
    return $earned_achievements_data;
}



// =============================================================================
// NEU: Einfache Getter-Funktionen für den externen Gebrauch
// =============================================================================

/**
 * Gibt das Level eines Benutzers zurück.
 * @param int $userID Die ID des Benutzers.
 * @return int Das Level des Benutzers.
 */
function achievements_get_user_level(int $userID): int {
    if ($userID <= 0) return 0;
    $stats = achievements_get_user_stats($userID);
    return (int)$stats['level'];
}

/**
 * Gibt die Gesamtpunkte eines Benutzers zurück.
 * @param int $userID Die ID des Benutzers.
 * @return int Die Gesamtpunkte des Benutzers.
 */
function achievements_get_user_points(int $userID): int {
    if ($userID <= 0) return 0;
    $stats = achievements_get_user_stats($userID);
    return (int)$stats['total_points'];
}

/**
 * Generiert den HTML-Code (Icon-Liste) für die Achievements eines Benutzers.
 * @param int $userID Die ID des Benutzers.
 * @return string Der HTML-Code der Icons.
 */
function achievements_get_user_icons_html(int $userID): string {
    if ($userID <= 0) return '';
    
    $earned_achievements = achievements_get_earned_data($userID);
    if (empty($earned_achievements)) return '';
    
    $html = '';
    $image_path = '/includes/plugins/achievements/images/icons/';

    foreach ($earned_achievements as $data) {
        $safe_description = !empty($data['allow_html'])
            ? nl2br(strip_tags($data['description'], '<b></b><i><u>'))
            : nl2br(htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8'));
        
        $name = htmlspecialchars($data['name']);
        $tooltip_title = htmlspecialchars('<b>' . $name . '</b><br>' . $safe_description, ENT_QUOTES);

        $html .= '<img src="' . htmlspecialchars($image_path . $data['image']) . '" 
                       alt="' . htmlspecialchars($name) . '" 
                       class="achievement-icon ms-2" 
                       data-bs-toggle="tooltip" 
                       data-bs-html="true" 
                       data-bs-title="' . $tooltip_title . '" 
                       style="width: 28px; height: 28px; vertical-align: middle;">';
    }
    
    return $html;
}

/**
 * Generiert die Achievement-Widgets für die Profilseite.
 */
function achievements_get_profile_widgets($userID) {
    global $languageService, $_database;

    $user_stats = achievements_get_user_stats($userID); // Enthält jetzt auch $user_stats['roles']
    $earned_achievements = achievements_get_earned_data($userID);
    
    $level_percent = ($user_stats['points_per_level'] > 0) 
        ? ($user_stats['total_points'] % $user_stats['points_per_level']) / $user_stats['points_per_level'] * 100 
        : 0;

    $post_type_html = '';
    foreach ($user_stats['counts'] as $type => $count) {
        if ($count > 0) {
            $display_name = $user_stats['labels'][$type] ?? $type;
            $post_type_html .= '<tr><td>' . htmlspecialchars($display_name) . '</td><td>' . $count . '</td></tr>';
        }
    }

    // Role-Achievements berücksichtigen
    $processed_achievements = [];
    $image_path = '/includes/plugins/achievements/images/icons/';
    
    foreach ($earned_achievements as $ach) {
        $safe_description = !empty($ach['allow_html'])
            ? nl2br(strip_tags($ach['description'], '<b></b><i><u>'))
            : nl2br(htmlspecialchars($ach['description'], ENT_QUOTES, 'UTF-8'));

        $is_role_achievement = $ach['type'] === 'role';
        $is_unlocked = $ach['is_unlocked'] ?? true; // Standard für manuell oder andere Arten

        if ($is_role_achievement) {
            $required_roles = !empty($ach['trigger_value'])
                ? array_map('trim', explode(',', $ach['trigger_value']))
                : [];
            $user_roles = $user_stats['roles'] ?? ['Benutzer'];
            $is_unlocked = !array_diff($required_roles, $user_roles); // alle erforderlichen Rollen vorhanden?
        }

        $processed_achievements[] = [
            'name' => $ach['name'],
            'description' => $safe_description,
            'image' => $ach['image'],
            'is_unlocked' => $is_unlocked
        ];
    }

    $achievements_grid_items = '';
    if (empty($processed_achievements)) {
        $achievements_grid_items = '<div class="col alert alert-info">' . $languageService->get("no_achievements") . '</div>';
    } else {
        foreach ($processed_achievements as $ach) {
            $img = !$ach['is_unlocked'] ? ($user_stats['locked_icon'] ?? 'locked-archv.png') : $ach['image'];
            $achievements_grid_items .= '
                <div class="col-lg-4 col-md-6 text-center mb-4">
                    <img src="' . htmlspecialchars($image_path . $img) . '" alt="' . htmlspecialchars($ach['name']) . '" style="width: 80px; height: 80px;" class="mb-2">
                    <h6 class="mt-2">' . htmlspecialchars($ach['name']) . '</h6>
                    <div class="small">' . $ach['description'] . '</div>
                </div>';
        }
    }
    
    return [
        'total_points' => $user_stats['total_points'], 
        'level' => $user_stats['level'], 
        'level_percent' => $level_percent,
        'post_type_html' => $post_type_html, 
        'achievements_sidebar_html' => achievements_get_user_icons_html($userID),
        'achievements_tab_button_html' => '<li class="nav-item" role="presentation"><button class="nav-link" id="achievements-tab" data-bs-toggle="tab" data-bs-target="#achievements" type="button" role="tab">' . $languageService->get('tab_achievements') . '</button></li>', 
        'achievements_tab_content_html' => '<div class="tab-pane fade" id="achievements" role="tabpanel"><h5>' . $languageService->get('achievements_title') . '</h5><div class="row">' . $achievements_grid_items . '</div></div>'
    ];
}

/**
 * Berechnet den Fortschritt für zeitbasierte Achievements.
 * @return array|null Ein Array mit Fortschrittsdaten oder null bei ungültigem Datum.
 */
function achievements_helper_calculate_registration_progress(?string $reg_date_str, int $required_value, string $time_unit): ?array {
    global $languageService;
    if (empty($reg_date_str) || $required_value < 0) {
        return null;
    }

    $reg = new DateTimeImmutable($reg_date_str);
    $now = new DateTimeImmutable('now');
    $days_since = $reg->diff($now)->days; // gesamte Tage seit Registrierung

    $current_units = 0;
    $required_days = 0;
    $unit_key = '';

    switch ($time_unit) {
        case 'days':
            $current_units = $days_since;
            $required_days = $required_value;
            $unit_key = 'day';
            break;

        case 'weeks':
            $current_units = floor($days_since / 7);
            $required_days = $required_value * 7;
            $unit_key = 'week';
            break;

        case 'months':
            $current_units = floor($days_since / 30.44); // Durchschnitt
            $required_days = (int)round($required_value * 30.44);
            $unit_key = 'month';
            break;

        case 'years':
            $current_units = floor($days_since / 365);   // Jahre ≈ 365 Tage
            $required_days = $required_value * 365;
            $unit_key = 'year';
            break;

        default:
            return null;
    }

    $is_unlocked = $days_since >= $required_days;

    // Plural/Singular
    $unit_name = method_exists($languageService, 'getPlural')
        ? $languageService->getPlural($unit_key, $required_value)
        : ($required_value === 1 ? $languageService->get($unit_key) : $languageService->get($unit_key . 's'));

    return [
        'current_days'    => $days_since,
        'required_days'   => $required_days,
        'is_unlocked'     => $is_unlocked,
        'current_units'   => $current_units,
        'required_units'  => $required_value,
        'unit'            => $time_unit,
        'requirement_text'=> $languageService->get('be_since') . ' ' . $required_value . ' ' . $unit_name . ' ' . $languageService->get('registered'),
    ];
}
?>
