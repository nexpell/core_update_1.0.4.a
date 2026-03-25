<?php

global $_database;

safe_query("CREATE TABLE IF NOT EXISTS plugins_messages (
  id INT(11) NOT NULL AUTO_INCREMENT,
  sender_id VARCHAR(255) NOT NULL,
  receiver_id VARCHAR(255) NOT NULL,
  text TEXT NOT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

safe_query("
    INSERT IGNORE INTO settings_plugins
        (pluginID, modulname, admin_file, activate, author, website, index_link, hiddenfiles, version, path, status_display, plugin_display, widget_display, delete_display, sidebar)
    VALUES
        ('', 'messenger', '', 1, 'T-Seven', 'https://www.nexpell.de', 'messenger', '', '1.0.3.3', 'includes/plugins/messenger/', 1, 1, 1, 1, 'deactivated')
");

safe_query("
    INSERT IGNORE INTO settings_plugins_lang
        (content_key, language, content, modulname, updated_at)
    VALUES
        ('plugin_name_messenger', 'de', 'Messenger', 'messenger', NOW()),
        ('plugin_name_messenger', 'en', 'Messenger', 'messenger', NOW()),
        ('plugin_name_messenger', 'it', 'Messenger', 'messenger', NOW()),
        ('plugin_info_messenger', 'de', 'Mit diesem Plugin kannst du private Nachrichten zwischen Benutzern senden und empfangen.', 'messenger', NOW()),
        ('plugin_info_messenger', 'en', 'With this plugin you can send and receive private messages between users.', 'messenger', NOW()),
        ('plugin_info_messenger', 'it', 'Con questo plugin puoi inviare e ricevere messaggi privati tra gli utenti.', 'messenger', NOW())
    ON DUPLICATE KEY UPDATE
        content = VALUES(content),
        modulname = VALUES(modulname),
        updated_at = VALUES(updated_at)
");

safe_query("
    INSERT IGNORE INTO settings_plugins_installed
        (name, modulname, description, version, author, url, folder, installed_date)
    VALUES
        ('Messenger', 'messenger', 'With this plugin you can send and receive private messages between users.', '1.0.3.3', 'nexpell-team', 'https://www.nexpell.de', 'messenger', NOW())
    ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        description = VALUES(description),
        version = VALUES(version),
        author = VALUES(author),
        url = VALUES(url),
        folder = VALUES(folder),
        installed_date = NOW()
");

safe_query("
  INSERT IGNORE INTO user_role_admin_navi_rights (id, roleID, type, modulname)
  VALUES ('', 1, 'link', 'messenger')
");
?>
