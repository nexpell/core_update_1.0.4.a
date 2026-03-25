<?php
return [
    'step_title' => 'Passaggio 4: Importa la struttura del database',
    'installer_title' => 'Importa la struttura del database – Passaggio 4',
    
    // Messaggi e stato
    'error_config_not_found' => '❌ File di configurazione non trovato. Si prega di completare prima il passaggio 3.',
    'error_db_connect' => '❌ Errore di connessione al database: ',
    'error_sql_not_found' => '❌ Il file SQL non è stato trovato.',
    'error_import' => '❌ Errore durante l\'importazione del database: ',
    'error_import_duplicate_key' => '<br>Questo errore si verifica perché il database non è vuoto. Si prega di eliminare tutte le tabelle o di utilizzare un nuovo database vuoto per procedere.',
    'success_import' => '✅ Importazione riuscita! <br><br>La struttura di base del database di nexpell è stata importata con successo. Tutte le tabelle e i dati predefiniti necessari sono stati creati. Sarai reindirizzato al passaggio successivo in pochi istanti per creare il tuo account amministratore.',
    'button_continue' => 'Continua al passaggio 5 (Account Admin)',
    'button_import' => 'Importa database',

    // Testo principale
    'intro_paragraph_1' => 'Con un clic, la struttura di base del database di nexpell verrà configurata automaticamente. Tutte le tabelle necessarie, i valori predefiniti e i dati di configurazione dal file <code>install/database.sql</code> verranno importati nel tuo database MySQL.',
    'intro_paragraph_2' => 'Questo passaggio è essenziale affinché nexpell funzioni correttamente. Tra gli altri, verranno creati i seguenti contenuti:',
    'intro_paragraph_3' => 'Assicurati che la tua connessione al database sia configurata correttamente e che il tuo utente abbia le autorizzazioni necessarie per creare tabelle.',
    'intro_paragraph_4' => 'Dopo l\'importazione riuscita, sarai reindirizzato automaticamente al passaggio successivo per creare il tuo account amministratore.',

    // Punti elenco
    'list_items' => [
        'Tabelle di sistema per utenti, ruoli e diritti di accesso',
        'Impostazioni di sistema di base e moduli predefiniti',
        'Contenuto di esempio per un migliore orientamento',
    ],
];