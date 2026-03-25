<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

global $_database, $languageService;

/* ============================
   Sprache & Settings
============================ */
$lang = $languageService->detectLanguage();

$recaptcha = nx_get_recaptcha_config();
$webkey = $recaptcha['webkey'];
$seckey = $recaptcha['seckey'];
$captchaEnabled = $recaptcha['enabled'];
$loggedin = (isset($_SESSION['userID']) && $_SESSION['userID'] > 0);

if (!$loggedin && $captchaEnabled) {
    nx_mark_recaptcha_required();
}

/* ============================
   Header
============================ */
$config = mysqli_fetch_array(
    safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1")
);
$class = htmlspecialchars($config['selected_style'] ?? '');

echo $tpl->loadTemplate("contact", "head", [
    'class'    => $class,
    'title'    => $languageService->get('title'),
    'subtitle' => 'Contact Us',
], 'theme');

/* ============================
   Error-Helper (lokal)
============================ */
if (!function_exists('generateErrorBoxFromArray')) {
    function generateErrorBoxFromArray(string $title, array $errors): string {
        $out = '<strong>' . htmlspecialchars($title) . '</strong><ul>';
        foreach ($errors as $err) {
            $out .= '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        return $out . '</ul>';
    }
}

/* ============================
   Default-Werte
============================ */
$name = '';
$from = '';
$subject = '';
$text = '';
$showerror = '';

nx_form_guard_prepare('contact', $_SERVER['REQUEST_METHOD'] !== 'POST');

/* ============================
   FORMULAR SENDEN
============================ */
if (($_POST['action'] ?? '') === 'send') {

    $getemail = $_POST['getemail'] ?? '';
    $name     = trim($_POST['name'] ?? '');
    $from     = trim($_POST['from'] ?? '');
    $subject  = trim($_POST['subject'] ?? '');
    $text     = str_replace('\r\n', "\n", $_POST['text'] ?? '');
    $honeypot = trim((string)($_POST['company'] ?? ''));

    $fehler = [];
    $run = 0;

    if ($honeypot !== '') {
        error_log('[CONTACT-SPAM] honeypot ip=' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        redirect(SeoUrlHandler::convertToSeoUrl('index.php?site=contact'), '');
        exit;
    }

    if (nx_form_guard_is_too_fast('contact', 4)) {
        $fehler[] = $languageService->get('submission_too_fast');
    }

    if (!nx_rate_limit_consume('contact', 3, 900, (string)($_SERVER['REMOTE_ADDR'] ?? ''))) {
        $fehler[] = $languageService->get('rate_limit_exceeded');
    }

    /* ============================
       Grundvalidierung
    ============================ */
    if ($name === '')           $fehler[] = $languageService->get('enter_name');
    if (!validate_email($from)) $fehler[] = $languageService->get('enter_mail');
    if ($subject === '')        $fehler[] = $languageService->get('enter_subject');
    if ($text === '')           $fehler[] = $languageService->get('enter_message');

    /* ============================
       SPAM-SCORE (BEST PRACTICE)
    ============================ */
    $spamScore = 0;

    if (preg_match('~https?://|www\.~i', $text)) $spamScore += 40;

    $spamWords = [
        'casino','bonus','bet','gambling','crypto',
        'promotion','affiliate','loan','viagra'
    ];
    foreach ($spamWords as $word) {
        if (stripos($text, $word) !== false) {
            $spamScore += 30;
            break;
        }
    }

    if ($text !== strip_tags($text)) $spamScore += 15;
    if (mb_strlen($text) > 500)      $spamScore += 10;
    if (!$captchaEnabled)            $spamScore += 10;

    if ($spamScore >= 50) {
        error_log('[CONTACT-SPAM] score=' . $spamScore . ' ip=' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        redirect(SeoUrlHandler::convertToSeoUrl('index.php?site=contact'), '');
        exit;
    }

    /* ============================
       Empfänger prüfen
    ============================ */
    $stmt = $_database->prepare("SELECT 1 FROM contact WHERE email = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $getemail);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            $fehler[] = $languageService->get('unknown_receiver');
        }
        $stmt->close();
    } else {
        $fehler[] = $languageService->get('server_error');
    }

    /* ============================
       reCAPTCHA
    ============================ */
    if ($loggedin) {
        $run = 1;
    } else {
        if ($captchaEnabled) {
            $token = $_POST['g-recaptcha-response'] ?? '';
            if ($token === '') {
                $fehler[] = $languageService->get('captcha_missing');
            } elseif (!nx_verify_recaptcha($token, (string)($_SERVER['REMOTE_ADDR'] ?? ''))) {
                $fehler[] = $languageService->get('captcha_invalid');
            } else {
                $run = 1;
            }
        } else {
            $run = 1;
        }
    }

    /* ============================
       MAIL SENDEN
    ============================ */
    if (!$fehler && $run) {

        $settings = mysqli_fetch_assoc(safe_query("SELECT * FROM settings"));
        $hp_title = $settings['hptitle'] ?? 'nexpell';
        $hp_url   = $settings['hpurl']   ?? 'https://' . $_SERVER['HTTP_HOST'];

        $message = '
<!DOCTYPE html>
<html lang="de">
<body style="margin:0;background:#f4f6f8;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;">
<tr><td align="center">
<table width="100%" style="max-width:620px;background:#fff;border-radius:12px;font-family:Arial;">
<tr><td style="background:#fe821d;padding:28px;color:#fff;">
<h1 style="margin:0;">' . htmlspecialchars($hp_title) . '</h1>
<p>Neue Nachricht über das Kontaktformular</p>
</td></tr>
<tr><td style="padding:32px;">
<p><strong>Von:</strong> ' . htmlspecialchars($name) . '</p>
<p><strong>E-Mail:</strong> ' . htmlspecialchars($from) . '</p>
<p><strong>Betreff:</strong> ' . htmlspecialchars($subject) . '</p>
<hr>
<div style="background:#f9fafb;padding:16px;border-radius:8px;">
' . nl2br(htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) . '
</div>
</td></tr>
<tr><td style="padding:18px;text-align:center;color:#777;">
<a href="' . htmlspecialchars($hp_url) . '">' . htmlspecialchars($hp_url) . '</a>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>';

        $sender = $settings['adminemail'] ?? 'noreply@' . $_SERVER['HTTP_HOST'];

        $sendmail = \nexpell\Email::sendEmail(
            $sender,
            'Contact',
            $getemail,
            $subject,
            $message,
            ['reply_to' => $from]
        );

        if (($sendmail['result'] ?? '') === 'fail') {
            $fehler[] = $sendmail['error'] ?? 'Mail error';
            $showerror = generateErrorBoxFromArray(
                $languageService->get('errors_there'),
                $fehler
            );
        } else {
            redirect(SeoUrlHandler::convertToSeoUrl('index.php?site=contact'), '', 3);
            exit;
        }

    } else {
        $showerror = generateErrorBoxFromArray(
            $languageService->get('errors_there'),
            $fehler
        );
    }
}

/* ============================
   Empfängerliste
============================ */
$getemail = '';
$res = safe_query("SELECT * FROM contact ORDER BY sort");
while ($ds = mysqli_fetch_assoc($res)) {
    $getemail .= '<option value="' . htmlspecialchars($ds['email']) . '">' .
                 htmlspecialchars($ds['name']) . '</option>';
}

/* ============================
   Template
============================ */
echo $tpl->loadTemplate("contact", "form", [
    'description'  => $languageService->get('description'),
    'showerror'    => $showerror,
    'getemail'     => $getemail,
    'name'         => htmlspecialchars($name),
    'from'         => htmlspecialchars($from),
    'subject'      => htmlspecialchars($subject),
    'text'         => htmlspecialchars($text),
    'security_code'=> $languageService->get('security_code'),
    'user'         => $languageService->get('user'),
    'mail'         => $languageService->get('mail'),
    'e_mail_info'  => $languageService->get('e_mail_info'),
    'lang_subject' => $languageService->get('subject'),
    'message'      => $languageService->get('message'),
    'lang_GDPRinfo'=> $languageService->get('GDPRinfo'),
    'send'         => $languageService->get('send'),
    'form_action'  => SeoUrlHandler::convertToSeoUrl('index.php?site=contact'),
    'info_captcha' => (!$loggedin && $captchaEnabled)
        ? '<div class="g-recaptcha" data-sitekey="' . htmlspecialchars($webkey) . '"></div>'
        : '',
], 'theme');

nx_form_guard_prepare('contact', true);
