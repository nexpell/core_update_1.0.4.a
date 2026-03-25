<?php
return [
    'step_title' => 'Step 4: Import Database Structure',
    'installer_title' => 'Import Database Structure – Step 4',
    
    // Messages and Status
    'error_config_not_found' => '❌ Configuration file not found. Please complete Step 3 first.',
    'error_db_connect' => '❌ Database connection error: ',
    'error_sql_not_found' => '❌ SQL file was not found.',
    'error_import' => '❌ Error importing the database: ',
    'error_import_duplicate_key' => '<br>This error occurs because the database is not empty. Please delete all tables or use a new, empty database to proceed.',
    'success_import' => '✅ Import successful! <br><br>The basic nexpell database structure has been successfully imported. All required tables and default data are now created. You will be redirected to the next step in a moment to create your admin account.',
    'button_continue' => 'Continue to Step 5 (Admin Account)',
    'button_import' => 'Import Database',

    // Main text
    'intro_paragraph_1' => 'With one click, the basic database structure of nexpell will be set up automatically. All required tables, default values, and configuration data from the <code>install/database.sql</code> file will be imported into your MySQL database.',
    'intro_paragraph_2' => 'This step is essential for nexpell to function correctly. The following content will be created, among others:',
    'intro_paragraph_3' => 'Make sure your database connection is set up correctly and your user has the necessary permissions to create tables.',
    'intro_paragraph_4' => 'After the successful import, you will be automatically redirected to the next step to create your admin account.',

    // List items
    'list_items' => [
        'System tables for users, roles, and access rights',
        'Basic system settings and default modules',
        'Example content for better orientation',
    ],
];