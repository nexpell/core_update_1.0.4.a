<?php
return [
    'step_title' => 'Schritt 5: Admin-Konto erstellen',
    'section_admin_account' => 'Administrator-Konto erstellen',
    'intro_text' => 'Lege dein persönliches Administratorkonto an, das der Hauptzugang zu deinem neuen CMS sein wird. Dieses Konto hat Vollzugriff auf alle Funktionen, einschließlich der Verwaltung von Inhalten, Benutzern und Systemkonfigurationen. Wähle einen starken Benutzernamen und ein sicheres Passwort, um dein System zu schützen. Nach Abschluss der Installation wirst du mit diesen Zugangsdaten im Adminbereich deines CMS einloggen.',
    'field_username' => 'Benutzername:',
    'field_email' => 'E-Mail:',
    'field_password' => 'Passwort:',
    'field_weburl' => 'Web-URL:',
    'button_create_admin' => 'Admin-Konto erstellen',
    'msg_error_fields_empty' => '❌ Bitte alle Felder ausfüllen.',
    
    // Status- und Fehlermeldungen
    'msg_error_config_missing' => '❌ Konfigurationsdatei fehlt. Bitte zuerst Schritt 2 und 3 durchführen.',
    'msg_error_db_connect' => '❌ Fehler bei der Datenbankverbindung: ',
    'msg_error_sql_not_found' => '❌ Admin-SQL-Datei nicht gefunden: ',
    'msg_error_exec_sql' => '❌ Fehler beim Ausführen der SQL-Befehle: ',
    'msg_error_partial_sql' => '⚠️ Fehler in einem SQL-Teil: ',
    'msg_admin_exists' => 'ℹ️ Admin-User existiert bereits. Du kannst <a href="?reset=1" class="btn btn-sm btn-outline-danger">hier zurücksetzen</a>.',
    'msg_success' => '✅ Admin-Konto erfolgreich erstellt! Du wirst in wenigen Sekunden automatisch zu <strong>Schritt 6</strong> weitergeleitet.',
    'msg_redirecting' => 'Du wirst in wenigen Sekunden automatisch weitergeleitet.',

    // Debug-Meldungen (optional, für Entwickler)
    'debug_reset_start' => '🧹 Debug: Admin-Datenbankeinträge werden entfernt...',
    'debug_reset_complete' => '✅ Debug: Reset abgeschlossen.',
];