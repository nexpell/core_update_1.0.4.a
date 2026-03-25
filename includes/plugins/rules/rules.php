<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\SeoUrlHandler;

global $_database, $languageService, $tpl;

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars((string)($config['selected_style'] ?? ''));

$data_array = [
    'class' => $class,
    'title' => $languageService->get('rules_title'),
    'subtitle' => 'Rules',
];
echo $tpl->loadTemplate('rules', 'head', $data_array, 'plugin');

$max = 5;
$currentLang = $_SESSION['language'] ?? 'de';
$currentLangSql = $_database instanceof mysqli
    ? mysqli_real_escape_string($_database, (string)$currentLang)
    : 'de';

$countResult = safe_query("
    SELECT COUNT(*) AS total
    FROM (
        SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(content_key, '_', 2), '_', -1) AS rule_id
        FROM plugins_rules
        WHERE content_key LIKE 'rule_%_title'
          AND is_active = 1
        GROUP BY rule_id
    ) AS rules_count
");
$countRow = mysqli_fetch_assoc($countResult);
$gesamt = (int)($countRow['total'] ?? 0);

$pages = max(1, (int)ceil($gesamt / $max));
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($page, $pages));
$start = ($page - 1) * $max;

$resRules = safe_query("
    SELECT
        SUBSTRING_INDEX(SUBSTRING_INDEX(content_key, '_', 2), '_', -1) AS rule_id,
        MAX(sort_order) AS sort_order,
        MAX(updated_at) AS updated_at,
        MAX(userID) AS userID
    FROM plugins_rules
    WHERE content_key LIKE 'rule_%_title'
      AND is_active = 1
    GROUP BY rule_id
    ORDER BY sort_order ASC, rule_id ASC
    LIMIT {$start}, {$max}
");

if (mysqli_num_rows($resRules) > 0) {
    while ($rule = mysqli_fetch_assoc($resRules)) {
        $ruleID = (int)($rule['rule_id'] ?? 0);

        $resTitle = safe_query("
            SELECT content
            FROM plugins_rules
            WHERE content_key = 'rule_{$ruleID}_title'
              AND language = '{$currentLangSql}'
            LIMIT 1
        ");
        $rowTitle = mysqli_fetch_assoc($resTitle);
        $title = (string)($rowTitle['content'] ?? '');

        $resText = safe_query("
            SELECT content
            FROM plugins_rules
            WHERE content_key = 'rule_{$ruleID}_text'
              AND language = '{$currentLangSql}'
            LIMIT 1
        ");
        $rowText = mysqli_fetch_assoc($resText);
        $text = (string)($rowText['content'] ?? '');

        $userID = (int)($rule['userID'] ?? 0);
        $poster = '<a href="'
            . htmlspecialchars(
                SeoUrlHandler::convertToSeoUrl('index.php?site=profile&id=' . $userID),
                ENT_QUOTES,
                'UTF-8'
            )
            . '"><strong>'
            . htmlspecialchars(getusername($userID), ENT_QUOTES, 'UTF-8')
            . '</strong></a>';

        $date = !empty($rule['updated_at'])
            ? date('d.m.Y', strtotime((string)$rule['updated_at']))
            : '';

        $data_array = [
            'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            'text' => htmlspecialchars($text, ENT_QUOTES, 'UTF-8'),
            'date' => $date,
            'poster' => $poster,
            'info' => $languageService->get('info'),
            'stand' => $languageService->get('stand'),
        ];

        echo $tpl->loadTemplate('rules', 'main', $data_array, 'plugin');
    }

    if ($pages > 1) {
        $rulesBaseUrl = SeoUrlHandler::convertToSeoUrl('index.php?site=rules');
        $rulesBaseUrl = rtrim($rulesBaseUrl, '/') . '/';
        echo $tpl->renderPagination($rulesBaseUrl, $page, $pages);
    }
} else {
    echo '<div class="alert alert-info">'
        . $languageService->get('no_rules_found')
        . '</div>';
}
?>
