ALTER TABLE `settings_themes`
  ADD COLUMN `slug` varchar(120) NOT NULL DEFAULT 'default' AFTER `modulname`,
  ADD COLUMN `manifest_path` varchar(255) DEFAULT NULL AFTER `pfad`,
  ADD COLUMN `layout_file` varchar(255) DEFAULT NULL AFTER `manifest_path`,
  ADD COLUMN `preview_image` varchar(255) DEFAULT NULL AFTER `layout_file`,
  ADD COLUMN `description` text DEFAULT NULL AFTER `preview_image`;

UPDATE `settings_themes`
SET `slug` = LOWER(COALESCE(NULLIF(`modulname`, ''), NULLIF(`pfad`, ''), 'default'))
WHERE `slug` = '' OR `slug` IS NULL;

UPDATE `settings_themes`
SET `manifest_path` = CONCAT('includes/themes/', `pfad`, '/theme.json')
WHERE (`manifest_path` IS NULL OR `manifest_path` = '') AND `pfad` <> '';

ALTER TABLE `settings_themes`
  ADD UNIQUE KEY `uniq_theme_slug` (`slug`);

CREATE TABLE IF NOT EXISTS `settings_theme_options` (
  `optionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `theme_slug` varchar(120) NOT NULL,
  `option_key` varchar(120) NOT NULL,
  `option_value` longtext DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`optionID`),
  UNIQUE KEY `uniq_theme_option` (`theme_slug`, `option_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
