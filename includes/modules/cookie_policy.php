<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;

global $languageService, $_database;

$lang = $languageService->detectLanguage();
$languageService->readModule('cookie_policy');

global $hp_title;
$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

$data_array = [
    'class' => $class,
    'title' => $languageService->get('cookie_policy'),
    'subtitle' => 'Cookie Policy',
    'cookie_policy' => $languageService->get('cookie_policy'),
];

echo $tpl->loadTemplate("cookie_policy", "head", $data_array, 'theme');

$content = '';
$contentKey = 'cookie_policy';

$stmt = $_database->prepare("
    SELECT content, updated_at
    FROM settings_content_lang
    WHERE content_key = ? AND language = ?
    LIMIT 1
");
$stmt->bind_param('ss', $contentKey, $lang);
$stmt->execute();
$stmt->bind_result($content, $updated_at);
$stmt->fetch();
$stmt->close();

if (empty($content) && $lang !== 'de') {
    $stmt = $_database->prepare("
        SELECT content, updated_at
        FROM settings_content_lang
        WHERE content_key = ? AND language = 'de'
        LIMIT 1
    ");
    $stmt->bind_param('s', $contentKey);
    $stmt->execute();
    $stmt->bind_result($content, $updated_at);
    $stmt->fetch();
    $stmt->close();
}

if (!empty($content)) {
    $timestamp = $updated_at ? strtotime($updated_at) : time();
    $date = date('d.m.Y H:i:s', $timestamp);

    $data_array = [
        'page_title' => $hp_title,
        'cookie_policy_text' => $content,
        'stand1' => $languageService->get('stand1'),
        'stand2' => $languageService->get('stand2'),
        'date' => $date,
    ];

    echo $tpl->loadTemplate(
        "cookie_policy",
        "content",
        $data_array,
        'theme'
    );
} else {
    $msg = $languageService->get('no_cookie_policy');
    if ($msg === '[no_cookie_policy]') {
        $msg = 'Keine Cookie-Richtlinie vorhanden.';
    }

    echo '<div class="alert alert-info">' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
}
