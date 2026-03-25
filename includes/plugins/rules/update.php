<?php
global $str, $modulname, $version;

$modulname = 'rules';
$version = '1.0.4';
$str = 'Rules';

echo "<div class='card'><div class='card-header'>{$str} Database Update</div><div class='card-body'>";

safe_query("CREATE TABLE IF NOT EXISTS plugins_rules (
  id INT(11) NOT NULL AUTO_INCREMENT,
  content_key VARCHAR(50) NOT NULL,
  language CHAR(2) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  userID INT(11) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT(11) DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_content_lang (content_key, language),
  KEY idx_content_key (content_key),
  KEY idx_language (language)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci");

$rulesSettingsCheck = safe_query("SHOW TABLES LIKE 'plugins_rules_settings'");
if ($rulesSettingsCheck instanceof mysqli_result && $rulesSettingsCheck->num_rows > 0) {
    safe_query("DROP TABLE plugins_rules_settings");
    echo "<div class='alert alert-info'>Legacy table <code>plugins_rules_settings</code> removed.</div>";
} else {
    echo "<div class='alert alert-secondary'>Legacy table <code>plugins_rules_settings</code> already removed.</div>";
}

echo "</div></div>";
?>
