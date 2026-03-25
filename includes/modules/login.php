<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

\nexpell\LoginSecurity::pruneRememberDeviceIfExpired($_COOKIE[\nexpell\LoginSecurity::cookieName()] ?? null);

$message = '';

use nexpell\LoginSecurity;
use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

// Initialisieren
global $_database, $languageService, $tpl;

$languageService->readModule('login');

// Nur Fehler auf der Login-Maske: success_message ist für Ziele nach Login (z. B. Startseite).
// Sonst bleibt eine alte „Login erfolgreich!“-Session stehen und erscheint beim nächsten GET hier.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_SESSION['userID']) && !empty($_SESSION['success_message'])) {
    unset($_SESSION['success_message']);
}

$flashTypes = [
    'error_message' => 'danger',
];
foreach ($flashTypes as $sessionKey => $alertType) {
    if (!empty($_SESSION[$sessionKey])) {
        $msg = (string)$_SESSION[$sessionKey];
        unset($_SESSION[$sessionKey]);

        $message .= '<div class="alert alert-' . $alertType . '" role="alert">'
                  . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')
                  . '</div>';
    }
}

$settingsRow = [];
if ($res = $_database->query("SELECT * FROM settings LIMIT 1")) {
    $settingsRow = $res->fetch_assoc() ?: [];
    $res->free();
}
$forceAll = (int)($settingsRow['twofa_force_all'] ?? 1) === 1;

$email = '';
$ip = $_SERVER['REMOTE_ADDR'];
$message_zusatz = '';
$isIpBanned = '';
$is_active = '';
$is_locked = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_SESSION['userID'])) {
        header('Location: /');
        exit;
    }

    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password_hash = $_POST['password_hash'] ?? $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = $languageService->get('error_invalid_email');
        header("Location: " . SeoUrlHandler::convertToSeoUrl('index.php?site=login'));
        exit;
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $loginResult = LoginSecurity::verifyLogin($email, $password_hash, $ip, $is_active, $is_locked);

    if ($loginResult['success']) {
        if (LoginSecurity::isIpBanned($ip)) {
            $message = '<div class="alert alert-danger" role="alert">' . $languageService->get('error_ip_banned') . '</div>';
            $isIpBanned = true;
        } else {

        // 2FA + „Gerät merken“: remember_device_salt muss in der DB existieren (siehe sql/twofa_migration.sql).
        $stmt = $_database->prepare(
            'SELECT userID, username, email, is_locked, twofa_enabled, twofa_method, remember_device_salt FROM users WHERE email = ?'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            if (!empty($user['is_locked']) && (int)$user['is_locked'] === 1) {
                $message = '<div class="alert alert-danger" role="alert">' . $languageService->get('error_account_locked') . '</div>';
                $isIpBanned = true;
            } else {
                $twofaMethod  = $user['twofa_method'] ?? 'email';
                $userWants2fa = isset($user['twofa_enabled']) && (int)$user['twofa_enabled'] === 1;
                $must2fa      = $forceAll || $userWants2fa;

                if ($must2fa && $twofaMethod === 'email') {
                    $hasRemember = LoginSecurity::checkRememberDeviceCookie(
                        $user['remember_device_salt'] ?? null,
                        (int)$user['userID']
                    );

                    if (!$hasRemember) {
                        $hp_title    = $settingsRow['hptitle'] ?? 'nexpell';
                        $admin_email = $settingsRow['adminemail'] ?? ('info@' . ($_SERVER['HTTP_HOST'] ?? 'example.com'));

                        $validUntil = (new \DateTimeImmutable('+' . \nexpell\LoginSecurity::TWOFA_TTL_MIN . ' minutes'))->format('H:i');
                        $brand      = $hp_title;
                        $brandEsc   = htmlspecialchars($hp_title, ENT_QUOTES, 'UTF-8');
                        $adminEsc   = htmlspecialchars($admin_email, ENT_QUOTES, 'UTF-8');
                        $mail2faLeadSuffix = trim((string)$languageService->get('mail_2fa_lead_valid_suffix'));
                        if ($mail2faLeadSuffix === 'mail_2fa_lead_valid_suffix') {
                            $mail2faLeadSuffix = '';
                        }

                        $subject = $languageService->get('mail_2fa_subject_prefix')
                                 . ' ' . $brand . ' '
                                 . $languageService->get('mail_2fa_subject_mid') . ' '
                                 . $validUntil . ' '
                                 . $languageService->get('mail_2fa_subject_suffix');

                        $templateData = [
                            'lang'       => htmlspecialchars($languageService->get('lang_code_html') ?: 'de', ENT_QUOTES, 'UTF-8'),
                            'preheader'  => htmlspecialchars(
                                $languageService->get('mail_2fa_subject_prefix') . ' ' . $brand . ' ' .
                                $languageService->get('mail_2fa_subject_mid') . ' ' . $validUntil . ' ' .
                                $languageService->get('mail_2fa_subject_suffix'),
                                ENT_QUOTES, 'UTF-8'
                            ),
                            'brandEsc'   => $brandEsc,
                            'leadHtml'   => htmlspecialchars($languageService->get('mail_2fa_lead_intro'), ENT_QUOTES, 'UTF-8')
                                         . ' ' . htmlspecialchars($languageService->get('mail_2fa_lead_valid_for'), ENT_QUOTES, 'UTF-8')
                                         . ' <strong>' . \nexpell\LoginSecurity::TWOFA_TTL_MIN . ' ' . htmlspecialchars($languageService->get('mail_2fa_minutes'), ENT_QUOTES, 'UTF-8') . '</strong> '
                                         . '(' . htmlspecialchars($languageService->get('mail_2fa_until'), ENT_QUOTES, 'UTF-8')
                                         . ' <strong>' . $validUntil . ' ' . htmlspecialchars($languageService->get('mail_2fa_clock'), ENT_QUOTES, 'UTF-8') . '</strong>)'
                                         . ($mail2faLeadSuffix !== '' ? ' ' . htmlspecialchars($mail2faLeadSuffix, ENT_QUOTES, 'UTF-8') : '')
                                         . '.',
                            'tip'        => htmlspecialchars($languageService->get('mail_2fa_tip'), ENT_QUOTES, 'UTF-8'),
                            'footerHtml' => htmlspecialchars($languageService->get('mail_2fa_footer_intro'), ENT_QUOTES, 'UTF-8')
                                         . ' <a href="mailto:' . $adminEsc . '" style="color:#6b7280;text-decoration:underline;">' . $adminEsc . '</a>.',
                            'year'       => date('Y'),
                        ];

                        $mailHtml = $tpl->loadTemplate('login', 'mail2fa', $templateData, 'theme');

                        try {
                            LoginSecurity::startEmail2faForUser(
                                $_database,
                                (int)$user['userID'],
                                (string)$user['email'],
                                $subject,
                                $mailHtml
                            );
                        } catch (\Throwable $e) {
                            error_log('[2FA] Mailversand-Fehler: ' . $e->getMessage());
                        }

                        $_SESSION['2fa'] = [
                            'user_id' => (int)$user['userID'],
                            'email'   => (string)$user['email'],
                            'method'  => 'email',
                            'nonce'   => bin2hex(random_bytes(16)),
                        ];

                        header('Location: ' . SeoUrlHandler::convertToSeoUrl('index.php?site=login2fa'));
                        exit;
                    }
                }

                @session_regenerate_id(true);

                // ===========================================
                // Session setzen (Basisdaten)
                // ===========================================
                $_SESSION['userID']   = (int)$user['userID'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email']    = $user['email'];

                // ===========================================
                // Rollen-System: alle Rollen + Namen laden
                // ===========================================
                $roles = [];
                $roleNames = [];

                $stmtRole = $_database->prepare("
                    SELECT r.roleID, r.role_name
                    FROM user_role_assignments ura
                    JOIN user_roles r ON ura.roleID = r.roleID
                    WHERE ura.userID = ?
                ");
                $stmtRole->bind_param("i", $user['userID']);
                $stmtRole->execute();
                $resultRole = $stmtRole->get_result();

                while ($rowRole = $resultRole->fetch_assoc()) {
                    $roles[] = (int)$rowRole['roleID'];
                    $roleNames[] = $rowRole['role_name'];
                }
                $stmtRole->close();

                $_SESSION['roles']       = $roles;
                $_SESSION['role_names']  = $roleNames;

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
                $_SESSION['is_honor']       = in_array(13, $roles, true);
                $_SESSION['is_streamer']    = in_array(14, $roles, true);
                $_SESSION['is_designer']    = in_array(15, $roles, true);
                $_SESSION['is_technician']  = in_array(16, $roles, true);

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
                    if ($row = mysqli_fetch_assoc($res)) {
                        $defaultRole = (int)$row['roleID'];
                        safe_query("INSERT INTO user_role_assignments (userID, roleID, created_at)
                                    VALUES (" . (int)$user['userID'] . ", $defaultRole, NOW())");
                        $_SESSION['roles'] = [$defaultRole];
                        $_SESSION['is_registered'] = true;
                        $_SESSION['userrole'] = 'user';
                    }
                }

                LoginSecurity::saveSession($user['userID']);

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
                $updateStmt->bind_param("sssii", $login_time, $login_time, $login_time, $is_online, $user['userID']);
                $updateStmt->execute();
                $updateStmt->close();

                $_SESSION['success_message'] = $languageService->get('success_login');

                header("Location: /");
                exit;
            }
        } else {
            $message = '<div class="alert alert-danger" role="alert">' . $languageService->get('error_not_found') . '</div>';
        }

        }

    } else {
        $userID = null;
        $stmt = $_database->prepare("SELECT userID, is_active FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $userID = (int)$row['userID'];

            if ((int)$row['is_active'] === 0) {
                $message = '<div class="alert alert-danger" role="alert">' . $languageService->get('error_account_inactive') . '</div>';
                $isIpBanned = true;
            } else {
                if (!LoginSecurity::isEmailOrIpBanned($email, $ip)) {
                    LoginSecurity::trackFailedLogin($userID, $email, $ip);

                    $failCount = LoginSecurity::getFailCount($ip, $email);
                    if ($failCount >= 5) {
                        LoginSecurity::banIp($ip, $userID, "Zu viele Fehlversuche", $email);
                        $_SESSION['error_message'] = $languageService->get('error_login_locked');
                    } else {
                        $_SESSION['error_message'] = str_replace('{failcount}', $failCount, $languageService->get('error_invalid_login'));
                    }
                } else {
                    $message = '<div class="alert alert-danger" role="alert">' . $languageService->get('error_email_or_ip_banned') . '</div>';
                    $isIpBanned = true;
                }
            }
        } else {
            $message = '<div class="alert alert-danger" role="alert">' . $languageService->get('error_not_found') . '</div>';
            $isIpBanned = true;
        }
    }

    if (isset($_SESSION['error_message'])) {
        $message = '<div class="alert alert-danger" role="alert">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
}

if (!empty($email)) {
    $isEmailBanned = LoginSecurity::isEmailBanned($email, $ip);
} else {
    $isEmailBanned = false;
}

if ($isEmailBanned) {
    $message = '<div class="alert alert-danger" role="alert">' . $languageService->get('error_email_banned') . '</div>';
    $isIpBanned = true;
}

$registerlink = '<a href="' . SeoUrlHandler::convertToSeoUrl('index.php?site=register') . '">' . $languageService->get('register_link') . '</a>';
$lostpasswordlink = '<a href="' . SeoUrlHandler::convertToSeoUrl('index.php?site=lostpassword') . '">' . $languageService->get('lostpassword_link') . '</a>';

$alreadyLoggedIn = !empty($_SESSION['userID']);
$homeHref         = SeoUrlHandler::convertToSeoUrl('index.php');
$logoutHref       = SeoUrlHandler::convertToSeoUrl('index.php?site=logout');
$usernameLoggedIn = htmlspecialchars(
    (string)($_SESSION['username'] ?? $_SESSION['email'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$data_array = [
    'already_logged_in' => $alreadyLoggedIn ? '1' : '',
    'already_logged_in_headline' => $languageService->get('already_logged_in_headline'),
    'already_logged_in_username' => $usernameLoggedIn,
    'already_logged_in_text' => $languageService->get('already_logged_in_text'),
    'goto_home' => $languageService->get('goto_home'),
    'goto_logout' => $languageService->get('goto_logout'),
    'home_href' => $homeHref,
    'logout_href' => $logoutHref,
    'login_headline' => $languageService->get('title'),
    'email_label' => $languageService->get('email_label'),
    'your_email' => $languageService->get('your_email'),
    'pass_label' => $languageService->get('pass_label'),
    'your_pass' => $languageService->get('your_pass'),
    'remember_me' => $languageService->get('remember_me'),
    'login_button' => $languageService->get('login_button'),
    'register_link' => $languageService->get('register_link'),
    'registerlink' => $registerlink,
    'lostpasswordlink' => $lostpasswordlink,
    'error_message' => $message,
    'message_zusatz' => $message_zusatz,
    'isIpBanned' => $isIpBanned,
    'welcome_back' => $languageService->get('welcome_back'),
    'reg_text' => $languageService->get('reg_text'),
    'login_text' => $languageService->get('login_text'),
];

echo $tpl->loadTemplate("login", "content", $data_array, 'theme');
