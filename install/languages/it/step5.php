<?php
return [
    'step_title' => 'Passaggio 5: Crea un account Admin',
    'section_admin_account' => 'Crea un account amministratore',
    'intro_text' => 'Crea il tuo account amministratore personale, che sarà l\'accesso principale al tuo nuovo CMS. Questo account ha pieno accesso a tutte le funzioni, inclusa la gestione di contenuti, utenti e configurazioni di sistema. Scegli un nome utente forte e una password sicura per proteggere il tuo sistema. Al termine dell\'installazione, accederai all\'area admin del tuo CMS con queste credenziali.',
    'field_username' => 'Nome utente:',
    'field_email' => 'Email:',
    'field_password' => 'Password:',
    'field_weburl' => 'URL web:',
    'button_create_admin' => 'Crea account Admin',
    'msg_error_fields_empty' => '❌ Si prega di compilare tutti i campi.',
    
    // Messaggi di stato e di errore
    'msg_error_config_missing' => '❌ File di configurazione mancante. Si prega di completare prima i passaggi 2 e 3.',
    'msg_error_db_connect' => '❌ Errore di connessione al database: ',
    'msg_error_sql_not_found' => '❌ File SQL admin non trovato: ',
    'msg_error_exec_sql' => '❌ Errore nell\'esecuzione dei comandi SQL: ',
    'msg_error_partial_sql' => '⚠️ Errore in una parte SQL: ',
    'msg_admin_exists' => 'ℹ️ L\'utente admin esiste già. Puoi <a href="?reset=1" class="btn btn-sm btn-outline-danger">resettare qui</a>.',
    'msg_success' => '✅ Account amministratore creato con successo! Sarai reindirizzato automaticamente al <strong>passaggio 6</strong> tra pochi secondi.',
    'msg_redirecting' => 'Sarai reindirizzato automaticamente tra pochi secondi.',

    // Messaggi di debug (opzionale, per gli sviluppatori)
    'debug_reset_start' => '🧹 Debug: rimozione delle voci del database admin...',
    'debug_reset_complete' => '✅ Debug: reset completato.',
];