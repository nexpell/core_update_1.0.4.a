<?php
declare(strict_types=1);

return static function ($database = null): array {
    $db = $database instanceof mysqli ? $database : ($GLOBALS['_database'] ?? null);

    if (!$db instanceof mysqli) {
        throw new RuntimeException('Database connection not available for migration 1.0.4');
    }

    $notes = [];
    $tableExists = static function (string $table) use ($db): bool {
        $result = $db->query("SHOW TABLES LIKE '" . $db->real_escape_string($table) . "'");
        $exists = $result && $result->num_rows > 0;
        if ($result) {
            $result->free();
        }
        return $exists;
    };

    $columnExists = static function (string $table, string $column) use ($db, $tableExists): bool {
        if (!$tableExists($table)) {
            return false;
        }
        $result = $db->query(
            "SHOW COLUMNS FROM `" . $db->real_escape_string($table) . "` LIKE '" . $db->real_escape_string($column) . "'"
        );
        $exists = $result && $result->num_rows > 0;
        if ($result) {
            $result->free();
        }
        return $exists;
    };

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

    if ($tableExists('plugins_footer')) {
        $legalKey = md5('Rechtliches');
        $legalKeyEsc = $db->real_escape_string($legalKey);
        $cookieUrl = 'index.php?site=cookie_policy';
        $cookieUrlEsc = $db->real_escape_string($cookieUrl);

        $db->query("
            INSERT INTO plugins_footer
                (row_type, category_key, section_title, section_sort, link_sort, footer_link_name, footer_link_url, new_tab)
            SELECT 'category', '{$legalKeyEsc}', 'Rechtliches', 2, 1, '', '', 0
            FROM DUAL
            WHERE NOT EXISTS (
                SELECT 1
                FROM plugins_footer
                WHERE row_type = 'category' AND category_key = '{$legalKeyEsc}'
            )
        ");

        $cookieLinkId = 0;
        $cookieLinkResult = $db->query("
            SELECT id
            FROM plugins_footer
            WHERE row_type = 'link'
              AND category_key = '{$legalKeyEsc}'
              AND footer_link_url = '{$cookieUrlEsc}'
            ORDER BY id ASC
            LIMIT 1
        ");
        if ($cookieLinkResult && ($cookieLinkRow = $cookieLinkResult->fetch_assoc())) {
            $cookieLinkId = (int)($cookieLinkRow['id'] ?? 0);
        }
        if ($cookieLinkResult) {
            $cookieLinkResult->free();
        }

        if ($cookieLinkId <= 0) {
            if (!$db->query("
                INSERT INTO plugins_footer
                    (row_type, category_key, section_title, section_sort, link_sort, footer_link_name, footer_link_url, new_tab)
                VALUES
                    ('link', '{$legalKeyEsc}', 'Rechtliches', 2, 4, '', '{$cookieUrlEsc}', 0)
            ")) {
                throw new RuntimeException('Failed to insert footer cookie_policy link: ' . $db->error);
            }
            $cookieLinkId = (int)$db->insert_id;
            $notes[] = 'Added footer link for cookie_policy.';
        } else {
            $db->query("
                UPDATE plugins_footer
                SET section_title = 'Rechtliches', link_sort = 4
                WHERE id = {$cookieLinkId}
                LIMIT 1
            ");
            $notes[] = 'Footer link for cookie_policy already exists.';
        }

        if ($cookieLinkId > 0 && $tableExists('plugins_footer_lang')) {
            $footerLinkTranslations = [
                'de' => 'Cookie-Richtlinie',
                'en' => 'Cookie Policy',
                'it' => 'Informativa sui Cookie',
            ];
            $contentKeyEsc = $db->real_escape_string('link_name_' . $cookieLinkId);
            foreach ($footerLinkTranslations as $iso => $content) {
                $isoEsc = $db->real_escape_string($iso);
                $contentEsc = $db->real_escape_string($content);
                if (!$db->query("
                    INSERT INTO plugins_footer_lang (content_key, language, content, updated_at)
                    VALUES ('{$contentKeyEsc}', '{$isoEsc}', '{$contentEsc}', NOW())
                    ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = NOW()
                ")) {
                    throw new RuntimeException('Failed to upsert footer language for cookie_policy: ' . $db->error);
                }
            }
        }
    } else {
        $notes[] = 'plugins_footer table not present; skipped footer cookie_policy link migration.';
    }

    if ($tableExists('navigation_dashboard_links')) {
        $oldModules = ['settings_imprint', 'settings_privacy_policy', 'settings_terms_of_service'];
        $newModule = 'settings_legal';
        $newUrl = 'admincenter.php?site=settings_legal';
        $newUrlEsc = $db->real_escape_string($newUrl);
        $oldModuleSql = "'" . implode("','", array_map([$db, 'real_escape_string'], $oldModules)) . "'";

        $existingLinkId = 0;
        $existingCatId = 0;
        $existingSort = 0;
        $existingLinkResult = $db->query("
            SELECT linkID, catID, sort
            FROM navigation_dashboard_links
            WHERE modulname = '{$newModule}'
               OR url = '{$newUrlEsc}'
            ORDER BY linkID ASC
            LIMIT 1
        ");
        if ($existingLinkResult && ($existingLinkRow = $existingLinkResult->fetch_assoc())) {
            $existingLinkId = (int)($existingLinkRow['linkID'] ?? 0);
            $existingCatId = (int)($existingLinkRow['catID'] ?? 0);
            $existingSort = (int)($existingLinkRow['sort'] ?? 0);
        }
        if ($existingLinkResult) {
            $existingLinkResult->free();
        }

        $oldLinkIds = [];
        $targetCatId = $existingCatId;
        $targetSort = $existingSort;
        $oldLinkResult = $db->query("
            SELECT linkID, catID, sort
            FROM navigation_dashboard_links
            WHERE modulname IN ({$oldModuleSql})
            ORDER BY sort ASC, linkID ASC
        ");
        if ($oldLinkResult) {
            while ($oldLinkRow = $oldLinkResult->fetch_assoc()) {
                $oldLinkIds[] = (int)($oldLinkRow['linkID'] ?? 0);
                if ($targetCatId <= 0) {
                    $targetCatId = (int)($oldLinkRow['catID'] ?? 0);
                }
                if ($targetSort <= 0) {
                    $targetSort = (int)($oldLinkRow['sort'] ?? 0);
                }
            }
            $oldLinkResult->free();
        }

        if ($targetCatId <= 0) {
            $settingsLinkResult = $db->query("
                SELECT catID
                FROM navigation_dashboard_links
                WHERE modulname IN ('settings_startpage', 'settings_static')
                ORDER BY linkID ASC
                LIMIT 1
            ");
            if ($settingsLinkResult && ($settingsLinkRow = $settingsLinkResult->fetch_assoc())) {
                $targetCatId = (int)($settingsLinkRow['catID'] ?? 0);
            }
            if ($settingsLinkResult) {
                $settingsLinkResult->free();
            }
        }

        if ($targetCatId <= 0) {
            $fallbackCatResult = $db->query("
                SELECT catID
                FROM navigation_dashboard_links
                ORDER BY catID ASC, linkID ASC
                LIMIT 1
            ");
            if ($fallbackCatResult && ($fallbackCatRow = $fallbackCatResult->fetch_assoc())) {
                $targetCatId = (int)($fallbackCatRow['catID'] ?? 0);
            }
            if ($fallbackCatResult) {
                $fallbackCatResult->free();
            }
        }

        if ($targetSort <= 0) {
            $sortResult = $db->query("
                SELECT COALESCE(MAX(sort), 0) + 1 AS next_sort
                FROM navigation_dashboard_links
                WHERE catID = {$targetCatId}
            ");
            if ($sortResult && ($sortRow = $sortResult->fetch_assoc())) {
                $targetSort = (int)($sortRow['next_sort'] ?? 1);
            }
            if ($sortResult) {
                $sortResult->free();
            }
        }
        if ($targetSort <= 0) {
            $targetSort = 1;
        }

        if ($existingLinkId <= 0) {
            if (!$db->query("
                INSERT INTO navigation_dashboard_links (catID, modulname, url, sort)
                VALUES ({$targetCatId}, '{$newModule}', '{$newUrlEsc}', {$targetSort})
            ")) {
                throw new RuntimeException('Failed to insert navigation_dashboard_links entry for settings_legal: ' . $db->error);
            }
            $existingLinkId = (int)$db->insert_id;
            $notes[] = 'Added dashboard link for settings_legal.';
        } else {
            if (!$db->query("
                UPDATE navigation_dashboard_links
                SET catID = {$targetCatId},
                    modulname = '{$newModule}',
                    url = '{$newUrlEsc}',
                    sort = {$targetSort}
                WHERE linkID = {$existingLinkId}
                LIMIT 1
            ")) {
                throw new RuntimeException('Failed to update dashboard link for settings_legal: ' . $db->error);
            }
            $notes[] = 'Dashboard link for settings_legal already exists.';
        }

        if ($existingLinkId > 0 && $tableExists('navigation_dashboard_lang')) {
            $navTranslations = [
                'de' => 'Rechtliches',
                'en' => 'Legal',
                'it' => 'Legale',
            ];
            $modulnameColumnExists = $columnExists('navigation_dashboard_lang', 'modulname');
            $contentKeyEsc = $db->real_escape_string('nav_link_' . $existingLinkId);
            foreach ($navTranslations as $iso => $content) {
                $isoEsc = $db->real_escape_string($iso);
                $contentEsc = $db->real_escape_string($content);
                $modulnameSql = $modulnameColumnExists ? ", modulname" : '';
                $modulnameValueSql = $modulnameColumnExists ? ", '{$newModule}'" : '';
                $modulnameUpdateSql = $modulnameColumnExists ? ", modulname = VALUES(modulname)" : '';
                if (!$db->query("
                    INSERT INTO navigation_dashboard_lang (content_key, language, content{$modulnameSql}, updated_at)
                    VALUES ('{$contentKeyEsc}', '{$isoEsc}', '{$contentEsc}'{$modulnameValueSql}, NOW())
                    ON DUPLICATE KEY UPDATE content = VALUES(content){$modulnameUpdateSql}, updated_at = NOW()
                ")) {
                    throw new RuntimeException('Failed to upsert navigation language for settings_legal: ' . $db->error);
                }
            }
        }

        if ($tableExists('user_role_admin_navi_rights')) {
            $roleIds = [];
            $roleResult = $db->query("
                SELECT DISTINCT roleID
                FROM user_role_admin_navi_rights
                WHERE modulname IN ({$oldModuleSql}, '{$newModule}')
            ");
            if ($roleResult) {
                while ($roleRow = $roleResult->fetch_assoc()) {
                    $roleIds[] = (int)($roleRow['roleID'] ?? 0);
                }
                $roleResult->free();
            }
            if (!in_array(1, $roleIds, true)) {
                $roleIds[] = 1;
            }

            foreach (array_unique(array_filter($roleIds)) as $roleId) {
                if (!$db->query("
                    INSERT IGNORE INTO user_role_admin_navi_rights (roleID, type, modulname)
                    VALUES ({$roleId}, 'link', '{$newModule}')
                ")) {
                    throw new RuntimeException('Failed to insert admin rights for settings_legal: ' . $db->error);
                }
            }

            if (!$db->query("
                DELETE FROM user_role_admin_navi_rights
                WHERE modulname IN ({$oldModuleSql})
            ")) {
                throw new RuntimeException('Failed to delete legacy admin rights for legal settings modules: ' . $db->error);
            }
            $notes[] = 'Migrated admin rights from legacy legal settings modules to settings_legal.';
        }

        if (!empty($oldLinkIds) && $tableExists('navigation_dashboard_lang')) {
            $contentKeys = [];
            foreach ($oldLinkIds as $oldLinkId) {
                if ($oldLinkId > 0) {
                    $contentKeys[] = "'" . $db->real_escape_string('nav_link_' . $oldLinkId) . "'";
                }
            }
            if (!empty($contentKeys)) {
                $db->query("
                    DELETE FROM navigation_dashboard_lang
                    WHERE content_key IN (" . implode(',', $contentKeys) . ")
                ");
            }
        }

        if (!$db->query("
            DELETE FROM navigation_dashboard_links
            WHERE modulname IN ({$oldModuleSql})
        ")) {
            throw new RuntimeException('Failed to delete legacy dashboard links for legal settings modules: ' . $db->error);
        }
        $notes[] = 'Removed legacy dashboard links for settings_imprint/settings_privacy_policy/settings_terms_of_service.';
    } else {
        $notes[] = 'navigation_dashboard_links table not present; skipped settings_legal dashboard migration.';
    }

    return [
        'success' => true,
        'notes' => $notes,
    ];
};
