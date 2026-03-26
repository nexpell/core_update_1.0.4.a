<?php

class Theme
{
    public function get_active_theme(): string
    {
        $themeManager = $GLOBALS['nx_theme_manager'] ?? null;
        if ($themeManager instanceof \nexpell\ThemeManager) {
            return $themeManager->getActiveThemeRelativePath() . '/';
        }

        return 'includes/themes/default/';
    }
}
