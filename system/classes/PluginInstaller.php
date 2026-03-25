<?php

namespace nexpell;

class PluginInstaller
{
    /* =====================================================
       INSTALL – ERSTINSTALLATION
    ===================================================== */
    public static function install(string $modulname, string $plugin_folder_path): void
    {
        global $_database;

        self::createPluginTables($modulname);
        self::insertPluginSettings($modulname);
        self::copyPluginFiles($plugin_folder_path, $modulname);
        self::registerPluginInDatabase($modulname);
    }

    /* =====================================================
       REINSTALL – DATEIEN NEU, DB BLEIBT
    ===================================================== */
    public static function reinstall(string $modulname, string $plugin_folder_path): void
    {
        // ❗ KEINE DB-ÄNDERUNGEN
        // ❗ KEINE TABELLEN
        // ❗ KEINE SETTINGS

        self::replacePluginFiles($plugin_folder_path, $modulname);
    }

    /* =====================================================
       DB: TABELLEN
    ===================================================== */
    private static function createPluginTables(string $modulname): void
    {
        $tables = self::getPluginTables($modulname);

        foreach ($tables as $table) {
            safe_query("
                CREATE TABLE IF NOT EXISTS `$table` (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        }
    }

    private static function getPluginTables(string $modulname): array
    {
        return [
            "plugins_{$modulname}_data",
            "plugins_{$modulname}_settings"
        ];
    }

    /* =====================================================
       SETTINGS
    ===================================================== */
    private static function insertPluginSettings(string $modulname): void
    {
        global $_database;

        safe_query("
            INSERT IGNORE INTO settings (name, value)
            VALUES (
                '" . mysqli_real_escape_string($_database, 'plugin_' . $modulname . '_enabled') . "',
                '1'
            )
        ");
    }

    /* =====================================================
       FILES
    ===================================================== */
    private static function copyPluginFiles(string $source, string $modulname): void
    {
        $destination = dirname(__DIR__, 2) . '/includes/plugins/' . $modulname;
        self::recurseCopy($source, $destination);
    }

    private static function replacePluginFiles(string $source, string $modulname): void
    {
        $destination = dirname(__DIR__, 2) . '/includes/plugins/' . $modulname;
        $preservedDirectories = self::backupPreservedDirectories($destination, $modulname);

        if (is_dir($destination)) {
            deleteFolder($destination);
        }

        self::recurseCopy($source, $destination);
        self::restorePreservedDirectories($destination, $preservedDirectories);
    }

    private static function getPreservedDirectories(string $modulname): array
    {
        $map = [
            'about'        => ['images'],
            'achievements' => ['images'],
            'articles'     => ['images'],
            'carousel'     => ['images'],
            'downloads'    => ['files'],
            'gallery'      => ['images'],
            'links'        => ['images'],
            'navigation'   => ['images'],
            'news'         => ['images'],
            'partners'     => ['images'],
            'raidplaner'   => ['img'],
            'sponsors'     => ['images'],
            'wiki'         => ['images'],
        ];

        return $map[$modulname] ?? [];
    }

    private static function backupPreservedDirectories(string $destination, string $modulname): array
    {
        $preserved = [];

        foreach (self::getPreservedDirectories($modulname) as $relativeDir) {
            $sourceDir = $destination . '/' . $relativeDir;
            if (!is_dir($sourceDir)) {
                continue;
            }

            $tmpDir = sys_get_temp_dir() . '/nexpell_preserve_' . $modulname . '_' . md5($sourceDir . microtime(true));
            self::recurseCopy($sourceDir, $tmpDir);
            $preserved[$relativeDir] = $tmpDir;
        }

        return $preserved;
    }

    private static function restorePreservedDirectories(string $destination, array $preservedDirectories): void
    {
        foreach ($preservedDirectories as $relativeDir => $backupDir) {
            if (!is_dir($backupDir)) {
                continue;
            }

            self::recurseCopy($backupDir, $destination . '/' . $relativeDir);
            deleteFolder($backupDir);
        }
    }

    private static function recurseCopy(string $src, string $dst): void
    {
        @mkdir($dst, 0755, true);

        foreach (scandir($src) as $file) {
            if ($file === '.' || $file === '..') continue;

            $s = $src . '/' . $file;
            $d = $dst . '/' . $file;

            is_dir($s)
                ? self::recurseCopy($s, $d)
                : copy($s, $d);
        }
    }

    /* =====================================================
       REGISTER
    ===================================================== */
    private static function registerPluginInDatabase(string $modulname): void
    {
        global $_database;

        safe_query("
            INSERT INTO settings_plugins_installed (modulname, installed_date)
            VALUES (
                '" . mysqli_real_escape_string($_database, $modulname) . "',
                NOW()
            )
        ");
    }
}
