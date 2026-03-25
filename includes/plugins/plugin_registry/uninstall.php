<?php
if (!defined('IN_ADMIN')) exit;

safe_query("DROP TABLE IF EXISTS plugins_registry_log");

safe_query("
DELETE FROM navigation_dashboard_links
WHERE modulname='plugin_registry'
");
