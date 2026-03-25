<?php

namespace nexpell;

class ThemeUninstaller
{
    private array $log = [];

    private function deleteDirectory(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            $itemPath = $item->getPathname();
            if ($item->isDir()) {
                if (!@rmdir($itemPath)) {
                    return false;
                }
                continue;
            }

            if (!@unlink($itemPath)) {
                return false;
            }
        }

        return @rmdir($path);
    }

    public function uninstall(string $themeFolder): void
    {
        global $_database;

        $themeFolder = trim($themeFolder);
        $themeDir = dirname(__DIR__, 2) . '/includes/themes/' . $themeFolder;

        if (is_dir($themeDir)) {
            if ($this->deleteDirectory($themeDir)) {
                $this->log[] = ['type' => 'success', 'message' => "Ordner {$themeFolder} wurde geloescht."];
            } else {
                $this->log[] = ['type' => 'danger', 'message' => "Fehler beim Loeschen des Ordners {$themeFolder}."];
            }
        } else {
            $this->log[] = ['type' => 'warning', 'message' => "Ordner {$themeFolder} nicht gefunden."];
        }

        if ($_database instanceof \mysqli) {
            $themeFolderEscaped = $_database->real_escape_string($themeFolder);
            safe_query("DELETE FROM settings_themes_installed WHERE modulname = '" . $themeFolderEscaped . "' OR folder = '" . $themeFolderEscaped . "'");
            safe_query("DELETE FROM settings_theme_options WHERE theme_slug = '" . $themeFolderEscaped . "'");
            $this->log[] = ['type' => 'info', 'message' => 'Eintraege in der Datenbank geloescht.'];
        }
    }

    public function getLog(): array
    {
        return $this->log;
    }
}
