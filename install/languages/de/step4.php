<?php
return [
    'step_title' => 'Schritt 4: Datenbankstruktur importieren',
    'installer_title' => 'Datenbankstruktur importieren – Schritt 4',
    
    // Meldungen und Status
    'error_config_not_found' => '❌ Konfigurationsdatei nicht gefunden. Bitte zuerst Schritt 3 abschließen.',
    'error_db_connect' => '❌ Fehler bei der Datenbankverbindung: ',
    'error_sql_not_found' => '❌ SQL-Datei wurde nicht gefunden.',
    'error_import' => '❌ Fehler beim Importieren der Datenbank: ', // Text bleibt nur als Präfix
    'error_import_duplicate_key' => '<br>Dieser Fehler tritt auf, weil die Datenbank nicht leer ist. Bitte lösche alle Tabellen oder verwende eine neue, leere Datenbank, um fortzufahren.',
    'success_import' => '✅ Import erfolgreich! <br><br>Die grundlegende Datenbankstruktur von nexpell wurde erfolgreich importiert. Alle erforderlichen Tabellen und Standarddaten sind nun angelegt. Du wirst in wenigen Augenblicken zum nächsten Schritt weitergeleitet, um dein Admin-Konto anzulegen.',
    'button_continue' => 'Weiter zu Schritt 5 (Admin-Konto)',
    'button_import' => 'Datenbank importieren',

    // Haupttext
    'intro_paragraph_1' => 'Mit einem Klick wird die grundlegende Datenbankstruktur von nexpell automatisch eingerichtet. Dabei werden alle erforderlichen Tabellen, Standardwerte und Konfigurationsdaten aus der Datei <code>install/database.sql</code> in deine MySQL-Datenbank importiert.',
    'intro_paragraph_2' => 'Dieser Schritt ist essenziell, damit nexpell korrekt funktioniert. Es werden unter anderem folgende Inhalte angelegt:',
    'intro_paragraph_3' => 'Stelle sicher, dass deine Datenbankverbindung korrekt eingerichtet ist und dein Benutzer die nötigen Berechtigungen zum Erstellen von Tabellen hat.',
    'intro_paragraph_4' => 'Nach dem erfolgreichen Import wirst du automatisch zum nächsten Schritt weitergeleitet, um dein Admin-Konto anzulegen.',

    // Listenpunkte
    'list_items' => [
        'Systemtabellen für Benutzer, Rollen und Zugriffsrechte',
        'Grundeinstellungen des Systems und Standardmodule',
        'Beispielinhalte zur besseren Orientierung',
    ],
];