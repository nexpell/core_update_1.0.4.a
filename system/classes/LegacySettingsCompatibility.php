<?php

namespace nexpell;

class LegacySettingsCompatibility
{
    public static function ensureSchema(\mysqli $database): void
    {
        $legacySettingsColumns = [
            'update_channel' => "ALTER TABLE `settings` ADD COLUMN `update_channel` VARCHAR(32) NOT NULL DEFAULT 'stable'"
        ];

        foreach ($legacySettingsColumns as $legacyColumn => $alterSql) {
            $check = $database->query(
                "SHOW COLUMNS FROM `settings` LIKE '" . $database->real_escape_string($legacyColumn) . "'"
            );

            $hasColumn = $check instanceof \mysqli_result && $check->num_rows > 0;
            if ($check instanceof \mysqli_result) {
                $check->free();
            }

            if (!$hasColumn) {
                $database->query($alterSql);
            }
        }
    }

    public static function rewriteSelectQuery(string $query): string
    {
        if (!preg_match('/^\s*select\s+(.*?)\s+from\s+`?settings`?\b/is', $query, $match)) {
            return $query;
        }

        $selectList = $match[1];
        $legacySettingsMap = [
            'update_channel' => "'stable' AS `update_channel`"
        ];

        foreach ($legacySettingsMap as $legacyColumn => $replacement) {
            if (stripos($selectList, $legacyColumn) === false) {
                continue;
            }

            $selectList = preg_replace(
                '/(?<![a-z0-9_`])(?:`?settings`?\.)?`?' . preg_quote($legacyColumn, '/') . '`?(?![a-z0-9_`])/i',
                $replacement,
                $selectList
            );
        }

        $rewritten = preg_replace(
            '/^(\s*select\s+)(.*?)(\s+from\s+`?settings`?\b.*)$/is',
            '$1' . $selectList . '$3',
            $query
        );

        return is_string($rewritten) ? $rewritten : $query;
    }
}
