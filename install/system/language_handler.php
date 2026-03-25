<?php
// Dieser Code kümmert sich um die Sprachauswahl und setzt die Session.
// Er kann in jeder "step"-Datei eingebunden werden.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Liste der unterstützten Sprachen
$langs = ['de', 'en', 'it'];

// Prüfen, ob eine Sprache in der URL (GET) gesetzt ist und gültig ist.
// Wenn ja, diese in der Session speichern.
if (isset($_GET['lang']) && in_array($_GET['lang'], $langs)) {
    $_SESSION['lang'] = $_GET['lang'];
} elseif (!isset($_SESSION['lang'])) {
    // Wenn keine Sprache in der Session gesetzt ist,
    // die Sprache aus den Browser-Headern erkennen.
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if (in_array($browser_lang, $langs)) {
        $_SESSION['lang'] = $browser_lang;
    } else {
        // Fallback-Sprache, wenn keine der bevorzugten Sprachen unterstützt wird
        $_SESSION['lang'] = 'de';
    }
}