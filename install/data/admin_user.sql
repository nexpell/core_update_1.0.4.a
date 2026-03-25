-- nexpell - Datenbankbasis
-- 28.05.2025

INSERT INTO `users` (
  `userID`, `registerdate`, `lastlogin`, `password_hash`, `password_pepper`, 
  `username`, `email`, `email_hide`, `email_change`, `email_activate`, 
  `role`, `is_active`, `is_locked`, `activation_code`, `activation_expires`, 
  `visits`, `language`, `last_update`, `login_time`, `last_activity`, `total_online_seconds`, `is_online`
) VALUES (
  1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '{adminpass}', '{adminpepper}', '{adminuser}', '{adminmail}', 1, '', '', 1, 1, 0, NULL, NULL, 0, 'de', NULL, NULL, NULL, 0, 0
);

INSERT INTO `user_role_assignments` (`assignmentID`, `userID`, `roleID`, `created_at`, `assigned_at`) 
VALUES (1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `contact` (`contactID`, `name`, `email`, `sort`) 
VALUES (1, 'Administrator', '{adminmail}', 1);

INSERT INTO `settings` (
  `settingID`, `hptitle`, `hpurl`, `clanname`, `clantag`, `adminname`, `adminemail`, `since`,
  `webkey`, `seckey`, `closed`, `default_language`, `keywords`, `startpage`, `use_seo_urls`
  ) VALUES (
  1, 'nexpell', '{adminweburl}', 'Mein Clan / Verein', '[RM]', '{adminuser}', '{adminmail}', 2025,
  'PLACEHOLDER_WEBKEY', 'PLACEHOLDER_SECKEY', 0, 'de', 
  'nexpell, CMS, Community-Management, Esport CMS, Webdesign, Clan-Design, Templates, Plugins, Addons, Mods, Anpassungen, Modifikationen, Tutorials, Downloads, Plugin-Entwicklung, Design-Anpassungen, Website-Builder, Digitales Projektmanagement',
  'startpage', 0
);

INSERT IGNORE INTO `user_username` (`userID`, `username`) 
VALUES (1, '{adminuser}');

INSERT INTO `settings_imprint` (`id`, `type`, `company_name`, `represented_by`, `tax_id`, `email`, `website`, `phone`, `disclaimer`, `address`, `postal_code`, `city`, `register_office`, `register_number`, `vat_id`, `supervisory_authority`, editor) VALUES
(1, 'private', '{adminuser}', '', '', '{adminmail}', '{adminweburl}', '+49 123 4567890', '', '', '', '', '', '', '', '', 0);
