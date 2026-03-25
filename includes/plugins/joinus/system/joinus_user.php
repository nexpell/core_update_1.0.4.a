<?php
declare(strict_types=1);

use nexpell\LoginSecurity;

function joinusBuildUsername(string $name, string $email): string
{
    $base = trim($name);
    if ($base === '') {
        $base = strstr($email, '@', true) ?: 'user';
    }

    $base = strtolower($base);
    $base = preg_replace('/[^a-z0-9]+/', '_', $base);
    $base = trim((string)$base, '_');
    $base = substr($base !== '' ? $base : 'user', 0, 24);

    return $base !== '' ? $base : 'user';
}

function joinusUsernameExists(mysqli $db, string $username): bool
{
    $stmt = $db->prepare("SELECT userID FROM users WHERE username = ? LIMIT 1");
    if (!$stmt) {
        return true;
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();

    return $exists;
}

function joinusResolveUniqueUsername(mysqli $db, string $name, string $email): string
{
    $base = joinusBuildUsername($name, $email);
    $candidate = $base;
    $suffix = 1;

    while (joinusUsernameExists($db, $candidate)) {
        $candidate = substr($base, 0, max(1, 24 - strlen((string)$suffix) - 1)) . '_' . $suffix;
        $suffix++;
    }

    return $candidate;
}

function createUserFromJoinUs(int $applicationId): array
{
    global $_database, $languageService;

    $stmt = $_database->prepare("
        SELECT id, email, name, role, type, status, admin_note
        FROM plugins_joinus_applications
        WHERE id = ?
        LIMIT 1
    ");
    if (!$stmt) {
        return ['success' => false, 'error' => true, 'message' => 'JoinUs application could not be loaded.'];
    }

    $stmt->bind_param("i", $applicationId);
    $stmt->execute();
    $application = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$application) {
        return ['success' => false, 'error' => true, 'message' => $languageService->get('todo_not_found')];
    }

    $email = trim((string)($application['email'] ?? ''));
    $name = trim((string)($application['name'] ?? ''));
    $requestedRoleId = (int)($application['role'] ?? 0);

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => true, 'message' => $languageService->get('create_user_invalid_email') ?: 'JoinUs application has no valid email address.'];
    }

    $stmt = $_database->prepare("SELECT userID, username FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        return ['success' => false, 'error' => true, 'message' => 'User lookup failed.'];
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $existingUser = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existingUser) {
        return [
            'success' => false,
            'error' => true,
            'message' => str_replace(
                ['%email%', '%username%'],
                [$email, (string)($existingUser['username'] ?? '')],
                $languageService->get('create_user_exists') ?: 'A user with this email already exists.'
            )
        ];
    }

    $username = joinusResolveUniqueUsername($_database, $name, $email);
    $plainPassword = LoginSecurity::generatePepper();
    $pepperPlain = LoginSecurity::generatePepper();
    $pepperEncrypted = LoginSecurity::encryptPepper($pepperPlain);
    $passwordHash = LoginSecurity::createPasswordHash($plainPassword, $email, $pepperPlain);

    $roleId = $requestedRoleId > 0 ? $requestedRoleId : 1;
    $isActive = 1;

    $stmt = $_database->prepare("
        INSERT INTO users (username, email, registerdate, role, is_active, password_hash, password_pepper)
        VALUES (?, ?, CURRENT_TIMESTAMP(), ?, ?, ?, ?)
    ");
    if (!$stmt) {
        return ['success' => false, 'error' => true, 'message' => 'User creation prepare failed.'];
    }

    $stmt->bind_param('ssiiss', $username, $email, $roleId, $isActive, $passwordHash, $pepperEncrypted);
    if (!$stmt->execute()) {
        $stmt->close();
        return ['success' => false, 'error' => true, 'message' => 'User creation failed.'];
    }

    $userId = (int)$_database->insert_id;
    $stmt->close();

    $stmt = $_database->prepare("INSERT INTO user_username (userID, username) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param('is', $userId, $username);
        $stmt->execute();
        $stmt->close();
    }

    $assignedAt = date('Y-m-d H:i:s');
    $stmt = $_database->prepare("
        INSERT INTO user_role_assignments (userID, roleID, assigned_at)
        VALUES (?, ?, ?)
    ");
    if ($stmt) {
        $stmt->bind_param('iis', $userId, $roleId, $assignedAt);
        $stmt->execute();
        $stmt->close();
    }

    $notePrefix = 'Created user #' . $userId . ' (' . $username . ') on ' . $assignedAt . '.';
    $adminNote = trim((string)($application['admin_note'] ?? ''));
    $newNote = $adminNote !== '' ? $notePrefix . "\n" . $adminNote : $notePrefix;

    $stmt = $_database->prepare("
        UPDATE plugins_joinus_applications
        SET status = 'accepted',
            last_status = status,
            processed_at = NOW(),
            admin_note = ?
        WHERE id = ?
    ");
    if ($stmt) {
        $stmt->bind_param('si', $newNote, $applicationId);
        $stmt->execute();
        $stmt->close();
    }

    return [
        'success' => true,
        'message' => str_replace(
            ['%username%', '%email%', '%password%', '%user_id%'],
            [$username, $email, $plainPassword, (string)$userId],
            $languageService->get('create_user_success') ?: 'User created successfully.'
        ),
        'user_id' => $userId,
        'username' => $username,
        'password' => $plainPassword
    ];
}
