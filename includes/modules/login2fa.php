<?php
if (session_status() === PHP_SESSION_NONE) session_start();

\nexpell\LoginSecurity::pruneRememberDeviceIfExpired($_COOKIE[\nexpell\LoginSecurity::cookieName()] ?? null);

use nexpell\LoginSecurity;
use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

global $_database, $languageService, $tpl;

$lang = $languageService->detectLanguage();
$languageService->readModule('login');

$login2faUrl = SeoUrlHandler::convertToSeoUrl('index.php?site=login2fa');
$loginUrl    = SeoUrlHandler::convertToSeoUrl('index.php?site=login');

$pending = $_SESSION['2fa'] ?? null;
if (!$pending || ($pending['method'] ?? '') !== 'email' || empty($pending['user_id'])) {
    header('Location: ' . $loginUrl);
    exit;
}

$message = '';

// Meldungen aus Session übernehmen
if (!empty($_SESSION['error_message'])) {
    $msg = (string)$_SESSION['error_message'];
    unset($_SESSION['error_message']);
    $message = '<div class="alert alert-danger" role="alert">'
             . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')
             . '</div>';
}
if (!empty($_SESSION['success_message'])) {
    $msg = (string)$_SESSION['success_message'];
    unset($_SESSION['success_message']);
    $message .= '<div class="alert alert-success" role="alert">'
              . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')
              . '</div>';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {

    try {
        // 1) 2FA-Pending prüfen
        if (empty($_SESSION['2fa']) || empty($_SESSION['2fa']['user_id']) || empty($_SESSION['2fa']['email'])) {
            $_SESSION['error_message'] = $languageService->get('twofa_missing');
            header('Location: ' . $loginUrl);
            exit;
        }
        $userId    = (int)$_SESSION['2fa']['user_id'];
        $userEmail = (string)$_SESSION['2fa']['email'];

        // 2) "Code erneut senden"?
        if (isset($_POST['resend'])) {
            try {
                // Settings laden (Brand/Absender)
                $settings = [];
                if ($res = $_database->query("SELECT hptitle, adminemail FROM settings LIMIT 1")) {
                    $settings = $res->fetch_assoc() ?: [];
                    $res->free();
                }
                $hp_title    = $settings['hptitle']    ?? 'nexpell';
                $admin_email = $settings['adminemail'] ?? ('info@' . ($_SERVER['HTTP_HOST'] ?? 'example.com'));

                // Betreff
                $validUntil = (new \DateTimeImmutable('+' . \nexpell\LoginSecurity::TWOFA_TTL_MIN . ' minutes'))->format('H:i');
                $mail2faLeadSuffix = trim((string)$languageService->get('mail_2fa_lead_valid_suffix'));
                if ($mail2faLeadSuffix === 'mail_2fa_lead_valid_suffix') {
                    $mail2faLeadSuffix = '';
                }
                $subject = $languageService->get('mail_2fa_subject_prefix')
                        . ' ' . $hp_title . ' '
                        . $languageService->get('mail_2fa_subject_mid') . ' '
                        . $validUntil . ' '
                        . $languageService->get('mail_2fa_subject_suffix');

                // Template-Daten
                $templateData = [
                    'lang'       => htmlspecialchars($languageService->get('lang_code_html') ?: 'de', ENT_QUOTES, 'UTF-8'),
                    'preheader'  => htmlspecialchars(
                        $languageService->get('mail_2fa_subject_prefix') . ' ' . $hp_title . ' ' .
                        $languageService->get('mail_2fa_subject_mid') . ' ' . $validUntil . ' ' .
                        $languageService->get('mail_2fa_subject_suffix'),
                        ENT_QUOTES, 'UTF-8'
                    ),
                    'brandEsc'   => htmlspecialchars($hp_title, ENT_QUOTES, 'UTF-8'),
                    'leadHtml'   => htmlspecialchars($languageService->get('mail_2fa_lead_intro'), ENT_QUOTES, 'UTF-8')
                                . ' '
                                . htmlspecialchars($languageService->get('mail_2fa_lead_valid_for'), ENT_QUOTES, 'UTF-8')
                                . ' <strong>' . \nexpell\LoginSecurity::TWOFA_TTL_MIN . ' ' . htmlspecialchars($languageService->get('mail_2fa_minutes'), ENT_QUOTES, 'UTF-8') . '</strong> '
                                . '(' . htmlspecialchars($languageService->get('mail_2fa_until'), ENT_QUOTES, 'UTF-8')
                                . ' <strong>' . $validUntil . ' ' . htmlspecialchars($languageService->get('mail_2fa_clock'), ENT_QUOTES, 'UTF-8') . '</strong>)'
                                . ($mail2faLeadSuffix !== '' ? ' ' . htmlspecialchars($mail2faLeadSuffix, ENT_QUOTES, 'UTF-8') : '')
                                . '.',
                    'tip'        => htmlspecialchars($languageService->get('mail_2fa_tip'), ENT_QUOTES, 'UTF-8'),
                    'footerHtml' => htmlspecialchars($languageService->get('mail_2fa_footer_intro'), ENT_QUOTES, 'UTF-8')
                                . ' <a href="mailto:' . htmlspecialchars($admin_email, ENT_QUOTES, 'UTF-8') . '" style="color:#6b7280;text-decoration:underline;">'
                                . htmlspecialchars($admin_email, ENT_QUOTES, 'UTF-8') . '</a>.',
                    'year'       => date('Y'),
                ];

                $mailHtml = $tpl->loadTemplate('login', 'mail2fa', $templateData, 'theme');

                \nexpell\LoginSecurity::startEmail2faForUser($_database, $userId, $userEmail, $subject, $mailHtml);

                $_SESSION['success_message'] = $languageService->get('twofa_code_resent');
            } catch (\Throwable $e) {
                $msg = $e->getMessage();
                if (stripos($msg, 'Bitte warte kurz') !== false) {
                    $_SESSION['error_message'] = $msg;
                } else {
                    error_log('[2FA] Resend fatal: ' . $msg);
                    $_SESSION['error_message'] = $languageService->get('error_send_2fa') . ' ' . $msg;
                }
            }
            header('Location: ' . $login2faUrl);
            exit;
        }

        // Fallback: Wenn JS aus ist
        if (empty($_POST['code']) && isset($_POST['otp']) && is_array($_POST['otp'])) {
            $_POST['code'] = preg_replace('/\D/', '', implode('', $_POST['otp']));
        }

        // 3) Code einlesen (numerisch, 6 Stellen)
        $clean = preg_replace('/\D/', '', (string)($_POST['code'] ?? ''));
        if ($clean === '' || strlen($clean) !== 6) {
            $_SESSION['error_message'] = $languageService->get('error_code_invalid');
            header('Location: ' . $login2faUrl);
            exit;
        }

        $stmt = $_database->prepare("SELECT is_locked, username, email FROM users WHERE userID=?");
        if (!$stmt) {
            throw new \RuntimeException('DB-Prepare fehlgeschlagen: ' . $_database->error);
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        if (!$row) {
            $_SESSION['error_message'] = $languageService->get('error_no_code');
            header('Location: ' . $loginUrl);
            exit;
        }

        if (!empty($row['is_locked']) && (int)$row['is_locked'] === 1) {
            $_SESSION['error_message'] = $languageService->get('error_account_locked');
            unset($_SESSION['2fa']);
            header('Location: ' . $loginUrl);
            exit;
        }

        $verified = LoginSecurity::verifyEmail2fa($_database, $userId, $clean);

        if (!$verified) {
            $stmt = $_database->prepare("
                SELECT twofa_locked_until, twofa_email_code_expires_at, twofa_email_code_hash
                FROM users WHERE userID=?
            ");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $diag = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$diag) {
                $_SESSION['error_message'] = $languageService->get('unexpected_error');
                header('Location: ' . $login2faUrl);
                exit;
            }

            $now = time();
            if (!empty($diag['twofa_locked_until']) && strtotime($diag['twofa_locked_until']) > $now) {
                $_SESSION['error_message'] = $languageService->get('error_twofa_locked');
            } elseif (empty($diag['twofa_email_code_hash'])
                || empty($diag['twofa_email_code_expires_at'])
                || strtotime($diag['twofa_email_code_expires_at']) < $now) {
                $_SESSION['error_message'] = empty($diag['twofa_email_code_hash'])
                    ? $languageService->get('error_no_code')
                    : $languageService->get('error_code_expired');
            } else {
                $_SESSION['error_message'] = $languageService->get('error_invalid_2fa');
            }
            header('Location: ' . $login2faUrl);
            exit;
        }

        $usernameFinal = (string)($row['username'] ?? '');
        $emailFinal    = (string)($row['email'] ?? $userEmail);

        // Remember-Device-Cookie (nach erfolgreicher Verifizierung) setzen
        if (!empty($_POST['remember_device'])) {
            try {
                \nexpell\LoginSecurity::issueRememberDeviceCookie($_database, (int)$userId, '+30 days');
            } catch (\Throwable $e) {
                error_log('[2FA] remember_device setzen fehlgeschlagen: ' . $e->getMessage());
            }
        }

        // 8) Session härten & Login finalisieren (inkl. Rollen wie login.php)
        @session_regenerate_id(true);
        unset($_SESSION['2fa']);

        $_SESSION['userID']   = $userId;
        $_SESSION['username'] = $usernameFinal;
        $_SESSION['email']    = $emailFinal;
        $_SESSION['loggedin'] = true;
        $_SESSION['twofa_ok'] = true;

        $roles = [];
        $roleNames = [];
        $stmtRole = $_database->prepare("
            SELECT r.roleID, r.role_name
            FROM user_role_assignments ura
            JOIN user_roles r ON ura.roleID = r.roleID
            WHERE ura.userID = ?
        ");
        $stmtRole->bind_param('i', $userId);
        $stmtRole->execute();
        $resultRole = $stmtRole->get_result();
        while ($rowRole = $resultRole->fetch_assoc()) {
            $roles[] = (int)$rowRole['roleID'];
            $roleNames[] = $rowRole['role_name'];
        }
        $stmtRole->close();

        $_SESSION['roles']      = $roles;
        $_SESSION['role_names'] = $roleNames;

        $_SESSION['is_admin']       = in_array(1,  $roles, true);
        $_SESSION['is_coadmin']     = in_array(2,  $roles, true);
        $_SESSION['is_leader']      = in_array(3,  $roles, true);
        $_SESSION['is_coleader']    = in_array(4,  $roles, true);
        $_SESSION['is_squadleader'] = in_array(5,  $roles, true);
        $_SESSION['is_warorg']      = in_array(6,  $roles, true);
        $_SESSION['is_moderator']   = in_array(7,  $roles, true);
        $_SESSION['is_editor']      = in_array(8,  $roles, true);
        $_SESSION['is_member']      = in_array(9,  $roles, true);
        $_SESSION['is_trial']       = in_array(10, $roles, true);
        $_SESSION['is_guest']       = in_array(11, $roles, true);
        $_SESSION['is_registered']  = in_array(12, $roles, true);
        $_SESSION['is_honor']         = in_array(13, $roles, true);
        $_SESSION['is_streamer']      = in_array(14, $roles, true);
        $_SESSION['is_designer']      = in_array(15, $roles, true);
        $_SESSION['is_technician']    = in_array(16, $roles, true);

        $_SESSION['roleID'] = !empty($roles) ? min($roles) : null;

        if ($_SESSION['is_admin']) {
            $_SESSION['userrole'] = 'admin';
        } elseif ($_SESSION['is_coadmin']) {
            $_SESSION['userrole'] = 'coadmin';
        } elseif ($_SESSION['is_moderator']) {
            $_SESSION['userrole'] = 'moderator';
        } elseif ($_SESSION['is_editor']) {
            $_SESSION['userrole'] = 'editor';
        } elseif ($_SESSION['is_registered']) {
            $_SESSION['userrole'] = 'user';
        } else {
            $_SESSION['userrole'] = 'guest';
        }

        if (empty($roles)) {
            $res = safe_query("SELECT roleID FROM user_roles WHERE is_default = 1 LIMIT 1");
            if ($rowDef = mysqli_fetch_assoc($res)) {
                $defaultRole = (int)$rowDef['roleID'];
                safe_query("INSERT INTO user_role_assignments (userID, roleID, created_at)
                            VALUES (" . (int)$userId . ", $defaultRole, NOW())");
                $_SESSION['roles'] = [$defaultRole];
                $_SESSION['is_registered'] = true;
                $_SESSION['userrole'] = 'user';
            }
        }

        LoginSecurity::saveSession($userId);

        $login_time = date('Y-m-d H:i:s');
        $is_online  = 1;
        $updateStmt = $_database->prepare("
            UPDATE users 
            SET 
                lastlogin = ?,
                login_time = ?,
                last_activity = ?,
                is_online = ?
            WHERE userID = ?
        ");
        $updateStmt->bind_param('sssii', $login_time, $login_time, $login_time, $is_online, $userId);
        $updateStmt->execute();
        $updateStmt->close();

        $_SESSION['success_message'] = $languageService->get('success_login');
        header('Location: /');
        exit;

    } catch (\Throwable $e) {
        error_log('[2FA] Fatal: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        $_SESSION['error_message'] = $languageService->get('unexpected_error');
        header('Location: ' . $login2faUrl);
        exit;
    }
}

// Template-Daten
$data_array = [
    'login_headline' => $languageService->get('title2fa'),
    'twofa_prompt'   => $languageService->get('twofa_prompt'),
    'enter_code'     => $languageService->get('enter_code'),
    'remember_device'=> $languageService->get('remember_device'),
    'resend'         => $languageService->get('resend_code'),
    'confirm'        => $languageService->get('confirm'),
    'error_message'  => $message,
];

echo $tpl->loadTemplate("login2fa", "content", $data_array, 'theme');
