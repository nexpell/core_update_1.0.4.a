<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\SeoUrlHandler;

global $_database, $tpl, $languageService, $pluginManager;

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

$data_array = [
    'class'    => $class,
    'title'    => $languageService->get('title'),
    'subtitle' => $languageService->get('subtitle')
];

echo $tpl->loadTemplate("joinus", "head", $data_array, 'plugin');

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

nx_form_guard_prepare('joinus', $_SERVER['REQUEST_METHOD'] !== 'POST');

$recaptcha = nx_get_recaptcha_config();
$webkey = $recaptcha['webkey'];
$recaptchaEnabled = $recaptcha['enabled'];

if (empty($_SESSION['userID']) && $recaptchaEnabled) {
    nx_mark_recaptcha_required();
}

$success = false;
$error = '';

$name = '';
$email = '';
$role = '';
$message = '';
$type = 'team';
$squadId = null;
$roleId = 0;
$roleCustom = '';

$canSubmit = !empty($_SESSION['userID']);

$joinusRoles = [];
$res = $_database->query("
    SELECT r.roleID, r.role_name
    FROM user_roles r
    INNER JOIN plugins_joinus_roles jr
        ON jr.role_id = r.roleID
       AND jr.is_enabled = 1
    WHERE r.is_active = 1
      AND r.roleID != 1
    ORDER BY r.role_name ASC
");
while ($row = $res->fetch_assoc()) {
    $joinusRoles[] = $row;
}

$joinusSquads = [];
$res = $_database->query("
    SELECT id, name
    FROM plugins_joinus_squads
    WHERE is_enabled = 1
    ORDER BY name ASC
");
while ($row = $res->fetch_assoc()) {
    $joinusSquads[] = $row;
}
$hasSquads = !empty($joinusSquads);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $error = $languageService->get('csrf_invalid');
    }

    if ($error === '' && !$canSubmit) {
        if (trim((string)($_POST['company'] ?? '')) !== '') {
            $error = $languageService->get('error_save');
        } elseif (!$recaptchaEnabled) {
            $canSubmit = true;
        }
    }

    if ($error === '' && nx_form_guard_is_too_fast('joinus', 5)) {
        $error = $languageService->get('submission_too_fast');
    }

    if ($error === '' && !nx_rate_limit_consume('joinus', 3, 1800, (string)($_SERVER['REMOTE_ADDR'] ?? ''))) {
        $error = $languageService->get('rate_limit_exceeded');
    }

    if ($error === '' && !$canSubmit && $recaptchaEnabled) {
        $recaptchaResponse = (string)($_POST['g-recaptcha-response'] ?? '');
        if ($recaptchaResponse === '' || !nx_verify_recaptcha($recaptchaResponse, (string)($_SERVER['REMOTE_ADDR'] ?? ''))) {
            $error = $languageService->get('recaptcha_error');
        } else {
            $canSubmit = true;
        }
    }

    if ($error === '' && $canSubmit) {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));
        $type = (string)($_POST['type'] ?? 'team');
        $squad_id = (int)($_POST['squad_id'] ?? 0);
        $squadId = $squad_id > 0 ? $squad_id : null;

        $roleId = 0;
        $roleCustom = '';

        if (isset($_POST['role']) && $_POST['role'] !== '') {
            $roleId = (int)$_POST['role'];
        }

        if ($roleId === -1) {
            $roleCustom = trim((string)($_POST['role_custom'] ?? ''));
            if ($roleCustom === '') {
                $error = $languageService->get('error_role_required');
            }
            $roleId = 0;
        }

        if ($roleId === 0 && $roleCustom === '') {
            $error = $languageService->get('error_role_required');
        }

        if (!in_array($type, ['team', 'partner', 'squad'], true)) {
            $type = 'team';
        }

        if ($type === 'squad' && $hasSquads && $squad_id === 0) {
            $error = $languageService->get('error_squad_required');
        }

        if ($name === '' || $email === '' || $message === '') {
            $error = $languageService->get('error_required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = $languageService->get('error_email');
        }
    }

    if ($error === '' && $canSubmit) {
        $stmt = $_database->prepare("
            INSERT INTO plugins_joinus_applications
            (name, email, role, role_custom, message, type, squad_id, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'new', NOW())
        ");

        if ($stmt) {
            $stmt->bind_param(
                "ssisssi",
                $name,
                $email,
                $roleId,
                $roleCustom,
                $message,
                $type,
                $squad_id
            );

            if ($stmt->execute()) {
                $success = true;
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $name = $email = $message = '';
                $roleId = 0;
                $roleCustom = '';
                $type = 'team';
                $squadId = null;
            } else {
                $error = $languageService->get('error_save');
            }

            $stmt->close();
        } else {
            $error = $languageService->get('error_save');
        }
    }
}

$types = [];
$res = $_database->query("
    SELECT type_key, label
    FROM plugins_joinus_types
    WHERE is_enabled = 1
    ORDER BY sort_order ASC
");
while ($row = $res->fetch_assoc()) {
    $types[] = [
        'type_key' => $row['type_key'],
        'label'    => $row['label'],
        'selected' => $type === $row['type_key'] ? 'selected' : ''
    ];
}

foreach ($joinusRoles as &$joinusRole) {
    $joinusRole['selected'] = $roleId === (int)$joinusRole['roleID'] ? 'selected' : '';
}
unset($joinusRole);

foreach ($joinusSquads as &$joinusSquad) {
    $joinusSquad['selected'] = $squadId === (int)$joinusSquad['id'] ? 'selected' : '';
}
unset($joinusSquad);

$recaptchaHtml = '';
if (empty($_SESSION['userID']) && $recaptchaEnabled) {
    $recaptchaHtml = '
    <div class="mb-3">
        <div class="g-recaptcha" data-sitekey="' . htmlspecialchars($webkey, ENT_QUOTES) . '"></div>
    </div>';
}

$data_array = [
    'title' => $languageService->get('title'),
    'intro' => $languageService->get('intro'),
    'label_name' => $languageService->get('label_name'),
    'label_email' => $languageService->get('label_email'),
    'label_type' => $languageService->get('label_type'),
    'label_role' => $languageService->get('label_role'),
    'label_message' => $languageService->get('label_message'),
    'types' => $types,
    'label_squad' => $languageService->get('label_squad'),
    'placeholder_type' => $languageService->get('placeholder_type'),
    'placeholder_role' => $languageService->get('placeholder_role'),
    'label_other' => $languageService->get('label_other'),
    'label_role_custom' => $languageService->get('label_role_custom'),
    'value_role_custom' => htmlspecialchars($roleCustom, ENT_QUOTES),
    'placeholder_squad' => $languageService->get('placeholder_squad'),
    'type_placeholder_selected' => $type === '' ? 'selected' : '',
    'squad_placeholder_selected' => $squadId === null ? 'selected' : '',
    'role_placeholder_selected' => ($roleId === 0 && $roleCustom === '') ? 'selected' : '',
    'other_role_selected' => $roleCustom !== '' ? 'selected' : '',
    'squad_wrapper_class' => $type === 'squad' ? '' : 'd-none',
    'role_other_class' => $roleCustom !== '' ? '' : 'd-none',
    'submit' => $languageService->get('submit'),
    'value_name' => htmlspecialchars($name, ENT_QUOTES),
    'value_email' => htmlspecialchars($email, ENT_QUOTES),
    'value_role' => htmlspecialchars($role, ENT_QUOTES),
    'value_message' => htmlspecialchars($message, ENT_QUOTES),
    'value_type' => $type,
    'success' => $success,
    'success_text' => $languageService->get('success'),
    'success_badge' => $languageService->get('success_badge'),
    'error' => $error,
    'success_hint' => $languageService->get('success_hint'),
    'back_home' => $languageService->get('back_home'),
    'contact_label' => $languageService->get('contact_label'),
    'contact_link' => true,
    'contact_url' => SeoUrlHandler::convertToSeoUrl('index.php?site=contact'),
    'csrf_token' => $_SESSION['csrf_token'],
    'recaptcha' => $recaptchaHtml,
    'roles' => $joinusRoles,
    'squads' => $joinusSquads,
    'has_squads' => $hasSquads,
    'squad_help' => $languageService->get('squad_help'),
    'form_badge' => $languageService->get('form_badge'),
    'sidebar_eyebrow' => $languageService->get('sidebar_eyebrow'),
    'sidebar_title' => $languageService->get('sidebar_title'),
    'sidebar_text' => $languageService->get('sidebar_text'),
    'sidebar_step_1_title' => $languageService->get('sidebar_step_1_title'),
    'sidebar_step_1_text' => $languageService->get('sidebar_step_1_text'),
    'sidebar_step_2_title' => $languageService->get('sidebar_step_2_title'),
    'sidebar_step_2_text' => $languageService->get('sidebar_step_2_text'),
    'sidebar_step_3_title' => $languageService->get('sidebar_step_3_title'),
    'sidebar_step_3_text' => $languageService->get('sidebar_step_3_text'),
    'submit_hint' => $languageService->get('submit_hint'),
];

echo $tpl->loadTemplate(
    'joinus',
    $success ? 'success' : 'form',
    $data_array,
    'plugin'
);

nx_form_guard_prepare('joinus', true);
