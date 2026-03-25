<?php
session_start();
require __DIR__ . '/system/language_handler.php';

$step_path = __DIR__ . "/languages/{$_SESSION['lang']}/locked.php";
$translations = require $step_path;
$html_lang = htmlspecialchars($_SESSION['lang']);
?>
<!doctype html>
<html lang="<?= $html_lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($translations['title']) ?></title>
    <link href="/install/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="alert alert-warning shadow rounded">
        <h4><?= htmlspecialchars($translations['heading']) ?></h4>
        <p><?= htmlspecialchars($translations['message1']) ?></p>
        <hr>
        <p><?= htmlspecialchars($translations['message2']) ?></p>
    </div>
</div>
</body>
</html>
