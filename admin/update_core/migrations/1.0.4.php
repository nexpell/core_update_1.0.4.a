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
