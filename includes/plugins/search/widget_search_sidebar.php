<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\SeoUrlHandler;
use nexpell\Database;

global $languageService, $_database;

$tpl = new Template();

if (!function_exists('normalizeWidgetSearchLanguage')) {
    function normalizeWidgetSearchLanguage(string $lang): string
    {
        $lang = strtolower(trim($lang));

        return match ($lang) {
            'gb', 'uk' => 'en',
            default => $lang !== '' ? $lang : 'de',
        };
    }
}

if (!function_exists('widgetSearchLanguage')) {
    function widgetSearchLanguage(): string
    {
        global $languageService;

        if (isset($languageService)) {
            if (property_exists($languageService, 'currentLanguage') && !empty($languageService->currentLanguage)) {
                return normalizeWidgetSearchLanguage((string) $languageService->currentLanguage);
            }

            if (method_exists($languageService, 'detectLanguage')) {
                $detected = $languageService->detectLanguage();
                if (is_string($detected) && $detected !== '') {
                    return normalizeWidgetSearchLanguage($detected);
                }
            }
        }

        foreach (['language', 'lang'] as $key) {
            if (!empty($_GET[$key]) && is_string($_GET[$key])) {
                return normalizeWidgetSearchLanguage($_GET[$key]);
            }
            if (!empty($_POST[$key]) && is_string($_POST[$key])) {
                return normalizeWidgetSearchLanguage($_POST[$key]);
            }
            if (!empty($_SESSION[$key]) && is_string($_SESSION[$key])) {
                return normalizeWidgetSearchLanguage($_SESSION[$key]);
            }
        }

        return 'de';
    }
}

if (!function_exists('widgetSearchText')) {
    function widgetSearchText(string $lang, string $key, LanguageService $languageService): string
    {
        $value = $languageService->get($key);
        if (is_string($value) && $value !== '' && !preg_match('/^\[.+\]$/', $value) && strtoupper($value) !== $key) {
            return $value;
        }

        $fallbacks = [
            'de' => [
                'button' => 'Suchen',
                'placeholder' => 'Suche…',
                'subtitle' => 'Finde, was du suchst',
                'title' => 'Suche',
            ],
            'en' => [
                'button' => 'Search',
                'placeholder' => 'Search…',
                'subtitle' => 'Find what you are looking for',
                'title' => 'Search',
            ],
            'it' => [
                'button' => 'Cerca',
                'placeholder' => 'Cerca…',
                'subtitle' => 'Trova quello che stai cercando',
                'title' => 'Ricerca',
            ],
        ];

        return $fallbacks[$lang][$key] ?? $fallbacks['de'][$key] ?? $key;
    }
}

// --- CONFIG: Style laden ---
$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);
$currentLang = widgetSearchLanguage();

// --- HEAD ---
$data_array = [
    'class'    => $class,
    'title'    => widgetSearchText($currentLang, 'title', $languageService),
    'subtitle' => widgetSearchText($currentLang, 'subtitle', $languageService),
];
echo $tpl->loadTemplate("search", "head", $data_array, "plugin");

// --- Suchparameter ---
$q        = isset($_GET['q']) ? trim($_GET['q']) : '';

// --- Suchformular ---
$data_array = [
    'placeholder' => widgetSearchText($currentLang, 'placeholder', $languageService),
    'button'      => widgetSearchText($currentLang, 'button', $languageService),
    'query'       => htmlspecialchars($q, ENT_QUOTES, 'UTF-8'),
    'quick_action'=> 'index.php',
    'current_lang'=> $currentLang,
];
echo $tpl->loadTemplate("search", "quick", $data_array, "plugin");
