<?php
declare(strict_types=1);

return static function ($database = null): array {
    $db = $database instanceof mysqli ? $database : ($GLOBALS['_database'] ?? null);

    if (!$db instanceof mysqli) {
        throw new RuntimeException('Database connection not available for migration 1.0.4');
    }

    $notes = [];

    $settingsColumnsToDrop = [
        'keywords' => 'Removed settings.keywords; meta keywords are no longer used.',
        'update_channel' => 'Removed settings.update_channel; legacy update channel setting is no longer used.',
        'use_seo_urls' => 'Removed settings.use_seo_urls; SEO URLs are now always active.',
    ];

    foreach ($settingsColumnsToDrop as $column => $successNote) {
        $check = $db->query("SHOW COLUMNS FROM settings LIKE '" . $db->real_escape_string($column) . "'");
        $hasColumn = $check && $check->num_rows > 0;
        if ($check) {
            $check->free();
        }

        if ($hasColumn) {
            if (!$db->query("ALTER TABLE settings DROP COLUMN `" . $column . "`")) {
                throw new RuntimeException('Failed to drop settings.' . $column . ': ' . $db->error);
            }
            $notes[] = $successNote;
        } else {
            $notes[] = 'settings.' . $column . ' already removed.';
        }
    }

    $usersColumnsToAdd = [
        'twofa_enabled' => "ALTER TABLE users ADD COLUMN twofa_enabled TINYINT(1) NOT NULL DEFAULT 0",
        'twofa_method' => "ALTER TABLE users ADD COLUMN twofa_method ENUM('email','totp') NULL DEFAULT 'email'",
        'twofa_email_code_hash' => "ALTER TABLE users ADD COLUMN twofa_email_code_hash VARCHAR(255) NULL",
        'twofa_email_code_expires_at' => "ALTER TABLE users ADD COLUMN twofa_email_code_expires_at DATETIME NULL",
        'twofa_email_last_sent_at' => "ALTER TABLE users ADD COLUMN twofa_email_last_sent_at DATETIME NULL",
        'twofa_failed_attempts' => "ALTER TABLE users ADD COLUMN twofa_failed_attempts INT NOT NULL DEFAULT 0",
        'twofa_locked_until' => "ALTER TABLE users ADD COLUMN twofa_locked_until DATETIME NULL",
        'remember_device_salt' => "ALTER TABLE users ADD COLUMN remember_device_salt VARCHAR(64) DEFAULT NULL",
    ];

    foreach ($usersColumnsToAdd as $column => $sql) {
        $check = $db->query("SHOW COLUMNS FROM users LIKE '" . $db->real_escape_string($column) . "'");
        $hasColumn = $check && $check->num_rows > 0;
        if ($check) {
            $check->free();
        }

        if ($hasColumn) {
            $notes[] = 'users.' . $column . ' already exists.';
            continue;
        }

        if (!$db->query($sql)) {
            throw new RuntimeException('Failed to add users.' . $column . ': ' . $db->error);
        }

        $notes[] = 'Added users.' . $column . '.';
    }

    $settingsColumnsToAdd = [
        'twofa_force_all' => "ALTER TABLE settings ADD COLUMN twofa_force_all TINYINT(1) NOT NULL DEFAULT 1",
    ];

    foreach ($settingsColumnsToAdd as $column => $sql) {
        $check = $db->query("SHOW COLUMNS FROM settings LIKE '" . $db->real_escape_string($column) . "'");
        $hasColumn = $check && $check->num_rows > 0;
        if ($check) {
            $check->free();
        }

        if ($hasColumn) {
            $notes[] = 'settings.' . $column . ' already exists.';
            continue;
        }

        if (!$db->query($sql)) {
            throw new RuntimeException('Failed to add settings.' . $column . ': ' . $db->error);
        }

        $notes[] = 'Added settings.' . $column . '.';
    }

    $tableCheck = $db->query("SHOW TABLES LIKE 'plugins_rules_settings'");
    $hasRulesSettingsTable = $tableCheck && $tableCheck->num_rows > 0;
    if ($tableCheck) {
        $tableCheck->free();
    }

    if ($hasRulesSettingsTable) {
        if (!$db->query("DROP TABLE plugins_rules_settings")) {
            throw new RuntimeException('Failed to drop table plugins_rules_settings: ' . $db->error);
        }
        $notes[] = 'Removed legacy table plugins_rules_settings.';
    } else {
        $notes[] = 'plugins_rules_settings already removed.';
    }

    return [
        'success' => true,
        'notes' => $notes,
    ];
};
